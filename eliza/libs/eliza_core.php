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

class Eliza
{
  protected static $instance;
  protected static $db;

  // Http client for browser emulating
  private $client;

  // HTML code of curent processing page
  private $currentPage;

  // HTTP headers of current page
  private $pageResponse;

  // peace of code for search user's phrase in see() method
  private $searchArea;

  // Test results for showing to user
  public $response = '';

  public $executionTime;
  public $testDescription;
  public $error = false;
  public $debug = false;
  public $responseFormat = 'json';
  public $testUrl = '';
  public $forms = '';

  public function __construct($options)
  {
    $this->setClient();
    $this->setConfig();
    $this->setOptions($options);
  }

  public function __clone() {}

  function __call($method, $args)
  {
    return call_user_func_array(array($this->decorators(), $method),$args);
  }

  function decorators()
  {
    if( !isset($this->decorators)) {
        require_once('method-injection.php');
        $this->decorators = new DecoratorStack($this);
    }
    return $this->decorators;
  }

  /**
   * Return instance of Eliza object
   *
   * @param type $return Return object without initialization
   * @return type
   */
  public static function test($return = false, $options = array())
  {
      if (null === self::$instance) {
          self::$instance = new self($options);
          /* Add additional modules */
          self::import();
      }

      call_user_func(array(self::$instance,'setOptions'),$options);

      if ($return) return self::$instance;

      call_user_func(array(self::$instance,'init'));
      return self::$instance;
  }

  public static function db() {
    if (self::$db === null) {
      self::$db = new Database();
    }
    return self::$db;
  }

  private static function import()
  {
    $I = self::$instance;
    if (!$modules = $I->getImportModules()) return;

    foreach ($modules as $key=>$moduleConfig) {
      if (is_array($moduleConfig)) {
        $moduleName = $key;
        // Set module config
        Config::setValue($moduleName,$moduleConfig);
      }
      else {
        $moduleName = $moduleConfig;
      }
      $I->mixin($moduleName.'Module');
    }
  }

  public function init($options = '')
  {
    $this->clearResponse();
  }

  public function setResponse($messages, $type = '')
  {
    if (!$messages) return;

    if (!is_array($messages)) {
      switch ($type)
      {
        case 'warning':
          $this->response .= $this->getWarningMessage($messages);
          break;
        case 'error':
          $this->response .= $this->getFailMessage($messages);
          $this->error = true;
          break;
        case 'success':
          $this->response .= $this->getPassMessage($messages);
          break;
        default:
          $this->response .= $messages;
          break;
      }
      return;
    }

    if ($type) {
      return;
    }

    $this->response .= (!$this->error) ? $this->getPassMessage($messages['pass']) : $this->getFailMessage($messages['fail']);
  }

  public function getCurrentPage()
  {
    return $this->currentPage;
  }

  public function getImportModules()
  {
    return $this->config['import'];
  }

  public function setClient()
  {
    $this->client = new webClient();
  }

  public function setConfig()
  {
    $this->config = Config::get();
  }

  public function setOptions($options)
  {
    if (!$options) return;

    foreach ($options as $option=>$value) {
      if (property_exists($this, $option)) {
        $this->$option = $value;
      }
    }

    $this->setTestHost();
  }

  public function want($description)
  {
    if ($this->error) return;

    $this->testDescription = $description;
    $this->response .=  '<br />'.$this->testDescription.'<br /><br />';
    return true;
  }

  public function go($path)
  {
    if ($this->error) return;

    if ($this->debug) $this->addDebugInfo('Start action "go"');

    $path = $this->createAbsoluteUrl($path);

    if ($this->debug) $this->addDebugInfo('Full page path is: '.$path);

    $r = substr(md5(time()),0,4);
    //Counter::start('Get page '.$path.' '.$r);
    $this->currentPage = $this->client->get($path);
   //Counter::end('Get page '.$path.' '.$r);

    if ($this->debug) $this->addDebugInfo('Page load time: '.round($pageLoadTime,4).' s');

    if ($this->currentPage) {
      $this->pageResponse = $this->client->head;

      if ($this->debug) {
        $this->addDebugInfo('Page '.$path.' is responded with:<br />'.$this->getPageResponse());
      }

      if ($this->pageResponse['Status']['Code'] == '200') {
        $this->forms = $this->client->parseAllForms();
        $this->saveCookies($this->client->_cookies);
        $this->response .= '<span class="success">OK</span>. '.$path.' is open.<br />';
      }
      else {
        $this->response .= $this->getPageErrorMessage();
        $this->error = true;
      }
    }
    else {
      $this->response .=  '<span class="fail">Failed.</span>'.$path.' is not open.<br />';
      $this->error = true;
    }

    if ($this->debug) $this->addDebugInfo('Finish action "go"');
  }

  public function fill($fields, $options = array())
  {
    if ($this->error) return;

    $softFill = isset($options['soft']) ? $options['soft'] : false;

    if ($this->debug) $this->addDebugInfo('Start action "fill"');

    foreach ($fields as $fieldName=>$fieldValue) {
      $isFill = false;
      foreach ($this->forms as $key=>$form) {
        if ($this->debug) $this->addDebugInfo('Check form: '.  print_r($form, true));

        if (array_key_exists($fieldName,$form['fields'])) {
          $this->forms[$key]['fields'][$fieldName] = $fieldValue;
          $this->response .=  '<span class="success">OK</span>. Field "'.$fieldName.'" is filled by value "'.$fieldValue.'".<br />';
          $isFill = true;
          break;
        }
        /*else {
          $this->response .=  'Field "'.$fieldName.'" is not exist. <span class="fail">Failed</span>.<br />';
          $this->error = true;
          break;
        }*/
      }
      if (!$isFill && !$this->error) {
        if ($softFill) {
          $this->setResponse('Field "'.$fieldName.'" is not exists','warning');
        }
        else {
          $this->response .=  '<span class="fail">Failed</span>. Field "'.$fieldName.'" is not filled.<br />';
          $this->error = true;
        }
      }
    }

    if ($this->debug) $this->addDebugInfo('Finish action "fill"');
  }

  public function select($fields)
  {
    if ($this->error) return;

    if ($this->debug) $this->addDebugInfo('Start action "select"');

    if (!$fields) {
      $this->response .=  '<span class="fail">Failed</span>. Fields is empty!<br />';
      $this->error = true;
      return;
    }

    foreach ($fields as $selectName=>$fieldValue) {
      $isFill = false;
      foreach ($this->forms as $key=>$form) {
        if ($this->debug) $this->addDebugInfo('Check form: '.  print_r($form, true));
        // Search select in the form
        if (array_key_exists($selectName,$form['select'])) {
          //Search option in the form
          if (in_array($fieldValue, $form['select'][$selectName])) {
            $this->forms[$key]['fields'][$selectName] = array_search($fieldValue, $form['select'][$selectName]);
            $this->response .=  '<span class="success">OK</span>. Select "'.$fieldValue.'" in drop down "'.$selectName.'".<br />';
            $isFill = true;
          }
          else {
            $this->response .=  '<span class="fail">Failed</span>. <span class="success">OK</span>. Option "'.$fieldValue.'" not exists in select "'.$selectName.'".<br />';
            $this->error = true;
          }
          break;
        }
        else {
          $this->response .=  '<span class="fail">Failed</span>. Select "'.$selectName.'" is not exist.<br />';
          $this->error = true;
          break;
        }
      }
      if (!$isFill && !$this->error) {
        $this->response .=  '<span class="fail">Failed</span>. Select "'.$selectName.'" is not filled.<br />';
        $this->error = true;
      }
    }

    if ($this->debug) $this->addDebugInfo('Finish action "select"');
  }

  public function see($text = '', $selector = '')
  {
    if ($this->error) return;

    if ($this->debug) $this->addDebugInfo('Start action "see"');

    if (!$text) {
      $this->response .=  '<span class="fail">Failed</span>. You need specify text for function "see".<br />';
      $this->error = true;
      return false;
    }

    $this->getSearchArea($selector);

    if (stripos($this->searchArea, $text)) {
      $this->response .=  '<span class="success">OK</span>. I see "'.$text.'".<br />';
    }
    else {
      $this->response .=  '<span class="fail">Failed</span>. I don\'t see "'.$text.'".<br />';
      $this->error = true;
    }

    if ($this->debug) $this->addDebugInfo('Finish action "see"');
    return true;
  }

  public function notSee($text = '')
  {
    if ($this->error) return;

    if ($this->debug) $this->addDebugInfo('Start action "notSee"');

    if (!$text) {
      $this->response .=  '<span class="fail">Failed</span>. You need specify text for function "notSee".<br />';
      $this->error = true;
      return false;
    }

    if (!strpos(strip_tags($this->currentPage), $text)) {
      $this->response .=  '<span class="success">OK</span>. I don\'t see "'.$text.'".<br />';
    }
    else {
      $this->response .=  '<span class="fail">Failed</span>. I see "'.$text.'".<br />';
      $this->error = true;
    }

    if ($this->debug) $this->addDebugInfo('Finish action "notSee"');
    return true;
  }

  public function click($link)
  {
    if ($this->error) return;

    if ($this->debug) $this->addDebugInfo('Start action "click"');

    // If user click on firm submit button
    if ($form = $this->isFormSubmit($link)) {
      if ($this->debug) $this->addDebugInfo('Link : '. $link. ' according to form: '.  print_r($form, true));

      $this->currentPage = $this->client->post($form['action'], $form);
      if ($this->currentPage) {
        $this->response .=  '<span class="success">OK</span>.'.$link.' is clicked.<br />';
      }
      else {
        $this->response .=  '<span class="fail">Failed</span>.'.$link.' is not clicked.<br />';
        $this->error = true;
      }
    }
    // If user click on page link
    else if ($linkUrl = $this->getLinkUrl($link)) {
      $this->go($linkUrl);
    }
    else {
      $this->response .=  '<span class="fail">Failed</span>.'.$link.' is not found.<br />';
      $this->error = true;
    }

    if ($this->debug) $this->addDebugInfo('Finish action "click"');
    return true;
  }

  public function submit($formKey) {
    if ($this->error) return;

    if ($this->debug) $this->addDebugInfo('Begin action "submit"');

    if (!$formKey) {
      $this->response .= '<span class="fail">Failed</span>. Name or Id form must be not empty.<br />';
      $this->error = true;
      return false;
    }

    $submitForm = '';
    foreach ($this->forms as $form) {
      if ($this->debug) $this->addDebugInfo('Check form: '.  print_r($form, true));

      if (isset($form['name']) && ($form['name'] == $formKey)) {
        $submitForm = $form;
        $message = '<span class="success">OK</span>. Form with name "'.$formKey.'" is submitted.<br />';
      }
      else if (isset($form['id']) && ($form['id'] == $formKey)) {
        $submitForm = $form;
        $message = '<span class="success">OK</span>. Form with id "'.$formKey.'" is submitted.<br />';
      }
    }

    if ($submitForm) {
      //$r = substr(md5(time()),0,4);
      //Counter::start('POST page '.$submitForm['action'].' '.$r);
      $this->currentPage = $this->client->post($submitForm['action'], $submitForm);
      //Counter::end('POST page '.$submitForm['action'].' '.$r);

      if ($this->currentPage) {
        $this->pageResponse = $this->client->head;
        if ($this->debug) {
          $this->addDebugInfo('Page '.$submitForm['action'].' is responded with:<br />'.$this->getPageResponse());
        }

        if ($this->pageResponse['Status']['Code'] == '200') {
          $this->forms = $this->client->parseAllForms();
          $this->saveCookies($this->client->_cookies);
          $this->response .= $message;
        }
        else {
          $this->response .= '<span class="fail">Failed</span>. Form "'.$formKey.'" not submitted. Check the action path "'.$submitForm['action'].'".<br />';
          $this->error = true;
        }
      }
      else {
        $this->response .= '<span class="fail">Failed</span>. Form "'.$formKey.'" not submitted. Response is empty!<br />';
        $this->error = true;
      }

      /*if ($this->currentPage) {
        $this->response .= $message;
      }
      else {
        $this->response .= 'Form "'.$formKey.'" not submitted. Check the action path "'.$submitForm['action'].'" <span class="fail">Failed</span>.<br />';
        $this->error = true;
      }*/
    }
    else {
      $this->response .= '<span class="fail">Failed</span>. Form with name or id "'.$formKey.'" not exists.<br />';
      $this->error = true;
    }

    if ($this->debug) $this->addDebugInfo('Finish action "submit"');

    return true;
  }

  public function addTest($testName)
  {
    if (file_exists(TEST_PATH.$testName.'.php')) {
      Test::includeTest($testName);
    }
    else {
      $this->response .= $this->getFailMessage('Test "'.$testName.'" is not exists.');
      $this->error = true;
    }
  }

  public function newTest($testName)
  {
    if ($this->error) return;

    if ($this->debug) $this->addDebugInfo('Begin new test with name "'.$testName.'"');

    $this->client = new phpWebHacks();
    $this->response .= '<hr /><p class="test-name">'.$testName.'</p>';
  }

  public function getLinkUrl($anchor) {
    preg_match('/<a[^>]*href\s*=\s*[\"]([^"]*)[\"][^>]*>[a-z0-9\s]*'.$anchor.'[a-z0-9\s]*<\/a>/i', $this->currentPage, $matches);

    return ($matches[1]) ? $matches[1] : false;
  }

  private function getSearchArea($selector = '')
  {
    if (!$selector) $this->searchArea = $this->clearSearchArea($this->currentPage);

    // If selector defined, ex. #container
    if ($selector) {

      // find area by Id
      if (strpos($selector,'#') !== false) {
        $id = str_replace('#', '', $selector);
        $html = new DOMDocument();
        @$html->loadHTML($this->currentPage);
        $this->searchArea = $this->clearSearchArea($html->getElementById($id)->nodeValue);
      }
    }
  }

  private function clearSearchArea($text)
  {
    $text = preg_replace(
      array(
        '/<option[^>]*>(.*)<\/option>/isU',
        '/<script[^>]*>(.*)<\/script>/isU',
        '/<head>(.*)<\/head>/isU'
      ),
      array('','',''),
      $text
    );
    //$text = strip_tags($text);

    return $text;
  }

  private function setTestHost() {
    if ($this->testUrl) {
      $this->config['host'] = $this->testUrl;
    }
    else if(isset($this->config['urls'][0])) {
      $this->config['host'] = $this->config['urls'][0];
    }
    return true;
  }

  private function createAbsoluteUrl($path)
  {
    if (stripos($path, 'http') === 0) return $path;
    if ($path == '/') return $this->config['host'];
    //if (strpos($path, $this->config['host'])) return $path;

    return trim($this->config['host'],'/').'/'.trim($path,'/');
  }

  private function isFormSubmit($link)
  {
    foreach ($this->forms as $form) {
      if (isset($form['submit']) && ($form['submit'] == $link)) return $form;
    }

    return false;
  }

  private function saveCookies($cookie)
  {
    file_put_contents(TEST_PATH.'cookies.dat',serialize($cookie));
  }

  public function showResponse()
  {
    if ($this->responseFormat == 'json')
      echo json_encode(array(
        'response'=>$this->response,
        'error' => $this->error,
      ));
    else
      echo $this->response;

    //echo '<br/>';
  }

  public function clearResponse()
  {
    $this->response = '';
    return true;
  }

  public function logger($testName)
  {
    $logFile = fopen(LOG_FILE,'a+');
    $logData = array(
      'date'=> date('d-m-Y H:i'),
      'name' => $testName,
      'procces' => strip_tags(br2nl($this->response)),
      'error' => $this->error,
      'executionTime' => $this->executionTime,
    );
    fwrite( $logFile, json_encode( $logData ) . PHP_EOL);
  }

  private function addDebugInfo($message) {
    $this->response .= '<div class="debug-info '.(strpos($message,'Finish') === 0 ? 'last' : '').'">'.$message.'</div>';
  }

  private function getPageResponse() {
    return '
      Status: '.$this->pageResponse['Status']['Code'].'<br />
      Message: '.$this->pageResponse['Status']['Message'].'<br />
      Content-Length: '.$this->pageResponse['Content-Length'].'<br />
      Content-Type: '.$this->pageResponse['Content-Type'].'<br />
    ';
  }

  private function getPageErrorMessage() {
    $message = '<span class="fail">Failed</span>. ';
    switch ($this->pageResponse['Status']['Code'])
    {
      case '404':
        $message .= 'Page not found!';
        break;

      case '500':
        $message .= 'Server internal error!';
        break;

      default:
        $message .= 'Page not open!';
    }
    $message .= '<br />';

    return $message;
  }

  private function getPassMessage($message)
  {
    return '<span class="success">OK</span>. '.$message.'<br />';
  }

  private function getFailMessage($message)
  {
    return '<span class="fail">Failed</span>. '.$message.'<br />';
  }

  private function getWarningMessage($message)
  {
    return '<span class="warning">Warning</span>. '.$message.'<br />';
  }
}