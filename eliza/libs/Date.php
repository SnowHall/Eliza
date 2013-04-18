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

class Date {
  
  // Week days
  public static $days = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');

  /**
   * Return days of month 1 - 31
   *
   * @return array
   */
  public static function generateDaysList() {
    $days = array();
    for ($i = 1; $i <= 31; $i++) {
      $days[$i] = $i;
    }
    return $days;
  }

  /**
   * Return days of week Sun - Sat
   *
   * @return array
   */
  public static function getWeekDaysList() {
    $days = array();
    foreach (self::$days as $day) {
      $days[$day] = $day;
    }
    return $days;
  }

  /**
   * Return minutes of hour 00 - 59
   *
   * @return array
   */
  public static function generateMinutesList() {
    $minutes = array();
    for ($i = 0; $i <=59; $i++) {
      $minutes[$i] = ($i < 10) ? '0'.$i : $i;
    }
    return $minutes;
  }
}
