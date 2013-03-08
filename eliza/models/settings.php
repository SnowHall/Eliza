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

class Settings
{
  public static function saveConfigFile($newConfig)
  {
    global $config;
    $config = array_merge($config, $newConfig);
    unset($config['tasks'],$config['groups']);
    file_put_contents(dirname(__FILE__).'/../config.php', '<?php return ' . var_export($config, true) . ';');

    return true;
  }
}
?>
