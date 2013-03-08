<?php
/**
 * Eliza - Simple php acceptance testing framework
 * 
 * 
 * @author		SnowHall - http://snowhall.com
 * @website		http://elizatesting.com
 * @email		support@snowhall.com
 * 
 * @version		0.1.0
 * @date		March 8, 2013
 * 
 * Eliza - simple framework for BDD development and acceptance testing.
 * Eliza has user-friendly web interface that allows run and manage your tests from your favorite browser.
 *
 * Copyright (c) 2009-2013
 */

class Config
{
  private static $config;

  private static function init() {
    self::$config = require ROOT_PATH.'/config.php';
  }

  public static function get() {
    if (null === self::$config) {
      self::init();
    }
    return self::$config;
  }

  public static function getValue($name, $defaultValue = null) {
    return isset(self::$config[$name]) ? self::$config[$name] : $defaultValue;
  }

  public static function setValue($name, $value) {
    self::$config[$name] = $value;
  }

  public static function getModuleConfig($module, $name, $defaultValue = null) {
    return isset(self::$config[$module][$name]) ? self::$config[$module][$name] : $defaultValue;
  }

}

?>
