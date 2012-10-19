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
     * Holds, if exist parent Object.
     * By default parent objects granted access of their children.
     *
     * @var Object
     */
    protected $parent;

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
     * @param Object $parent
     */
    public function setParent(Object $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return Object
     */
    public function getParent()
    {
        return $this->parent;
    }
}