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

date_default_timezone_set("America/Denver");
ini_set('memory_limit', '256M');

define('ROOT_PATH',  dirname(__FILE__).'/');
define('TEST_PATH',dirname(__FILE__).'/tests/');
define('TEMPLATE_PATH',dirname(__FILE__).'/views/');
define('DATA_PATH',dirname(__FILE__).'/data/');
define('MODELS_PATH',dirname(__FILE__).'/models/');
define('LIBS_PATH',dirname(__FILE__).'/libs/');
define('MODULES_PATH',dirname(__FILE__).'/modules/');

define('LOG_FILE',DATA_PATH.'tests.log');
define('TASKS_FILE',DATA_PATH.'tasks.json');
define('GROUPS_FILE',DATA_PATH.'groups.json');
define('QUEUE_FILE',DATA_PATH.'queue.json');

require_once 'libs/functions.php';
require_once 'libs/webclient.php';
require_once 'libs/eliza_core.php';
require_once 'libs/geshi/geshi.php';

session_start();

spl_autoload_register('autoload');
register_shutdown_function('fatalErrorHandle');

$config = require_once 'config.php';
$config['groups'] = Group::getGroups();
$config['tasks'] = Task::getTasks();

$app = Eliza::test();





