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

        $site = new ResourceAggregate();

        $this->assertEquals(0, count($site->getResources()));

        $site->setResources($resources);

        $this->assertEquals($resources, $site->getResources());

        $this->assertEquals(2, count($site->getResources()));
    }

    public function testResourceAdd()
    {
        $site = new ResourceAggregate();

        $resource1 = new Resource('One');
        $resource2 = new Resource('Tow');

        $this->assertEquals(0, count($site->getResources()));

        $site->addResource($resource1);
        $site->addResource($resource2);

        $this->assertEquals(2, count($site->getResources()));

        $this->assertEquals(array('One' => $resource1, 'Tow' => $resource2), $site->getResources());
    }

    public function testGetResourcesNames()
    {
        $site = new ResourceAggregate();

        $resource1 = new Resource('One');
        $resource2 = new Resource('Tow');

        $this->assertEquals(0, count($site->getResources()));

        $site->addResource($resource1);
        $site->addResource($resource2);

        $this->assertEquals(2, count($site->getResources()));

        $this->assertSame(array('One',  'Tow'), $site->getResourcesNames());
    }

    public function testRemoveResources()
    {
        $site = new ResourceAggregate();

        $resource1 = new Resource('One');
        $resource2 = new Resource('Tow');

        $this->assertEquals(0, count($site->getResources()));

        $site->addResource($resource1);
        $site->addResource($resource2);

        $this->assertEquals(2, count($site->getResources()));

        $site->removeResources();

        $this->assertEquals(0, count($site->getResources()));

        $this->assertNull($site->getResource('One'));
        $this->assertNull($site->getResource('Tow'));
    }

    public function testRemoveResource()
    {
        $site = new ResourceAggregate();

        $resource1 = new Resource('One');
        $resource2 = new Resource('Tow');

        $this->assertEquals(0, count($site->getResources()));

        $site->addResource($resource1);
        $site->addResource($resource2);

        $this->assertEquals(2, count($site->getResources()));

        $site->removeResource('One');
        $this->assertEquals(1, count($site->getResources()));
        $this->assertEquals($resource2, $site->getResource('Tow'));

        $site->removeResource('UnDefinedTow');
        $this->assertEquals(1, count($site->getResources()));

        $site->removeResource('Tow');
        $this->assertEquals(0, count($site->getResources()));
    }

    public function testAddObjectWithSameName()
    {
        $site = new ResourceAggregate();

        $resource1 = new Resource('One');
        $resource2 = new Resource('One');

        $site->addResource($resource1);
        $site->addResource($resource2); // last added wins

        $this->assertEquals(1, count($site->getResources()));
        $this->assertSame($resource2, $site->getResource('One'));
    }
}