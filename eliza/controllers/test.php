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
      $tests = Test::getTests();
      $queue = Test::getQueueTests();
      foreach ($queue as $key=>$value) {
        $queue[$key] = $value['name'];
      }
      $content .= render('test/index',array('tests'=>$tests,'queue'=>$queue,'config'=>$config));
      break;

    case 'create':
        if (isset($_POST['Test'])) {
          $form = $_POST['Test'];
          $errors = array();

          if (empty($form['test-name'])) {
            $errors['test-name'] = 'Field "Test Name" shouldn\'t be empty';
          }

          if (!$errors) {
            $save = Test::saveTest($form['test-name'],$form['test-description']);
            redirect('index.php');
          }
          else {
            $content .= render('test/create',array('errors'=>$errors));
          }
        }
        else {
          $content .= render('test/create');
        }
        break;

     case 'delete':
        if (isset($_GET['test'])) {
          $filename = TEST_PATH.preg_replace('#[^a-zA-Z0-9_]#', '', $_GET['test']).'.php';
          if (file_exists($filename)) {
            unlink($filename);
          }
          redirect('index.php');
        }
        break;

     case 'view':
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
       $content .= render('test/view',array('test'=>$test));
       break;

     case 'run':
       if ($_GET['test']) {
         $testName = preg_replace('#[^a-zA-Z0-9_]#', '', $_GET['test']);
         $testUrl = isset($_GET['testUrl']) ? $_GET['testUrl'] : '';
         $debug = isset($_GET['debug']) ? (bool)$_GET['debug'] : null;
         $responseFormat = isset($_GET['responseFormat']) ? $_GET['responseFormat'] : 'html';
         Test::testExecute($testName, true, $debug, $responseFormat, $testUrl);
       }
       if ($_GET['group']) {
         if(isset($config['groups'][$_GET['group']]['tests'])) {
           foreach ($config['groups'][$_GET['group']]['tests'] as $testName) {
             echo 'Test "'.$testName.'" is running<br>';
             Test::testExecute($testName);
           }
         }
       }
       break;

     case 'addqueue':
       if ($_GET['test']) {
         Test::addTestToQueue($_GET['test'],$_POST['testUrl']);
         echo render('test/_popup_queue');
         exit();
       }
       break;

     case 'debug':
       if ($_GET['test']) {
         $content .= render('test/debug',array('test'=>$_GET['test']));
       }
       else {
         throw new Exception('Page not found!');
       }
       break;

     case 'getrunpopup':
       $urls = $config['urls'];
       $test = isset($_POST['test']) ? $_POST['test'] : null;
       echo render('test/_popup_run',array('urls'=>$urls,'test'=>$test));
       exit();
       break;

     case 'getaddqueuepopup':
       $urls = $config['urls'];
       $test = isset($_POST['test']) ? $_POST['test'] : null;
       echo render('test/_popup_queue_urls',array('urls'=>$urls,'test'=>$test));
       exit();
       break;

     default:
       throw new Exception('Invalid request');
  }