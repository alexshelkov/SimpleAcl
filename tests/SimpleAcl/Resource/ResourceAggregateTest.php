<?php
namespace SimpleAclTest\Resource;

use PHPUnit_Framework_TestCase;

use SimpleAcl\Resource;
use SimpleAcl\Resource\ResourceAggregate;


class ResourceAggregateTest extends PHPUnit_Framework_TestCase
{
    public function testSetAndGetResources()
    {
        $resources = array('One' => new Resource('One'), 'Tow' => new Resource('Tow'));

        $user = new ResourceAggregate();

        $this->assertEquals(0, count($user->getResources()));

        $user->setResources($resources);

        $this->assertEquals($resources, $user->getResources());

        $this->assertEquals(2, count($user->getResources()));
    }

    public function testResourceAdd()
    {
        $user = new ResourceAggregate();

        $resource1 = new Resource('One');
        $resource2 = new Resource('Tow');

        $this->assertEquals(0, count($user->getResources()));

        $user->addResource($resource1);
        $user->addResource($resource2);

        $this->assertEquals(2, count($user->getResources()));

        $this->assertEquals(array('One' => $resource1, 'Tow' => $resource2), $user->getResources());
    }

    public function testRemoveResources()
    {
        $user = new ResourceAggregate();

        $resource1 = new Resource('One');
        $resource2 = new Resource('Tow');

        $this->assertEquals(0, count($user->getResources()));

        $user->addResource($resource1);
        $user->addResource($resource2);

        $this->assertEquals(2, count($user->getResources()));

        $user->removeResources();

        $this->assertEquals(0, count($user->getResources()));

        $this->assertNull($user->getResource('One'));
        $this->assertNull($user->getResource('Tow'));
    }

    public function testRemoveResource()
    {
        $user = new ResourceAggregate();

        $resource1 = new Resource('One');
        $resource2 = new Resource('Tow');

        $this->assertEquals(0, count($user->getResources()));

        $user->addResource($resource1);
        $user->addResource($resource2);

        $this->assertEquals(2, count($user->getResources()));

        $user->removeResource('One');
        $this->assertEquals(1, count($user->getResources()));
        $this->assertEquals($resource2, $user->getResource('Tow'));

        $user->removeResource('UnDefinedTow');
        $this->assertEquals(1, count($user->getResources()));

        $user->removeResource('Tow');
        $this->assertEquals(0, count($user->getResources()));
    }

    public function testAddObjectWithSameName()
    {
        $user = new ResourceAggregate();

        $resource1 = new Resource('One');
        $resource2 = new Resource('One');

        $user->addResource($resource1);
        $user->addResource($resource2); // last added wins

        $this->assertEquals(1, count($user->getResources()));
        $this->assertSame($resource2, $user->getResource('One'));
    }
}