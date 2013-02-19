<?php
namespace SimpleAcl;

use SimpleAcl\Resource;
use SimpleAcl\Role;
use SimpleAcl\RuleResult;

/**
 * Used to connects Role and Resources together.
 *
 */
class Rule
{
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
    protected $action;

    /**
     * @var Role
     */
    protected $role;

    /**
     * @var Resource
     */
    protected $resource;

    /**
     * Create Rule with given name.
     *
     * @param $name
     */
    public function __construct($name = null)
    {
        $this->setName($name);
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
     * @return bool
     */
    public function getAction()
    {
        return (bool)$this->action;
    }

    /**
     * Used for recursively walk by Role & Resource children.
     *
     * @param Role $role
     * @param Resource $resource
     * @param string $needRoleName
     * @param string $needResourceName
     * @param $priority
     *
     * @return RuleResult|null
     */
    protected function isAllowedRecursive(Role $role, Resource $resource, $needRoleName, $needResourceName, $priority)
    {
        if ( $role->getName() == $needRoleName && $resource->getName() == $needResourceName ) {
            return new RuleResult($this, $priority);
        }

        foreach ( $role->getChildren() as $child ) {
            $isAllowed = $this->isAllowedRecursive($child, $resource, $needRoleName, $needResourceName, $priority - 1);
            if ( $isAllowed !== null ) {
                return $isAllowed;
            }
        }

        foreach ( $resource->getChildren() as $child ) {
            $isAllowed = $this->isAllowedRecursive($role, $child, $needRoleName, $needResourceName, $priority - 1);
            if ( $isAllowed !== null ) {
                return $isAllowed;
            }
        }

        return null;
    }

    /**
     * Check owing Role & Resource (and their children) and match its with $roleName & $resourceName;
     * if match was found depending on action allow or deny access to $resourceName for $roleName.
     *
     * @param string $roleName
     * @param string $resourceName
     * @return RuleResult|null null is returned if there is no matched Role & Resource in this rule.
     *                         RuleResult otherwise.
     */
    public function isAllowed($roleName, $resourceName)
    {
        return $this->isAllowedRecursive($this->getRole(), $this->getResource(), $roleName, $resourceName, 0);
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
}