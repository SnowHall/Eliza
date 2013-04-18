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

class TestController extends Controller
{
  /**
   * Shows tests list
   */
  public function actionIndex() {
    $tests = Test::getTests();
    $queue = Test::getQueueTests();
    if ($queue) {
      foreach ($queue as $key=>$value) {
        $queue[$key] = $value['name'];
      }
    }
    echo $this->render('test/index',array(
      'tests'=>$tests,
      'queue'=>$queue
    ));
  }

  /**
   * Create new test
   */
  public function actionCreate() {
    if (isset($_POST['Test'])) {
      $form = $_POST['Test'];
      $errors = array();

      if (empty($form['test-name'])) {
        $errors['test-name'] = 'Field "Test Name" shouldn\'t be empty';
      }

      if (!$errors) {
        Test::saveTest($form['test-name'],$form['test-description']);
        $this->redirect('index.php');
      }
      else {
        echo $this->render('test/create',array(
          'errors'=>$errors
        ));
      }
    }
    else {
      echo $this->render('test/create');
    }
  }

  /**
   * Delete test
   */
  public function actionDelete() {
    if (isset($_GET['test'])) {
      $filename = TEST_PATH.preg_replace('#[^a-zA-Z0-9_]#', '', $_GET['test']).'.php';
      if (file_exists($filename)) {
        unlink($filename);
      }
      $this->redirect('index.php');
    }
  }

  /**
   * Show test info
   */
  public function actionView() {
    if (isset($_GET['test'])) {
      $name = preg_replace('#[^a-zA-Z0-9_]#', '', $_GET['test']);
      $filename = TEST_PATH.$name.'.php';
      if (file_exists($filename)) {
        $test['name'] = $name;
        $test['file'] = $name.'.php';
        $test['content'] = file_get_contents($filename);

        $geshi = new GeSHi($test['content'], 'php');
        $test['content'] = $geshi->parse_code();
      }
    }
    echo $this->render('test/view',array('test'=>$test));
  }

  /**
   * Execute test or tests group
   */
  public function actionRun() {
    if (!empty($_GET['test'])) {
      $testName = preg_replace('#[^a-zA-Z0-9_]#', '', $_GET['test']);
      $testUrl = isset($_GET['testUrl']) ? $_GET['testUrl'] : '';
      $debug = isset($_GET['debug']) ? (bool)$_GET['debug'] : null;
      $responseFormat = isset($_GET['responseFormat']) ? $_GET['responseFormat'] : 'html';
      Test::testExecute($testName, true, $debug, $responseFormat, $testUrl);
    }
    if (!empty($_GET['group'])) {
      $group = Group::get($_GET['group']);
      if(isset($group['tests'])) {
        foreach ($group['tests'] as $testName) {
          Eliza::app()->setResponse('Group "'.$group['name'].'" is running');
          Eliza::app()->setResponse('Test "'.$testName.'" is running');
          $debug = isset($_GET['debug']) ? (bool)$_GET['debug'] : null;
          $responseFormat = isset($_GET['responseFormat']) ? $_GET['responseFormat'] : 'html';
          Test::testExecute($testName, false, 0, 'json');
        }
        Eliza::app()->showResponse();
      }
    }
  }

  /**
   * Shows popup for choosing urls and adding test to execution queue
   */
  public function actionGetaddqueuepopup() {
    $urls = Config::getValue('urls',array());
    $test = isset($_POST['test']) ? $_POST['test'] : null;
    echo $this->render('test/_popup_queue_urls',array('urls'=>$urls,'test'=>$test));
    exit();
  }

  /**
   * Shows popup for show add queue message
   */
  public function actionAddqueue() {
    if ($_GET['test']) {
      Test::addTestToQueue($_GET['test'],$_POST['testUrl']);
      echo $this->render('test/_popup_queue');
      exit();
    }
  }

  /**
   * Run test in debug-mode
   *
   * @throws Exception page not found
   */
  public function actionDebug() {
    if (!$_GET['test']) throw new Exception('Page not found!');
    echo $this->render('test/debug',array('test'=>$_GET['test']));
  }

  /**
   * Shows popup for test running
   */
  public function actionGetrunpopup() {
    $urls = Config::getValue('urls',array());
    $test = isset($_POST['test']) ? $_POST['test'] : null;
    echo $this->render('test/_popup_run',array('urls'=>$urls,'test'=>$test));
    exit();
  }
}