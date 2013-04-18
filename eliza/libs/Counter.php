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

class Counter
{
  // Counters log file
  const COUNTER_LOG_FILE = 'counters.log';

  // Current executed counters
  private static $countersStack;

  // Ended counters
  private static $countersEnded;

  /**
   * Start counter with specified name
   *
   * @param string $name Counter name
   */
  public static function start($name = '') {
    if (!$name) $name = substr(md5(microtime(true)),'5');
    self::$countersStack[$name] = microtime(true);
  }

  /**
   * End specified counter execution
   *
   * @param string $name Counter name
   * @return int Counter execution time
   */
  public static function end($name = '') {
    return self::$countersEnded[$name] = microtime(true) - array_pop(self::$countersStack);
  }

  /**
   * Save counters in the log file
   */
  public static function saveCounters() {
    $fp = fopen(dirname(__FILE__).'/../data/'.self::COUNTER_LOG_FILE,'a');
    foreach (self::$countersEnded as $key=>$counter) {
      fputs($fp, date('Y-m-d G:i:s').' - '.$_SERVER['REQUEST_URI'].PHP_EOL.
        'Counter "'.$key.'": '.round($counter,6).'s'.PHP_EOL
      );
    }
  }
}