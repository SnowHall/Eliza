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

class MainController extends Controller
{
  /**
   * Shows index site page
   */
  public function actionIndex() {
    $indexPage = Config::get('index_page');
    if ($indexPage && (trim($indexPage,'/') != 'main/index')) {
      $this->redirect($this->url($indexPage));
    }
  }

  /**
   * Shows Eliza help page
   */
  public function actionHelp() {
    echo $this->render('main/help');
  }

  /**
   * Shows test execution reports graphs
   */
  public function actionReports() {
    $weekReport = Report::getReportData('week');
    $monthReport = Report::getReportData('month');
    echo $this->render('main/reports',array('weekReport'=>$weekReport, 'monthReport'=>$monthReport));
  }

  /**
   * Shows Eliza commands help
   */
  public function actionCommands() {
    echo $this->render('main/commands');
  }

  /**
   * Show Eliza mudules methods help
   */
  public function actionModules() {
      $moduleId = isset($_GET['module']) ? intval($_GET['module']) : null;
      $modules = Help::getModulesInfo();
      $module = null;

      if ($moduleId) {
        $module = Help::getModuleInfoById($moduleId);
        $methods = Help::getMethodsInfoByModule($moduleId);
      }
      else {
        $methods = Help::getMethodsInfo();
      }

      echo $this->render('main/modules',array(
        'modules'=>$modules,
        'methods'=>$methods,
        'module'=>$module,
      ));
  }

  /**
   * Shows Gpl lisense text
   */
  public function actionGpl() {
    echo $this->render('main/gpl');
  }
}