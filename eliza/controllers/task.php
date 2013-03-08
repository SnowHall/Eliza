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
      $tasks = $config['tasks'] ? $config['tasks'] : Task::getTasks();
      $groups = Group::getGroups();
      $content .= render('task/index',array(
        'tasks'=>$tasks,
        'groups'=>$groups,
      ));
      break;

    case 'create':
        $errors = array();
        if (isset($_POST['Task'])) {
          $form = $_POST['Task'];
          $errors = Task::taskValidate($form);

          if (!$errors) {
            Task::saveTask($form);
            redirect(url('task/index'));
          }
        }
        $tests = Test::getTests();
        $groups = Group::getGroups();
        $content .= render('task/create',array(
          'tests'=>$tests,
          'groups'=>$groups,
          'errors'=>$errors,
          'config'=>$config
        ));
        break;

     case 'edit':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $errors = array();
        if (isset($_POST['Task'])) {
          $form = $_POST['Task'];
          $errors = Task::taskValidate($form);
          if (!$errors) {
            Task::saveTask($form);
            redirect(url('task/index'));
          }
        }
        $tests = Test::getTests();
        $groups = Group::getGroups();
        $task = $config['tasks'][$id];
        /*switch($task['intimePeriod'])
        {
          case '86400':
            $task['intimePeriod'] = 'daily';
            break;

          case '604800':
            $task['intimePeriod'] = 'weekly';
            break;

          default:*/
            $task['intimePeriod'] = round($task['intimePeriod'] / (60 * 60 * 24));
            /*break;
        }*/

        $content .= render('task/update',array(
          'tests'=>$tests,
          'groups'=>$groups,
          'errors'=>$errors,
          'config'=>$config,
          'task'=>$task,
          'taskId' => $id,
        ));
        break;

     case 'delete':
        if (isset($_GET['id'])) {
          unset($config['tasks'][$_GET['id']]);
          Task::saveAllTasks();
          redirect(url('task/index'));
        }
        break;

     case 'view':
       if (isset($_GET['id'])) {
         $task = $config['tasks'][$_GET['id']];
       }
       $content .= render('task/view',array('task'=>$task,'id'=>$_GET['id']));
       break;

     /*case 'addtest':
       if (isset($_POST['group']) && isset($config['groups'][$_POST['group']])) {
          $config['groups'][$_POST['group']]['tests'][] = $_POST['test-name'];
          saveGroup();
          redirect(url('group/index'));
       }
       break;

     case 'getaddpopup':
       $tests = getTestsList($_POST['group']);
       echo render('group/_popup',array('tests'=>$tests));
       exit();*/

     case 'removetest':
       if (isset($_POST['task'], $_POST['test'])) {
         $taskTests = $config['tasks'][$_POST['task']]['tests'];

         if (in_array($_POST['test'], $taskTests)) {
           $key = array_search($_POST['test'], $taskTests);
           unset($config['tasks'][$_POST['task']]['tests'][$key]);
           Task::saveAllTasks();
         }
       }
       exit();

     default:
       throw new Exception('Invalid request');
  }