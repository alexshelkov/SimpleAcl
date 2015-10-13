<?php
namespace SimpleAcl\Role;

use SimpleAcl\Object\ObjectAggregate;
use SimpleAcl\Role;

/**
 * Holds roles.
 *
 * Allow pass itself to Acl::isAllowed method as $roleName.
 *
 * @package SimpleAcl\Role
 */
class RoleAggregate extends ObjectAggregate implements RoleAggregateInterface
{
  /**
   * Add role.
   *
   * @param Role $role
   */
  public function addRole(Role $role)
  {
    parent::addObject($role);
  }

  /**
   * Remove all roles.
   *
   */
  public function removeRoles()
  {
    parent::removeObjects();
  }

  /**
   * Remove role by name.
   *
   * @param $roleName
   */
  public function removeRole($roleName)
  {
    parent::removeObject($roleName);
  }

  /**
   * Add roles from array.
   *
   * @param array $roles
   */
  public function setRoles($roles)
  {
    parent::setObjects($roles);
  }

  /**
   * Return all roles.
   *
   * @return array()|Role[]
   */
  public function getRoles()
  {
    return parent::getObjects();
  }

  /**
   * Return array of names for registered roles.
   *
   * @return array
   */
  public function getRolesNames()
  {
    return parent::getObjectNames();
  }

  /**
   * Return role by name.
   *
   * @param $roleName
   *
   * @return null|Role
   */
  public function getRole($roleName)
  {
    return parent::getObject($roleName);
  }
}
