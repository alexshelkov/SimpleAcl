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
        $this->removeObject($object->getName());
        $this->objects[] = $object;
    }

    protected function removeObjects()
    {
        $this->objects = array();
    }

    /**
     * @param string $objectName
     *
     * @return bool
     */
    protected function removeObject($objectName)
    {
        foreach ($this->objects as $objectIndex => $object) {
            if ( $object->getName() == $objectName ) {
                unset($this->objects[$objectIndex]);
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $objects
     */
    protected function setObjects($objects)
    {
        /** @var Object $object */
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
     *
     * @return null|Object
     */
    protected function getObject($objectName)
    {
        foreach ($this->objects as $object) {
            if ( $object->getName() == $objectName ) {
                return $object;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    protected function getObjectNames()
    {
        $names = array();

        foreach ($this->objects as $object) {
            $names[] = $object->getName();
        }

        return $names;
    }

}