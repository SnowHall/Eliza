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
 * Represents shelduer tasks
 */
class Task
{

  // List of current tasks
  public static $tasks;

  /**
   * Initialize tasks. Get tasks from storage (JSON)
   *
   * @return array Tasks
   */
  public static function init() {
    if (!empty(self::$tasks)) return self::$tasks;
    self::$tasks = Helpers::getFromJson(TASKS_FILE);
    return self::$tasks;
  }

  /**
   * Returns all current tasks
   *
   * @return array Tasks
   */
  public static function getAll() {
    if (empty(self::$tasks)) self::init();

    return self::$tasks;
  }

  /**
   * Return task by Id
   *
   * @param int $id Task Id
   * @return array Task
   */
  public static function get($id) {
    if (!isset(self::$tasks[$id])) return;

    return self::$tasks[$id];
  }

  /**
   * Save task form
   *
   * @param type $form
   * @return boolean
   */
  public static function saveForm($form)
  {
    $task = array();
    $task['name'] = $form['name'];
    $task['lastUpdate'] = $form['lastUpdate'] ? base64_decode($form['lastUpdate']) : 'not run';
    $task['periods'] = $form['periods'];

    switch ($task['periods'])
    {
      case 'hourly':
        $task['hourly_hours'] = $form['hourly_hours'];
        $task['hourly_minutes'] = $form['hourly_minutes'];
        break;

      case 'daily':
        $task['daily_days'] = serialize($form['daily_days']);
        $task['daily_runtime'] = $form['daily_runtime'];
        break;

      case 'weekly':
        $task['weekly_days'] = $form['weekly_days'];
        $task['weekly_runtime'] = $form['weekly_runtime'];
        break;

      case 'monthly':
        $task['monthly_days'] = $form['monthly_days'];
        $task['monthly_runtime'] = $form['monthly_runtime'];
        break;

      default:
        return false;
    }

    if ($form['sendEmail']) {
      $task['sendEmail'] = 1;
      $task['email'] = $form['email'];
    }

    if ($form['created']) {
      $task['created'] = base64_decode($form['created']);
    }
    else {
      $date = new DateTime(null, new DateTimeZone(Timezone::getTimezonByOffset(Config::getValue('timezone'))));
      $task['created'] = $date->format('Y-m-d H:i:s');
    }

    if (isset($form['tests'])) {
      foreach($form['tests'] as $test) {
        $task['tests'][] = $test;
      }
    }
    if (isset($form['groups'])) {
      foreach($form['groups'] as $group) {
        $task['groups'][] = $group;
      }
    }
    $id = $form['taskId'] ? $form['taskId'] : substr(md5(uniqid()),2,6);

    return self::save($task, $id);
  }

  /**
   * Save task by Id
   *
   * @param type $task Task info
   * @param type $id Task Id. Will be generated if not specified
   * @return boolean
   */
  public static function save($task, $id = null) {
    if (!$id) $id = substr(md5(uniqid()),2,6);

    self::$tasks[$id] = $task;
    return self::saveAll();
  }

  /**
   * Save all tasks
   *
   * @return boolean
   */
  public static function saveAll() {
    return file_put_contents(TASKS_FILE, json_encode(self::$tasks));
  }

  /**
   * Delete task by Id
   *
   * @param int $id Task Id
   * @return boolean
   */
  public static function delete($id) {
    if (!$id || !isset(self::$tasks[$id])) return false;

    unset(self::$tasks[$id]);
    return self::saveAll();
  }

  /**
   * Remove test from task
   *
   * @param type $taskId Task Id
   * @param type $testId Test name
   * @return boolean
   */
  public static function removeTest($taskId, $testId) {
    $taskTests = self::$tasks[$taskId]['tests'];
    // If test not in the group - return
    if (!in_array($testId, $taskTests)) return;

    $key = array_search($testId,$taskTests);
    unset(self::$tasks[$taskId]['tests'][$key]);
    self::saveAll();
  }

	/**
   * Remove group from task
   *
   * @param type $taskId Task Id
   * @param type $grouptId Group id
   * @return boolean
   */
  public static function removeGroup($taskId, $groupId) {
    $taskGroup = self::$tasks[$taskId]['groups'];
    // If test not in the group - return
    if (!in_array($groupId, $taskGroup)) return;

		$key = array_search($groupId, $taskGroup);
    unset(self::$tasks[$taskId]['groups'][$key]);
    self::saveAll();
  }

  public static function updateField($id, $field, $value) {
    self::$tasks[$id][$field] = $value;
    self::saveAll();
  }

  /**
   * Validate task form
   *
   * @param type $form
   * @return string
   */
  public static function validate($form)
  {
    $errors = array();

    if (empty($form['name'])) {
      $errors['name'] = 'Field "Task Name" shouldn\'t be empty';
    }
    switch ($form['periods'])
    {
      case 'hourly':
        if (empty($form['hourly_hours'])) $errors['hourly_hours'] = 'Field "Run every _ hours" shouldn\'t be empty';
        if (empty($form['hourly_minutes'])) $errors['hourly_minutes'] = 'Field "Run In" shouldn\'t be empty';
        break;

      case 'daily':
        if (empty($form['daily_days'])) $errors['daily_days'] = 'You should choose at least one day of week';
        if (empty($form['daily_runtime'])) $errors['daily_runtime'] = 'Field "Start time" shouldn\'t be empty';
        if (!preg_match('/^[0-9]{1,2}:[0-9]{1,2}$/',$form['daily_runtime'])) $errors['daily_runtime'] = 'Field "Start time" should be in format 00:00';
        break;

      case 'weekly':
        if (empty($form['weekly_days'])) $errors['weekly_days'] = 'You should choose the day of week';
        if (empty($form['weekly_runtime'])) $errors['weekly_runtime'] = 'Field "Start time" shouldn\'t be empty';
        if (!preg_match('/^[0-9]{1,2}:[0-9]{1,2}$/',$form['weekly_runtime'])) $errors['weekly_runtime'] = 'Field "Start time" should be in format 00:00';
        break;

      case 'monthly':
        if (empty($form['monthly_days'])) $errors['monthly_days'] = 'You should choose the day of week';
        if (empty($form['monthly_runtime'])) $errors['monthly_runtime'] = 'Field "Start time" shouldn\'t be empty';
        if (!preg_match('/^[0-9]{1,2}:[0-9]{1,2}$/',$form['monthly_runtime'])) $errors['monthly_runtime'] = 'Field "Start time" should be in format 00:00';
        break;

      default:
        $errors['periods'] = 'Choose valid period for test running';
    }
    if ($form['sendEmail']) {
      $form['email'] = rtrim(trim($form['email']),';');

      if(empty($form['email'])) {
        $errors['email'] = 'Field "Email" shouldn\'t be empty';
      }
      else {
        $emails = explode(';',$form['email']);
        foreach ($emails as $email) {
          if (!Form::emailValidate(trim($email))) {
            $errors['email'] = 'Enter correct emails.';
            break;
          }
        }
      }
    }
    if (!isset($form['tests']) && !isset($form['groups'])) {
      $errors['assign-tests'] = 'At least one group or one test should be choosen.';
    }

    return $errors;
  }
}
?>
