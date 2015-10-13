<?php
namespace SimpleAcl\Resource;

use SimpleAcl\Object\ObjectAggregate;
use SimpleAcl\Resource;

/**
 * Holds resources.
 *
 * Allow pass itself to Acl::isAllowed method as $resourceName.
 *
 * @package SimpleAcl\Resource
 */
class ResourceAggregate extends ObjectAggregate implements ResourceAggregateInterface
{
  /**
   * Add resource.
   *
   * @param \SimpleAcl\Resource $resource
   */
  public function addResource(Resource $resource)
  {
    parent::addObject($resource);
  }

  /**
   * Remove all resources.
   *
   */
  public function removeResources()
  {
    parent::removeObjects();
  }

  /**
   * Remove resource by name.
   *
   * @param $resourceName
   */
  public function removeResource($resourceName)
  {
    parent::removeObject($resourceName);
  }

  /**
   * Add resources from array.
   *
   * @param array $resources
   */
  public function setResources($resources)
  {
    parent::setObjects($resources);
  }

  /**
   * Return all resources.
   *
   * @return array()|Resource[]
   */
  public function getResources()
  {
    return parent::getObjects();
  }

  /**
   * Return array of names for registered resources.
   *
   * @return array
   */
  public function getResourcesNames()
  {
    return parent::getObjectNames();
  }

  /**
   * Return resource by name.
   *
   * @param $resourceName
   *
   * @return null|Resource
   */
  public function getResource($resourceName)
  {
    return parent::getObject($resourceName);
  }
}
