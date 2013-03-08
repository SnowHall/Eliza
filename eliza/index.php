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

   error_reporting(E_ALL & ~E_NOTICE);
   ini_set('display_errors', '1');

  require_once 'init.php';

  try
  {
    $route = explode('/', $_GET['r']);

    $controller = $route[0] ? preg_replace('#[^a-zA-Z0-9]#','',$route[0]) : 'main';
    $action = $route[1] ? preg_replace('#[^a-zA-Z0-9]#','',$route[1]) : 'index';

    if (!file_exists(ROOT_PATH.'controllers/'.$controller.'.php'))
      throw new Exception('Page not found!', 404);

    require_once ROOT_PATH.'controllers/'.$controller.'.php';

    if (!$_GET['ajax']) {
      echo render('main',array('content'=>$content));
    }
 }
 catch (Exception $e)
 {
   $content = render('main/error',array('message'=>$e->getMessage(),'code'=>$e->getCode()));
   echo render('main',array('content'=>$content));
 }
?>
