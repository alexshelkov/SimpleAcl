<?php
namespace SimpleAcl\Role;

use SimpleAcl\Role;

interface RoleAggregateInterface
{
    public function getRole($roleName);

    public function addRole(Role $role);

    public function setRoles($roles);

    public function removeRoles();

    public function removeRole($roleName);

    public function getRoles();
}