<?php
namespace SimpleAcl;

use SplPriorityQueue as Base;

class SplPriorityQueue extends Base
{
    protected $queueOrder = PHP_INT_MAX;

    public function insert($datum, $priority)
    {
        if ( is_int($priority) ) {
            $priority = array($priority, $this->queueOrder--);
        }
        parent::insert($datum, $priority);
    }
}