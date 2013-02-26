<?php
namespace SimpleAcl;

use SplPriorityQueue as Base;

class SplPriorityQueue extends Base
{
    protected $queueOrder = 0;

    public function insert($datum, $priority)
    {
        if ( is_int($priority) ) {
            $priority = array($priority, $this->queueOrder++);
        }
        parent::insert($datum, $priority);
    }
}