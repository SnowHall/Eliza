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
      $groups = $config['groups'] ? $config['groups'] : Group::getGroups();
      $content .= render('group/index',array('groups'=>$groups));
      break;

    case 'create':
        if (isset($_POST['Group'])) {
          $form = $_POST['Group'];
          $errors = array();

          if (empty($form['name'])) {
            $errors['name'] = 'Field "Test Name" shouldn\'t be empty';
          }

          if (!$errors) {
            Group::saveGroup($form['name']);
            redirect(url('group/index'));
          }
          else {
            $content .= render('group/create',array('errors'=>$errors));
          }
        }
        else {
          $content .= render('group/create');
        }
        break;

     case 'delete':
        if (isset($_GET['id'])) {
          unset($config['groups'][$_GET['id']]);
          Group::saveGroup();
          redirect(url('group/index'));
        }
        break;

     case 'view':
       if (isset($_GET['id'])) {
         $group = $config['groups'][$_GET['id']];
       }
       $content .= render('group/view',array('group'=>$group,'id'=>$_GET['id']));
       break;

     case 'addtest':
       if (isset($_POST['group']) && isset($config['groups'][$_POST['group']])) {
          $config['groups'][$_POST['group']]['tests'][] = $_POST['test-name'];
          Group::saveGroup();
          redirect(url('group/index'));
       }
       break;

     case 'getaddpopup':
       $tests = Test::getTestsList($_POST['group']);
       echo render('group/_popup',array('tests'=>$tests));
       exit();

     case 'removetest':
       if (isset($_POST['group'], $_POST['test']))
       {
         $groupTests = $config['groups'][$_POST['group']]['tests'];
         if (in_array($_POST['test'],$groupTests)){
           $key = array_search($_POST['test'],$groupTests);
           unset($config['groups'][$_POST['group']]['tests'][$key]);
           Group::saveGroup();
         }
       }
       exit();

     default:
       throw new Exception('Invalid request');
  }