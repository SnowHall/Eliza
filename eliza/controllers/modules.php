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

class ModulesController extends Controller
{
  /*
   * Show modules list
   */
  public function actionIndex() {
      $modules = Module::getAvailableModules();
      if (isset($_POST['modules'])) {
        foreach ($modules as $key=>$options) {
          if (isset($_POST['modules'][$key]) && $_POST['modules'][$key] == 'on') {
            $modules[$key]['enabled'] = true;
          }
          else {
            $modules[$key]['enabled'] = false;
          }
        }
        Module::saveConfig($modules);
        Flash::set('success', 'Settings have been save');
        $this->redirect($this->url('settings/index'));
      }

      echo $this->render('modules/index',array(
        'modules'=>$modules,
      ));
      break;
  }
  /**
   * Save module's config
   */
  public function actionSaveconfig() {
    if (isset($_POST['Options']) && isset($_POST['moduleId']) && $_POST['moduleId']) {
      $moduleId = $_POST['moduleId'];
      $isLocal = (isset($_POST['isLocal']) && $_POST['isLocal']) ? true : false;
      Module::updateConfig($moduleId, $_POST['Options'], $isLocal);
      Flash::set('success', 'Settings for module "'.$moduleId.'" have been save');

      $this->redirect($this->url('settings/index'));
    }
  }

  /**
   * Shows module's config popup
   */
   public function actionGetconfigpopup() {
     $moduleId = isset($_POST['module']) ? $_POST['module'] : null;
      $moduleName = $moduleId.'Module';
      $model = new $moduleName;
      $options = $model->options();
      $module = Module::getModule($moduleId);
      $hasLocal = Module::hasLocalConfig();
      echo $this->render('modules/_popup_config',array(
        'options'=>$options,
        'module'=>$module,
        'moduleId'=>$moduleId,
        'hasLocal'=>$hasLocal
      ));
      exit();
   }
}