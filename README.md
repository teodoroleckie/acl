# Tleckie\Acl access control list (ACL)
Tleckie\Acl component provides a lightweight and flexible access control list (ACL) implementation for privileges management. 
In general, an application may utilize such ACL‘s to control access to certain protected objects by other requesting objects.


[![Latest Version on Packagist](https://img.shields.io/packagist/v/tleckie/acl.svg?style=flat-square)](https://packagist.org/packages/tleckie/acl)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/teodoroleckie/acl/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/teodoroleckie/acl/?branch=main)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/teodoroleckie/acl/badges/code-intelligence.svg?b=main)](https://scrutinizer-ci.com/code-intelligence)
[![Build Status](https://scrutinizer-ci.com/g/teodoroleckie/acl/badges/build.png?b=main)](https://scrutinizer-ci.com/g/teodoroleckie/acl/build-status/main)

## Installation

You can install the package via composer:

```bash
composer require tleckie/acl
```

## Usage

```php
<?php

include_once "vendor/autoload.php";

$acl = new Acl();

$acl->addRole('USER-0');
$acl->addRole('USER-1', ['USER-0']); // (USER-0) parent role.
$acl->addRole('USER-2', ['USER-1']); // (USER-1) parent role.

$acl->addResource('RESOURCE-0'); 
$acl->addResource('RESOURCE-1', ['RESOURCE-0']); // (RESOURCE-0) parent resource.
$acl->addResource('RESOURCE-2', ['RESOURCE-1']); // (RESOURCE-1) parent resource.
$acl->addResource('RESOURCE-3', ['RESOURCE-2']); // (RESOURCE-2) parent resource.

$acl->allow(['USER-0'], ['RESOURCE-0']);
$acl->deny(['USER-1'], ['RESOURCE-3'],['view','edit','list']);

$acl->isAllowed('USER-0','RESOURCE-2'); // true
$acl->isAllowed('USER-1','RESOURCE-3'); // true
$acl->isAllowed('USER-1','RESOURCE-3', 'view'); // false
$acl->isAllowed('USER-2','RESOURCE-3'); // true
$acl->isAllowed('USER-2','RESOURCE-3', 'view'); // false
```