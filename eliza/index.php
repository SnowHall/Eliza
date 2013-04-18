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

  error_reporting(E_ALL & ~E_NOTICE);
  ini_set('display_errors', '1');

  require_once dirname(__FILE__).'/init.php';

  try
  {
    $app = Eliza::app();
    $app->runController();

		if (Config::getValue('install')) {
			$app->controller->redirect('install.php');
		}

    if (empty($_GET['ajax'])) {
       echo $app->controller->render('main',array('content'=>$app->controller->getContent()));
    }
    else {
      echo $app->controller->getContent();
    }
 }
 catch (Exception $e)
 {
   // return errors for ajax JSON responses
   if ($_GET['responseFormat'] == 'json') {
      echo json_encode(array('error'=>true,'response'=>$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine()));
      exit();
   }
   echo Controller::renderError('main/error',array('message'=>$e->getMessage(),'code'=>$e->getCode()));
 }