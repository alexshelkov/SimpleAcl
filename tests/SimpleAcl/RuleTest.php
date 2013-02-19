<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;

use SimpleAcl\Role;
use SimpleAcl\Resource;
use SimpleAcl\Rule;

class RuleTest extends PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $rule = new Rule('Rule');
        $this->assertEquals($rule->getName(), 'Rule');
        $rule->setName('NewRuleName');
        $this->assertEquals($rule->getName(), 'NewRuleName');
    }

    public function testAction()
    {
        $rule = new Rule('Rule');
        $rule->setAction(true);
        $this->assertTrue($rule->getAction());

        $rule->setAction(false);
        $this->assertFalse($rule->getAction());

        // Action can be mixed, but getAction must return bool
        $a = array();
        $rule->setAction($a);
        $this->assertFalse($rule->getAction());
        $this->assertAttributeEquals($a, 'action', $rule);

        $a = array(1, 2, 3);
        $rule->setAction($a);
        $this->assertTrue($rule->getAction());
        $this->assertAttributeEquals($a, 'action', $rule);
    }

    public function testRolesAndResources()
    {
        $rule = new Rule('Rule');

        $role = new Role('Role');
        $rule->setRole($role);
        $this->assertSame($rule->getRole(), $role);

        $resource = new Resource('Resource');
        $rule->setResource($resource);
        $this->assertSame($rule->getResource(), $resource);
    }
}