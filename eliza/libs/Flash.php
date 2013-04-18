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

/**
 * Flash messages. Save and show instant messages only once time.
 */
class Flash
{
  /**
   * Set flash message - save as SESSION variable
   *
   * @param string $type Message Id. Message may be got by Id
   * @param string $message
   */
  public static function set($type, $message) {
    if(!isset($_SESSION['flash'])) {
      $_SESSION['flash'] = array();
    }
    $_SESSION['flash'][$type] = $message;
  }

  /**
   * Get flash message from SESSION array by Id
   *
   * @param string $type Message Id
   * @return string Flash message text
   */
  public static function get($type) {
    if(isset($_SESSION['flash'])) {
      $message = $_SESSION['flash'][$type];
      self::unsetFlash();
      return $message;
    }
    return null;
  }

  /**
   * Unset flash message
   */
  public static function unsetFlash() {
    if(isset($_SESSION['flash'])) {
        $_SESSION['flash'] = null;
    }
  }
}