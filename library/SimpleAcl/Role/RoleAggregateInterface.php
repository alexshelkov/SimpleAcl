<?php
namespace SimpleAcl\Role;

use SimpleAcl\Role;

/**
 * Interface RoleAggregateInterface
 *
 * @package SimpleAcl\Role
 */
interface RoleAggregateInterface
{
  /**
   * Return array of names for registered roles.
   *
   * @return array
   */
  public function getRolesNames();
}