<div class="help">
  <p>Eliza functions:</p>
  <table>
    <thead>
      <td>Function</td>
      <td>Description</td>
    </thead>
    <tr>
      <td>Acceptance testing</td>
      <td>Writing Acceptance tests for project in user-friendly form.</td>
    </tr>
    <tr>
      <td>Unit tests</td>
      <td>Writing Unit tests for project in PHPUnit-like format (PHPUnit not required!).</td>
    </tr>
    <tr>
      <td>Test History</td>
      <td>Writing and researching test's running history.</td>
    </tr>
    <tr>
      <td>Test Groups</td>
      <td>Associating tests into groups and working with these groups: run, tasks, etc.</td>
    </tr>
    <tr>
      <td>Test Queue</td>
      <td>Adding tests into running queue.</td>
    </tr>
    <tr>
      <td>Test Debug</td>
      <td>Flexible mechanism for debugging tests.</td>
    </tr>
    <tr>
      <td>Test Tasks</td>
      <td>The "tasks" - run tests, and groups with a set of parameters in a certain time.</td>
    </tr>
    <tr>
      <td>Notifications</td>
      <td>Notify the administrator about important events by Email.</td>
    </tr>
  </table>
  <hr />
  <h2>Documentation</h2>
  <h3>Introduction</h3>
    <p>Eliza - acceptance and unit testing system, based on simple user-friendly commands.
    Eliza may be used for BDD and TDD development or only
    for acceptance and unit testing. Eliza allows test your system in whole.</p>
    <p>Eliza support all type of web interfaces, excluding JavaScript-rich and AJAX applications.</p>
  <h3>Requirements</h3>
  <ul>
    <li>PHP version 5.2 or higher.</li>
  </ul>
  <h3>Installation</h3>
    <p>In the first you should download full package from <a href="http://elizatesting.com/main/download">here</a>.</p>
    <p>Then unpack this archive into root folder of your site.</p>
    <p>Set unix user's rights on this folder 755 (chmod 0755).</p>
    <p>Now Eliza is ready for test writing!</p>
  <h3>Usage</h3>
    <h4>Acceptance tests</h4>
    <p>Now we can write simple test for acceptance testing of user's login. This is simple test code:</p>
    <?php
      $geshi = new GeSHi('
        $I = new AcceptanceTest();
        $I->want(\'Check Authorization\');
        $I->go(\'/\');
        $I->fill(array(\'Login\'=>\'demo\',\'Password\'=>\'qwerty\'));
        $I->click(\'Login\');
        $I->see(\'Congratulation!\');', 'php'
      );
      echo $geshi->parse_code();
    ?>
    <p>Let's see what this example doing. In the first we create new Acceptance test instance:</p>
    <?php $geshi->set_source('$I = new AcceptanceTest();') ?>
    <p><?php echo $geshi->parse_code() ?></p>
    <p>Now we can write test description for our test. It's may be useful for test's definition in the test's execte results.</p>
    <?php $geshi->set_source('$I->want(\'Check Authorization\');') ?>
    <p><?php echo $geshi->parse_code() ?></p>
    <p>How you can see, all commands has a people-friendly form, that allows us match test's commands and phrases fron real life.</p>
    <p>Let's take first action - go to the particular page:</p>
    <?php $geshi->set_source('$I->go(\'/\');') ?>
    <p><?php echo $geshi->parse_code() ?></p>
    <p>Then fill needed fields in login form, usually this are "login" and "password". We can pass array of form's fields into the function:</p>
    <?php $geshi->set_source('$I->fill(array(\'Login\'=>\'demo\',\'Password\'=>\'qwerty\'));') ?>
    <p><?php echo $geshi->parse_code() ?></p>
    <p>Now we should "click" on submit button for submitting the form. In our example, submit button has value "Login". Use this value for description our action:</p>
    <?php $geshi->set_source('$I->click(\'Login\');') ?>
    <p><?php echo $geshi->parse_code() ?></p>
    <p>And finally we should check result of perfoming our actions. What you see, when you log in in a site? It may be you username or any message. In our case this is the message "Congratulations!":</p>
    <?php $geshi->set_source('$I->see(\'Congratulation!\');') ?>
    <p><?php echo $geshi->parse_code() ?></p>
    <p>Result of test executing is presented below:</p>
    <p><img src="assets/img/test_auth.jpg" /></p>
    <p>You can see that test's commands syntax equals to PHP syntax. This allows you keep general code style.</p>
    <p>You can find test example in ELIZA_ROOT/tests folders with name <em>demo_acceptance.php</em></p>

  <h4>Unit tests</h4>
  <p>Let's write simple Unit tests. This is Unit test for simple class code:</p>
  <?php
      $geshi = new GeSHi('
        require_once DATA_PATH.\'SimpleClass.php\';

        class SimpleClassTest extends UnitTest
        {
          function testSetName() {
              $class = new SimpleClass();
              $class->setName(\'Ricardo\');
              $this->assertEquals(\'Ricardo\', $class->getName());
          }

          function testCheckName() {
            $class = new SimpleClass();
            $class->setName(\'Edward\');
            $this->assertTrue($class->checkName());
          }

          function testSaveLog() {
            $class = new SimpleClass();
            $class->saveLog(\'Log Info\');
            $this->assertFileExists(DATA_PATH.\'unit_test.log\');
          }
        }','php'
      );
      echo $geshi->parse_code();
    ?>
  <p>If you are already familiar with PHPUnit, this code will look familiar.
    Let's take an example more.</p>
  <p>In the first we should include testing files to the test. We can use for this
  include_once or require_once PHP commands. We will be test class called "SimpleClass".
  This class you can find in Eliza's data folder.</p>
  <?php $geshi->set_source('require_once DATA_PATH.\'SimpleClass.php\';') ?>
  <p><?php echo $geshi->parse_code() ?></p>

  <p>Then we need to create Unit test class extended UnitTest class. You may call
  it how you want, but we reccomend call it as [Testing class name]Test, where
  [Testing class name] - name of class that you want to test.</p>
  <?php $geshi->set_source('class SimpleClassTest extends UnitTest') ?>
  <p><?php echo $geshi->parse_code() ?></p>

  <p>Class test contains one or more methods that testing one or more methods of your class.
  Test is considered passed if all this methods are passed.</p>
  <p>Lets see on method <em>testSetName</em>. In this method we want to test
  correct assign the $name SimpleClass property. For this we can use setName()
  and getName() SimpleClass methods.</p>
  <p>Create instanse of SimpleClass for its testing.</p>
  <?php $geshi->set_source('$class = new SimpleClass();') ?>
  <p><?php echo $geshi->parse_code() ?></p>

  <p>Call setName() method of our testing class for initialize $name property.</p>
  <?php $geshi->set_source('$class->setName(\'Ricardo\');') ?>
  <p><?php echo $geshi->parse_code() ?></p>

  <p>Eliza supports some functions for the Unit testing. You can find them
    <a href="<?php echo $this->url('main/commands'); ?>">here</a>. Function assertEquals
    compares two value for equal. Let's compare expected $name property value and current
    $name property value.</p>
  <?php $geshi->set_source('$this->assertEquals(\'Ricardo\', $class->getName());') ?>
  <p><?php echo $geshi->parse_code() ?></p>
  <p>Result of test executing is presented below:</p>
  <p><img src="assets/img/unit_test.jpg" /></p>
  <p>You can find test example in ELIZA_ROOT/tests folders with name <em>demo_unit.php</em></p>

  <h5>See more:</h5>
  <ul>
    <li><a href="<?php echo $this->url('main/modules'); ?>">Methods list</a></li>
  </ul>
</div>