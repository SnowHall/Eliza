<?php
/**
 * Eliza - Simple php acceptance testing framework
 *
 *
 * @author		SnowHall - http://snowhall.com
 * @website		http://elizatesting.com
 * @email		support@snowhall.com
 *
 * @version		0.2.0
 * @date		April 18, 2013
 *
 * Eliza - simple framework for BDD development and acceptance testing.
 * Eliza has user-friendly web interface that allows run and manage your tests from your favorite browser.
 *
 * Copyright (c) 2009-2013
 */

class UnitTest
{
  // Global execution context
  public $app;

  // Class of executed test
  private $testName;

  // Name of current test (test's class method)
  private $methodName;

  // Set if current test isn't pass test method
  public $notAccept = false;

  // Set if curren test failed
  public $testError = false;

  public function __construct($testName) {
    $this->setApp();
    $this->setTestClass($testName);
  }

  public function setApp() {
    $this->app = Eliza::app();
  }

  public function setTestClass($testName) {
    $this->testName = $testName;
  }

  /**
   * Mark test as incomplete if $condition not true
   *
   * @param bool $condition Compared condition
   */
  public function assertTrue($condition) {
    if ($condition !== true) $this->setMethodFail();
  }

  /**
   * Mark test as incomplete if $condition not false
   *
   * @param bool $condition Compared condition
   */
  public function assertFalse($condition) {
    if ($condition !== false) $this->setMethodFail();
  }

  /**
   * Mark test as incomplete if $actual value not equal $expected value
   *
   * @param mixed $expected Expected value
   * @param mixed $actual Compared value
   */
  public function assertEquals($expected, $actual) {
    if ($expected !== $actual) $this->setMethodFail();
  }

  /**
   * Mark test as incomplete if file $filename not exists
   *
   * @param string $filename Checked file
   */
  public function assertFileExists($filename) {
    if (!file_exists($filename)) $this->setMethodFail();
  }

  /**
   * Mark test as incomplete if $actual value is not empty
   *
   * @param mixed $actual Compared value
   */
  public function assertEmpty($actual) {
    if (!empty($actual)) $this->setMethodFail();
  }

  /**
   * Mark test as incomplete if $actual value is empty
   *
   * @param mixed $actual Compared value
   */
  public function assertNotEmpty($actual) {
    if (empty($actual)) $this->setMethodFail();
  }

  /**
   * Mark test as incomplete if $actual value is not null
   *
   * @param mixed $actual Compared value
   */
  public function assertNull($actual) {
    if (!is_null($actual)) $this->setMethodFail();
  }

  /**
   * Mark test as incomplete if $actual value is null
   *
   * @param mixed $actual Compared value
   */
  public function assertNotNull($actual) {
    if (is_null($actual)) $this->setMethodFail();
  }

  /**
   * Invoke test methods by calling Reflection methods with args
   *
   * @param array $methods Array of Reflection methods
   */
  public function invoke($methods) {
    $test = new $this->testName($this->testName);

    // call methods with its params
    foreach ($methods as $method) {
      $test->methodName = $method->getName();
      $method->invokeArgs($test, $method->getParameters());
      $test->setMethodResponse();
      $test->clearMethodResponse();
    }
    $test->setTestResponse();
  }

  /**
   * Set current method response after executing test
   */
  private function setMethodResponse() {
    if (!$this->notAccept) {
      $this->app->setResponse('Method "'.$this->methodName.'" is completed.', 'success');
    }
    else {
      $this->app->setResponse('Method "'.$this->methodName.'" is failed.', 'error');
    }
  }

  /**
   * Clear current method response for next method
   */
  private function clearMethodResponse() {
    $this->notAccept = false;
  }

  /**
   * Set current test response after executing all tests
   */
  private function setTestResponse() {
    if (!$this->testError) {
      $this->app->setResponse('Test "'.$this->testName.'" is completed.', 'success');
    }
    else {
      $this->app->setResponse('Test "'.$this->testName.'" is failed.', 'error');
    }
  }

  /**
   * Set flags if test's method has been failed
   */
  private function setMethodFail() {
    $this->notAccept = true;
    $this->testError = true;
  }
}