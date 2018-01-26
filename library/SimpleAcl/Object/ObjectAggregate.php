<?php
namespace SimpleAcl\Object;

use SimpleAcl\BaseObject;

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
    protected function addObject(BaseObject $object)
    {
        if ( $this->getObject($object) ) {
            return;
        }
        $this->objects[] = $object;
    }

    protected function removeObjects()
    {
        $this->objects = array();
    }

    /**
     * @param Object|string $objectName
     *
     * @return bool
     */
    protected function removeObject($objectName)
    {
        if ($objectName instanceof BaseObject ) {
            $objectName = $objectName->getName();
        }

        foreach ($this->objects as $objectIndex => $object) {
            if ( $object->getName() === $objectName ) {
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
     * @param Object|string $objectName
     *
     * @return null|Object
     */
    protected function getObject($objectName)
    {
        if ($objectName instanceof BaseObject ) {
            $objectName = $objectName->getName();
        }

        foreach ($this->objects as $object) {
            if ( $object->getName() === $objectName ) {
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