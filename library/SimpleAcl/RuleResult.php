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
     * @var string
     */
    protected $needRoleName;

    /**
     * @var string
     */
    protected $needResourceName;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var string
     */
    protected  $id;

    /**
     * @param Rule $rule
     * @param int $priority
     * @param $needRoleName
     * @param $needResourceName
     */
    public function __construct(Rule $rule, $priority, $needRoleName, $needResourceName)
    {
        $this->id = uniqid();
        $this->rule = $rule;
        $this->priority = $priority;
        $this->needRoleName = $needRoleName;
        $this->needResourceName = $needResourceName;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return string
     */
    public function getNeedResourceName()
    {
        return $this->needResourceName;
    }

    /**
     * @return string
     */
    public function getNeedRoleName()
    {
        return $this->needRoleName;
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
        return $this->getRule()->getAction($this);
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}