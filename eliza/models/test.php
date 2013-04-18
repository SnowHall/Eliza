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
    $tests = self::getTests();
    $group = Group::get($groupId);
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
    if (!$name) return false;
    $name = self::resolveName($name);
    $description = $description ? preg_replace('#[^a-zA-Z0-9_\s]#','',$description) :
      Config::getValue('default_description');
    $template = Config::getValue('test_template');

    $testContent = str_ireplace(array('{name}','{description}','{n}'),array($name,$description,"\r\n"),$template);

    file_put_contents(TEST_PATH.$name.'.php', $testContent);
  }

  public static function testExecute($name, $show = true, $debug = false, $responseFormat = 'html', $testUrl = '')
  {
    $options = array(
      'debug' => $debug,
      'responseFormat' => $responseFormat,
      'testUrl' => $testUrl,
      'error' => false,
    );
    $app = Eliza::app($options);

    Counter::start("executionTime");
    if (file_exists(TEST_PATH.$name.'.php')) {
      $testFilePath = TEST_PATH.$name.'.php';
      require_once $testFilePath;
      $testType = self::getTestType($testFilePath);
      if ($testType == 'unit') {
        $unitClass = self::getUnitClassName($testFilePath);
        $unitTest = new UnitTest($unitClass);
        $ref = new ReflectionClass($unitClass);
        $methods = $ref->getMethods();
        // Remove nested methods
        foreach ($methods as $key=>$method) {
          if ($method->class != $unitClass) unset($methods[$key]);
        }
        $unitTest->invoke($methods);
      }
    }
    else {
      $app->setResponse('Test "'.$name.'" not exists!','error');
    }
    $app->executionTime = Counter::end("executionTime");
    $app->setResponse('<br />');

    if (!$debug && Config::getValue('history_store_time') !== '0') {
      $app->logger($name);
    }
    if ($show) {
      $app->showResponse();
      $app->clearResponse();
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
    return Helpers::getFromJson(QUEUE_FILE);
  }

  public static function addTestToQueue($name, $testUrl)
  {
    $tests = self::getQueueTests();
    if (empty($tests) || !in_array($name, $tests)) {
      $tests[] = array(
        'name' => $name,
        'url' => $testUrl
      );
    }
    return file_put_contents(QUEUE_FILE, json_encode($tests));
  }

  public static function getTestType($testPath) {
    $test = file_get_contents($testPath);
    if (strpos($test, 'UnitTest')) {
      return 'unit';
    }
    else if (preg_match('/new\sAcceptance\(\)/is', $test)) {
      return 'acceptance';
    }
    else return 'undefined';
  }

  public static function getUnitClassName($testPath) {
    $test = file_get_contents($testPath);
    preg_match('/class[^a-zA-Z]*([a-zA-Z]*)[^a-zA-Z]*extends /is', $test, $matches);
    if (isset($matches[1])) return $matches[1];
    return false;
  }

  public static function getTestsCheckboxList($tests) {
    $list = array();
    foreach($tests as $test) {
      $list[$test] = $test;
    }
    return $list;
  }

  public static function resolveName($name) {
    // change tesn name form "test new name" form to "testNewName" form
    $parts = explode(' ', $name);
    if (count($parts) > 1) {
      $name = implode('', array_map('ucfirst', $parts));
    }
    // clear name
    $name = preg_replace('#[^a-zA-Z0-9_]#','',$name);
    return $name;
  }
}