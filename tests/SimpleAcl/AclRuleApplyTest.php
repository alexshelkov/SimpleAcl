<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;

use SimpleAcl\Acl;
use SimpleAcl\Role;
use SimpleAcl\Resource;
use SimpleAcl\Rule;
use SimpleAcl\Role\RoleAggregate;
use SimpleAcl\Resource\ResourceAggregate;
use SimpleAcl\RuleResult;
use SimpleAcl\RuleWide;

class AclRuleApplyTest extends PHPUnit_Framework_TestCase
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

    public function testParentRoles()
    {
        $acl = new Acl;

        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $admin->addChild($moderator);
        $moderator->addChild($user);

        $page = new Resource('Page');

        // Parent elements must NOT grant access
        $acl->addRule($user, $page, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Page', 'View'));

        $acl = new Acl;

        // Child elements must inherit access
        $acl->addRule($admin, $page, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Page', 'View'));

        // but last added rules wins
        $acl->addRule($user, $page, new Rule('View'), false);
        $acl->addRule($moderator, $page, new Rule('View'), false);

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

        $site->addChild($blog);
        $blog->addChild($page);

        // Parent elements must NOT have access
        $acl->addRule($user, $page, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Site', 'View'));

        $acl = new Acl;

        // Child elements must inherit access
        $acl->addRule($user, $site, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Site', 'View'));

        // but last added rules wins
        $acl->addRule($user, $page, new Rule('View'), false);
        $acl->addRule($user, $blog, new Rule('View'), false);

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Site', 'View'));
    }

    public function testParentRolesAndResources()
    {
        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $admin->addChild($moderator);
        $moderator->addChild($user);

        $page = new Resource('Page');
        $blog = new Resource('Blog');
        $site = new Resource('Site');

        $site->addChild($blog);
        $blog->addChild($page);

        $acl = new Acl;

        $acl->addRule($user, $page, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Page', 'View'));

        $this->assertFalse($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Blog', 'View'));

        $this->assertFalse($acl->isAllowed('User', 'Site', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Site', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Site', 'View'));

        $acl = new Acl;

        $acl->addRule($admin, $page, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('Admin', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Site', 'View'));

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Site', 'View'));

        $this->assertTrue($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Site', 'View'));

        $acl = new Acl;

        $acl->addRule($user, $site, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Site', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Site', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Site', 'View'));

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Page', 'View'));

        $this->assertTrue($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Blog', 'View'));

        // test add rule in the middle
        $acl = new Acl;

        $acl->addRule($moderator, $blog, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Site', 'View'));

        $this->assertTrue($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Site', 'View'));

        $this->assertFalse($acl->isAllowed('Admin', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Site', 'View'));

        // test add rule on the top
        $acl = new Acl;

        $acl->addRule($admin, $site, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Site', 'View'));

        $this->assertTrue($acl->isAllowed('Admin', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Blog', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Site', 'View'));

        $this->assertTrue($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Blog', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Site', 'View'));
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

    public function testGetResult()
    {
        $self = $this;

        $testReturnResult = function ($result, $expected) use ($self) {
            $index = 0;
            foreach ($result as $r) {
                $self->assertSame($expected[$index], $r->getRule());
                $index++;
            }
            $self->assertEquals(count($expected), $index);
        };

        $acl = new Acl;

        $user = new Role('User');
        $resource = new Resource('Page');

        $view = new Rule('View');
        $edit = new Rule('Edit');
        $remove = new Rule('Remove');

        $acl->addRule($user, $resource, $view, true);
        $acl->addRule($user, $resource, $edit, true);
        $acl->addRule($user, $resource, $remove, true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Page', 'Edit'));
        $this->assertTrue($acl->isAllowed('User', 'Page', 'Remove'));

        $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'View'), array($view));
        $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'Edit'), array($edit));
        $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'Remove'), array($remove));

        $acl = new Acl;

        $acl->addRule($user, $resource, $view, false);
        $acl->addRule($user, $resource, $edit, false);
        $acl->addRule($user, $resource, $remove, false);

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Page', 'Edit'));
        $this->assertFalse($acl->isAllowed('User', 'Page', 'Remove'));

        $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'View'), array($view));
        $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'Edit'), array($edit));
        $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'Remove'), array($remove));

        // test RuleResult order
        $acl = new Acl;

        $view1 = new Rule('View');
        $view2 = new Rule('View');
        $view3 = new Rule('View');
        $view4 = new Rule('View');

        $acl->addRule($user, $resource, $view, false);
        $acl->addRule($user, $resource, $view1, true);
        $acl->addRule($user, $resource, $view2, false);
        $acl->addRule($user, $resource, $view3, true);
        $acl->addRule($user, $resource, $view4, false);

        $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'View'), array($view4, $view3, $view2, $view1, $view));
    }

    public function testRuleWithNullActionNotCounts()
    {
        $acl = new Acl;

        $user = new Role('User');
        $resource = new Resource('Resource');

        $nullAction = new Rule('View');

        $acl->addRule($user, $resource, 'View', true);
        $acl->addRule($user, $resource, $nullAction, null);

        $this->assertTrue($acl->isAllowed('User', 'Resource', 'View'));
    }

    public function testActionCallable()
    {
        $acl = new Acl;

        $user = new Role('User');
        $resource = new Resource('Resource');

        $acl->addRule($user, $resource, 'View', function () {
            return true;
        });

        $this->assertTrue($acl->isAllowed('User', 'Resource', 'View'));
    }

    public function testChangePriorityViaActionCallable()
    {
        $getResults = function($resultCollection) {
            $rs = array();
            foreach ( $resultCollection as $r ) {
                $rs[] = $r;
            }
            return $rs;
        };

        $acl = new Acl;

        $user = new Role('User');
        $resource = new Resource('Resource');

        $acl->addRule($user, $resource, 'View', function(RuleResult $r){
            $r->setPriority(10);
            return true;
        });

        $acl->addRule($user, $resource, 'View', false);

        $this->assertTrue($acl->isAllowed('User', 'Resource', 'View'));

        $acl = new Acl();

        $p = new Role('P');
        $c1 = new Role('C1');
        $c2 = new Role('C2');
        $c3 = new Role('C3');

        $p->addChild($c1);
        $p->addChild($c2);
        $p->addChild($c3);

        $view = new Rule('View');
        $acl->addRule($p, $resource, $view, function (RuleResult $r) {
            $role = $r->getNeedRoleName();

            if ( $role == 'C3' || $role == 'P' ) {
                return true;
            }

            $r->setPriority(10);

            return $role == 'C1';
        });

        $view1 = new Rule('View');
        $acl->addRule($c1, $resource, $view1, false);
        $view2 = new Rule('View');
        $acl->addRule($c2, $resource, $view2, true);
        $view3 = new Rule('View');
        $acl->addRule($c3, $resource, $view3, false);

        $this->assertTrue($acl->isAllowed('P', 'Resource', 'View'));
        $this->assertTrue($acl->isAllowed('C1', 'Resource', 'View'));
        $this->assertFalse($acl->isAllowed('C2', 'Resource', 'View'));
        $this->assertFalse($acl->isAllowed('C3', 'Resource', 'View'));

        $rs = $getResults($acl->isAllowedReturnResult('P', 'Resource', 'View'));
        $this->assertCount(1, $rs);
        $this->assertEquals(0, $rs[0]->getPriority());
        $this->assertTrue($rs[0]->getAction());
        $this->assertSame($view, $rs[0]->getRule());
        $this->assertSame('P', $rs[0]->getNeedRoleName());
        $this->assertSame('Resource', $rs[0]->getNeedResourceName());

        $rs = $getResults($acl->isAllowedReturnResult('C1', 'Resource', 'View'));
        $this->assertCount(2, $rs);
        $this->assertEquals(10, $rs[0]->getPriority());
        $this->assertSame($view, $rs[0]->getRule());
        $this->assertTrue($rs[0]->getAction());
        $this->assertSame('C1', $rs[0]->getNeedRoleName());
        $this->assertSame('Resource', $rs[0]->getNeedResourceName());
        $this->assertEquals(0, $rs[1]->getPriority());
        $this->assertFalse($rs[1]->getAction());
        $this->assertSame($view1, $rs[1]->getRule());
        $this->assertSame('C1', $rs[1]->getNeedRoleName());
        $this->assertSame('Resource', $rs[1]->getNeedResourceName());

        $rs = $getResults($acl->isAllowedReturnResult('C2', 'Resource', 'View'));
        $this->assertCount(2, $rs);
        $this->assertEquals(10, $rs[0]->getPriority());
        $this->assertSame($view, $rs[0]->getRule());
        $this->assertFalse($rs[0]->getAction());
        $this->assertSame('C2', $rs[0]->getNeedRoleName());
        $this->assertSame('Resource', $rs[0]->getNeedResourceName());
        $this->assertEquals(0, $rs[1]->getPriority());
        $this->assertTrue($rs[1]->getAction());
        $this->assertSame($view2, $rs[1]->getRule());
        $this->assertSame('C2', $rs[1]->getNeedRoleName());
        $this->assertSame('Resource', $rs[1]->getNeedResourceName());

        $rs = $getResults($acl->isAllowedReturnResult('C3', 'Resource', 'View'));
        $this->assertCount(2, $rs);
        $this->assertEquals(-1, $rs[1]->getPriority());
        $this->assertSame($view, $rs[1]->getRule());
        $this->assertTrue($rs[1]->getAction());
        $this->assertSame('C3', $rs[1]->getNeedRoleName());
        $this->assertSame('Resource', $rs[1]->getNeedResourceName());
        $this->assertEquals(0, $rs[0]->getPriority());
        $this->assertFalse($rs[0]->getAction());
        $this->assertSame($view3, $rs[0]->getRule());
        $this->assertSame('C3', $rs[0]->getNeedRoleName());
        $this->assertSame('Resource', $rs[0]->getNeedResourceName());
    }

    public function testSetAggregates()
    {
        $acl = new Acl();

        $u = new Role('U');
        $r = new Resource('R');

        $roleAgr = new RoleAggregate();
        $roleAgr->addRole($u);

        $resourceAgr = new ResourceAggregate();
        $resourceAgr->addResource($r);

        $self = $this;

        $rule = new Rule('View');

        $acl->addRule($u, $r, $rule, function (RuleResult $r) use ($roleAgr, $resourceAgr, $self) {
            $self->assertSame($roleAgr, $r->getRoleAggregate());
            $self->assertSame($resourceAgr, $r->getResourceAggregate());

            return true;
        });

        $this->assertTrue($acl->isAllowed($roleAgr, $resourceAgr, 'View'));

        $rule->setAction(function (RuleResult $r) use ($self) {
            $self->assertNull($r->getRoleAggregate());
            $self->assertNull($r->getResourceAggregate());

            return true;
        });

        $this->assertTrue($acl->isAllowed('U', 'R', 'View'));
    }

    public function testAddRuleOneArgument()
    {
        $acl = new Acl();

        $rule = new Rule('View');

        $acl->addRule($rule);

        // only action determines is access allowd or not for rule with null role and resource
        $this->assertFalse($acl->isAllowed('U', 'R', 'View'));

        $rule->setAction(true);

        // rule matched any role or resource as it have null for both
        $this->assertTrue($acl->isAllowed('U', 'R', 'View'));

        // nothing is change if only one argument use, action is not overwritten to null
        $acl->addRule($rule);
        $this->assertTrue($acl->isAllowed('U', 'R', 'View'));

        // rule not matched if wrong rule name used
        $this->assertFalse($acl->isAllowed('U', 'R', 'NotMatchedView'));

        $u = new Role('U1');
        $rule->setRole($u);

        $r = new Resource('R1');
        $rule->setResource($r);

        $acl->addRule($rule);

        $this->assertFalse($acl->isAllowed('U', 'R', 'View'));
        // role and resource not overwritten
        $this->assertTrue($acl->isAllowed('U1', 'R1', 'View'));
        $this->assertSame($u, $rule->getRole());
        $this->assertSame($r, $rule->getResource());
    }

    public function testAddRuleTowArguments()
    {
        $acl = new Acl();

        $rule = new Rule('View');

        $rule->setAction(false);

        // rule overwrite action
        $acl->addRule($rule, true);

        // rule matched any role or resource as it have null for both
        $this->assertTrue($acl->isAllowed('U', 'R', 'View'));

        // rule not matched if wrong rule name used
        $this->assertFalse($acl->isAllowed('U', 'R', 'NotMatchedView'));

        $u = new Role('U1');
        $rule->setRole($u);

        $r = new Resource('R1');
        $rule->setResource($r);

        $acl->addRule($rule, true);

        $this->assertFalse($acl->isAllowed('U', 'R', 'View'));
        // role and resource not overwritten
        $this->assertTrue($acl->isAllowed('U1', 'R1', 'View'));

        $acl->addRule($rule, null);

        $this->assertNull($rule->getAction());
    }

    public function testAddRuleThreeArguments()
    {
        $acl = new Acl();

        $rule = new Rule('View');

        $rule->setAction(false);

        $u = new Role('U');
        $r = new Resource('R');

        $acl->addRule($u, $r, $rule);

        $this->assertFalse($acl->isAllowed('U', 'R', 'View'));
        $rule->setAction(true);
        $this->assertTrue($acl->isAllowed('U', 'R', 'View'));

        $u1 = new Role('U1');
        $r1 = new Resource('R1');

        // role and resource changed
        $acl->addRule($u1, $r1, $rule);

        $this->assertSame($u1, $rule->getRole());
        $this->assertSame($r1, $rule->getResource());

        $this->assertFalse($acl->isAllowed('U', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R1', 'View'));
    }

    public function testRuleOrResourceNull()
    {
        $acl = new Acl();

        $rule = new Rule('View');

        $rule->setAction(false);

        $u = new Role('U');
        $r = new Resource('R');

        $acl->addRule(null, $r, $rule, true);

        $this->assertTrue($acl->isAllowed('Any', 'R', 'View'));
        $this->assertFalse($acl->isAllowed('Any', 'R1', 'View'));
        $this->assertNull($rule->getRole());
        $this->assertSame($r, $rule->getResource());

        $acl->addRule($u, null, $rule, true);
        $this->assertTrue($acl->isAllowed('U', 'Any', 'View'));
        $this->assertFalse($acl->isAllowed('U1', 'Any', 'View'));
        $this->assertNull($rule->getResource());
        $this->assertSame($u, $rule->getRole());
    }

    public function testRuleWide()
    {
        $acl = new Acl();

        $rule = new RuleWide('RuleWide');

        $u = new Role('U');
        $r = new Resource('R');

        $acl->addRule($u, $r, $rule, true);

        $this->assertTrue($acl->isAllowed('U', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R', null));
        $this->assertFalse($acl->isAllowed('NotExist', 'R', null));
        $this->assertFalse($acl->isAllowed('U', 'NotExist', null));

        // null role and resource
        $acl = new Acl();

        $acl->addRule(null, null, $rule, true);

        $this->assertTrue($acl->isAllowed('U', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R', null));

        $this->assertTrue($acl->isAllowed(null, null, null));
        $acl->removeRuleById($rule->getId());
        $this->assertFalse($acl->isAllowed(null, null, null));

        // null resource
        $acl = new Acl();

        $u = new Role('U');

        $acl->addRule($u, null, $rule, true);

        $this->assertTrue($acl->isAllowed('U', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R', null));
        $this->assertTrue($acl->isAllowed('U', null, 'View'));
        $this->assertFalse($acl->isAllowed('NotExist', 'R', 'View'));
        $this->assertFalse($acl->isAllowed(null, 'R', 'View'));

        // null role
        $acl = new Acl();

        $r = new Resource('R');

        $acl->addRule(null, $r, $rule, true);

        $this->assertTrue($acl->isAllowed('U', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R', null));
        $this->assertTrue($acl->isAllowed(null, 'R', 'View'));
        $this->assertFalse($acl->isAllowed('U', 'NotExist', 'View'));
        $this->assertFalse($acl->isAllowed('U', null, 'View'));
    }

    public function testRuleApplyPriority()
    {
        $acl = new Acl();

        $rule = new Rule('View');
        $rule->setPriority(1);

        $u = new Role('U');
        $r = new Resource('R');

        $acl->addRule($u, $r, $rule, false);

        $acl->addRule($u, $r, 'View', true);

        $this->assertFalse($acl->isAllowed('U', 'R', 'View'));

        $rule->setPriority(0);

        $this->assertTrue($acl->isAllowed('U', 'R', 'View'));
    }

    /**
     * Testing edge conditions.
     */

    public function testEdgeConditionLastAddedRuleWins()
    {
        $acl = new Acl;

        $user = new Role('User');

        $page = new Resource('Page');

        $acl->addRule($user, $page, new Rule('View'), false);
        $acl->addRule($user, $page, new Rule('View'), true);

        $this->assertAttributeCount(2, 'rules', $acl);
        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));

        $acl->removeRule(null, null, 'View', false);

        $this->assertAttributeCount(1, 'rules', $acl);
        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));

        $acl->addRule($user, $page, new Rule('View'), false);

        $this->assertAttributeCount(2, 'rules', $acl);
        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));

        $acl->addRule($user, $page, new Rule('View'), true);
        $this->assertAttributeCount(3, 'rules', $acl);
        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
    }

    public function testEdgeConditionParentRolesAndResourcesWithMultipleRules()
    {
        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $admin->addChild($moderator);
        $moderator->addChild($user);

        $page = new Resource('Page');
        $blog = new Resource('Blog');
        $site = new Resource('Site');

        $site->addChild($blog);
        $blog->addChild($page);

        $acl = new Acl;

        $acl->addRule($moderator, $blog, new Rule('View'), true);
        $acl->addRule($user, $page, new Rule('View'), false);

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Site', 'View'));

        $this->assertTrue($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Site', 'View'));

        $this->assertFalse($acl->isAllowed('Admin', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Site', 'View'));
    }

    public function testEdgeConditionAggregateLastAddedWins()
    {
        $acl = new Acl;

        $user = new Role('User');
        $moderator = new Role('Moderator');

        $page = new Resource('Page');

        $userGroup = new RoleAggregate();

        $userGroup->addRole($moderator);
        $userGroup->addRole($user);

        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($moderator, $page, new Rule('View'), false);

        $this->assertTrue($acl->isAllowed($userGroup, 'Page', 'View'));

        $userGroup->removeRole('User');
        $this->assertFalse($acl->isAllowed($userGroup, 'Page', 'View'));
        $userGroup->addRole($user);
        $this->assertTrue($acl->isAllowed($userGroup, 'Page', 'View'));

        $acl = new Acl;

        $userGroup = new RoleAggregate();

        $userGroup->addRole($moderator);
        $userGroup->addRole($user);

        // changing rule orders don't change result
        $acl->addRule($moderator, $page, new Rule('View'), false);
        $acl->addRule($user, $page, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed($userGroup, 'Page', 'View'));

        $userGroup->removeRole('User');
        $this->assertFalse($acl->isAllowed($userGroup, 'Page', 'View'));
        $userGroup->addRole($user);
        $this->assertTrue($acl->isAllowed($userGroup, 'Page', 'View'));

        // test case when priority matter
        $acl = new Acl;

        $userGroup = new RoleAggregate();

        $userGroup->addRole($moderator);
        $userGroup->addRole($user);

        $contact = new Resource('Contact');
        $page->addChild($contact);

        $acl->addRule($moderator, $contact, new Rule('View'), true);
        $acl->addRule($user, $page, new Rule('View'), false);

        // user rule match first but moderator has higher priority
        $this->assertTrue($acl->isAllowed($userGroup, 'Contact', 'View'));

        $acl->addRule($user, $contact, new Rule('View'), false);

        // now priorities are equal
        $this->assertFalse($acl->isAllowed($userGroup, 'Contact', 'View'));
    }

    public function testComplexGraph()
    {
        $acl = new Acl();

        $u = new Role('U');
        $u1 = new Role('U1');
        $u2 = new Role('U2');
        $u3 = new Role('U3');

        $u->addChild($u1);
        $u->addChild($u2);
        $u->addChild($u3);

        $r = new Resource('R');
        $r1 = new Resource('R1');
        $r2 = new Resource('R2');
        $r3 = new Resource('R3');
        $r4 = new Resource('R4');
        $r5 = new Resource('R5');

        $r->addChild($r1);
        $r->addChild($r2);
        $r->addChild($r3);

        $r3->addChild($r4);
        $r3->addChild($r5);

        $a = new Rule('View');

        $acl->addRule($u, $r, $a, true);

        $this->assertTrue($acl->isAllowed('U', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R2', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R3', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R4', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R2', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R3', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R4', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R2', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R3', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R4', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R2', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R3', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R4', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R5', 'View'));

        $a2 = new Rule('View');

        $acl->addRule($u, $r3, $a2, false);

        $this->assertTrue($acl->isAllowed('U', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R2', 'View'));
        $this->assertFalse($acl->isAllowed('U', 'R3', 'View'));
        $this->assertFalse($acl->isAllowed('U', 'R4', 'View'));
        $this->assertFalse($acl->isAllowed('U', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R2', 'View'));
        $this->assertFalse($acl->isAllowed('U1', 'R3', 'View'));
        $this->assertFalse($acl->isAllowed('U1', 'R4', 'View'));
        $this->assertFalse($acl->isAllowed('U1', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R2', 'View'));
        $this->assertFalse($acl->isAllowed('U2', 'R3', 'View'));
        $this->assertFalse($acl->isAllowed('U2', 'R4', 'View'));
        $this->assertFalse($acl->isAllowed('U2', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R2', 'View'));
        $this->assertFalse($acl->isAllowed('U3', 'R3', 'View'));
        $this->assertFalse($acl->isAllowed('U3', 'R4', 'View'));
        $this->assertFalse($acl->isAllowed('U3', 'R5', 'View'));

        $a3 = new Rule('View');
        $a4 = new Rule('View');
        $acl->addRule($u2, $r4, $a3, true);
        $acl->addRule($u2, $r5, $a4, true);

        $this->assertTrue($acl->isAllowed('U', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R2', 'View'));
        $this->assertFalse($acl->isAllowed('U', 'R3', 'View'));
        $this->assertFalse($acl->isAllowed('U', 'R4', 'View'));
        $this->assertFalse($acl->isAllowed('U', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R2', 'View'));
        $this->assertFalse($acl->isAllowed('U1', 'R3', 'View'));
        $this->assertFalse($acl->isAllowed('U1', 'R4', 'View'));
        $this->assertFalse($acl->isAllowed('U1', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R2', 'View'));
        $this->assertFalse($acl->isAllowed('U2', 'R3', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R4', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R2', 'View'));
        $this->assertFalse($acl->isAllowed('U3', 'R3', 'View'));
        $this->assertFalse($acl->isAllowed('U3', 'R4', 'View'));
        $this->assertFalse($acl->isAllowed('U3', 'R5', 'View'));
    }

    public function testCustomRule()
    {
        require_once __DIR__ . '/../Stubs/CustomRule.php';

        // must match any role and act as wide
        $acl = new Acl();

        $rule = new \MathAnyRoleAndActAsWide('MathAnyRoleAndActAsWide');

        $u = new Role('U');
        $r = new Resource('R');

        $acl->addRule($u, $r, $rule, true);

        $this->assertTrue($acl->isAllowed('U', 'R', 'ShouldActAsWide'));
        $this->assertTrue($acl->isAllowed('U1', 'R', 'ShouldActAsWide'), 'Must work with any role');
        $this->assertFalse($acl->isAllowed('U', 'R1', 'ShouldActAsWide'));
        $this->assertFalse($acl->isAllowed('U1', 'R1', 'MathAnyRoleAndActAsWide'));

        // must match any resource and act as wide
        $acl = new Acl();

        $rule = new \MathAnyResourceAndActAsWide('MathAnyResourceAndActAsWide');

        $u = new Role('U');
        $r = new Resource('R');

        $acl->addRule($u, $r, $rule, true);

        $this->assertTrue($acl->isAllowed('U', 'R', 'ShouldActAsWide'));
        $this->assertTrue($acl->isAllowed('U', 'R1', 'ShouldActAsWide'), 'Must work with any resource');
        $this->assertFalse($acl->isAllowed('U1', 'R', 'ShouldActAsWide'));
        $this->assertFalse($acl->isAllowed('U1', 'R1', 'MathAnyResourceAndActAsWide'));

        // must match anything
        $acl = new Acl();
        $rule = new \MatchAnything('MathAnything');

        $u = new Role('U');
        $r = new Resource('R');

        $acl->addRule($u, $r, $rule, true);
        $this->assertTrue($acl->isAllowed('anything', 'anything', 'anything'));
    }
}