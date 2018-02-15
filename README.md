Simple Access Control List (ACL) for PHP.

[![Build Status](https://travis-ci.org/alexshelkov/SimpleAcl.svg?branch=master)](https://travis-ci.org/alexshelkov/SimpleAcl)
[![Coverage Status](https://coveralls.io/repos/alexshelkov/SimpleAcl/badge.svg?branch=master&service=github)](https://coveralls.io/github/alexshelkov/SimpleAcl?branch=master)
_____________________________________________________________________________________________________________________
#### Install
##### Using composer
Add following in your composer.json:
```json
{
    "require": {
        "alexshelkov/simpleacl": "2.*"
    }
}
```
##### Manual
Download library and register PSR-0 compatible autoloader.
________________________________________________________
#### Usage
##### Basic usage
###### Theory
There is 4 kind of objects: *Rules*, *Roles*, *Resources* and *Acl* which holds list of *Rules*. Some *Rule* can grant access for some *Role* to some *Resource*.

###### Create rules
Lets create "View" Rule, and with with it grant access for "User" to "Page" (note: all names are case sensitive):
```php
$view = new Rule('View');
$view->setRole(new Role('User'));
$view->setResource(new Resource('Page'));
$view->setAction(true); // true means that we allow access

var_dump((bool)$view->isAllowed('User', 'Page')); // true
```

###### Add rules
There is not much sense in rules without Acl. So we need to add rules in it. In next example we add few rules in Acl and see whats happens.
```php
$acl = new Acl();

$user = new Role('User');
$admin = new Role('Admin');

$siteFrontend = new Resource('SiteFrontend');
$siteBackend = new Resource('SiteBackend');

$acl->addRule($user, $siteFrontend, new Rule('View'), true);
$acl->addRule($admin, $siteFrontend, 'View', true); // you can use string as rule
$acl->addRule($admin, $siteBackend, 'View', true);

var_dump($acl->isAllowed('User', 'SiteFrontend', 'View')); // true
var_dump($acl->isAllowed('User', 'SiteBackend', 'View')); // false
var_dump($acl->isAllowed('Admin', 'SiteFrontend', 'View')); // true
var_dump($acl->isAllowed('Admin', 'SiteBackend', 'View')); // true
```
They are various way to add rules to *Acl*, addRule method accepts from one to four arguments, so you can also add rules like this:
```php
<?php
// before add $view rule to Acl you can set it action, role or resource
$acl->addRule($view);

// where is true -- is action
$acl->addRule($view, true);

// in that case action must be set before adding rule
$acl->addRule($user, $siteBackend, $view);
```

###### Roles and resource inheritance
As you maybe notice in previous example we have some duplication of code, because both "User" and "Admin" was allowed to "View" "SiteFrontend" we added 2 rules. But it is possible to avoid this using roles inheritance.
```php
$acl = new Acl();

$user = new Role('User');
$admin = new Role('Admin');
$user->addChild($admin); // add user's child

$siteFrontend = new Resource('SiteFrontend');
$siteBackend = new Resource('SiteBackend');

$acl->addRule($user, $siteFrontend, 'View', true);
$acl->addRule($admin, $siteBackend, 'View', true);

var_dump($acl->isAllowed('User', 'SiteFrontend', 'View')); // true
var_dump($acl->isAllowed('User', 'SiteBackend', 'View')); // false
var_dump($acl->isAllowed('Admin', 'SiteFrontend', 'View')); // true
var_dump($acl->isAllowed('Admin', 'SiteBackend', 'View')); // true
```
Inheritance works for resources too.

##### Using callbacks
You can create more complex rules using callbacks.
```php
$acl = new Acl();

$user = new Role('User');
$siteFrontend = new Resource('SiteFrontend');

$acl->addRule($user, $siteFrontend, 'View', function (SimpleAcl\RuleResult $ruleResult) {
    echo $ruleResult->getNeedRoleName() . "\n";
    echo $ruleResult->getNeedResourceName() . "\n";
    echo $ruleResult->getPriority() . "\n";
    echo $ruleResult->getRule()->getRole()->getName() . "\n";
    echo $ruleResult->getRule()->getResource()->getName() . "\n";

    return true;
});


var_dump($acl->isAllowed('User', 'SiteFrontend', 'View')); // true

// Outputs:
// User
// SiteFrontend
// 0
// User
// SiteFrontend
// bool(true)
```

##### Using role and resource aggregates
It is possible to check access not for particular Role or Resource, but for objects which aggregate them. These kind of objects must implement, respectively, SimpleAcl\Role\RoleAggregateInterface and SimpleAcl\Role\ResourceAggregateInterface.

You can use SimpleAcl\Role\RoleAggregate and SimpleAcl\Role\ResourceAggregate as object which allow aggregation.
```php
$acl = new Acl();

$user = new Role('User');
$admin = new Role('Admin');

$all = new RoleAggregate;
$all->addRole($user);
$all->addRole($admin);

$siteFrontend = new Resource('SiteFrontend');
$siteBackend = new Resource('SiteBackend');

$acl->addRule($user, $siteFrontend, 'View', true);
$acl->addRule($admin, $siteBackend, 'View', true);

var_dump($acl->isAllowed($all, 'SiteFrontend', 'View')); // true
var_dump($acl->isAllowed($all, 'SiteBackend', 'View')); // true
```

You can have access to role and resource aggregates in callbacks.
```php
$acl->addRule($user, $siteFrontend, 'View', function (SimpleAcl\RuleResult $ruleResult) {
    var_dump($ruleResult->getRoleAggregate());
    var_dump($ruleResult->getResourceAggregate());
});

var_dump($acl->isAllowed($all, 'SiteFrontend', 'View')); // true
```

__For more help check out wiki pages.__