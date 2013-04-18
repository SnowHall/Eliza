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

class GroupController extends Controller
{
  /**
   * Shows grops list
   */
  public function actionIndex() {
    $groups = Group::getAll();
    echo $this->render('group/index',array('groups'=>$groups));
  }

  /**
   * Create new group
   */
  public function actionCreate() {
    if (isset($_POST['Group'])) {
      $form = $_POST['Group'];
      $errors = array();

      if (empty($form['name'])) {
        $errors['name'] = 'Field "Test Name" shouldn\'t be empty';
      }

      if (!$errors) {
        Group::saveGroupByName($form['name']);
        $this->redirect($this->url('group/index'));
      }
      else {
        echo $this->render('group/create',array('errors'=>$errors));
      }
    }
    else {
      echo $this->render('group/create');
    }
  }

  /**
   * Delete group
   */
  public function actionDelete() {
    if (isset($_GET['id'])) {
      Group::delete($_GET['id']);
      $this->redirect($this->url('group/index'));
    }
  }

  /**
   * Shows group info
   */
  public function actionView() {
    if (isset($_GET['id'])) {
      $group = Group::get($_GET['id']);
    }
    echo $this->render('group/view',array('group'=>$group,'id'=>$_GET['id']));
  }

  /**
   * Add new test to the group
   */
  public function actionAddtest() {
    if (isset($_POST['group'])) {
      $group = Group::get($_POST['group']);
      if ($group) {
        $group['tests'][] = $_POST['test-name'];
        Group::save($group, $_POST['group']);
        $this->redirect($this->url('group/index'));
      }
    }
  }

  /**
   * Shows modal window for adding new test
   */
  public function actionGetaddpopup() {
    $tests = Test::getTestsList($_POST['group']);
    echo $this->render('group/_popup',array('tests'=>$tests));
    exit();
  }

  /**
   * Remove test from group
   */
  public function actionRemovetest() {
    if (isset($_POST['group'], $_POST['test'])) {
      Group::removeTest($_POST['group'], $_POST['test']);
    }
    exit();
  }
}