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

  switch($action)
  {
    case 'index':
      $history = History::getLog();
      $content .= render('history/index',array('history'=>$history));
      break;

    case 'clear':
      History::clearLog();
      redirect(url('history/index'));
      break;

    case 'getmore':
      $id = intval($_POST['id']);
      $history = History::getLog();
      echo History::markResult(nl2br($history[$id]->procces));
      break;

     default:
       throw new Exception('Invalid request');
  }