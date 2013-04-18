<?php
  /**
   * Unit test demo example.
   *
   * Checks such methods in SimpleClass as:
   * - getName();
   * - checkName();
   * - saveLog();
   */

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