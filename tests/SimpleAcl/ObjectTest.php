<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;

use SimpleAcl\BaseObject;

class ObjectTest extends PHPUnit_Framework_TestCase
{
    public function testName()
    {
        /** @var Object $object  */
        $object = $this->getMockForAbstractClass('SimpleAcl\BaseObject', array('TestName'));

        $this->assertEquals($object->getName(), 'TestName');
        $object->setName('NewName');
        $this->assertEquals($object->getName(), 'NewName');
    }

    public function testAddChild()
    {
        /** @var Object $parent */
        $parent = $this->getMockForAbstractClass('SimpleAcl\BaseObject', array('Parent'));

        $child = $this->getMockForAbstractClass('SimpleAcl\BaseObject', array('Child'));

        $parent->addChild($child);

        $this->assertEquals(1, count($parent->getChildren()));
        $this->assertSame($child, $parent->hasChild($child));
        $this->assertSame($child, $parent->hasChild('Child'));
    }

    public function testRemoveChild()
    {
        /** @var Object $parent */
        $parent = $this->getMockForAbstractClass('SimpleAcl\BaseObject', array('Parent'));

        $child = $this->getMockForAbstractClass('SimpleAcl\BaseObject', array('Child'));

        $this->assertFalse($parent->removeChild($child));

        $parent->addChild($child);

        $this->assertEquals(1, count($parent->getChildren()));
        $this->assertSame($child, $parent->hasChild($child));
        $this->assertSame($child, $parent->hasChild('Child'));

        $this->assertTrue($parent->removeChild($child));

        $this->assertNull($parent->hasChild($child));
        $this->assertEquals(0, count($parent->getChildren()));
    }

    public function testAddSameChild()
    {
        /** @var Object $parent */
        $parent = $this->getMockForAbstractClass('SimpleAcl\BaseObject', array('Parent'));

        $child = $this->getMockForAbstractClass('SimpleAcl\BaseObject', array('Child'));

        $parent->addChild($child);

        $this->assertEquals(1, count($parent->getChildren()));
        $this->assertSame($child, $parent->hasChild($child));

        $parent->addChild($child);
        $this->assertEquals(1, count($parent->getChildren()));

        $child2 = $this->getMockForAbstractClass('SimpleAcl\BaseObject', array('Child'));

        $parent->addChild($child2);

        $this->assertEquals(1, count($parent->getChildren()));
        $this->assertSame($child, $parent->hasChild('Child'));
        $this->assertSame($child, $parent->hasChild($child));
        $this->assertSame($child, $parent->hasChild($child2));

        $this->assertNotSame($child2, $parent->hasChild($child2));
    }

    public function testGetChildren()
    {
        $parent = $this->getMockForAbstractClass('SimpleAcl\BaseObject', array('TestName'));

        $child1 = $this->getMockForAbstractClass('SimpleAcl\BaseObject', array('TestNameChild1'));
        $parent->addChild($child1);

        $child2 = $this->getMockForAbstractClass('SimpleAcl\BaseObject', array('TestNameChild2'));
        $parent->addChild($child2);

        $this->assertSame(array($child1, $child2), $parent->getChildren());
    }
}