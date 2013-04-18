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

class Module
{
  // Modules list of files from modules folder
  private static $modulesList = array();
  // Modules list with config
  private static $availableModules = array();

  /**
   * Initialize module object. Set application context.
   */
  public function __construct() {
    $this->app = Eliza::app();
  }

  /**
   * Module options
   *
   * @return array
   */
  public function options() {
    return array();
  }

  /**
   * Returns modules list
   *
   * @return array Modules list
   */
  public static function getModulesList() {
    if (self::$modulesList) return self::$modulesList;

    self::$modulesList = Helpers::scanDirectory(MODULES_PATH, false);

    foreach(self::$modulesList as $key=>$file) {
      self::$modulesList[$key] = str_replace('module.php', '', strtolower($file));
    }

    return self::$modulesList;
  }

  /**
   * Returns activated modules
   *
   * @return array Activated modules
   */
  public static function getAvailableModules() {
    if (self::$availableModules) return self::$availableModules;
    $modulesList = self::getModulesList();
    $config = file_exists(MODULES_FILE) ? Helpers::getFromJson(MODULES_FILE) : array();

    foreach ($modulesList as $module) {
      // Check saved config
      if (!empty($config) && key_exists($module, $config)) {
        self::$availableModules[$module] = $config[$module];
      }
      else {
        self::$availableModules[$module] = array('enabled'=>false);
      }
      // Check .Info file
      if (file_exists(MODULES_PATH.$module.'/'.$module.'.info')) {
        $info = parse_ini_file(MODULES_PATH.$module.'/'.$module.'.info');

        if ($info) {
          self::$availableModules[$module]['info'] = $info;
        }
      }
    }

    return self::$availableModules;
  }

  /**
   * Save modules config to the config file
   *
   * @param array $modules Modules config
   * @param boolean $local Save config as local
   * @return boolean
   */
  public static function saveConfig($modules = array(), $local = false) {
    if (!$modules) $modules = self::$availableModules;
    $fileName = $local ? str_replace('.json', '', MODULES_FILE).'-local.json' : MODULES_FILE;
    return file_put_contents($fileName, json_encode($modules));
  }

  /**
   * Update config value
   *
   * @param string $moduleId Module name
   * @param array $options Config options
   * @param boolean $isLocal Is config local or not
   * @return boolean
   */
  public static function updateConfig($moduleId, $options = array(), $isLocal = false) {
    if (!$options) return false;

    $currentOptions = self::$availableModules[$moduleId]['options'] ?
      self::$availableModules[$moduleId]['options'] : array();

    self::$availableModules[$moduleId]['options'] = array_merge($currentOptions,$options);

    self::saveConfig(self::$availableModules, $isLocal);
  }

  /**
   * Return module by name
   *
   * @param string $name Module name
   * @return type
   */
  public static function getModule($name) {
    return self::$availableModules[$name];
  }

  /**
   * Define has module local config or not
   *
   * @return boolean True if local module's config exists
   */
  public static function hasLocalConfig() {
    $localFile = str_replace('.json', '', MODULES_FILE).'-local.json';
    return file_exists($localFile);
  }
}