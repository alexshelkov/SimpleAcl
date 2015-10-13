<?php
namespace SimpleAcl\Resource;

use SimpleAcl\Resource;

/**
 * Interface ResourceAggregateInterface
 *
 * @package SimpleAcl\Resource
 */
interface ResourceAggregateInterface
{
  /**
   * Return array of names for registered resources.
   *
   * @return array
   */
  public function getResourcesNames();
}