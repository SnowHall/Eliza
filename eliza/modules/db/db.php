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

class dbModule extends Module
{
  // Db connection
  private $db;

  // Current query
  private $query;

  /**
   * Initialize db module
   */
  public function __construct()
  {
    parent::__construct();
    $this->initDb();
  }

  /**
   * Module options for Db connection
   *
   * @return array Module's options
   */
  public function options() {
    return array(
      'username'=>'',
      'password'=>'',
      'host'=>'',
      'dbname'=>'',
    );
  }

  /**
   * Initialize Db connection
   */
  public function initDb()
  {
     try {
       $username = Config::getModuleConfig('db','username');
       $password =  Config::getModuleConfig('db','password');
       $host = Config::getModuleConfig('db','host');
       $dbname = Config::getModuleConfig('db','dbname');

       if ($username && $host) {
          $this->db = new PDO('mysql:host='.$host.';dbname='.$dbname, $username, $password);
          if (!$this->db) {
            die('Connection Db error.'.var_dump(PDO::errorInfo()));
          }
       }
     } catch (PDOException $e) {
       die('Connection Db error: ' . $e->getMessage());
     }
  }

  /**
   * Represents user action: check fields existing in specified table
   *
   * @param string $table Table name
   * @param array $fields Fields names
   * @return type
   */
  public function seeInDb($table, $fields)
  {
    if (!$this->db) {
      $this->test->setResponse('There is no connection with database! Please check Db module settings.','error');
      return;
    }

    // Find table in DB
    $table = preg_replace('/[^a-zA-Z0-9]/','',$table);

    //Find fields in table
    $this->query = $this->db->query("DESCRIBE ".$table);
    if ($this->query) {
      $tableFields = $this->query->fetchAll(PDO::FETCH_COLUMN);
      $cond = '';
      foreach ($fields as $key=>$field) {
        //Check field existing in table
        if (!in_array($key,$tableFields)) {
          $this->test->setResponse('Field "'.$key.'" not exists!','error');
        }
        //Agregate SQL condition for searching row in Db
        $cond .= $key.' = :'.$key.' AND ';
      }
      $cond = trim($cond, ' AND ');
    }
    else {
      $this->test->setResponse('Table "'.$table.'" not exists!','error');
    }

    $this->query = $this->db->prepare("SELECT * FROM ".$table." where ".$cond);

    foreach ($fields as $key=>&$field) {
      $this->query->bindParam(':'.$key, $field);
    }
    $this->query->execute();

    if ($this->query->rowCount()) {
      $this->test->setResponse('I see row in Db','success');
    }
    else {
      $this->test->setResponse('I don\'t see row in Db','error');
    }
  }
}