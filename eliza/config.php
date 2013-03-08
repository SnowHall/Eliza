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

return array (
  'host' => 'http://example.com',
  'admin_email' => 'admin@example.com',
  'test_template' => '<?php{n}{n}  $I = new Eliza(); {n}  $I->want("{description}"); {n}{n}?>',
  'default_description' => 'This is new test',
  'index_page' => 'test/index',
  'log' => true,
  'import' => 
  array (
    'error',
    //= Uncomment it to activate DB module
    /*'db' => 
    array (
      'username' => 'root',
      'password' => '',
      'host' => 'localhost',
      'dbname' => 'eliza',
    ),*/
  ),
  'urls' => 
  array (
    0 => 'http://snowhall.com',
  ),
);