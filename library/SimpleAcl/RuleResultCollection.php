<?php
namespace SimpleAcl;

use SimpleAcl\RuleResult;
use SplPriorityQueue;
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
    protected $collection;

    public function __construct()
    {
        $this->collection = new SplPriorityQueue();
    }

    public function getIterator()
    {
        return $this->collection;
    }

    /**
     * @param RuleResult $result
     */
    public function add(RuleResult $result = null)
    {
        if ( ! $result ) {
            return;
        }

        $this->collection->insert($result, $result->getPriority());
    }

    /**
     * @return bool
     */
    public function get()
    {
        if ( ! $this->any() ) {
            return false;
        }

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
