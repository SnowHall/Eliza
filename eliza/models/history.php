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

class History
{
  public static $history = array();

  public static function getLog() {
    if (self::$history) return self::$history;

    if (file_exists(LOG_FILE)) {
      $logFile = fopen(LOG_FILE,'r');
      $history = array();

      while(!feof($logFile)) {
        $string = fgets($logFile);
        if ($string) $history[] = json_decode($string);
      }

      self::$history = array_reverse($history);
    }
    else {
      $logFile = fopen(LOG_FILE,'w');
    }

    return self::$history;
  }

  public static function clearLog()
  {
    $logFile = fopen(LOG_FILE, "w");
    return true;
  }

  public static function markResult($text)
  {
    $text = str_replace(array('OK.','Failed.'), array('<span class="result success">OK.</span>','<span class="result error">Failed.</span>'), $text);

    return $text;
  }
}

?>
