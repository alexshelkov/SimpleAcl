<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;

use SimpleAcl\Acl;
use SimpleAcl\Role;
use SimpleAcl\Resource;
use SimpleAcl\Rule;
use SimpleAcl\Role\RoleAggregate;
use SimpleAcl\Resource\ResourceAggregate;

class AclTest extends PHPUnit_Framework_TestCase
{
    public function testThrowsExceptionWhenBadRule()
    {
        $acl = new Acl;
        $this->setExpectedException('SimpleAcl\Exception\InvalidArgumentException', 'SimpleAcl\Rule or string');
        $acl->addRule(new Role('User'), new Resource('Page'), new \stdClass(), true);
    }

    public function testThrowsExceptionWhenBadArgumentsCount()
    {
        $this->setExpectedException('SimpleAcl\Exception\InvalidArgumentException', 'accepts only one, tow, three or four arguments');

        $acl = new Acl;
        $acl->addRule(new Role(1), new Resource(1), new Rule(1), true, 'test');
    }

    public function testThrowsExceptionWhenBadRole()
    {
        $this->setExpectedException('SimpleAcl\Exception\InvalidArgumentException', 'Role must be an instance of SimpleAcl\Role or null');

        $acl = new Acl;
        $acl->addRule(new \StdClass(1), new Resource('test'), new Rule('test'), true);
    }

    public function testThrowsExceptionWhenBadResource()
    {
        $this->setExpectedException('SimpleAcl\Exception\InvalidArgumentException', 'Resource must be an instance of SimpleAcl\Resource or null');

        $acl = new Acl;
        $acl->addRule(new Role('test'), new \StdClass(1), new Rule('test'), true);
    }

    public function testSetRuleClassOriginal()
    {
        $acl = new Acl;
        $acl->setRuleClass('SimpleAcl\Rule');

        $this->assertEquals('SimpleAcl\Rule', $acl->getRuleClass());
    }

    public function testSetRuleNotExistingClass()
    {
        $this->setExpectedException('SimpleAcl\Exception\RuntimeException', 'Rule class not exist');

        $acl = new Acl;
        $acl->setRuleClass('BadClassTest');

        $this->assertEquals('SimpleAcl\Rule', $acl->getRuleClass());
    }

    public function testSetRuleNotInstanceOfRuleClass()
    {
        $this->setExpectedException('SimpleAcl\Exception\RuntimeException', 'Rule class must be instance of SimpleAcl\Rule');

        eval('class NotInstanceOfRuleClass {}');

        $acl = new Acl;
        $acl->setRuleClass('NotInstanceOfRuleClass');

        $this->assertEquals('SimpleAcl\Rule', $acl->getRuleClass());
    }

    public function testSetRuleClass()
    {
        eval('class GoodRuleClass extends \SimpleAcl\Rule {}');

        $acl = new Acl;
        $acl->setRuleClass('GoodRuleClass');

        $this->assertEquals('GoodRuleClass', $acl->getRuleClass());
    }

    public function testAddSameRules()
    {
        $acl = new Acl;

        $rule = new Rule('Edit');

        $user = new Role('User');
        $page = new Resource('Page');

        $superUser = new Role('SuperUser');
        $superPage = new Resource('SuperPage');

        $acl->addRule($user, $page, $rule, true);

        $this->assertSame($rule->getRole(), $user);
        $this->assertSame($rule->getResource(), $page);

        // If rule already exist don't add it in Acl, but change Role, Resource and Action
        $acl->addRule($superUser, $superPage, $rule, true);

        $this->assertNotSame($rule->getRole(), $user);
        $this->assertNotSame($rule->getResource(), $page);

        $this->assertSame($rule->getRole(), $superUser);
        $this->assertSame($rule->getResource(), $superPage);

        $this->assertFalse($acl->isAllowed('User', 'Page', 'Edit'));
        $this->assertTrue($acl->isAllowed('SuperUser', 'SuperPage', 'Edit'));

        $acl->addRule($superUser, $superPage, $rule, false);

        $this->assertFalse($acl->isAllowed('SuperUser', 'SuperPage', 'Edit'));

        $this->assertAttributeCount(1, 'rules', $acl);

        // rule should overwrite $role, $resource and $action when they actually used in addRule

        $acl->addRule($superUser, $superPage, $rule);
        $this->assertFalse($acl->isAllowed('SuperUser', 'SuperPage', 'Edit'));
        $this->assertAttributeCount(1, 'rules', $acl);

        $acl->addRule($rule);
        $this->assertFalse($acl->isAllowed('SuperUser', 'SuperPage', 'Edit'));
        $this->assertSame($rule->getRole(), $superUser);
        $this->assertSame($rule->getResource(), $superPage);

        $acl->addRule($rule, true);
        $this->assertTrue($acl->isAllowed('SuperUser', 'SuperPage', 'Edit'));
        $this->assertSame($rule->getRole(), $superUser);
        $this->assertSame($rule->getResource(), $superPage);
    }

    public function testRemoveAllRules()
    {
        $acl = new Acl;

        $user = new Role('User');
        $resource = new Resource('Page');

        $acl->addRule($user, $resource, new Rule('View'), true);
        $acl->addRule($user, $resource, new Rule('Edit'), true);
        $acl->addRule($user, $resource, new Rule('Remove'), true);

        $this->assertAttributeCount(3, 'rules', $acl);

        $acl->removeAllRules();

        $this->assertAttributeCount(0, 'rules', $acl);
    }

    public function testRemoveRuleActAsRemoveAllRules()
    {
        $acl = new Acl;

        $user = new Role('User');
        $resource = new Resource('Page');

        $acl->addRule($user, $resource, new Rule('View'), true);
        $acl->addRule($user, $resource, new Rule('Edit'), true);
        $acl->addRule($user, $resource, new Rule('Remove'), true);

        $this->assertAttributeCount(3, 'rules', $acl);

        $acl->removeRule();

        $this->assertAttributeCount(0, 'rules', $acl);
    }

    public function testRemoveRuleNotMatch()
    {
        $acl = new Acl;

        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $page = new Resource('Page');
        $blog = new Resource('Blog');
        $site = new Resource('Site');

        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($moderator, $blog, new Rule('Edit'), true);
        $acl->addRule($admin, $site, new Rule('Remove'), true);

        $this->assertAttributeCount(3, 'rules', $acl);
        $acl->removeRule('RoleNotMatch');
        $this->assertAttributeCount(3, 'rules', $acl);

        $this->assertAttributeCount(3, 'rules', $acl);
        $acl->removeRule(null, 'ResourceNotMatch');
        $this->assertAttributeCount(3, 'rules', $acl);

        $this->assertAttributeCount(3, 'rules', $acl);
        $acl->removeRule(null, 'ResourceNotMatch');
        $this->assertAttributeCount(3, 'rules', $acl);

        $this->assertAttributeCount(3, 'rules', $acl);
        $acl->removeRule(null, null, 'RuleNotMatch');
        $this->assertAttributeCount(3, 'rules', $acl);

        $this->assertAttributeCount(3, 'rules', $acl);
        $acl->removeRule('RoleNotMatch', 'ResourceNotMatch', 'RuleNotMatch');
        $this->assertAttributeCount(3, 'rules', $acl);
    }

    public function testRemoveRule()
    {
        $acl = new Acl;

        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $page = new Resource('Page');
        $blog = new Resource('Blog');
        $site = new Resource('Site');

        // Remove rules by Role
        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($user, $blog, new Rule('Edit'), true);
        $acl->addRule($user, $site, new Rule('Remove'), true);

        $this->assertAttributeCount(3, 'rules', $acl);
        $acl->removeRule('User');
        $this->assertAttributeCount(0, 'rules', $acl);

        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($user, $blog, new Rule('Edit'), true);
        $acl->addRule($moderator, $site, new Rule('Remove'), true);

        $acl->removeRule('User');
        $this->assertAttributeCount(1, 'rules', $acl);

        $acl->removeRule();

        // Remove rules by Resource
        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($moderator, $page, new Rule('Edit'), true);
        $acl->addRule($admin, $page, new Rule('Remove'), true);

        $this->assertAttributeCount(3, 'rules', $acl);
        $acl->removeRule(null, 'Page');
        $this->assertAttributeCount(0, 'rules', $acl);

        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($moderator, $page, new Rule('Edit'), true);
        $acl->addRule($admin, $blog, new Rule('Remove'), true);

        $this->assertAttributeCount(3, 'rules', $acl);
        $acl->removeRule(null, 'Page');
        $this->assertAttributeCount(1, 'rules', $acl);

        $acl->removeRule();

        // Remove rules by Rule
        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($moderator, $blog, new Rule('View'), true);
        $acl->addRule($admin, $site, new Rule('View'), true);

        $this->assertAttributeCount(3, 'rules', $acl);
        $acl->removeRule(null, null, 'View');
        $this->assertAttributeCount(0, 'rules', $acl);

        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($moderator, $blog, new Rule('View'), true);
        $acl->addRule($admin, $site, new Rule('Edit'), true);

        $this->assertAttributeCount(3, 'rules', $acl);
        $acl->removeRule(null, null, 'View');
        $this->assertAttributeCount(1, 'rules', $acl);

        $acl->removeRule();

        // Remove rules by Role & Resource & Rule
        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($moderator, $blog, new Rule('Edit'), true);
        $acl->addRule($admin, $site, new Rule('Remove'), true);

        $this->assertAttributeCount(3, 'rules', $acl);
        $acl->removeRule('User', 'Page', 'View');
        $this->assertAttributeCount(2, 'rules', $acl);
        $acl->removeRule('Moderator', 'Blog', 'Edit');
        $this->assertAttributeCount(1, 'rules', $acl);
        $acl->removeRule('Admin', 'Site', 'Remove');
        $this->assertAttributeCount(0, 'rules', $acl);

        // Remove rules by pairs
        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($moderator, $blog, new Rule('Edit'), true);
        $acl->addRule($admin, $site, new Rule('Remove'), true);

        $this->assertAttributeCount(3, 'rules', $acl);
        $acl->removeRule('User', 'Page');
        $this->assertAttributeCount(2, 'rules', $acl);
        $acl->removeRule('Moderator', null, 'Edit');
        $this->assertAttributeCount(1, 'rules', $acl);
        $acl->removeRule(null, 'Site', 'Remove');
        $this->assertAttributeCount(0, 'rules', $acl);

        $acl->removeRule();
    }

    public function testRemoveRuleById()
    {
        $acl = new Acl;

        $user = new Role('User');

        $page = new Resource('Page');

        $rule1 = new Rule('View');
        $rule2 = new Rule('View');
        $rule3 = new Rule('View');

        $acl->addRule($user, $page, $rule1, true);
        $acl->addRule($user, $page, $rule2, true);
        $acl->addRule($user, $page, $rule3, true);

        $acl->removeRuleById('bad_id_test');

        $this->assertAttributeCount(3, 'rules', $acl);

        $acl->removeRuleById($rule1->getId());

        $this->assertAttributeCount(2, 'rules', $acl);

        $acl->removeRuleById($rule2->getId());
        $acl->removeRuleById($rule3->getId());

        $this->assertAttributeCount(0, 'rules', $acl);
    }

    public function testHasRule()
    {
        $acl = new Acl;

        $user = new Role('User');

        $page = new Resource('Page');

        $rule1 = new Rule('View');
        $rule2 = new Rule('View');
        $rule3 = new Rule('View');

        $acl->addRule($user, $page, $rule1, true);
        $acl->addRule($user, $page, $rule2, true);

        $this->assertSame($rule1, $acl->hasRule($rule1));
        $this->assertSame($rule2, $acl->hasRule($rule2->getId()));
        $this->assertFalse($acl->hasRule($rule3));
    }
}