# Eliza

Eliza is a simple Open Source Unit and Acceptance testing framework

### Description

You can start using unit and acceptance testing in your projects now with Eliza.


#### Sample Acceptance test

``` php
<?php

  // Initialize test
  $I = new AcceptanceTest();

  // Specify short definition for test
  $I->want('Test demo site');

  // Go to the page from which to start testing
  $I->go('http://snowhall.com');

  // Verifying the opening pages by checking the information contained in it
  $I->see('Web Development Service');

  // Check PHP and SQL errors on the page
  $I->checkError();

  // Go to link
  $I->click('Web Development Service');

  // Verifying the opening pages by checking the information contained in it
  $I->see('MVC frameworks');
```

#### Sample Unit test

``` php
<?php

  // Include file with test class
  require_once DATA_PATH.'SimpleClass.php';

  // Define new test class
  class SimpleClassTest extends UnitTest
  {
    function testSetName() {
      $class = new SimpleClass();
      $class->setName('Ricardo');
      $this->assertEquals('Ricardo', $class->getName());
    }

    function testCheckName() {
      $class = new SimpleClass();
      $class->setName('Edward');
      $this->assertTrue($class->checkName());
    }

    function testSaveLog() {
      $class = new SimpleClass();
      $class->saveLog('Log Info');
      $this->assertFileExists(DATA_PATH.'unit_test.log');
    }
  }
```

## Installation

Requirements

- PHP 5.1.0 or above;

Put folder "eliza" in the root directory of your project. You may rename this folder for more security or add password protection by .htaccess.

Now you can go to Eliza web interface that placed on address: YOUR_CITE.COM/eliza and write your first test.

## Getting Started

Write tests in your favorite IDE and put them into "tests" directory in Eliza project directory.

You can to add extra functionality to your Acceptance tests with Eliza's modules.
Enable needed modules in "Settings" section of web interface.


### License
Code is licensed under [GPL v3.0](http://elizatesting.com/help/gpl)

(c) SnowHall Ltd 2007-2013