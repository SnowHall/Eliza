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

class Controller {

  // Rendered HTML content
  private $content = '';

  /**
   * Execute actions before requesting controller action
   */
  public function beforeAction() {
    ob_start();
  }

  /**
   * Execute actions after requesting controller action
   */
  public function afterAction() {
    $this->content = ob_get_contents();
    ob_end_clean();
  }

  /**
   * Render views template
   *
   * @param type $template Template file
   * @param type $vars Variables transmitted to the template
   * @return type Rendered template
   * @throws Exception Template not found
   */
  public function render($template, $vars = array()) {
    if ($vars) extract($vars);
    $templateFile = TEMPLATE_PATH.$template.'.php';

    if (!file_exists($templateFile))
      throw new Exception('Template "'.$template.'" not found!', 404);

    ob_start();
    require_once $templateFile;
    $view = ob_get_contents();
    ob_end_clean();

    return $view;
  }

  /**
   * Render error view. Function is static for case if controller not run.
   *
   * @param string $template Template file
   * @param array $vars Variables transmitted to the template
   * @return string Rendered template
   */
  public static function renderError($template, $vars = array()) {
    if ($vars) extract($vars);
    $templateFile = TEMPLATE_PATH.$template.'.php';

    ob_start();
    require_once $templateFile;
    $view = ob_get_contents();
    ob_end_clean();

    return $view;
  }

  /**
   * Return current rendered content
   *
   * @return string HTML content
   */
  public function getContent() {
     return $this->content;
  }

  /**
   * Generate url from route
   *
   * @param type $route Application route in format <controller>/<action>
   * @param type $params GET params
   * @return string Generated URL
   * @throws Exception Page not found
   */
  public function url($route, $params = '')
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

  /**
   * Redirect user to the specified location
   * 
   * @param type $url
   */
  public function redirect($url)
  {
    header('Location: '.$url);
    exit();
  }
}