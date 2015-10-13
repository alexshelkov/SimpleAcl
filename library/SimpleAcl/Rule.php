<?php
namespace SimpleAcl;

use RecursiveIteratorIterator;
use SimpleAcl\Resource;
use SimpleAcl\Resource\ResourceAggregateInterface;
use SimpleAcl\Role;
use SimpleAcl\Role\RoleAggregateInterface;

/**
 * Used to connects Role and Resources together.
 *
 * @package SimpleAcl
 */
class Rule
{
  /**
   * Holds rule id.
   *
   * @var mixed
   */
  protected $id;

  /**
   * Rule priority affect the order the rule is applied.
   *
   * @var int
   */
  protected $priority = 0;

  /**
   * Hold name of rule.
   *
   * @var string
   */
  protected $name;

  /**
   * Action used when determining is rule allow access to its Resource and Role.
   *
   * @var mixed
   */
  protected $action = false;

  /**
   * @var Role
   */
  protected $role;

  /**
   * @var Resource
   */
  protected $resource;

  /**
   * @var RoleAggregateInterface
   */
  protected $roleAggregate;

  /**
   * @var ResourceAggregateInterface
   */
  protected $resourceAggregate;

  /**
   * Create Rule with given name.
   *
   * @param $name
   */
  public function __construct($name)
  {
    $this->setId();
    $this->setName($name);
  }

  /**
   * Set aggregate objects.
   *
   * @param $roleAggregate
   * @param $resourceAggregate
   */
  public function resetAggregate($roleAggregate, $resourceAggregate)
  {
    if ($roleAggregate instanceof RoleAggregateInterface) {
      $this->setRoleAggregate($roleAggregate);
    } else {
      $this->roleAggregate = null;
    }

    if ($resourceAggregate instanceof ResourceAggregateInterface) {
      $this->setResourceAggregate($resourceAggregate);
    } else {
      $this->resourceAggregate = null;
    }
  }

  /**
   * @return ResourceAggregateInterface
   */
  public function getResourceAggregate()
  {
    return $this->resourceAggregate;
  }

  /**
   * @param ResourceAggregateInterface $resourceAggregate
   */
  public function setResourceAggregate(ResourceAggregateInterface $resourceAggregate)
  {
    $this->resourceAggregate = $resourceAggregate;
  }

  /**
   * @return RoleAggregateInterface
   */
  public function getRoleAggregate()
  {
    return $this->roleAggregate;
  }

  /**
   * @param RoleAggregateInterface $roleAggregate
   */
  public function setRoleAggregate(RoleAggregateInterface $roleAggregate)
  {
    $this->roleAggregate = $roleAggregate;
  }

  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param mixed $id
   */
  public function setId($id = null)
  {
    if (null === $id) {
      $id = $this->generateId();
    }

    $this->id = $id;
  }

  /**
   * @param RuleResult|null $ruleResult
   *
   * @return bool|null
   */
  public function getAction(RuleResult $ruleResult = null)
  {
    $actionResult = $this->action;

    if (
        !is_callable($actionResult)
        ||
        null === $ruleResult
    ) {
      if (null !== $actionResult) {
        return (bool)$actionResult;
      } else {
        return null;
      }
    }

    $actionResult = call_user_func($this->action, $ruleResult);

    if (null !== $actionResult) {
      return (bool)$actionResult;
    } else {
      return null;
    }
  }

  /**
   * @param mixed $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }

  /**
   * Check owing Role & Resource (and their children) and match its with $roleName & $resourceName;
   * if match was found depending on action allow or deny access to $resourceName for $roleName.
   *
   * @param        $needRuleName
   * @param string $needRoleName
   * @param string $needResourceName
   *
   * @return RuleResult|null null is returned if there is no matched Role & Resource in this rule.
   *                         RuleResult otherwise.
   */
  public function isAllowed($needRuleName, $needRoleName, $needResourceName)
  {
    if ($this->isRuleMatched($needRuleName)) {

      if (null !== $this->getRole()) {
        $roles = new RecursiveIteratorIterator($this->getRole(), RecursiveIteratorIterator::SELF_FIRST);
      } else {
        $roles = array(null);
      }

      if (null !== $this->getResource()) {
        $resources = new RecursiveIteratorIterator($this->getResource(), RecursiveIteratorIterator::SELF_FIRST);
      } else {
        $resources = array(null);
      }

      foreach ($roles as $role) {
        foreach ($resources as $resource) {

          $roleDepth = $role ? $roles->getDepth() : 0;
          $resourceDepth = $resource ? $resources->getDepth() : 0;

          $depth = $roleDepth + $resourceDepth;
          $result = $this->match($role, $resource, $needRoleName, $needResourceName, -$depth);

          if ($result) {
            return $result;
          }
        }
      }
    }

    return null;
  }

  /**
   * Check if rule can be used.
   *
   * @param $neeRuleName
   *
   * @return bool
   */
  protected function isRuleMatched($neeRuleName)
  {
    return $this->getName() === $neeRuleName;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @return Role
   */
  public function getRole()
  {
    return $this->role;
  }

  /**
   * @param Role|null $role
   */
  public function setRole(Role $role = null)
  {
    $this->role = $role;
  }

  /**
   * @return \SimpleAcl\Resource
   */
  public function getResource()
  {
    return $this->resource;
  }

  /**
   * @param \SimpleAcl\Resource|null $resource
   */
  public function setResource(Resource $resource = null)
  {
    $this->resource = $resource;
  }

  /**
   * Check if $role and $resource match to need role and resource.
   *
   * @param Role|null                $role
   * @param \SimpleAcl\Resource|null $resource
   * @param string                   $needRoleName
   * @param string                   $needResourceName
   * @param                          $priority
   *
   * @return RuleResult|null
   */
  protected function match(Role $role = null, Resource $resource = null, $needRoleName, $needResourceName, $priority)
  {
    if (
        (
            null === $role
            ||
            ($role && $role->getName() === $needRoleName)
        )
        &&
        (
            null === $resource
            ||
            ($resource && $resource->getName() === $needResourceName)
        )
    ) {
      return new RuleResult($this, $priority, $needRoleName, $needResourceName);
    }

    return null;
  }

  /**
   * @return int
   */
  public function getPriority()
  {
    return $this->priority;
  }

  /**
   * @param int $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }

  /**
   * Creates an id for rule.
   *
   * @return string
   */
  protected function generateId()
  {
    return bin2hex(openssl_random_pseudo_bytes(10));
  }
}