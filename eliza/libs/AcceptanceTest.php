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

class AcceptanceTest {

  // Global execution context
  public $app;

  // Current test context
  public static $test;

  // Http client for browser emulating
  private $client;

  // HTML code of curent processing page
  private $currentPage;

  // HTTP headers of current page
  private $pageResponse;

  // peace of code for search user's phrase in see() method
  private $searchArea;

  // Test description
  public $testDescription;

  // HTML page forms
  public $forms = '';

  // Test error flag
  private $error;

  function __call($method, $args)
  {
    return call_user_func_array(array($this->decorators(), $method),$args);
  }

  /**
   * Add classes as decorators for Dependency Injection
   *
   * @return type
   */
  function decorators()
  {
    if( !isset($this->decorators)) {
        require_once('DecoratorStack.php');
        $this->decorators = new DecoratorStack($this,array('getModulesList','getAvailableModules','saveConfig','getModule','options','updateConfig','hasLocalConfig'));
        $this->decorators->unknown_message_type = 'You have not activated a module which were used in "%s"';
    }
    return $this->decorators;
  }

  /**
   * Returns test object
   *
   * @return AcceptanceTest
   */
  public static function test() {
    return self::$test;
  }

  /**
   * Add additional modules through Dependency Injection
   */
  private function import()
  {
    if (!$modules = Module::getAvailableModules()) return;

    foreach ($modules as $key=>$options) {
      if ($modules[$key]['enabled']) {
        $this->mixin($key.'Module');
      }
    }
  }

  /**
   * Initialize AcceptanceTest object
   */
  public function __construct() {
    $this->setApp();
    $this->setTest();
    $this->setClient();
    $this->setTestHost();
    $this->import();
  }

  /**
   * Return current testing HTML page
   *
   * @return string HTML page code
   */
  public function getCurrentPage() {
    return $this->currentPage;
  }

  /**
   * Set global application context
   */
  public function setApp() {
    $this->app = Eliza::app();
  }

  /**
   * Set test context for getting test functions without creating object
   */
  public function setTest() {
    self::$test = $this;
  }

  /**
   * Set HTTP client
   */
  public function setClient() {
    $this->client = new Webclient();
  }

  /**
   * Represent test description
   *
   * @param type $description Test description
   * @return boolean
   */
  public function want($description) {
    if ($this->app->error) return;

    $this->testDescription = $description;
    $this->app->setResponse('<br />'.$this->testDescription.'<br />');
    return true;
  }

  /**
   * Represents user's action: Go to the specify HTML page
   *
   * @param type $path HTML page path
   * @return type
   */
  public function go($path) {
    if ($this->app->error) return;

    if ($this->app->debug) $this->app->addDebugInfo('Start action "go"');

    $path = $this->createAbsoluteUrl($path);

    if ($this->app->debug) $this->app->addDebugInfo('Full page path is: '.$path);

    $r = substr(md5(time()),0,4);

    $this->currentPage = $this->client->get($path);

    if ($this->currentPage) {
      $this->pageResponse = $this->client->head;

      if ($this->app->debug) {
        $this->app->addDebugInfo('Page '.$path.' is responded with:<br />'.$this->getPageResponse());
      }

      if ($this->pageResponse['Status']['Code'] == '200') {
        $this->forms = $this->client->parseAllForms();
        $this->saveCookies($this->client->_cookies);
        $this->app->setResponse($path.' is open','success');
      }
      else {
        $this->app->setResponse($this->getPageErrorMessage(),'error');
      }
    }
    else {
      $this->app->setResponse($path.' is not open','error');
    }

    if ($this->app->debug) $this->app->addDebugInfo('Finish action "go"');
  }

  /**
   * Represents user's action: Fill text or textarea fields in HTML form
   *
   * @param array $fields Text form elements
   * @param type $options Action options
   * @return type
   */
  public function fill($fields, $options = array())
  {
    if ($this->app->error) return;

    $softFill = isset($options['soft']) ? $options['soft'] : false;

    if ($this->app->debug) $this->app->addDebugInfo('Start action "fill"');

    foreach ($fields as $fieldName=>$fieldValue) {
      $isFill = false;
      foreach ($this->forms as $key=>$form) {
        if ($this->app->debug) $this->app->addDebugInfo('Check form: '.  print_r($form, true));

        if (array_key_exists($fieldName,$form['fields'])) {
          $this->forms[$key]['fields'][$fieldName] = $fieldValue;
          $this->app->setResponse('Field "'.$fieldName.'" is filled by value "'.$fieldValue.'"','success');
          $isFill = true;
          break;
        }
      }
      if (!$isFill && !$this->app->error) {
        if ($softFill) {
          $this->app->setResponse('Field "'.$fieldName.'" is not exists','warning');
        }
        else {
          $this->app->setResponse('Field "'.$fieldName.'" is not filled.','error');
        }
      }
    }

    if ($this->app->debug) $this->app->addDebugInfo('Finish action "fill"');
  }

  /**
   * Represents user's action: Select dropdown value in HTML form
   *
   * @param type $fields Dropdown form elements
   * @return type
   */
  public function select($fields)
  {
    if ($this->app->error) return;

    if ($this->app->debug) $this->app->addDebugInfo('Start action "select"');

    if (!$fields) {
      $this->app->setResponse('Fields is empty!','error');
      return;
    }

    foreach ($fields as $selectName=>$fieldValue) {
      $isFill = false;
      foreach ($this->forms as $key=>$form) {
        if ($this->app->debug) $this->app->addDebugInfo('Check form: '.  print_r($form, true));
        // Search select in the form
        if (array_key_exists($selectName,$form['select'])) {
          //Search option in the form
          if (in_array($fieldValue, $form['select'][$selectName])) {
            $this->forms[$key]['fields'][$selectName] = array_search($fieldValue, $form['select'][$selectName]);
            $this->app->setResponse('Select "'.$fieldValue.'" in drop down "'.$selectName.'"','success');
            $isFill = true;
          }
          else {
            $this->app->setResponse('Option "'.$fieldValue.'" not exists in select "'.$selectName.'"','error');
          }
          break;
        }
        else {
          $this->app->setResponse('Select "'.$selectName.'" is not exist.','error');
          break;
        }
      }
      if (!$isFill && !$this->app->error) {
        $this->app->setResponse('Select "'.$selectName.'" is not filled.','error');
      }
    }

    if ($this->app->debug) $this->app->addDebugInfo('Finish action "select"');
  }

  /**
   * Represents user's action: Find text on the page
   *
   * @param string $text Searching text
   * @param string $selector Specify CSS selector for element that contains text
   * @return boolean
   */
  public function see($text = '', $selector = '')
  {
    if ($this->app->error) return;

    if ($this->app->debug) $this->app->addDebugInfo('Start action "see"');

    if (!$text) {
      $this->app->setResponse('You need specify text for function "see".', 'error');
      return false;
    }

    $this->getSearchArea($selector);

    if (stripos($this->searchArea, $text)) {
      $this->app->setResponse('I see "'.$text, 'success');
    }
    else {
      $this->app->setResponse('I don\'t see "'.$text, 'error');
    }

    if ($this->app->debug) $this->app->addDebugInfo('Finish action "see"');
    return true;
  }

  /**
   * Represents user's action: Not find text on the page
   *
   * @param type $text Searching text
   * @return boolean
   */
  public function notSee($text = '')
  {
    if ($this->app->error) return;

    if ($this->app->debug) $this->app->addDebugInfo('Start action "notSee"');

    if (!$text) {
      $this->app->setResponse('You need specify text for function "notSee"', 'error');
      return false;
    }

    if (!strpos(strip_tags($this->currentPage), $text)) {
      $this->app->setResponse('I don\'t see "'.$text, 'success');
    }
    else {
      $this->app->setResponse('I see "'.$text, 'error');
    }

    if ($this->app->debug) $this->app->addDebugInfo('Finish action "notSee"');
    return true;
  }

  /**
   * Represents user's action: Click link or submit button on the page
   *
   * @param string $link Link or button text
   * @return boolean
   */
  public function click($link)
  {
    if ($this->app->error) return;

    if ($this->app->debug) $this->app->addDebugInfo('Start action "click"');

    // If user click on firm submit button
    if ($form = $this->isFormSubmit($link)) {
      if ($this->app->debug) $this->app->addDebugInfo('Link : '. $link. ' according to form: '.  print_r($form, true));

      $this->currentPage = $this->client->post($form['action'], $form);
      if ($this->currentPage) {
        $this->app->setResponse($link.' is clicked.', 'success');
      }
      else {
        $this->app->setResponse($link.' is not clicked.', 'error');
      }
    }
    // If user click on page link
    else if ($linkUrl = $this->getLinkUrl($link)) {
      $this->go($linkUrl);
    }
    else {
      $this->app->setResponse($link.' is not found.', 'error');
    }

    if ($this->app->debug) $this->app->addDebugInfo('Finish action "click"');
    return true;
  }

  /**
   * Represents user's action: Submit form on the page by name or id
   *
   * @param type $formKey
   * @return boolean
   */
  public function submit($formKey) {
    if ($this->app->error) return;

    if ($this->app->debug) $this->app->addDebugInfo('Begin action "submit"');

    if (!$formKey) {
      $this->app->setResponse('Name or Id form must be not empty.', 'error');
      return false;
    }

    $submitForm = '';
    foreach ($this->forms as $form) {
      if ($this->app->debug) $this->app->addDebugInfo('Check form: '.  print_r($form, true));

      if (isset($form['name']) && ($form['name'] == $formKey)) {
        $submitForm = $form;
        $message = 'Form with name "'.$formKey.'" is submitted.';
      }
      else if (isset($form['id']) && ($form['id'] == $formKey)) {
        $submitForm = $form;
        $message = 'Form with id "'.$formKey.'" is submitted.';
      }
    }

    if ($submitForm) {
      $this->currentPage = $this->client->post($submitForm['action'], $submitForm);

      if ($this->currentPage) {
        $this->pageResponse = $this->client->head;

        if ($this->pageResponse['Status']['Code'] == '200') {
          $this->forms = $this->client->parseAllForms();
          $this->saveCookies($this->client->_cookies);
          $this->app->setResponse($message, 'success');
        }
        else {
          $this->app->setResponse('Form "'.$formKey.'" not submitted. Check the action path "'.$submitForm['action'].'"', 'error');
        }
      }
      else {
        $this->app->setResponse('Form "'.$formKey.'" not submitted. Response is empty!', 'error');
      }
    }
    else {
      $this->app->setResponse('Form with name or id "'.$formKey.'" not exists.', 'error');
    }

    if ($this->app->debug) $this->app->addDebugInfo('Finish action "submit"');

    return true;
  }

  /**
   * Add test to the current test
   *
   * @param string $testName Test name
   */
  public function addTest($testName)
  {
    if (file_exists(TEST_PATH.$testName.'.php')) {
      Test::includeTest($testName);
    }
    else {
      $this->app->setResponse('Test "'.$testName.'" is not exists.','error');
    }
  }

  /**
   * Start new test in the current test
   *
   * @param string $testName test name
   * @return type
   */
  public function newTest($testName)
  {
    if ($this->app->error) return;

    if ($this->app->debug) $this->app->addDebugInfo('Begin new test with name "'.$testName.'"');

    $this->client = new phpWebHacks();
    $this->app->setResponse('<hr /><p class="test-name">'.$testName.'</p>');
  }

  /**
   * Returns link's href attribute
   *
   * @param string $anchor Links anchor text
   * @return Link's URL
   */
  public function getLinkUrl($anchor) {
    preg_match('/<a[^>]*href\s*=\s*[\"]([^"]*)[\"][^>]*>[a-z0-9\s]*'.$anchor.'[a-z0-9\s]*<\/a>/i', $this->currentPage, $matches);

    return (!empty($matches[1])) ? $matches[1] : false;
  }

  /**
   * Returns specifying search text area for searching text
   *
   * @param string $selector CSS element selector
   */
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

  /**
   * Remove hidden fields from search area
   *
   * @param string $text Page text
   * @return string Cleared text
   */
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

    return $text;
  }

  /**
   * Set test executing Url
   *
   * @return boolean
   */
  private function setTestHost() {
    if ($this->app->testUrl) {
      $this->app->config['host'] = $this->app->testUrl;
    }
    else if(isset($this->config['urls'][0])) {
      $this->app->config['host'] = $this->app->config['urls'][0];
    }
    return true;
  }

  /**
   * Create absolute url
   *
   * @param string $path relative Url
   * @return type
   */
  private function createAbsoluteUrl($path) {
    if (stripos($path, 'http') === 0) return $path;
    if ($path == '/') return $this->app->config['host'];

    return trim($this->app->config['host'],'/').'/'.trim($path,'/');
  }

  /**
   * Define is link submit button
   *
   * @param string $link
   * @return boolean
   */
  private function isFormSubmit($link)
  {
    if (empty($this->forms)) return false;

    foreach ($this->forms as $form) {
      if (isset($form['submit']) && ($form['submit'] == $link)) return $form;
    }

    return false;
  }

  /**
   * Save HTML page cookies
   *
   * @param string $cookie Cookies contains
   * @return boolean
   */
  private function saveCookies($cookie)
  {
    return file_put_contents(TEST_PATH.'cookies.dat',serialize($cookie));
  }

  /**
   * Get page response
   *
   * @return string Main info about page response
   */
  private function getPageResponse() {
    return '
      Status: '.(!empty($this->pageResponse['Status']['Code']) ? $this->pageResponse['Status']['Code'] : '').'<br />
      Message: '.(!empty($this->pageResponse['Status']['Message']) ? $this->pageResponse['Status']['Message'] : '').'<br />
      Content-Length: '.(!empty($this->pageResponse['Content-Length']) ? $this->pageResponse['Content-Length'] : '').'<br />
      Content-Type: '.(!empty($this->pageResponse['Content-Type']) ? $this->pageResponse['Content-Type'] : '').'<br />
    ';
  }

  /**
   * Returns Page error message if HTML page has error
   *
   * @return string Error message
   */
  private function getPageErrorMessage() {
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

    return $message;
  }
}