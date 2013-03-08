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

function autoload($className) {
  try {
    $clearClassName = strtolower($className);

    // Include model files
    if (file_exists(MODELS_PATH.$clearClassName.'.php'))
      require_once MODELS_PATH.$clearClassName.'.php';
    // Include modules files
    else if (file_exists(MODULES_PATH.$className.'.php'))
      require_once MODULES_PATH.$className.'.php';
    // Include libs files
    else if (file_exists(LIBS_PATH.$className.'.php'))
      require_once LIBS_PATH.$className.'.php';
  }
  catch (Exception $e) {
    die('Class '.$className.' not found!');
  }
}

function fatalErrorHandle() {
  $error = error_get_last();
  file_put_contents(DATA_PATH.'application.log', date('Y-m-d G:i').' '.$error['message'].' in file '.$error['file'].' on line '.$error['line'].PHP_EOL, FILE_APPEND);
}

function render($template, $vars = array())
{
  if ($vars) extract($vars);
  $templateFile = TEMPLATE_PATH.$template.'.php';
  if (!file_exists($templateFile)) die('Template not found!');
  ob_start();
  include $templateFile;
  $view = ob_get_contents();
  ob_end_clean();
  return $view;
}

function url($route,$params = '')
{
  if (!$route) throw new Exception('Page not found!', 404);
  $uri = '?r='.htmlentities($route);
  if ($params && is_array($params)) {
    foreach ($params as $key=>$param) {
      $uri .= '&'.$key.'='.$param;
    }
  }

  return 'index.php'.$uri;
}

function redirect($url)
{
  header('Location: '.$url);
  exit();
}

function flash($type, $message) {
  if(!isset($_SESSION['flash'])) {
    $_SESSION['flash'] = array();
  }
  $_SESSION['flash'][$type] = $message;
}

function unsetFlash() {
  if(isset($_SESSION['flash'])) {
      $_SESSION['flash'] = null;
  }
}

function getFlash($type) {
  if(isset($_SESSION['flash'])) {
    $message = $_SESSION['flash'][$type];
    unsetFlash();
    return $message;
  }
  return null;
}

function br2nl($text) {
  $text = preg_replace('/<br\s*[\/]?>/i',PHP_EOL,$text);
  return $text;
}

function getFormValue($containers, $attribute, $defaultValue = '')
{
  if (!is_array($containers)) $containers = array($containers);

  foreach($containers as $container) {
    if (isset($container[$attribute])) $returnValue = $container[$attribute];
  }

  if (!$returnValue) $returnValue = $defaultValue;

  return htmlspecialchars($returnValue);
}

function emailValidate($email) {
  if (!$email) return false;

  return preg_match('/^[a-zA-Z0-9]+(?:\.[a-zA-Z0-9]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/',$email);
}
?>
