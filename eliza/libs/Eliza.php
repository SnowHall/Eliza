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

class Eliza
{
  // Current object context
  protected static $instance;

  // Default timezone
  private $defaultTimezone = "America/Denver";

  // Test results for showing to user
  public $response = '';

  // Test url for acceptance testing
  public $testUrl = '';

  // Request route
  public $route = '';

  // Current controller object
  public $controller;

  // Controller from HTTP request
  public $controllerRequest;

	// Default controller
	private $defaultController = 'test';

	// Default action
	private $defaultAction = 'index';

  // Action from HTTP request
  public $actionRequest;

  // Test execution time
  public $executionTime;

  // Test error flag
  public $error = false;

  // Test debug flag
  public $debug = false;

  // Test response format
  public $responseFormat = 'json';

  // Application config
  public $config;

  /**
   * Initialize Eliza application object
   *
   * @param type $options
   */
  public function __construct($options)
  {
    $this->setConfig();
    $this->setRoute();
    $this->setController();
    $this->initGroups();
    $this->initTasks();
    $this->setTimezone();
    $this->setOptions($options);
  }

  public function __clone() {}

  /**
   * Return instance of Eliza object
   *
   * @param type $return Return object without initialization
   * @return type
   */
  public static function app($options = array()) {
      if (null === self::$instance) {
          self::$instance = new self($options);
      }
      call_user_func(array(self::$instance,'setOptions'),$options);

      return self::$instance;
  }

  /**
   * Set HTTP request route
   */
  public function setRoute() {
		$this->route = !empty($_GET['r']) ? explode('/', $_GET['r']) : '';
  }

  /**
   * Set current controller
   */
  public function setController() {
		// If Eliza not installed yet run istaller
		$this->controllerRequest = !empty($this->route[0]) ? preg_replace('#[^a-zA-Z0-9]#','',$this->route[0]) : $this->defaultController;
    $this->actionRequest = !empty($this->route[1]) ? preg_replace('#[^a-zA-Z0-9]#','',$this->route[1]) : $this->defaultAction;
  }

  /**
   * Run current controller
   *
   * @throws Exception Controller not found, Action not found
   */
  public function runController() {
    $controllerClass = ucfirst($this->controllerRequest).'Controller';

    if (!class_exists($controllerClass))
      throw new Exception('Controller "'.ucfirst($this->controllerRequest).'" not found!', 404);

    $this->controller = new $controllerClass;

    if (!method_exists($this->controller, 'action'.ucfirst($this->actionRequest)))
      throw new Exception('Action "'.ucfirst($this->actionRequest).'" not found!', 404);

    $this->controller->beforeAction();
    call_user_func_array(array($this->controller, 'action'.ucfirst($this->actionRequest)), array());
    $this->controller->afterAction();
  }

  /**
   * Set test's command execution response
   *
   * @param type $message Message
   * @param type $type Response type (warning, error, success)
   * @return boolean
   */
  public function setResponse($message, $type = '') {
    if (!$message) return false;

    if (!is_array($message)) {
      switch ($type)
      {
        case 'warning':
          $this->response .= $this->getWarningMessage($message);
          break;
        case 'error':
          $this->response .= $this->getFailMessage($message);
          $this->error = true;
          break;
        case 'success':
          $this->response .= $this->getPassMessage($message);
          break;
        default:
          $this->response .= $this->getMessage($message);
          break;
      }
      return;
    }

    $this->response .= (!$this->error) ? $this->getPassMessage($message['pass']) : $this->getFailMessage($message['fail']);
  }

  /**
   * Set application config
   */
  public function setConfig() {
    $this->config = Config::get();
  }

  /**
   * Init tests groups
   */
  public function initGroups() {
    Group::init();
  }

  /**
   * Init tests tasks
   */
  public function initTasks() {
    Task::init();
  }

  /**
   * Set application timezone
   *
   * @return timezone name
   */
  public function setTimezone() {
    $offset = isset($this->config['timezone']) ? $this->config['timezone'] : $this->defaultTimezone;
    return Timezone::setTimezoneByOffset($offset);
  }

  /**
   * Set application additional options
   *
   * @param array $options Application's options
   * @return type
   */
  public function setOptions($options) {
    if (!$options) return;

    foreach ($options as $option=>$value) {
      if (property_exists($this, $option)) {
        $this->$option = $value;
      }
    }
  }

  /**
   * Show test execution response
   */
  public function showResponse() {
    if ($this->responseFormat == 'json') {
      echo json_encode(array(
        'response'=>$this->response,
        'error' => $this->error,
      ));
    }
    else {
      echo $this->response;
    }
  }

  /**
   * Clear test execution response
   * @return boolean
   */
  public function clearResponse() {
    $this->response = '';
    return true;
  }

  /**
   * Sve in history test execution response
   *
   * @param string $testName Test name
   */
  public function logger($testName) {
    $logFile = fopen(LOG_FILE,'a+');
    $logData = array(
      'date'=> date('d-m-Y H:i'),
      'name' => $testName,
      'procces' => strip_tags(Helpers::br2nl($this->response)),
      'error' => $this->error,
      'executionTime' => $this->executionTime,
    );
    fwrite($logFile, json_encode($logData) . PHP_EOL);
    fclose($logFile);
  }

  /**
   * Get command's success execution message
   *
   * @param string $message Message
   * @return string Formatted message
   */
  private function getPassMessage($message) {
    return '<span class="success">OK</span>. '.$message.'<br />';
  }

  /**
   * Get command's failed execution message
   *
   * @param string $message Message
   * @return string Formatted message
   */
  private function getFailMessage($message) {
    return '<span class="fail">Failed</span>. '.$message.'<br />';
  }

  /**
   * Get command's warning execution message
   *
   * @param string $message Message
   * @return string Formatted message
   */
  private function getWarningMessage($message) {
    return '<span class="warning">Warning</span>. '.$message.'<br />';
  }

  /**
   * Get command's simple execution message
   *
   * @param string $message Message
   * @return string Formatted message
   */
  private function getMessage($message) {
    return $message.'<br />';
  }

  /**
   * Add to response debug info
   *
   * @param string $message Message
   */
  public function addDebugInfo($message) {
    $this->response .= '<div class="debug-info '.(strpos($message,'Finish') === 0 ? 'last' : '').'">'.$message.'</div>';
  }
}