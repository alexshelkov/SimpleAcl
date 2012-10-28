<?php
namespace SimpleAcl\Role;

use SimpleAcl\Role;

interface RoleAggregateInterface
{
    /**
     * Return array of names for registered roles.
     *
     * @return array
     */
    public function getRolesNames();
}