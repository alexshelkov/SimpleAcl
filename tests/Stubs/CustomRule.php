<?php
use SimpleAcl\Rule;
use SimpleAcl\Role;
use SimpleAcl\Resource;
use SimpleAcl\RuleResult;

class MathAnyRoleAndActAsWide extends Rule
{
    protected function match(Role $role = null, Resource $resource = null, $needRoleName, $needResourceName, $priority)
    {
        if ( is_null($resource) || ($resource && $resource->getName() === $needResourceName) ) {
            return new RuleResult($this, $priority, $needRoleName, $needResourceName);
        }

        return null;
    }

    protected function isRuleMatched($neeRuleName)
    {
        return true;
    }
}

class MathAnyResourceAndActAsWide extends Rule
{
    protected function match(Role $role = null, Resource $resource = null, $needRoleName, $needResourceName, $priority)
    {
        if ( is_null($role) || ($role && $role->getName() === $needRoleName) ) {
            return new RuleResult($this, $priority, $needRoleName, $needResourceName);
        }

        return null;
    }

    protected function isRuleMatched($neeRuleName)
    {
        return true;
    }
}

class MatchAnything extends Rule
{
    protected function match(Role $role = null, Resource $resource = null, $needRoleName, $needResourceName, $priority)
    {
        return new RuleResult($this, $priority, $needRoleName, $needResourceName);
    }

    protected function isRuleMatched($neeRuleName)
    {
        return true;
    }
}