# Eliza

Eliza is a simple acceptance testing framework

### Description

You can start using acceptance testing in your projects now with Eliza.


#### Sample acceptance test

``` php
<?php

$I = Eliza::test();
$I->want("Check Authorization");
$I->go("/");
$I->fill(array("Login"=>"demo","Password"=>"demo"));
$I->click("Login");
$I->see("You are logged in!");
```

## Installation

Requirements

- PHP 5.1.0 or above;
- cUrl PHP library.

Put folder "eliza" in the root directory of your project. You may rename this folder for more security or add password protection by .htaccess.

Now you can go to Eliza web interface that placed on address: YOUR_CITE.COM/eliza and write your first test.

## Getting Started

Write tests in your favorite IDE and put them into "tests" directory in Eliza project directory.

You can to add extra functionality to your tests with Eliza's modules. Specify needed modules in "import" section of config file.

``` php
'import' => array (
  'error',
  'db' => array (
    'username' => 'root',
    'password' => 'qwerty',
    'host' => 'localhost',
    'dbname' => 'eliza',
  ),
),
```

### License
Code is licensed under [GPL v3.0](http://elizatesting.com/help/gpl)

(c) SnowHall Ltd 2007-2013