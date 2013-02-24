<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;

use SimpleAcl\RuleResult;
use SimpleAcl\Rule;

class RuleResultTest extends PHPUnit_Framework_TestCase
{
    public function testRuleResult()
    {
        $rule = new Rule('Test');
        $rule->setAction(true);
        $result = new RuleResult($rule, 0, 'testNeedRole', 'testNeedResource');

        $this->assertSame($rule, $result->getRule());
        $this->assertEquals('testNeedRole', $result->getNeedRoleName());
        $this->assertEquals('testNeedResource', $result->getNeedResourceName());
        $this->assertEquals(0, $result->getPriority());
        $this->assertEquals($rule->getAction($result), $result->getAction());
    }
}