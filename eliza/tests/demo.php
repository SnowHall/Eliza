<?php
  /**
   * Test demo example.
   *
   * Emulates user's actions scenario:
   * - go to snowhall.com site
   * - check openning the main page. Condition: on page exists phrase "Web Development Service"
   * - go to link "Web Development Service";
   * - check openning the "Web Development Service" page. Condition: on page exists phrase "MVC frameworks"
  */

  // Initialize test
  $I = Eliza::test();

  // Specify short definition for test
  $I->want('Test demo site');

  // Go to the page from which to start testing
  $I->go('http://snowhall.com');

  // Verifying the opening pages by checking the information contained in it
  $I->see('Web Development Service');

  // Go to link
  $I->click('Web Development Service');

  // Verifying the opening pages by checking the information contained in it
  $I->see('MVC frameworks');