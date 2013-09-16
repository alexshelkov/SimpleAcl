<?php
namespace SimpleAcl;

use SimpleAcl\RuleResult;
use SimpleAcl\SplPriorityQueue;
use IteratorAggregate;

/**
 * Holds RuleResult sorted according priority.
 *
 */
class RuleResultCollection implements IteratorAggregate
{
    /**
     * @var SplPriorityQueue
     */
    public $collection;

    public function __construct()
    {
        $this->collection = new SplPriorityQueue();
    }

    public function getIterator()
    {
        return clone $this->collection;
    }

    /**
     * @param RuleResult $result
     */
    public function add(RuleResult $result = null)
    {
        if ( ! $result ) {
            return;
        }

        if ( is_null($result->getAction()) ) {
            return;
        }

        $this->collection->insert($result, $result->getPriority(), $result->getRulePriority());
    }

    /**
     * @return bool
     */
    public function get()
    {
        if ( ! $this->any() ) {
            return false;
        }

        /** @var RuleResult $result  */
        $result = $this->collection->top();

        return $result->getAction();
    }

    /**
     * @return bool
     */
    public function any()
    {
        return $this->collection->count() > 0;
    }
}
