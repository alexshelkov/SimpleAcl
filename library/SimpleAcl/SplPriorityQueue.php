<?php
namespace SimpleAcl;

use SplPriorityQueue as Base;

/**
 * Class SplPriorityQueue
 *
 * @package SimpleAcl
 */
class SplPriorityQueue extends Base
{
  /**
   * @var int
   */
  protected $queueOrder = 0;

  /**
   * insert
   *
   * @param mixed $datum
   * @param mixed $priority
   * @param int   $rulePriority
   */
  public function insert($datum, $priority, $rulePriority = 0)
  {
    if (is_int($priority)) {
      $priority = array($priority, $rulePriority, $this->queueOrder++);
    }
    parent::insert($datum, $priority);
  }
}