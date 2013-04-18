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

class IndexController extends Controller
{
  /**
   * Shows test lists on the index page
   */
  public function actionIndex() {
    $tests = Test::getTests();
    echo $this->render('index',array('tests'=>$tests));
  }
}