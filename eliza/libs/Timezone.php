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

class Timezone {

  // List of timezones keys - timezone name, values - timezone Greenwich offset
  public static $zonelist = array(
    'Kwajalein' => -12.00,
    'Pacific/Midway' => -11.00,
    'Pacific/Honolulu' => -10.00,
    'America/Anchorage' => -9.00,
    'America/Los_Angeles' => -8.00,
    'America/Denver' => -7.00,
    'America/Tegucigalpa' => -6.00,
    'America/New_York' => -5.00,
    'America/Caracas' => -4.30,
    'America/Halifax' => -4.00,
    'America/St_Johns' => -3.30,
    'America/Argentina/Buenos_Aires' => -3.00,
    'America/Sao_Paulo' => -3.00,
    'Atlantic/South_Georgia' => -2.00,
    'Atlantic/Azores' => -1.00,
    'Europe/Dublin' => 0,
    'Europe/Belgrade' => 1.00,
    'Europe/Minsk' => 2.00,
    'Asia/Kuwait' => 3.00,
    'Asia/Tehran' => 3.30,
    'Asia/Muscat' => 4.00,
    'Asia/Yekaterinburg' => 5.00,
    'Asia/Kolkata' => 5.30,
    'Asia/Katmandu' => 5.45,
    'Asia/Dhaka' => 6.00,
    'Asia/Rangoon' => 6.30,
    'Asia/Krasnoyarsk' => 7.00,
    'Asia/Brunei' => 8.00,
    'Asia/Seoul' => 9.00,
    'Australia/Darwin' => 9.30,
    'Australia/Canberra' => 10.00,
    'Asia/Magadan' => 11.00,
    'Pacific/Fiji' => 12.00,
    'Pacific/Tongatapu' => 13.00
  );

  /**
   * Set application default timezone by its Greenwich offset
   *
   * @param int $offset Timezone Greenwich offset
   * @return boolean
   */
  public static function setTimezoneByOffset($offset = 0) {
    $timezone = self::getTimezonByOffset($offset);
    $currentDate = new DateTime();
    $DateTimeZone = timezone_open($timezone);
    date_timezone_set($currentDate, $DateTimeZone);
    date_default_timezone_set($timezone);
    return true;
  }

  /**
   * Return list of timezones for Dropdown list
   *
   * @return array All timezones list
   */
  public static function getTimezoneList() {
    $zones = array();
    foreach (self::$zonelist as $offset) {
      $zones[$offset] =  gmdate("l, F j, Y - H:i", time() + $offset * 3600);
    }
    return $zones;
  }

  /**
   * Get timezone name by Greenwich offset
   *
   * @param int $offset Timezone Greenwich offset
   * @return string Timezone name
   */
  public static function getTimezonByOffset($offset) {
    $index = array_keys(self::$zonelist, $offset);
    return $index[0];
  }
}