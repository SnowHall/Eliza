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

class Report {

  public static function getReportData($period = 'week')
  {
    $history = History::getLog();

    $periodDay = 7;
    switch ($period)
    {
      case 'week':
        $periodDay = 7;
        break;

      case 'month':
        $periodDay = 30;
        break;
    }

    $currentDay = strtotime(date('Y-m-d'));
    $statistic = array();
    $i = 0;
    while($i <= $periodDay) {
      $y = 0;
      $count = 0;
      $error = 0;
      /* Check row in history for specify date */
      foreach ($history as $row) {
        // Extract day from date
        list($day,$time) = explode(' ',$row->date);
        if (strtotime($day) < $currentDay) break;
        if (strtotime($day) == $currentDay) {
           if ($row->error) $error++;
           $count++;
        }
      }
      // If there are errors find error's percent
      if ($error) $y = round(( $error / $count ) * 100);

      $statistic[] = array(
        'x' => $currentDay,
        'y' => $y
      );
      $currentDay = strtotime('-1 day', $currentDay);
      $i++;
    }

    // Add last day with 100%;
    $statistic[] = array(
      'x' => strtotime('+1 day', $currentDay),
      'y' => 100
    );
    $statistic = array_reverse($statistic);

    /*$statistic = array(array('x'=> -1893456000, 'y'=>100),
      array('x'=> -1893456000, 'y' => 25),
      array('x' => -1577923200, 'y' => 29),
      array('x' => -1262304000, 'y' => 34),
      array('x' => -946771200, 'y' => 35),
      array('x' => -631152000, 'y' => 39),
      array('x' => -315619200, 'y' => 44),
      array('x' => 0, 'y' => 49),
      array('x' => 315532800, 'y' => 50),
      array('x' => 631152000, 'y' => 49),
      array('x' => 946684800, 'y' => 53),
      array('x' => 1262304000, 'y' => 55),
      //array('x' => 1262304000, 'y' => 100),

    );*/

    return json_encode($statistic);
  }
}