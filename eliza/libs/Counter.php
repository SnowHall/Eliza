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

class Counter
{
  const COUNTER_LOG_FILE = 'counters.log';

  private static $countersStack;
  private static $countersEnded;

  public static function start($name = '') {
    if (!$name) $name = substr(md5(microtime(true)),'5');
    self::$countersStack[$name] = microtime(true);
  }

  public static function end($name = '') {
    return self::$countersEnded[$name] = microtime(true) - array_pop(self::$countersStack);
  }

  public static function saveCounters() {
    $fp = fopen(dirname(__FILE__).'/../data/'.self::COUNTER_LOG_FILE,'a');
    foreach (self::$countersEnded as $key=>$counter) {
      fputs($fp, date('Y-m-d G:i:s').' - '.$_SERVER['REQUEST_URI'].PHP_EOL.
        'Counter "'.$key.'": '.round($counter,6).'s'.PHP_EOL
      );
    }
  }
}

?>
