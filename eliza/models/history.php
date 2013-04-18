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

      if ($history) {
        $history = self::checkStoreTime($history);
        self::$history = array_reverse($history);
      }
    }
    else {
      fopen(LOG_FILE,'w');
    }

    return self::$history;
  }

  public static function clearLog()
  {
    fopen(LOG_FILE, "w");
    return true;
  }

  public static function markResult($text)
  {
    return str_replace(array('OK.','Failed.'), array('<span class="result success">OK.</span>','<span class="result error">Failed.</span>'), $text);
  }

  /**
   * Remove old history
   * @param type $history Log array sorted by date ASC
   */
  public static function checkStoreTime($history) {
    $storeTime = Config::getValue('history_store_time','');

    // If check - save all history OR not store history
    if ($storeTime === '' || $storeTime === '0' || !is_numeric($storeTime)) return $history;

    $requirePeriod = time() - $storeTime * 24 * 60 * 60;

    foreach ($history as $key=>$logRow) {
      if (strtotime($logRow->date) < $requirePeriod) {
        unset($history[$key]);
      }
    }

    self::saveHistory($history);

    return $history;
  }

  public static function saveHistory($history) {
    if (!$history) return;
    $logFile = fopen(LOG_FILE,'w');
    foreach ($history as $logRow) {
      fwrite($logFile, json_encode($logRow) . PHP_EOL);
    }
    fclose($logFile);
  }
}

?>
