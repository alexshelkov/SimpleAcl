<?php
namespace SimpleAcl;

use SimpleAcl\Resource;
use SimpleAcl\Role;

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
    public function __construct($name)
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
     * Used for recursively walk by Role & Resource parents.
     *
     * @param Role $role
     * @param Resource $resource
     * @param string $needRoleName
     * @param string $needResourceName
     * @return bool|null
     */
    protected function isAllowedRecursive(Role $role, Resource $resource, $needRoleName, $needResourceName)
    {
        if ( $role->getName() == $needRoleName && $resource->getName() == $needResourceName ) {
            return $this->getAction() === true;
        }

        if ( $parent = $role->getParent() ) {
            $isAllowed = $this->isAllowedRecursive($parent, $resource, $needRoleName, $needResourceName);
            if ( $isAllowed !== null) {
                return $isAllowed;
            }
        }

        if ( $parent = $resource->getParent() ) {
            $isAllowed = $this->isAllowedRecursive($role, $parent, $needRoleName, $needResourceName);
            if ( $isAllowed !== null) {
                return $isAllowed;
            }
        }

        return null;
    }

    /**
     * Check owing Role & Resource (and their parents) and match its with $roleName & $resourceName;
     * if match was found depending on action allow or deny access to $resourceName for $roleName.
     *
     * @param string $roleName
     * @param string $resourceName
     * @return bool|null null is returned if there is no matched Role & Resource in this rule.
     *                   boll otherwise.
     */
    public function isAllowed($roleName, $resourceName)
    {
        return $this->isAllowedRecursive($this->getRole(), $this->getResource(), $roleName, $resourceName);
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
