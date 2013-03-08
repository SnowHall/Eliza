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

class Task
{
  public static function getTasks()
  {
    $tasks = array();

    if (file_exists(TASKS_FILE)) {
      $tasks = json_decode(file_get_contents(TASKS_FILE),true);
    }
    return $tasks;
  }

  public static function saveTask($form = '')
  {
    global $config;

    if ($form) {
      $task = array();
      $task['name'] = $form['name'];
      $task['executionType'] = $form['executionType'][0];
      $task['lastUpdate'] = $form['lastUpdate'] ? $form['lastUpdate'] : time();

      switch ($task['executionType'])
      {
        case 'period':
          // Delete minutes for run test in 00:00
          $lastUpdate = getdate($task['lastUpdate']);
          $task['lastUpdate'] = $form['lastUpdate'] ? $form['lastUpdate'] - $lastUpdate['minutes'] * 60 : $task['lastUpdate'] - $lastUpdate['minutes'] * 60;
          $task['period'] = $form['period'];
          break;

        case 'intime':
          $task['lastUpdate'] = $form['lastUpdate'] ? $form['lastUpdate'] : strtotime($_POST['Task']['runtime']);
          $task['periodType'] = $form['periodType'][0];
          $task['runtime'] = $form['runtime'];

          switch ($task['periodType'])
          {
            case 'manual':
              $task['intimePeriod'] = $form['intimePeriod'] * 24 * 3600;
              break;

            case 'daily':
              $task['intimePeriod'] = 24 * 3600;
              break;

            case 'weekly':
              $task['intimePeriod'] = 24 * 7 * 3600;
              break;

            default:
              $task['intimePeriod'] = 24 * 3600;
              break;
          }
          break;

        default:
          return false;
      }

      if ($form['sendEmail']) {
        $task['sendEmail'] = 1;
        $task['email'] = $form['email'];
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
      $config['tasks'][$id] = $task;
    }

    return self::saveAllTasks();
  }

  public static function saveAllTasks() {
    global $config;

    return file_put_contents(TASKS_FILE, json_encode($config['tasks']));
  }

  public static function taskValidate($form)
  {
    $errors = array();

    if (empty($form['name'])) {
      $errors['name'] = 'Field "Task Name" shouldn\'t be empty';
    }
    if (isset($form['executionType']) && ($form['executionType'][0] == 'period') && empty($form['period'])) {
      $errors['period'] = 'Field "Each hours" shouldn\'t be empty';
    }
    if ($form['sendEmail'] && empty($form['email'])) {
      $errors['email'] = 'Field "Start from" shouldn\'t be empty';
    }
    if ($form['sendEmail'] && !preg_match('/^[a-zA-Z0-9]+(?:\.[a-zA-Z0-9]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/',$form['email'])) {
      $errors['email'] = 'Enter correct email.';
    }
    if (isset($form['executionType']) && ($form['executionType'][0] == 'intime') && ($form['periodType'][0] == 'manual') && empty($form['intimePeriod'])) {
      $errors['intimePeriod'] = 'Field "Manual period" shouldn\'t be empty';
    }
    if (isset($form['runtime']) && !preg_match('/^[0-9]{1,2}:[0-9]{1,2}$/',$form['runtime'])) {
      $errors['runtime'] = 'Field "Run in" should be in format 00:00';
    }
    if (!isset($form['tests']) && !isset($form['groups'])) {
      $errors['assign-tests'] = 'At least one group or one test should be choosen.';
    }

    return $errors;
  }
}
?>
