<?php
namespace SimpleAcl;

use SimpleAcl\Rule;

/**
 * Returned as result of Rule::isAllowed
 *
 */
class RuleResult
{
    /**
     * @var Rule
     */
    protected $rule;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @param Rule $rule
     * @param int $priority
     */
    public function __construct(Rule $rule, $priority)
    {
        $this->rule = $rule;
        $this->priority = $priority;
    }

    /**
     * @return Rule
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @return bool
     */
    public function getAction()
    {
        return $this->getRule()->getAction();
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }
}