<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;

use SimpleAcl\Role;
use SimpleAcl\Resource;

class RoleAndResourceTest extends PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $role = new Role('Role');
        $this->assertEquals($role->getName(), 'Role');
        $role->setName('NewRoleName');
        $this->assertEquals($role->getName(), 'NewRoleName');

        $resource= new Resource('Resource');
        $this->assertEquals($resource->getName(), 'Resource');
        $resource->setName('NewResourceName');
        $this->assertEquals($resource->getName(), 'NewResourceName');
    }

    public function testParent()
    {
        $role = new Role('Role');
        $parentRole = new Role('ParentRole');
        $role->setParent($parentRole);
        $this->assertSame($role->getParent(), $parentRole);

        $role->setParent();
        $this->assertNull($role->getParent());

        $resource = new Resource('Resource');
        $parentResource = new Resource('ParentResource');
        $resource->setParent($parentResource);
        $this->assertSame($resource->getParent(), $parentResource);

        $resource->setParent();
        $this->assertNull($resource->getParent());
    }
}