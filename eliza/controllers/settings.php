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

class SettingsController extends Controller
{
  /**
   * Shows settings form
   */
  public function actionIndex() {
    if (isset($_POST['ConfigForm'])) {
      $newConfig = array();
      //Set default url
      $defaultUrl = isset($_POST['defaultUrl']) ? intval($_POST['defaultUrl']) : 0;
      $newConfig['urls'][] = $_POST['urls'][$defaultUrl];
      unset($_POST['urls'][$defaultUrl]);
      $newConfig['urls'] = array_merge($newConfig['urls'],$_POST['urls']);

      if (isset($_POST['ConfigForm']['admin_email'])) $newConfig['admin_email'] = $_POST['ConfigForm']['admin_email'];
      if (isset($_POST['ConfigForm']['timezone'])) $newConfig['timezone'] = $_POST['ConfigForm']['timezone'];
      if (isset($_POST['ConfigForm']['history_store_time'])) $newConfig['history_store_time'] = $_POST['ConfigForm']['history_store_time'];

      Config::save($newConfig);
      Flash::set('success','Settings successful changed!');
      $this->redirect($this->url('settings/index'));
    }
    $config = Config::get();
    $timezones = Timezone::getTimezoneList();
    echo $this->render('settings/index',array('config'=>$config,'timezones'=>$timezones));
  }
}