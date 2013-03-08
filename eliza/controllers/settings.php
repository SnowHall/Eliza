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
      if (isset($_POST['ConfigForm']))
      {
        $newConfig = array();
        //Set default url
        $defaultUrl = isset($_POST['defaultUrl']) ? intval($_POST['defaultUrl']) : 0;
        $newConfig['urls'][] = $_POST['urls'][$defaultUrl];
        unset($_POST['urls'][$defaultUrl]);
        $newConfig['urls'] = array_merge($newConfig['urls'],$_POST['urls']);

        if (isset($_POST['ConfigForm']['admin_email'])) $newConfig['admin_email'] = $_POST['ConfigForm']['admin_email'];

        Settings::saveConfigFile($newConfig);
        flash('success','Settings successful changed!');
        redirect(url('settings/index'));
      }
      $content .= render('settings/index',array('config'=>$config));
      break;


     default:
       throw new Exception('Invalid request');
  }