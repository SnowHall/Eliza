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

  $cronTmpFile = dirname(__FILE__).'/data/_cron_run.tmp';
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

  $app = Eliza::app();

  /* Run tests from queue */
  $tests = Test::getQueueTests();

  if($tests) {
    foreach($tests as $test) {
      Test::testExecute($test['name'], false, false, 'html', $test['url']);
    }
    fopen(QUEUE_FILE, "w");
  }

  /* Run tasks */
  $tasks = Task::getAll();
  $groups = Group::getAll();

  $currentDate = new DateTime();

  if ($tasks) {
    foreach($tasks as $key=>$task) {
      // Find next running time
      $startTime = $task['lastUpdate'] != 'not run' ? $task['lastUpdate'] : $task['created'];

      // Set current timezone
      $startDate = new DateTime($startTime);

      switch($task['periods'])
      {
        /*
         * Hourly running
         *
         * 1. Add to last update (or created) day task's number of hours
         * 2. Set task's minutes
        */
        case 'hourly':
          $expectedTime = $startDate->modify('+ '.intval($task['hourly_hours']).' hours')->
            setTime($startDate->format('H'),$task['hourly_minutes']);
          break;

        /**
         * Daily running
         *
         * 1. Add a day to last update (or created) day while not find expected week day
         * 2. Set task's hours and task's minutes
         */
        case 'daily':
          $weekDays = unserialize($task['daily_days']);
          if (empty($weekDays)) break;

          do {
            $expectedTime = $startDate->modify('+ 1 day');
          }
          while (!in_array($expectedTime->format('D'), $weekDays));

          list($hours, $minutes) = explode(':', $task['daily_runtime']);
          $expectedTime->setTime($hours, $minutes);
          break;

        /**
         * Weekly running
         *
         * 1. Find day on next week after last update (or created)
         * 2. Set task's hours and task's minutes
         */
        case 'weekly':
          if (empty($task['weekly_days'])) break;
          $expectedTime = $startDate->modify('next '.$task['weekly_days']);
          list($hours, $minutes) = explode(':', $task['weekly_runtime']);
          $expectedTime->setTime($hours, $minutes);
          break;

        /**
         * Monthly running
         *
         * 1. Find next month after last update (or created)
         * 2. Set task's day of month
         * 3. Set task's hours and task's minutes
         */
        case 'monthly':
          if (empty($task['monthly_days'])) break;
          $expectedTime = $startDate->modify('+ 1 month');
          $expectedTime->setDate($startDate->format('Y'), $startDate->format('m'), $task['monthly_days']);
          list($hours, $minutes) = explode(':', $task['monthly_runtime']);
          $expectedTime->setTime($hours, $minutes);
      }

      if (!$expectedTime || $currentDate < $expectedTime) continue;

      if (isset($task['tests'])) {
        foreach ($task['tests'] as $testName) {
          Test::testExecute($testName, false);

          if ($task['sendEmail'] && trim($task['email'])) {
            $test = Eliza::app();
            $message = 'Task: '.$task['name'].PHP_EOL.'Test: '.$testName.PHP_EOL.'Execution date: '.date('j M Y, H:i',time())."\n\n".
              'Test Log:'.PHP_EOL.strip_tags(Helpers::br2nl($test->response));
            $subject = '['.$task['name'].']'.' Test "'.$testName.'" ';
            $subject .= ($test->error) ? 'has been Failed.' : 'has been Successfully executed.';

            $emails = explode(';',$task['email']);
            foreach ($emails as $email) {
              mail(trim($email), $subject, $message);
            }
          }
        }
      }
      if (isset($task['groups'])) {
        foreach ($task['groups'] as $groupId) {
          $group = $groups[$groupId];
          foreach ($group['tests'] as $testName) {
            Test::testExecute($testName, false);

						if ($task['sendEmail'] && trim($task['email'])) {
							$test = Eliza::app();
							$message = 'Task: '.$task['name'].PHP_EOL.'Test: '.$testName.PHP_EOL.'Execution date: '.date('j M Y, H:i',time())."\n\n".
								'Test Log:'.PHP_EOL.strip_tags(Helpers::br2nl($test->response));
							$subject = '['.$task['name'].']'.' Test "'.$testName.'" ';
							$subject .= ($test->error) ? 'has been Failed.' : 'has been Successfully executed.';

							$emails = explode(';',$task['email']);
							foreach ($emails as $email) {
								mail(trim($email), $subject, $message);
							}
						}
          }
        }
      }
			Task::updateField($key, 'lastUpdate', $startDate->date);
    }
  }

  // delete tmp cron file if exists
  if (file_exists($cronTmpFile)) unlink($cronTmpFile);

  echo 'Finish';

  return true;