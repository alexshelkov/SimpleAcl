<?php
namespace SimpleAcl\Object;

use SimpleAcl\Object;

/**
 * Implement common function for Role and Resources.
 *
 * @package SimpleAcl\Object
 */
abstract class ObjectAggregate
{
  /**
   * @var Object[]
   */
  protected $objects = array();

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
    if ($objectName instanceof Object) {
      $objectName = $objectName->getName();
    }

    foreach ($this->objects as $objectIndex => $object) {
      if ($object->getName() === $objectName) {
        unset($this->objects[$objectIndex]);

        return true;
      }
    }

    return false;
  }

  /**
   * @return array|Object[]
   */
  protected function getObjects()
  {
    return $this->objects;
  }

  /**
   * @param array $objects
   */
  protected function setObjects($objects)
  {
    /** @var \SimpleAcl\Object $object */
    foreach ($objects as $object) {
      $this->addObject($object);
    }
  }

  /**
   * @param \SimpleAcl\Object $object
   */
  protected function addObject(Object $object)
  {
    if ($this->getObject($object)) {
      return;
    }

    $this->objects[] = $object;
  }

  /**
   * @param Object|string $objectName
   *
   * @return null|Object
   */
  protected function getObject($objectName)
  {
    if ($objectName instanceof Object) {
      $objectName = $objectName->getName();
    }

    foreach ($this->objects as $object) {
      if ($object->getName() === $objectName) {
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