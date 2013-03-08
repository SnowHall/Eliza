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

class Test
{
  private static $tests;

  public static function getTests()
  {
    if (self::$tests) return self::$tests;

    self::$tests = scandir(TEST_PATH);

    foreach (self::$tests as $key=>$file) {
      if ($file == '.' || $file == '..' || !fnmatch('*.php', $file) || fnmatch('*-local.php', $file)) {
        unset(self::$tests[$key]);
        continue;
      }
      self::$tests[$key] = str_replace('.php', '', $file);
    }

    return self::$tests;
  }

  public static function getTestsList($groupId)
  {
    global $config;
    $tests = self::getTests();
    $group = $config['groups'][$groupId];
    $testsList = array();

    foreach($tests as $test) {
      if (!isset($group['tests'])) return $tests;
      else if (!in_array($test, $group['tests'],true)) {
        $testsList[] = $test;
      }
    }

    return $testsList;
  }

  public static function saveTest($name, $description)
  {
    global $config;
    if (!$name) return false;
    $name = preg_replace('#[^a-zA-Z0-9_]#','',$name);
    $description = $description ? preg_replace('#[^a-zA-Z0-9_\s]#','',$description) : $config['default_description'];
    $template = $config['test_template'];

    $testContent = str_ireplace(array('{name}','{description}','{n}'),array($name,$description,"\r\n"),$template);

    file_put_contents(TEST_PATH.$name.'.php', $testContent);
  }

  public static function testExecute($name, $show = true, $debug = false, $responseFormat = 'html', $testUrl = '')
  {
    global $config;

    $options = array(
      'debug' => $debug,
      'responseFormat' => $responseFormat,
      'testUrl' => $testUrl,
    );
    $test = Eliza::test(false, $options);

    Counter::start("executionTime");
    if (file_exists(TEST_PATH.$name.'.php')) {
      require_once TEST_PATH.$name.'.php';
    }
    $test->executionTime = Counter::end("executionTime");

    if ($config['log'] && !$debug) {
      $test->logger($name);
    }
    if ($show) {
      $test->showResponse();
    }
  }

  public static function includeTest($name)
  {
    if (file_exists(TEST_PATH.$name.'.php')) {
      include TEST_PATH.$name.'.php';
      return true;
    }
    return false;
  }

  public static function getQueueTests()
  {
    $tests = array();
    if (file_exists(QUEUE_FILE)) {
      $tests = json_decode(file_get_contents(QUEUE_FILE),true);
    }
    return $tests ? $tests : array();
  }

  public static function addTestToQueue($name, $testUrl)
  {
    $tests = self::getQueueTests();
    if (!in_array($name,$tests)) {
      $tests[] = array(
        'name' => $name,
        'url' => $testUrl
      );
    }
    return file_put_contents(QUEUE_FILE, json_encode($tests));
  }
}
?>
