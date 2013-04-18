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

class TaskController extends Controller
{
  /**
   * Shows tasks list
   */
  public function actionIndex() {
    $allTasks = Task::getAll();
    $tasks = !empty($allTasks) ? array_reverse($allTasks) : null;
    $groups = Group::getAll();
    echo $this->render('task/index',array(
      'tasks'=>$tasks,
      'groups'=>$groups,
    ));
  }

  /**
   * Create new task
   */
  public function actionCreate() {
    $errors = array();
    if (isset($_POST['Task'])) {
      $form = $_POST['Task'];
      $errors = Task::validate($form);

      if (!$errors) {
        Task::saveForm($form);
        $this->redirect($this->url('task/index'));
      }
    }
    $tests = Test::getTests();
    $groups = Group::getAll();
    echo $this->render('task/create',array(
      'tests'=>$tests,
      'groups'=>$groups,
      'errors'=>$errors
    ));
  }

  /**
   * Edit task
   */
  public function actionEdit() {
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $errors = array();
    if (isset($_POST['Task'])) {
      $form = $_POST['Task'];
      $errors = Task::validate($form);
      if (!$errors) {
        Task::saveForm($form);
        $this->redirect($this->url('task/index'));
      }
    }
    $tests = Test::getTests();
    $groups = Group::getAll();
    $task = Task::get($id);

    echo $this->render('task/update',array(
      'tests'=>$tests,
      'groups'=>$groups,
      'errors'=>$errors,
      'task'=>$task,
      'taskId' => $id,
    ));
  }

  /**
   * Delete task
   */
  public function actionDelete() {
    if (isset($_GET['id'])) {
      Task::delete($_GET['id']);
      $this->redirect($this->url('task/index'));
    }
  }

  /**
   * Shows task's info
   */
  public function actionView() {
    if (isset($_GET['id'])) {
      $task = Task::get($_GET['id']);
    }
		$groups = Group::getAll();
    echo $this->render('task/view',array(
		  'groups'=>$groups,
			'task'=>$task,
			'id'=>$_GET['id']
		));
  }

  /**
   * Remove test from task
   */
  public function actionRemovetest() {
    if (isset($_POST['task'], $_POST['test'])) {
      Task::removeTest($_POST['task'], $_POST['test']);
    }
    exit();
  }

	/**
   * Remove group from task
   */
  public function actionRemovegroup() {
    if (isset($_POST['task'], $_POST['group'])) {
      Task::removeGroup($_POST['task'], $_POST['group']);
    }
    exit();
  }
}