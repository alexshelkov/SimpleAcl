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
     * @var bool
     */
    protected $isCacheAction = true;

    /**
     * @var array
     */
    public $cachedActions = array();

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

    public function resetAggregate()
    {
        $this->roleAggregate = null;
        $this->resourceAggregate = null;
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
     * @return mixed
     */
    protected function generateId()
    {
        return uniqid();
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
        $this->cachedActions = array();
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

        $id = $ruleResult->getId();

        if ( $this->isCacheAction() && array_key_exists($id, $this->cachedActions) ) {
            return $this->cachedActions[$id];
        }

        $actionResult = call_user_func($this->action, $ruleResult);
        $actionResult = is_null($actionResult) ? $actionResult : (bool)$actionResult;

        if ( $this->isCacheAction() ) {
            $this->cachedActions[$id] = $actionResult;
        }

        return $actionResult;
    }

    /**
     * Check if $role and $resource match to need role and resource.
     *
     * @param Role $role
     * @param Resource $resource
     * @param string $needRoleName
     * @param string $needResourceName
     * @param $priority
     *
     * @return RuleResult|null
     */
    protected function match(Role $role, Resource $resource, $needRoleName, $needResourceName, $priority)
    {
        if ( $role->getName() == $needRoleName && $resource->getName() == $needResourceName ) {
            return new RuleResult($this, $priority, $needRoleName, $needResourceName);
        }

        return null;
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
        if ( $this->getName() == $needRuleName ) {
            $this->cachedActions = array();

            $roles = new RecursiveIteratorIterator($this->getRole(), RecursiveIteratorIterator::SELF_FIRST);
            $resources = new RecursiveIteratorIterator($this->getResource(), RecursiveIteratorIterator::SELF_FIRST);
            foreach ($roles as $role) {
                foreach ($resources as $resource) {
                    $depth = $roles->getDepth() + $resources->getDepth();
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
     * @param Role $role
     */
    public function setRole(Role $role)
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
     * @param Resource $resource
     */
    public function setResource(Resource $resource)
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
     * @param boolean $isCacheActionInRuleResult
     */
    public function setIsCacheAction($isCacheActionInRuleResult)
    {
        $this->isCacheAction = $isCacheActionInRuleResult;
    }

    /**
     * @return boolean
     */
    public function isCacheAction()
    {
        return $this->isCacheAction;
    }
}