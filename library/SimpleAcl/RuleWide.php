<?php
namespace SimpleAcl;

use SimpleAcl\Rule;

class RuleWide extends Rule
{
    /**
     * Wide rule always works.
     *
     * @param $neeRuleName
     * @return bool
     */
    protected function isRuleMatched($neeRuleName)
    {
        return true;
    }
}

