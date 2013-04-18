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

class errorModule extends Module
{
  // Errors stack
  private $errors;

  // Current HTML page
  private $page;

  /**
   * Represents user action: check errors existing on the current page
   * @return type
   */
  public function checkError()
  {
    if ($this->app->error) return;

    $this->clearErrors();
    $this->getErrors();
    $this->showErrors();
  }

  /**
   * Clear errors stack
   */
  private function clearErrors()
  {
    $this->errors = array();
  }

  /**
   * Find errors on the page
   */
  private function getErrors()
  {
    $test = AcceptanceTest::test();
    $this->page = $test->getCurrentPage();
    $this->checkPhpFatal();
    $this->checkSqlErrors();
    $this->checkPhpParseErrors();
    $this->checkPhpWarnings();
  }

  /**
   * Check SQL errors
   */
  private function checkSqlErrors()
  {
    preg_match_all('/You have an error in your SQL syntax(.+?)[on|at] line [0-9]+/is', $this->page, $errors);
    if (isset($errors[0]) && $errors[0]) {
      foreach ($errors[0] as $error) {
        $this->errors[] = $error;
      }
    }
  }

  /**
   * Check PHP warnings
   */
  private function checkPhpWarnings()
  {
    preg_match_all('/Warning:(.+?)[on|at] line [0-9]+/is', $this->page, $errors);
    if (isset($errors[0]) && $errors[0]) {
      foreach ($errors[0] as $error) {
        $this->errors[] = $error;
      }
    }
  }

  /**
   * Check PHP parse errors
   */
  private function checkPhpParseErrors()
  {
    preg_match_all('/Parse error:(.+?)[on|at] line [0-9]+/is', $this->page, $errors);
    if (isset($errors[0]) && $errors[0]) {
      foreach ($errors[0] as $error) {
        $this->errors[] = $error;
      }
    }
  }

  /**
   * Check PHP fatal errors
   */
  private function checkPhpFatal()
  {
    preg_match_all('/Fatal error:(.+?)[on|at] line [0-9]+/is', $this->page, $errors);
    if (isset($errors[0]) && $errors[0]) {
      foreach ($errors[0] as $error) {
        $this->errors[] = $error;
      }
    }
  }

  /**
   * Shows errors if exists
   * @return boolean
   */
  private function showErrors()
  {
    if (!$this->errors) {
      //$this->app->setResponse('Has no errors','success');
      return true;
    }

    foreach ($this->errors as $error) {
      $this->app->setResponse($error,'error');
    }
  }
}