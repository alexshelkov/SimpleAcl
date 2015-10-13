<?php
namespace SimpleAcl;

use IteratorAggregate;
use SimpleAcl\Object\RecursiveIterator;

/**
 * Use to keep shared function between Roles and Resources.
 *
 * @package SimpleAcl
 */
abstract class Object implements IteratorAggregate
{
  /**
   * Hold the name of object.
   *
   * @var string
   */
  protected $name;

  /**
   * Holds children.
   *
   * @var Object[]
   */
  protected $children = array();

  /**
   * Create Object with given name.
   *
   * @param string $name
   */
  public function __construct($name)
  {
    $this->setName($name);
  }

  /**
   * @return RecursiveIterator
   */
  public function getIterator()
  {
    return new RecursiveIterator(array($this));
  }

  /**
   * Adds child if it not added.
   *
   * @param \SimpleAcl\Object $child
   */
  public function addChild(Object $child)
  {
    if ($this->hasChild($child)) {
      return;
    }

    $this->children[] = $child;
  }

  /**
   * Checks if object have child.
   *
   * @param Object|string $childName
   *
   * @return null|Object
   */
  public function hasChild($childName)
  {
    if ($childName instanceof Object) {
      $childName = $childName->getName();
    }

    foreach ($this->children as $child) {
      if ($child->getName() === $childName) {
        return $child;
      }
    }

    return null;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * Remove child, return true if child was removed.
   *
   * @param Object|string $needChild
   *
   * @return bool
   */
  public function removeChild($needChild)
  {
    if ($needChild instanceof Object) {
      $needChild = $needChild->getName();
    }

    foreach ($this->children as $childIndex => $child) {
      if ($child->getName() === $needChild) {
        unset($this->children[$childIndex]);

        return true;
      }
    }

    return false;
  }

  /**
   * Returns children.
   *
   * @return Object[]
   */
  public function getChildren()
  {
    return $this->children;
  }
}