<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;

use SimpleAcl\Role;
use SimpleAcl\Resource;
use SimpleAcl\Rule;

/**
 * Don't test Rule::isAllowed here, its tested in AclTest
 */
class RuleTest extends PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $rule = new Rule('Rule');
        $this->assertEquals($rule->getName(), 'Rule');
        $rule->setName('NewRuleName');
        $this->assertEquals($rule->getName(), 'NewRuleName');
    }

    public function testAction()
    {
        $rule = new Rule('Rule');
        $rule->setAction(true);
        $this->assertTrue($rule->getAction());

        $rule->setAction(false);
        $this->assertFalse($rule->getAction());

        // Action can be mixed, but getAction must return bool
        $a = array();
        $rule->setAction($a);
        $this->assertFalse($rule->getAction());
        $this->assertAttributeEquals($a, 'action', $rule);

        $a = array(1, 2, 3);
        $rule->setAction($a);
        $this->assertTrue($rule->getAction());
        $this->assertAttributeEquals($a, 'action', $rule);
    }

    public function testRolesAndResources()
    {
        $rule = new Rule('Rule');

        $role = new Role('Role');
        $rule->setRole($role);
        $this->assertSame($rule->getRole(), $role);

        $resource = new Resource('Resource');
        $rule->setResource($resource);
        $this->assertSame($rule->getResource(), $resource);
    }

    public function testIsAllowed()
    {
        $rule = new Rule('Rule');

        $role = new Role('Role');
        $rule->setRole($role);

        $resource = new Resource('Resource');
        $rule->setResource($resource);

        $rule->setAction(true);

        $this->assertTrue($rule->isAllowed('Role', 'Resource'));

        $rule->setAction(false);

        $this->assertFalse($rule->isAllowed('Role', 'Resource'));
    }

    public function testIsAllowedWithParents()
    {
        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $user->setParent($moderator);
        $moderator->setParent($admin);

        $page = new Resource('Page');
        $blog = new Resource('Blog');
        $site = new Resource('Site');

        $page->setParent($blog);
        $blog->setParent($site);

        $rule = new Rule('Rule');
        $rule->setRole($user);
        $rule->setResource($page);
        $rule->setAction(true);

        // Parent elements must grant access
        $this->assertTrue($rule->isAllowed('User', 'Page'));
        $this->assertTrue($rule->isAllowed('Moderator', 'Page'));
        $this->assertTrue($rule->isAllowed('Admin', 'Page'));

        $this->assertTrue($rule->isAllowed('User', 'Blog'));
        $this->assertTrue($rule->isAllowed('Moderator', 'Blog'));
        $this->assertTrue($rule->isAllowed('Admin', 'Blog'));

        $this->assertTrue($rule->isAllowed('User', 'Site'));
        $this->assertTrue($rule->isAllowed('Moderator', 'Site'));
        $this->assertTrue($rule->isAllowed('Admin', 'Site'));

        $rule = new Rule('Rule');
        $rule->setRole($admin);
        $rule->setResource($page);
        $rule->setAction(true);

        // Admin must have access to all page parents resources, but other checks must return null
        $this->assertTrue($rule->isAllowed('Admin', 'Page'));
        $this->assertTrue($rule->isAllowed('Admin', 'Blog'));
        $this->assertTrue($rule->isAllowed('Admin', 'Site'));

        $this->assertNull($rule->isAllowed('User', 'Page'));
        $this->assertNull($rule->isAllowed('User', 'Blog'));
        $this->assertNull($rule->isAllowed('User', 'Site'));

        $this->assertNull($rule->isAllowed('Moderator', 'Page'));
        $this->assertNull($rule->isAllowed('Moderator', 'Blog'));
        $this->assertNull($rule->isAllowed('Moderator', 'Site'));
        
        $rule = new Rule('Rule');
        $rule->setRole($user);
        $rule->setResource($site);
        $rule->setAction(true);

        // All users must have access to site, but other checks must return null
        $this->assertTrue($rule->isAllowed('User', 'Site'));
        $this->assertTrue($rule->isAllowed('Moderator', 'Site'));
        $this->assertTrue($rule->isAllowed('Admin', 'Site'));

        $this->assertNull($rule->isAllowed('User', 'Page'));
        $this->assertNull($rule->isAllowed('Moderator', 'Page'));
        $this->assertNull($rule->isAllowed('Admin', 'Page'));

        $this->assertNull($rule->isAllowed('User', 'Blog'));
        $this->assertNull($rule->isAllowed('Moderator', 'Blog'));
        $this->assertNull($rule->isAllowed('Admin', 'Blog'));
    }
}