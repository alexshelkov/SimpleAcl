<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;

use SimpleAcl\RuleResultCollection;
use SimpleAcl\RuleResult;
use SimpleAcl\Rule;

class RuleResultCollectionTest extends PHPUnit_Framework_TestCase
{
    public function testEmpty()
    {
        $collection = new RuleResultCollection();
        $this->assertFalse($collection->any());
        $this->assertFalse($collection->get());
    }

    public function testAddNull()
    {
        $collection = new RuleResultCollection();

        $collection->add(null);

        $this->assertFalse($collection->any());
        $this->assertFalse($collection->get());
    }

    public function testAdd()
    {
        $collection = new RuleResultCollection();

        $rule = new Rule('Test');
        $result = new RuleResult($rule, 0, 'testNeedRole', 'testNeedResource');

        $collection->add($result);

        $this->assertTrue($collection->any());
        $this->assertEquals($result->getAction(), $collection->get());

        $index = 0;
        foreach ( $collection as $r ) {
            $this->assertSame($result, $r);
            $index++;
        }
        $this->assertEquals(1, $index);
    }

    public function testMultipleAdd()
    {
        $collection = new RuleResultCollection();

        $rule = new Rule('Test');
        $result = new RuleResult($rule, 0, 'testNeedRole', 'testNeedResource');

        $rule2 = new Rule('Test2');
        $result2 = new RuleResult($rule2, 0, 'testNeedRole', 'testNeedResource');

        $collection->add($result);
        $collection->add($result2);

        $this->assertTrue($collection->any());

        $results = array($result, $result2);

        $index = 0;
        foreach ( $collection as $r ) {
            $this->assertSame($results[$index], $r);
            $index++;
        }
        $this->assertEquals(2, $index);
    }
}