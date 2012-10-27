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
    public function testEmpty()
    {
        $acl = new Acl;

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
    }

    public function testUnDefinedRule()
    {
        $acl = new Acl;
        $acl->addRule(new Role('User'), new Resource('Page'), new Rule('View'), true);

        $this->assertFalse($acl->isAllowed('User', 'Page', 'UnDefinedRule'));
    }

    public function testUnDefinedRoleOrResource()
    {
        $acl = new Acl;
        $acl->addRule(new Role('User'), new Resource('Page'), new Rule('View'), true);

        $this->assertFalse($acl->isAllowed('NotDefinedRole', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'NotDefinedResource', 'View'));
        $this->assertFalse($acl->isAllowed('NotDefinedRole', 'NotDefinedResource', 'View'));
    }

    public function testThrowsExceptionWhenBadRule()
    {
        $acl = new Acl;
        $this->setExpectedException('SimpleAcl\Exception\InvalidArgumentException', 'SimpleAcl\Rule or string');
        $acl->addRule(new Role('User'), new Resource('Page'), new \stdClass(), true);
    }

    public function testOneRoleOneResourceOneRule()
    {
        $acl = new Acl;
        $acl->addRule(new Role('User'), new Resource('Page'), new Rule('View'), true);
        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));

        $acl = new Acl;
        $acl->addRule(new Role('User'), new Resource('Page'), new Rule('View'), false);
        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
    }

    public function testOneRoleOneResourceMultipleRule()
    {
        $acl = new Acl;

        $user = new Role('User');
        $resource = new Resource('Page');

        $acl->addRule($user, $resource, new Rule('View'), true);
        $acl->addRule($user, $resource, new Rule('Edit'), true);
        $acl->addRule($user, $resource, new Rule('Remove'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Page', 'Edit'));
        $this->assertTrue($acl->isAllowed('User', 'Page', 'Remove'));

        $acl = new Acl;

        $user = new Role('User');
        $resource = new Resource('Page');

        $acl->addRule($user, $resource, new Rule('View'), false);
        $acl->addRule($user, $resource, new Rule('Edit'), false);
        $acl->addRule($user, $resource, new Rule('Remove'), false);

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Page', 'Edit'));
        $this->assertFalse($acl->isAllowed('User', 'Page', 'Remove'));
    }

    public function testMultipleRolesMultipleResourcesMultipleRules()
    {
        $runChecks = function(PHPUnit_Framework_TestCase $phpUnit, Acl $acl, $allowed) {
            // Checks for page
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Page', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Page', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Page', 'Remove'));

            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Page', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Page', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Page', 'Remove'));

            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Page', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Page', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Page', 'Remove'));
    
            // Checks for blog
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Blog', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Blog', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Blog', 'Remove'));
    
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Blog', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Blog', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Blog', 'Remove'));
    
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Blog', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Blog', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Blog', 'Remove'));
    
            // Checks for site
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Site', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Site', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Site', 'Remove'));

            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Site', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Site', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Site', 'Remove'));

            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Site', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Site', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Site', 'Remove'));
        };
        
        $acl = new Acl;

        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $page = new Resource('Page');
        $blog = new Resource('Blog');
        $site = new Resource('Site');

        $runChecks($this, $acl, false);

        // Rules for page
        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($user, $page, new Rule('Edit'), true);
        $acl->addRule($user, $page, new Rule('Remove'), true);

        $acl->addRule($moderator, $page, new Rule('View'), true);
        $acl->addRule($moderator, $page, new Rule('Edit'), true);
        $acl->addRule($moderator, $page, new Rule('Remove'), true);

        $acl->addRule($admin, $page, new Rule('View'), true);
        $acl->addRule($admin, $page, new Rule('Edit'), true);
        $acl->addRule($admin, $page, new Rule('Remove'), true);

        // Rules for blog
        $acl->addRule($user, $blog, new Rule('View'), true);
        $acl->addRule($user, $blog, new Rule('Edit'), true);
        $acl->addRule($user, $blog, new Rule('Remove'), true);

        $acl->addRule($moderator, $blog, new Rule('View'), true);
        $acl->addRule($moderator, $blog, new Rule('Edit'), true);
        $acl->addRule($moderator, $blog, new Rule('Remove'), true);

        $acl->addRule($admin, $blog, new Rule('View'), true);
        $acl->addRule($admin, $blog, new Rule('Edit'), true);
        $acl->addRule($admin, $blog, new Rule('Remove'), true);

        // Rules for site
        $acl->addRule($user, $site, new Rule('View'), true);
        $acl->addRule($user, $site, new Rule('Edit'), true);
        $acl->addRule($user, $site, new Rule('Remove'), true);

        $acl->addRule($moderator, $site, new Rule('View'), true);
        $acl->addRule($moderator, $site, new Rule('Edit'), true);
        $acl->addRule($moderator, $site, new Rule('Remove'), true);

        $acl->addRule($admin, $site, new Rule('View'), true);
        $acl->addRule($admin, $site, new Rule('Edit'), true);
        $acl->addRule($admin, $site, new Rule('Remove'), true);

        $runChecks($this, $acl, true);

    }

    public function testSameRules()
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
    }

    public function testParentRoles()
    {
        $acl = new Acl;

        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $user->setParent($moderator);
        $moderator->setParent($admin);

        $page = new Resource('Page');

        // Parent elements must grant access
        $acl->addRule($user, $page, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Page', 'View'));

        $acl = new Acl;

        // Child elements must NOT have access
        $acl->addRule($admin, $page, new Rule('View'), true);

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Page', 'View'));
    }

    public function testParentResources()
    {
        $acl = new Acl;

        $user = new Role('User');

        $page = new Resource('Page');
        $blog = new Resource('Blog');
        $site = new Resource('Site');

        $page->setParent($blog);
        $blog->setParent($site);

        // Parent elements must grant access
        $acl->addRule($user, $page, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Site', 'View'));

        $acl = new Acl;

        // Child elements must NOT have access
        $acl->addRule($user, $site, new Rule('View'), true);

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Site', 'View'));
    }

    public function testParentRolesAndResources()
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

        $acl = new Acl;

        $acl->addRule($user, $page, new Rule('View'), true);

        // Parent elements must grant access
        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Page', 'View'));

        $this->assertTrue($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Blog', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Blog', 'View'));

        $this->assertTrue($acl->isAllowed('User', 'Site', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Site', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Site', 'View'));

        $acl = new Acl;

        $acl->addRule($admin, $page, new Rule('View'), true);

        // Admin must have access to all page parents resources, but not admin children's
        $this->assertTrue($acl->isAllowed('Admin', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Blog', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Site', 'View'));

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Site', 'View'));

        $this->assertFalse($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Site', 'View'));

        $acl = new Acl;

        $acl->addRule($user, $site, new Rule('View'), true);

        // All users must have access to site, but not children's of site
        $this->assertTrue($acl->isAllowed('User', 'Site', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Site', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Site', 'View'));

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Page', 'View'));

        $this->assertFalse($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Blog', 'View'));
    }

    public function testParentRolesAndResourcesWithMultipleRules()
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

        $acl = new Acl;

        $acl->addRule($user, $blog, new Rule('View'), true);
        $acl->addRule($moderator, $page, new Rule('View'), true);

        // User and it parent's must have access to blog and site, but not page
        // Admin and moderator must have access to page
        $this->assertTrue($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Blog', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Blog', 'View'));

        $this->assertTrue($acl->isAllowed('User', 'Site', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Site', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Site', 'View'));

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Page', 'View'));
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

    public function testAggregateBadRolesAndResources()
    {
        $acl = new Acl;

       $user = new Role('User');

       $page = new Resource('Page');

       $acl->addRule($user, $page, new Rule('View'), true);

       $this->assertFalse($acl->isAllowed('User', new \stdClass(), 'View'));
       $this->assertFalse($acl->isAllowed(new \stdClass(), 'Page', 'Edit'));
    }

    public function testAggregateEmptyRolesAndResources()
    {
        $acl = new Acl;

       $user = new Role('User');
       $moderator = new Role('Moderator');
       $admin = new Role('Admin');

       $page = new Resource('Page');
       $blog = new Resource('Blog');
       $site = new Resource('Site');

       $userGroup = new RoleAggregate();
       $siteGroup = new ResourceAggregate();

       $acl->addRule($user, $page, new Rule('View'), true);
       $acl->addRule($moderator, $blog, new Rule('Edit'), true);
       $acl->addRule($admin, $site, new Rule('Remove'), true);

       $this->assertFalse($acl->isAllowed($userGroup, $siteGroup, 'View'));
       $this->assertFalse($acl->isAllowed($userGroup, $siteGroup, 'Edit'));
       $this->assertFalse($acl->isAllowed($userGroup, $siteGroup, 'Remove'));
    }

    public function testAggregateRoles()
    {
        $acl = new Acl;

        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $page = new Resource('Page');
        $blog = new Resource('Blog');
        $site = new Resource('Site');

        $userGroup = new RoleAggregate();

        $userGroup->addRole($user);
        $userGroup->addRole($moderator);
        $userGroup->addRole($admin);

        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($moderator, $blog, new Rule('Edit'), true);
        $acl->addRule($admin, $site, new Rule('Remove'), true);

        $this->assertTrue($acl->isAllowed($userGroup, 'Page', 'View'));
        $this->assertTrue($acl->isAllowed($userGroup, 'Blog', 'Edit'));
        $this->assertTrue($acl->isAllowed($userGroup, 'Site', 'Remove'));
    }

    public function testAggregateResources()
    {
        $acl = new Acl;

        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $page = new Resource('Page');
        $blog = new Resource('Blog');
        $site = new Resource('Site');

        $siteGroup = new ResourceAggregate();

        $siteGroup->addResource($page);
        $siteGroup->addResource($blog);
        $siteGroup->addResource($site);

        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($moderator, $blog, new Rule('Edit'), true);
        $acl->addRule($admin, $site, new Rule('Remove'), true);

        $this->assertTrue($acl->isAllowed('User', $siteGroup, 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', $siteGroup, 'Edit'));
        $this->assertTrue($acl->isAllowed('Admin', $siteGroup, 'Remove'));
    }

    public function testAggregateRolesAndResources()
    {
        $acl = new Acl;

        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $page = new Resource('Page');
        $blog = new Resource('Blog');
        $site = new Resource('Site');

        $userGroup = new RoleAggregate();
        $userGroup->addRole($user);
        $userGroup->addRole($moderator);
        $userGroup->addRole($admin);

        $siteGroup = new ResourceAggregate();
        $siteGroup->addResource($page);
        $siteGroup->addResource($blog);
        $siteGroup->addResource($site);

        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($moderator, $blog, new Rule('Edit'), true);
        $acl->addRule($admin, $site, new Rule('Remove'), true);

        $this->assertTrue($acl->isAllowed($userGroup, $siteGroup, 'View'));
        $this->assertTrue($acl->isAllowed($userGroup, $siteGroup, 'Edit'));
        $this->assertTrue($acl->isAllowed($userGroup, $siteGroup, 'Remove'));
    }

    public function testStringAsRule()
    {
        $acl = new Acl;

        $user = new Role('User');
        $resource = new Resource('Page');

        $acl->addRule($user, $resource, 'View', true);
        $acl->addRule($user, $resource, 'Edit', true);
        $acl->addRule($user, $resource, 'Remove', true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Page', 'Edit'));
        $this->assertTrue($acl->isAllowed('User', 'Page', 'Remove'));

        $acl = new Acl;

        $acl->setRuleClass('SimpleAcl\Rule');

        $user = new Role('User');
        $resource = new Resource('Page');

        $acl->addRule($user, $resource, 'View', false);
        $acl->addRule($user, $resource, 'Edit', false);
        $acl->addRule($user, $resource, 'Remove', false);

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Page', 'Edit'));
        $this->assertFalse($acl->isAllowed('User', 'Page', 'Remove'));
    }


    /**
     * Testing edge conditions.
     */

    public function testEdgeConditionFirstAddedRuleWins()
    {
        $acl = new Acl;

        $user = new Role('User');

        $page = new Resource('Page');

        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($user, $page, new Rule('View'), false);

        $this->assertAttributeCount(2, 'rules', $acl);
        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));

        $acl->removeRule(null, null, 'View', false);

        $this->assertAttributeCount(1, 'rules', $acl);
        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
    }

    public function testEdgeConditionAggregateRolesFirstAddedRoleWins()
    {
        $acl = new Acl;

        $user = new Role('User');
        $moderator = new Role('Moderator');

        $page = new Resource('Page');

        $userGroup = new RoleAggregate();

        $userGroup->addRole($user);
        $userGroup->addRole($moderator);

        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($moderator, $page, new Rule('View'), false);

        $this->assertTrue($acl->isAllowed($userGroup, 'Page', 'View'));

        $userGroup->removeRole('User');

        $this->assertFalse($acl->isAllowed($userGroup, 'Page', 'View'));
    }

    public function testEdgeConditionAggregateResourcesFirstAddedResourceWins()
    {
        $acl = new Acl;

        $user = new Role('User');

        $page = new Resource('Page');
        $blog = new Resource('Blog');

        $siteGroup = new ResourceAggregate();
        $siteGroup->addResource($page);
        $siteGroup->addResource($blog);

        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($user, $blog, new Rule('View'), false);

        $this->assertTrue($acl->isAllowed('User', $siteGroup, 'View'));

        $siteGroup->removeResource('Page');

        $this->assertFalse($acl->isAllowed('User', $siteGroup, 'View'));
    }
}
