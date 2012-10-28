<?php
namespace SimpleAcl\Resource;

use SimpleAcl\Resource;

interface ResourceAggregateInterface
{
    /**
     * Return array of names for registered resources.
     *
     * @return array
     */
    public function getResourcesNames();
}