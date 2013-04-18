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

/*
 * Test class for unit tests
 */
class SimpleClass
{
  private $name;

  private $age;

  public function setName($name) {
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function setAge($age) {
    $this->age = $age;
  }

  public function getAge() {
    return $this->age;
  }

  public function checkName() {
    if (strlen($this->name) < 20) return true;
    return false;
  }

  public function saveLog($logInfo) {
    $filename = DATA_PATH.'unit_test.log';
    return file_put_contents($filename, $logInfo);
  }
}
