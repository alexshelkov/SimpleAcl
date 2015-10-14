<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;

use SimpleAcl\RuleResult;
use SimpleAcl\Rule;

class RuleResultTest extends PHPUnit_Framework_TestCase
{
    public function testRuleResult()
    {
        $roleAggregate = $this->getMock('SimpleAcl\Role\RoleAggregateInterface');
        $resourceAggregate = $this->getMock('SimpleAcl\Resource\ResourceAggregateInterface');

        $rule = new Rule('Test');

        $rule->setRoleAggregate($roleAggregate);
        $rule->setResourceAggregate($resourceAggregate);
        $rule->setAction(true);
        $result = new RuleResult($rule, 0, 'testNeedRole', 'testNeedResource');

        $this->assertSame($rule, $result->getRule());
        $this->assertEquals('testNeedRole', $result->getNeedRoleName());
        $this->assertEquals('testNeedResource', $result->getNeedResourceName());
        $this->assertEquals(0, $result->getPriority());
        $this->assertEquals($rule->getAction($result), $result->getAction());

        $result->setPriority(10);
        $this->assertEquals(10, $result->getPriority());

        $this->assertSame($roleAggregate, $result->getRoleAggregate());
        $this->assertSame($resourceAggregate, $result->getResourceAggregate());

        $this->assertNotEmpty($result->getId());
    }
}