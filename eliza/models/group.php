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

class Group
{
  public static function getGroups()
  {
    global $config;

    if ($config['groups']) return $config['groups'];

    $groups = array();
    if (file_exists(GROUPS_FILE)) {
      $groups = json_decode(file_get_contents(GROUPS_FILE),true);
    }
    return $groups;
  }

  public static function saveGroup($name = '')
  {
    global $config;

    if ($name)
    {
      $group = array();
      $group['name'] = $name;
      $id = substr(md5(uniqid()),2,6);
      $config['groups'][$id] = $group;
    }

    return file_put_contents(GROUPS_FILE, json_encode($config['groups']));
  }
}

?>
