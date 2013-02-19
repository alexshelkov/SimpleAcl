<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;

use SimpleAcl\Object;

class ObjectTest extends PHPUnit_Framework_TestCase
{
    public function testName()
    {
        /** @var Object $object  */
        $object = $this->getMockForAbstractClass('SimpleAcl\Object', array('TestName'));

        $this->assertEquals($object->getName(), 'TestName');
        $object->setName('NewName');
        $this->assertEquals($object->getName(), 'NewName');
    }

    public function testAddChild()
    {
        /** @var Object $parent */
        $parent = $this->getMockForAbstractClass('SimpleAcl\Object', array('Parent'));

        $child = $this->getMockForAbstractClass('SimpleAcl\Object', array('Child'));

        $parent->addChild($child);

        $this->assertEquals(1, count($parent->getChildren()));
        $this->assertSame($child, $parent->hasChild($child));
    }

    public function testRemoveChild()
    {
        /** @var Object $parent */
        $parent = $this->getMockForAbstractClass('SimpleAcl\Object', array('Parent'));

        $child = $this->getMockForAbstractClass('SimpleAcl\Object', array('Child'));

        $this->assertFalse($parent->removeChild($child));

        $parent->addChild($child);

        $this->assertEquals(1, count($parent->getChildren()));
        $this->assertTrue($parent->removeChild($child));
        $this->assertEquals(0, count($parent->getChildren()));
    }

    public function testAddSameChild()
    {
        /** @var Object $parent */
        $parent = $this->getMockForAbstractClass('SimpleAcl\Object', array('Parent'));

        $child = $this->getMockForAbstractClass('SimpleAcl\Object', array('Child'));

        $parent->addChild($child);

        $this->assertEquals(1, count($parent->getChildren()));
        $this->assertSame($child, $parent->hasChild($child));

        $parent->addChild($child);
        $this->assertEquals(1, count($parent->getChildren()));
    }
}