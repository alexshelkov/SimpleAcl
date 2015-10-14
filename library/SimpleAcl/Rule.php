<?php
namespace SimpleAcl;

use SimpleAcl\Resource;
use SimpleAcl\Role;
use SimpleAcl\RuleResult;
use RecursiveIteratorIterator;

use SimpleAcl\Role\RoleAggregateInterface;
use SimpleAcl\Resource\ResourceAggregateInterface;

/**
 * Used to connects Role and Resources together.
 *
 */
class Rule
{
    /**
     * Holds rule id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Rule priority affect the order the rule is applied.
     *
     * @var int
     */
    protected $priority = 0;

    /**
     * Hold name of rule.
     *
     * @var string
     */
    protected $name;

    /**
     * Action used when determining is rule allow access to its Resource and Role.
     *
     * @var mixed
     */
    protected $action = false;

    /**
     * @var Role
     */
    protected $role;

    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @var RoleAggregateInterface
     */
    protected $roleAggregate;

    /**
     * @var ResourceAggregateInterface
     */
    protected $resourceAggregate;

    /**
     * Create Rule with given name.
     *
     * @param $name
     */
    public function __construct($name)
    {
        $this->setId();
        $this->setName($name);
    }

    /**
     * Set aggregate objects.
     *
     * @param $roleAggregate
     * @param $resourceAggregate
     */
    public function resetAggregate($roleAggregate, $resourceAggregate)
    {
        if ( $roleAggregate instanceof RoleAggregateInterface ) {
            $this->setRoleAggregate($roleAggregate);
        } else {
            $this->roleAggregate = null;
        }

        if ( $resourceAggregate instanceof ResourceAggregateInterface ) {
            $this->setResourceAggregate($resourceAggregate);
        } else {
            $this->resourceAggregate = null;
        }
    }

    /**
     * @param ResourceAggregateInterface $resourceAggregate
     */
    public function setResourceAggregate(ResourceAggregateInterface $resourceAggregate)
    {
        $this->resourceAggregate = $resourceAggregate;
    }

    /**
     * @return ResourceAggregateInterface
     */
    public function getResourceAggregate()
    {
        return $this->resourceAggregate;
    }

    /**
     * @param RoleAggregateInterface $roleAggregate
     */
    public function setRoleAggregate(RoleAggregateInterface $roleAggregate)
    {
        $this->roleAggregate = $roleAggregate;
    }

    /**
     * @return RoleAggregateInterface
     */
    public function getRoleAggregate()
    {
        return $this->roleAggregate;
    }

    /**
     * Creates an id for rule.
     *
     * @return string
     */
    protected function generateId()
    {
        return bin2hex(openssl_random_pseudo_bytes(10));
    }

    /**
     * @param mixed $id
     */
    public function setId($id = null)
    {
        if ( is_null($id) ) {
            $id = $this->generateId();
        }

        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @param RuleResult|null $ruleResult
     *
     * @return bool|null
     */
    public function getAction(RuleResult $ruleResult = null)
    {
        $actionResult = $this->action;
        if ( ! is_callable($actionResult) || is_null($ruleResult) ) {
            return is_null($actionResult) ? $actionResult : (bool)$actionResult;
        }

        $actionResult = call_user_func($this->action, $ruleResult);
        $actionResult = is_null($actionResult) ? $actionResult : (bool)$actionResult;

        return $actionResult;
    }

    /**
     * Check if $role and $resource match to need role and resource.
     *
     * @param Role|null $role
     * @param Resource|null $resource
     * @param string $needRoleName
     * @param string $needResourceName
     * @param $priority
     *
     * @return RuleResult|null
     */
    protected function match(Role $role = null, Resource $resource = null, $needRoleName, $needResourceName, $priority)
    {
        if ( (is_null($role) || ($role && $role->getName() === $needRoleName)) &&
            (is_null($resource) || ($resource && $resource->getName() === $needResourceName)) ) {
            return new RuleResult($this, $priority, $needRoleName, $needResourceName);
        }

        return null;
    }

    /**
     * Check if rule can be used.
     *
     * @param $neeRuleName
     * @return bool
     */
    protected function isRuleMatched($neeRuleName)
    {
        return $this->getName() === $neeRuleName;
    }

    /**
     * Check owing Role & Resource (and their children) and match its with $roleName & $resourceName;
     * if match was found depending on action allow or deny access to $resourceName for $roleName.
     *
     * @param $needRuleName
     * @param string $needRoleName
     * @param string $needResourceName
     *
     * @return RuleResult|null null is returned if there is no matched Role & Resource in this rule.
     *                         RuleResult otherwise.
     */
    public function isAllowed($needRuleName, $needRoleName, $needResourceName)
    {
        if ( $this->isRuleMatched($needRuleName) ) {
            if ( ! is_null($this->getRole()) ) {
                $roles = new RecursiveIteratorIterator($this->getRole(), RecursiveIteratorIterator::SELF_FIRST);
            } else {
                $roles = array(null);
            }

            if ( ! is_null($this->getResource()) ) {
                $resources = new RecursiveIteratorIterator($this->getResource(), RecursiveIteratorIterator::SELF_FIRST);
            } else {
                $resources = array(null);
            }

            foreach ($roles as $role) {
                foreach ($resources as $resource) {
                    $roleDepth = $role ? $roles->getDepth() : 0;
                    $resourceDepth = $resource ? $resources->getDepth() : 0;

                    $depth = $roleDepth + $resourceDepth;
                    $result = $this->match($role, $resource, $needRoleName, $needResourceName, -$depth);

                    if ( $result ) {
                        return $result;
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param Role|null $role
     */
    public function setRole(Role $role = null)
    {
        $this->role = $role;
    }

    /**
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param Resource|null $resource
     */
    public function setResource(Resource $resource = null)
    {
        $this->resource = $resource;
    }

    /**
     * @return Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }
}