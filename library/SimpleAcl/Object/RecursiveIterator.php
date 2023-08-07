<?php
namespace SimpleAcl\Object;

use RecursiveIterator as SplIterator;

/**
 * Used to iterate by Roles and Resources hierarchy.
 *
 */
class RecursiveIterator implements SplIterator
{
    /**
     * @var Object
     */
    protected $objects = array();

    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->objects);
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        return next($this->objects);
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        if ( is_null(key($this->objects)) ) {
            return null;
        }

        return $this->current()->getName();
    }

    #[\ReturnTypeWillChange]
    public function valid()
    {
        return $this->key() !== null;
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        reset($this->objects);
    }

    #[\ReturnTypeWillChange]
    public function hasChildren()
    {
        if ( is_null($this->key()) ) {
            return false;
        }

        $object = $this->current();

        return count($object->getChildren()) > 0;
    }

    #[\ReturnTypeWillChange]
    public function getChildren()
    {
        $object = $this->current();
        $children = $object->getChildren();

        return new RecursiveIterator($children);
    }

    public function __construct($objects)
    {
        $this->objects = $objects;
    }
}
