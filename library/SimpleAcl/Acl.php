<?php
namespace SimpleAcl;

use SimpleAcl\Role;
use SimpleAcl\Resource;
use SimpleAcl\Rule;

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
     * Return true if rule was already added.
     *
     * @param Rule $needRule
     * @return bool
     */
    public function hasRule(Rule $needRule)
    {
        foreach ( $this->rules as $rule ) {
            if ( $rule === $needRule ) {
                return true;
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
     * @param Role $role
     * @param Resource $resource
     * @param Rule $rule
     * @param mixed $action
     */
    public function addRule(Role $role, Resource $resource, Rule $rule, $action)
    {
        $rule->setRole($role);
        $rule->setResource($resource);
        $rule->setAction($action);

        if ( ! $this->hasRule($rule) ) {
            $this->rules[] = $rule;
        }
    }

    /**
     * Checks is access allowed.
     *
     * @param string $roleName
     * @param string $resourceName
     * @param string $ruleName
     * @return bool
     */
    public function isAllowed($roleName, $resourceName, $ruleName)
    {
        foreach ( $this->rules as $rule ) {
            if ( $rule->getName() == $ruleName ) {
                $isAllowed = $rule->isAllowed($roleName, $resourceName);
                if ( $isAllowed !== null ) {
                    return $isAllowed;
                }
            }
        }

        return false;
    }
}
