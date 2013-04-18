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

ini_set('memory_limit', '128M');

$current_folder = dirname(__FILE__);

define('ROOT_PATH', $current_folder.'/');
define('TEST_PATH',$current_folder.'/tests/');
define('TEMPLATE_PATH',$current_folder.'/views/');
define('CONTROLLERS_PATH',$current_folder.'/controllers/');
define('DATA_PATH',$current_folder.'/data/');
define('CONFIG_PATH',$current_folder.'/config/');
define('MODELS_PATH',$current_folder.'/models/');
define('LIBS_PATH',$current_folder.'/libs/');
define('MODULES_PATH',$current_folder.'/modules/');
define('VENDORS_PATH',$current_folder.'/vendors/');


define('LOG_FILE',DATA_PATH.'tests.log');
define('TASKS_FILE',DATA_PATH.'tasks.json');
define('GROUPS_FILE',DATA_PATH.'groups.json');
define('QUEUE_FILE',DATA_PATH.'queue.json');
define('MODULES_HELP_FILE',DATA_PATH.'modules-help.json');
define('METHODS_HELP_FILE',DATA_PATH.'methods-help.json');
define('MODULES_FILE',CONFIG_PATH.'modules.json');
define('CONFIG_FILE',CONFIG_PATH.'config.php');

require_once VENDORS_PATH.'geshi/geshi.php';

session_start();

spl_autoload_register('autoload');
register_shutdown_function('fatalErrorHandle');

/**
 * Autoload classes
 *
 * @param string $className Loaded class name
 */
function autoload($className) {
  try {
    $clearClassName = strtolower($className);

    // Include model files
    if (file_exists(MODELS_PATH.$clearClassName.'.php'))
      require_once MODELS_PATH.$clearClassName.'.php';
    // Include controller files
    else if (file_exists(CONTROLLERS_PATH.str_replace('controller','',$clearClassName).'.php')) {
      require_once CONTROLLERS_PATH.str_replace('controller','',$clearClassName).'.php';
    }
    // Include modules files
    else if (file_exists(MODULES_PATH.str_replace('Module', '', $className).'/'.str_replace('Module', '', $className).'.php')){
      require_once MODULES_PATH.'/'.str_replace('Module', '', $className).'/'.str_replace('Module', '', $className).'.php';
    }
    // Include libs files
    else if (file_exists(LIBS_PATH.$className.'.php'))
      require_once LIBS_PATH.$className.'.php';
  }
  catch (Exception $e) {
    die('Class '.$className.' not found!');
  }
}

/**
 * Error handler for fatal errors
 */
function fatalErrorHandle() {
  $error = error_get_last();
  if (empty($error)) return;
  file_put_contents(DATA_PATH.'application.log', date('Y-m-d G:i').' '.$error['message'].' in file '.$error['file'].' on line '.$error['line'].PHP_EOL, FILE_APPEND);
}