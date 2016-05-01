<?php
namespace SimpleAcl;

use SimpleAcl\Role;
use SimpleAcl\Resource;
use SimpleAcl\Rule;

use SimpleAcl\Role\RoleAggregateInterface;
use SimpleAcl\Resource\ResourceAggregateInterface;
use SimpleAcl\Exception\InvalidArgumentException;
use SimpleAcl\Exception\RuntimeException;
use SimpleAcl\RuleResultCollection;

/**
 * Access Control List (ACL) management.
 *
 */
class Acl
{
    /**
     * Contains registered rules.
     *
     * @var Rule[]
     */
    protected $rules = array();

    /**
     * Class name used when rule created from string.
     *
     * @var string
     */
    protected $ruleClass = 'SimpleAcl\Rule';

    /**
     * Set rule class.
     *
     * @param string $ruleClass
     */
    public function setRuleClass($ruleClass)
    {
        if ( ! class_exists($ruleClass) ) {
            throw new RuntimeException('Rule class not exist');
        }

        if ( ! is_subclass_of($ruleClass, 'SimpleAcl\Rule') && $ruleClass != 'SimpleAcl\Rule' ) {
            throw new RuntimeException('Rule class must be instance of SimpleAcl\Rule');
        }

        $this->ruleClass = $ruleClass;
    }

    /**
     * Return rule class.
     *
     * @return string
     */
    public function getRuleClass()
    {
        return $this->ruleClass;
    }

    /**
     * Return true if rule was already added.
     *
     * @param Rule | mixed $needRule Rule or rule's id
     * @return bool
     */
    public function hasRule($needRule)
    {
        $needRuleId = ($needRule instanceof Rule) ? $needRule->getId() : $needRule;
        
        foreach ( $this->rules as $rule ) {
            if ( $rule->getId() === $needRuleId ) {
                return $rule;
            }
        }

        return false;
    }

    /**
     * Adds rule.
     *
     * Assign $role, $resource and $action to added rule.
     * If rule was already registered only change $role, $resource and $action for that rule.
     *
     * This method accept 1, 2, 3 or 4 arguments:
     *
     * addRule($rule)
     * addRule($rule, $action)
     * addRule($role, $resource, $rule)
     * addRule($role, $resource, $rule, $action)
     *
     * @param Role $role
     * @param Resource $resource
     * @param Rule|string $rule
     * @param mixed $action
     *
     * @throws InvalidArgumentException
     */
    public function addRule()
    {
        $args = func_get_args();
        $argsCount = count($args);

        $role = null;
        $resource = null;
        $action = null;

        if ( $argsCount == 4 || $argsCount == 3 ) {
            $role = $args[0];
            $resource = $args[1];
            $rule = $args[2];
            if ( $argsCount == 4) {
                $action = $args[3];
            }
        } elseif( $argsCount == 2 ) {
            $rule = $args[0];
            $action = $args[1];
        } elseif ( $argsCount == 1 ) {
            $rule = $args[0];
        } else {
            throw new InvalidArgumentException(__METHOD__ . ' accepts only one, tow, three or four arguments');
        }

        if ( ! is_null($role) && ! $role instanceof Role ) {
            throw new InvalidArgumentException('Role must be an instance of SimpleAcl\Role or null');
        }

        if ( ! is_null($resource) && ! $resource instanceof Resource ) {
            throw new InvalidArgumentException('Resource must be an instance of SimpleAcl\Resource or null');
        }

        if ( is_string($rule) ) {
            $ruleClass = $this->getRuleClass();
            $rule = new $ruleClass($rule);
        }

        if ( ! $rule instanceof Rule ) {
            throw new InvalidArgumentException('Rule must be an instance of SimpleAcl\Rule or string');
        }

        if ( $exchange = $this->hasRule($rule) ) {
            $rule = $exchange;
        }

        if ( ! $exchange ) {
            $this->rules[] = $rule;
        }

        if ( $argsCount == 3 || $argsCount == 4 ) {
            $rule->setRole($role);
            $rule->setResource($resource);
        }

        if ( $argsCount == 2 || $argsCount == 4 ) {
            $rule->setAction($action);
        }
    }

    /**
     * Get names.
     *
     * @param string|RoleAggregateInterface|ResourceAggregateInterface $object
     * @return array
     */
    protected function getNames($object)
    {
        if ( is_string($object) || is_null($object) ) {
            return array($object);
        } elseif ( $object instanceof RoleAggregateInterface ) {
            return $object->getRolesNames();
        } elseif ( $object instanceof ResourceAggregateInterface ) {
            return $object->getResourcesNames();
        }

        return array();
    }

    /**
     * Check is access allowed by some rule.
     * Returns null if rule don't match any role or resource.
     *
     * @param string $roleName
     * @param string $resourceName
     * @param $ruleName
     * @param RuleResultCollection $ruleResultCollection
     * @param string|RoleAggregateInterface $roleAggregate
     * @param string|ResourceAggregateInterface $resourceAggregate
     */
    protected function isRuleAllow($roleName, $resourceName, $ruleName, RuleResultCollection $ruleResultCollection, $roleAggregate, $resourceAggregate)
    {
        foreach ($this->rules as $rule) {
            $rule->resetAggregate($roleAggregate, $resourceAggregate);

            $result = $rule->isAllowed($ruleName, $roleName, $resourceName);
            $ruleResultCollection->add($result);
        }
    }

    /**
     * Checks is access allowed.
     *
     * @param string|RoleAggregateInterface $roleName
     * @param string|ResourceAggregateInterface $resourceName
     * @param string $ruleName
     * @return bool
     */
    public function isAllowed($roleName, $resourceName, $ruleName)
    {
        return $this->isAllowedReturnResult($roleName, $resourceName, $ruleName)->get();
    }

    /**
     * Checks is access allowed.
     *
     * @param string|RoleAggregateInterface $roleAggregate
     * @param string|ResourceAggregateInterface $resourceAggregate
     * @param string $ruleName
     *
     * @return RuleResultCollection
     */
    public function isAllowedReturnResult($roleAggregate, $resourceAggregate, $ruleName)
    {
        $ruleResultCollection = new RuleResultCollection();

        $roles = $this->getNames($roleAggregate);
        $resources = $this->getNames($resourceAggregate);

        foreach ($roles as $roleName) {
            foreach ($resources as $resourceName) {
                $this->isRuleAllow($roleName, $resourceName, $ruleName, $ruleResultCollection, $roleAggregate, $resourceAggregate);
            }
        }

        return $ruleResultCollection;
    }

    /**
     * Remove all rules.
     *
     */
    public function removeAllRules()
    {
        $this->rules = array();
    }

    /**
     * Remove rules by rule name and (or) role and resource.
     *
     * @param null|string $roleName
     * @param null|string $resourceName
     * @param null|string $ruleName
     * @param bool $all
     */
    public function removeRule($roleName = null, $resourceName = null, $ruleName = null, $all = true)
    {
        if ( is_null($roleName) && is_null($resourceName) && is_null($ruleName) ) {
            $this->removeAllRules();
            return;
        }

        foreach ( $this->rules as $ruleIndex => $rule ) {
            if ( $ruleName === null || ($ruleName !== null && $ruleName === $rule->getName()) ) {
                if ( $roleName === null || ($roleName !== null && $rule->getRole() && $rule->getRole()->getName() === $roleName) ) {
                    if ( $resourceName === null || ($resourceName !== null && $rule->getResource() && $rule->getResource()->getName() === $resourceName) ) {
                        unset($this->rules[$ruleIndex]);
                        if ( ! $all ) {
                            return;
                        }
                    }
                }
            }
        }
    }

    /**
     * Removes rule by its id.
     *
     * @param mixed $ruleId
     */
    public function removeRuleById($ruleId)
    {
        foreach ($this->rules as $ruleIndex => $rule) {
            if ( $rule->getId() === $ruleId ) {
                unset($this->rules[$ruleIndex]);
                return;
            }
        }
    }
}
