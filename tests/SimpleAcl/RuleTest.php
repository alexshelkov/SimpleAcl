<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;

use SimpleAcl\Role;
use SimpleAcl\Resource;
use SimpleAcl\Rule;
use SimpleAcl\RuleResult;

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
        $ruleResult = new RuleResult($rule, 0, 'testNeedRoleName', 'testNeedResourceName');

        $rule->setAction(true);
        $this->assertTrue($rule->getAction($ruleResult));

        $rule->setAction(false);
        $this->assertFalse($rule->getAction($ruleResult));

        // Action can be mixed, but getAction must return bool
        $a = array();
        $rule->setAction($a);
        $this->assertFalse($rule->getAction($ruleResult));
        $this->assertAttributeEquals($a, 'action', $rule);

        $a = array(1, 2, 3);
        $rule->setAction($a);
        $this->assertTrue($rule->getAction($ruleResult));
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

    public function testId()
    {
        $rule = new Rule('Rule');

        $this->assertNotNull($rule->getId());
    }

    public function testActionCallable()
    {
        $rule = new Rule('Rule');
        $ruleResult = new RuleResult($rule, 0, 'testNeedRoleName', 'testNeedResourceName');

        $self = $this;
        $isCalled = false;

        $rule->setAction(function(RuleResult $r) use (&$isCalled, $self) {
            $isCalled = true;
            $self->assertEquals('testNeedRoleName', $r->getNeedRoleName());
            $self->assertEquals('testNeedResourceName', $r->getNeedResourceName());
            $self->assertEquals(0, $r->getPriority());

            return true;
        });

        $this->assertTrue($rule->getAction($ruleResult));

        $this->assertTrue($isCalled);
    }
}