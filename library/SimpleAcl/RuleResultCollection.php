<?php
namespace SimpleAcl;

use IteratorAggregate;

/**
 * Holds RuleResult sorted according priority.
 *
 * @package SimpleAcl
 */
class RuleResultCollection implements IteratorAggregate
{
  /**
   * @var SplPriorityQueue
   */
  public $collection;

  /**
   * __construct
   */
  public function __construct()
  {
    $this->collection = new SplPriorityQueue();
  }

  /**
   * @return SplPriorityQueue
   */
  public function getIterator()
  {
    return clone $this->collection;
  }

  /**
   * @param RuleResult $result
   */
  public function add(RuleResult $result = null)
  {
    if (!$result) {
      return;
    }

    if (null === $result->getAction()) {
      return;
    }

    $this->collection->insert($result, $result->getPriority(), $result->getRulePriority());
  }

  /**
   * @return bool
   */
  public function get()
  {
    if (!$this->any()) {
      return false;
    }

    /** @var RuleResult $result */
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
