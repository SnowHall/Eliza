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

  $cronTmpFile = dirname(__FILE__).'/_cron_run.tmp';
  if (file_exists($cronTmpFile)) {
    // file contain time last cron execution
    $time = preg_replace('/[^0-9]/','',file_get_contents($cronTmpFile));

    //wait 15 minutes until delete temp file if php script is crushed
    if ((time() - $time) < 60 * 15) exit();

    unlink($cronTmpFile);
  }
  else {
    file_put_contents($cronTmpFile, time());
  }

  require_once dirname(__FILE__).'/init.php';

  /* Run tests from queue */
  $tests = Test::getQueueTests();

  if($tests) {
    foreach($tests as $test) {
      Test::testExecute($test['name'], false, false, 'html', $test['url']);
    }
    fopen(QUEUE_FILE, "w");
  }

  /* Run tasks */
  $tasks = Task::getTasks();
  $groups = Group::getGroups();

  if ($tasks) {

    foreach($tasks as $key=>$task) {
      if ($task['executionType'] == 'period') {
        $lastUpdateInfo = getdate($task['lastUpdate']);

        $expectedTime = $task['lastUpdate'] + $task['period'] * 3600 - $lastUpdateInfo['minutes'];
      }
      else if ($task['executionType'] == 'intime') {
        //find hours, minutes, days NOW
        $nowDateInfo = getdate();

        //find days from lastUpdate
        $lastUpdateInfo = getdate($task['lastUpdate']);

        //find hours, minutes from task
        list($runHours,$runMinutes) = explode(':',$task['runtime']);

        // Calculate expected time of test execution
        if ($lastUpdateInfo['hours'] < $runHours) {
          $expectedTime = $task['lastUpdate'] + $task['intimePeriod'] + ($runHours - $lastUpdateInfo['hours']) * 3600 - $lastUpdateInfo['minutes'] * 60 + $runMinutes * 60;
        }
        else {
          $expectedTime = $task['lastUpdate'] + $task['intimePeriod'] - ($lastUpdateInfo['hours'] - $runHours) * 3600 - $lastUpdateInfo['minutes'] * 60 + $runMinutes * 60;
        }

        // For daily tasks we can shift time execution on a day earlier if expected time hours is meet twice
        if (($task['intimePeriod'] == '86400') && ($expectedTime - $task['intimePeriod'] > $task['lastUpdate'])) {
          $expectedTime -= $task['intimePeriod'];
        }
      }

      if (time() < $expectedTime) continue;

      if (isset($task['tests'])) {
        foreach ($task['tests'] as $testName) {
          Test::testExecute($testName, false);

          if ($task['sendEmail']) {
            $test = Eliza::test(true);
            $message = 'Task: '.$task['name'].PHP_EOL.'Test: '.$testName.PHP_EOL.'Execution date: '.date('j M Y, H:i',time())."\n\n".
              'Test Log:'.PHP_EOL.strip_tags(br2nl($test->response));
            $subject = '['.$task['name'].']'.' Test "'.$testName.'" ';
            $subject .= ($test->error) ? 'has been Failed.' : 'has been Successfully executed.';
            mail($task['email'], $subject, $message);
          }
        }
        $config['tasks'][$key]['lastUpdate'] = time();
      }
      if (isset($task['groups'])) {
        foreach ($task['groups'] as $groupId) {
          $group = $groups[$groupId];
          foreach ($group['tests'] as $testName) {
            Test::testExecute($testName, false);

            if ($task['sendEmail']) {
              $test = Eliza::test(true);
              $message = 'Task: '.$task['name'].PHP_EOL.'Test: '.$testName.PHP_EOL.'Execution date: '.date('j M Y, H:i',time())."\n\n".
              strip_tags(br2nl($test->response));
              $subject = '['.$task['name'].']'.' Test "'.$testName.'" ';
              $subject .= ($test->error) ? 'has been Failed.' : 'has been Successfully executed.';
              mail($task['email'], $subject, $message);
            }
          }
        }
        $config['tasks'][$key]['lastUpdate'] = time();
      }
    }
    Task::saveAllTasks();
  }

  // delete tmp cron file if exists
  if (file_exists($cronTmpFile)) unlink($cronTmpFile);

  echo 'Finish';

  return true;