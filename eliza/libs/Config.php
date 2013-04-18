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

class Config
{
  // Current project settings
  private static $config;

  /**
   * Init config. Get current settings from config file
   */
  private static function init() {
    self::$config = require_once CONFIG_FILE;
  }

  /**
   * Get all settings
   *
   * @return array
   */
  public static function get() {
    if (null === self::$config) {
      self::init();
    }
    return self::$config;
  }

  /**
   * Return config value
   *
   * @param type $name Name of config setting
   * @param type $defaultValue Default value for config
   * @return mixed Config value
   */
  public static function getValue($name, $defaultValue = null) {
    if (!self::$config) self::init();

    return isset(self::$config[$name]) ? self::$config[$name] : $defaultValue;
  }

  /**
   * Set config value
   *
   * @param string $name Name of config setting
   * @param mixed $value Value for config setting
   */
  public static function setValue($name, $value) {
    self::$config[$name] = $value;
    self::save();
  }

  /**
   * Return module's settiings
   *
   * @param string $module Module name
   * @param string $name Setting name
   * @param mixed $defaultValue Default setting value
   * @return mixed Config value
   */
  public static function getModuleConfig($module, $name, $defaultValue = null) {
    return isset(self::$config[$module][$name]) ? self::$config[$module][$name] : $defaultValue;
  }

  /**
   * Remove setting value from config file
   *
   * @param string $name Setting name
   */
  public static function removeValue($name) {
    unset(self::$config[$name]);
    self::save();
  }

  /**
   * Save config array to config file.
   *
   * @param array $newConfig Config array
   * @return boolean
   */
  public static function save($newConfig = null) {
    $config = $newConfig ? array_merge(self::$config, $newConfig) : self::$config;

    if ($config) {
      file_put_contents(CONFIG_FILE, '<?php return ' . var_export($config, true) . ';');
    }
    return true;
  }
}

?>
