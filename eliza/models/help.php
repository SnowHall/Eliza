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

class Help {
  // Help info about Eliza's modules
  public static $modules;

  // Help info about Eliza's methods
  public static $methods;

  /**
   * Returns info about Eliza's modules
   *
   * @return array Modules Info
   */
  public static function getModulesInfo() {
    if (self::$modules) return self::$modules;
    self::$modules = Helpers::getFromJson(MODULES_HELP_FILE);
    return self::$modules;
  }

  /**
   * Return module info by module Id
   *
   * @param int $id Module id
   * @return array Module info
   */
  public static function getModuleInfoById($id) {
    if (!self::$modules) self::getModulesInfo();

    return self::$modules[$id];
  }

  /**
   * Returns info about Eliza's methods
   *
   * @return array Methods Info
   */
  public static function getMethodsInfo() {
    if (self::$methods)  return self::$methods;
    self::$methods = Helpers::getFromJson(METHODS_HELP_FILE);
    return self::$methods;
  }

  /**
   * Returns info about Eliza module's methods by module Id
   *
   * @param int $moduleId Module id
   * @return array Methods Info
   */
  public static function getMethodsInfoByModule($moduleId) {
    if (!self::$methods)  self::getMethodsInfo();
    $methods = array();
    foreach (self::$methods as $method) {
      if ($method['module_id'] == $moduleId) $methods[] = $method;
    }
    return $methods;
  }
}
