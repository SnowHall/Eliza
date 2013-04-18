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

/**
 * Represents tests groups
 */
class Group
{
  // List of current groups
  public static $groups;

  /**
   * Initialize groups. Get groups from storage (JSON)
   *
   * @return array Tests groups
   */
  public static function init() {
    if (self::$groups) return self::$groups;
    self::$groups = Helpers::getFromJson(GROUPS_FILE);
    return self::$groups;
  }

  /**
   * Returns all current groups
   *
   * @return array Groups
   */
  public static function getAll() {
    return self::$groups;
  }

  /**
   * Return group by Id
   *
   * @param int $id Group Id
   * @return array Group
   */
  public static function get($id) {
    if (!isset(self::$groups[$id])) return;

    return self::$groups[$id];
  }

  /**
   * Save group by Id
   *
   * @param type $group Group info
   * @param type $id Group Id. Will be generated if not specified
   * @return boolean
   */
  public static function save($group, $id = null) {
    if (!$id) $id = substr(md5(uniqid()),2,6);

    self::$groups[$id] = $group;
    return self::saveAll();
  }

  /**
   * Save group with specified name
   *
   * @param type $name Group name
   * @return boolean
   */
  public static function saveGroupByName($name) {
    $group = array();
    $group['name'] = $name;
    $id = substr(md5(uniqid()),2,6);
    self::$groups[$id] = $group;

    return self::saveAll();
  }

  /**
   *  Save all groups
   *
   * @return boolean Saving status
   */
  public static function saveAll() {
    return file_put_contents(GROUPS_FILE, json_encode(self::$groups));
  }

  /**
   * Delete group by Id
   *
   * @param int $id Group Id
   * @return boolean
   */
  public static function delete($id) {
    if (!$id || !isset(self::$groups[$id])) return false;

    unset(self::$groups[$id]);
    return self::saveAll();
  }

  /**
   * Remove test from group
   *
   * @param type $groupId Group Id
   * @param type $testId Test name
   * @return boolean
   */
  public static function removeTest($groupId, $testId) {
    $groupTests = self::$groups[$groupId]['tests'];
    // If test not in the group - return
    if (!in_array($testId, $groupTests)) return;

    $key = array_search($testId,$groupTests);
    unset(self::$groups[$groupId]['tests'][$key]);
    self::saveAll();
  }

  /**
   * Return Groups checkboxList
   *
   * @param array $groups
   * @return array List of checkboxes
   */
  public static function getGroupsCheckboxList($groups) {
    $list = array();
    foreach($groups as $key=>$group) {
      $list[$key] = $group['name'];
    }
    return $list;
  }
}