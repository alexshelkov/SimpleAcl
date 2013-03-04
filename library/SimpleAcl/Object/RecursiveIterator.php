<?php
namespace SimpleAcl\Object;

use RecursiveIterator as SplIterator;
use SimpleAcl\Object;

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

    public function current()
    {
        return current($this->objects);
    }

    public function next()
    {
        next($this->objects);
    }

    public function key()
    {
        if ( is_null(key($this->objects)) ) {
            return null;
        }

        return $this->current()->getName();
    }

    public function valid()
    {
        return key($this->objects) !== null;
    }

    public function rewind()
    {
        reset($this->objects);
    }

    public function hasChildren()
    {
        if ( is_null(key($this->objects)) ) {
            return false;
        }

        $object = $this->current();

        return count($object->getChildren()) > 0;
    }

    public function getChildren()
    {
        $object = current($this->objects);
        $children = $object->getChildren();

        return new RecursiveIterator($children);
    }


    public function __construct($objects)
    {
        $this->objects = $objects;
    }
}
