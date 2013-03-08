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
      if (isset($config['index_page']) && (trim($config['index_page'],'/') != 'main/index')) {
        redirect(url($config['index_page']));
      }
      break;

    case 'help':
      $content .= render('main/help');
      break;

    case 'reports':
      $weekReport = Report::getReportData('week');
      $monthReport = Report::getReportData('month');
      $content .= render('main/reports',array('weekReport'=>$weekReport, 'monthReport'=>$monthReport));
      break;

    case 'commands':
      $content .= render('main/commands');
      break;

     default:
       throw new Exception('Invalid request');
  }