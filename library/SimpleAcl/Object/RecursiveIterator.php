<?php
namespace SimpleAcl\Object;

use RecursiveIterator as SplIterator;
use SimpleAcl\BaseObject;

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
        return next($this->objects);
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
        return $this->key() !== null;
    }

    public function rewind()
    {
        return reset($this->objects);
    }

    public function hasChildren()
    {
        if ( is_null($this->key()) ) {
            return false;
        }

        $object = $this->current();

        return count($object->getChildren()) > 0;
    }

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