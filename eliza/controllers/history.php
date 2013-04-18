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

class HistoryController extends Controller
{
  /**
   * Shows test execution history
   */
  public function actionIndex() {
    $history = History::getLog();
    echo $this->render('history/index',array('history'=>$history));
  }

  /**
   * Clear test execution history
   */
  public function actionClear() {
    History::clearLog();
    $this->redirect($this->url('history/index'));
  }

  /**
   * Expand test execution history for certain test
   */
  public function actionGetmore() {
    $id = intval($_POST['id']);
    $history = History::getLog();
    echo History::markResult(nl2br($history[$id]->procces));
  }
}