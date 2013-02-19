<?php
namespace SimpleAcl;

/**
 * Use to keep shared function between Roles and Resources.
 *
 */
abstract class Object
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
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Adds child if it not added.
     *
     * @param Object $child
     */
    public function addChild(Object $child)
    {
        if ( $this->hasChild($child) ) {
            return;
        }
        $this->children[] = $child;
    }

    /**
     * Remove child, return true if child was removed.
     *
     * @param Object $needChild
     *
     * @return bool
     */
    public function removeChild(Object $needChild)
    {
        foreach ($this->children as $childIndex => $haveChild) {
            if ( $haveChild === $needChild ) {
                unset($this->children[$childIndex]);
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if object have child.
     *
     * @param Object $needChild
     *
     * @return null|Object
     */
    public function hasChild(Object $needChild)
    {
        foreach ( $this->children as $haveChild ) {
            if ( $haveChild === $needChild ) {
                return $needChild;
            }
        }
        return null;
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