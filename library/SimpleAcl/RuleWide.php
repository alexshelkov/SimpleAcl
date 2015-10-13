<?php
namespace SimpleAcl;

/**
 * Class RuleWide
 *
 * @package SimpleAcl
 */
class RuleWide extends Rule
{
  /**
   * Wide rule always works.
   *
   * @param $neeRuleName
   *
   * @return bool
   */
  protected function isRuleMatched($neeRuleName)
  {
    return true;
  }
}

