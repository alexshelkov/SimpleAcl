<?php
namespace SimpleAcl\Object;

use SimpleAcl\Object;

/**
 * Implement common function for Role and Resources.
 *
 */
abstract class ObjectAggregate
{
    /**
     * @var Object[]
     */
    protected $objects = array();

    /**
     * @param Object $object
     */
    protected function addObject(Object $object)
    {
        $this->objects[$object->getName()] = $object;
    }

    protected function removeObjects()
    {
        $this->objects = array();
    }

    /**
     * @param string $objectName
     */
    protected function removeObject($objectName)
    {
        foreach ( $this->objects as $objectIndex => $object ) {
            if ( $object->getName() == $objectName ) {
                unset($this->objects[$objectIndex]);
                return;
            }
        }
    }

    /**
     * @param array $objects
     */
    protected function setObjects($objects)
    {
        /** @var Object $object  */
        foreach ($objects as $object) {
            $this->addObject($object);
        }
    }

    /**
     * @return array|Object[]
     */
    protected function getObjects()
    {
        return $this->objects;
    }

    /**
     * @param string $objectName
     * @return null|Object
     */
    protected function getObject($objectName)
    {
        if ( isset($this->objects[$objectName]) ) {
            return $this->objects[$objectName];
        }

        return null;
    }
}
