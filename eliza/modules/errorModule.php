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

class errorModule extends Module
{
  private $errors;
  private $page;

  public function checkError()
  {
    if ($this->test->error) return;

    $this->clearErrors();
    $this->getErrors();
    $this->showErrors();
  }

  private function clearErrors()
  {
    $this->errors = array();
  }

  private function getErrors()
  {
    $this->page = $this->test->getCurrentPage();
    $this->checkPhpFatal();
    $this->checkSqlErrors();
    $this->checkPhpParseErrors();
    $this->checkPhpWarnings();
  }

  private function checkSqlErrors()
  {
    preg_match_all('/You have an error in your SQL syntax(.+?)[on|at] line [0-9]+/is', $this->page, $errors);
    if (isset($errors[0]) && $errors[0]) {
      foreach ($errors[0] as $error) {
        $this->errors[] = $error;
      }
    }
  }

  private function checkPhpWarnings()
  {
    preg_match_all('/Warning:(.+?)[on|at] line [0-9]+/is', $this->page, $errors);
    if (isset($errors[0]) && $errors[0]) {
      foreach ($errors[0] as $error) {
        $this->errors[] = $error;
      }
    }
  }

  private function checkPhpParseErrors()
  {
    preg_match_all('/Parse error:(.+?)[on|at] line [0-9]+/is', $this->page, $errors);
    if (isset($errors[0]) && $errors[0]) {
      foreach ($errors[0] as $error) {
        $this->errors[] = $error;
      }
    }
  }

  private function checkPhpFatal()
  {
    preg_match_all('/Fatal error:(.+?)[on|at] line [0-9]+/is', $this->page, $errors);
    if (isset($errors[0]) && $errors[0]) {
      foreach ($errors[0] as $error) {
        $this->errors[] = $error;
      }
    }
  }

  private function showErrors()
  {
    if (!$this->errors) return;

    foreach ($this->errors as $error) {
      $this->test->setResponse($error,'error');
    }
  }
}