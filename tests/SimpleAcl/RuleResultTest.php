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
        $result = new RuleResult($rule, 0);

        $this->assertSame($rule, $result->getRule());
        $this->assertEquals(0, $result->getPriority());
        $this->assertEquals($rule->getAction(), $result->getAction());
    }
}