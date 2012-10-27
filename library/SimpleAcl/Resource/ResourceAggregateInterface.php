<?php
namespace SimpleAcl\Resource;

use SimpleAcl\Resource;

interface ResourceAggregateInterface
{
    public function getResource($resourceName);

    public function addResource(Resource $resource);

    public function setResources($resources);

    public function removeResources();

    public function removeResource($resourceName);

    public function getResources();
}