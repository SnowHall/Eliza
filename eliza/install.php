<?php

  error_reporting(E_ALL & ~E_NOTICE);
  ini_set('display_errors', '1');

  require_once dirname(__FILE__).'/init.php';

	$app = Eliza::app();
  $app->runController();

	if (!empty($_GET['run'])) {
		Config::setValue('install', false);
		$app->controller->redirect($app->controller->url('test/index'));
	}

	$requiredRights = array(
	  DATA_PATH => true,
	  CONFIG_PATH => true,
	  TEST_PATH => true,
	  TEMPLATE_PATH => false,
	  CONTROLLERS_PATH => false,
	  MODELS_PATH => false,
	  LIBS_PATH => false,
	  MODULES_PATH => false,
	  VENDORS_PATH => false,
	);

	?>

<html>
  <head>
    <meta charset="UTF-8" />
    <title>Eliza - Simple php acceptance and unit testing framework</title>
    <link href="assets/css/main.css" rel="stylesheet" media="screen">
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/main.js"></script>
  </head>
  <body screen_capture_injected="true">
    <div class="container tests-list">
			<div class="navbar">
        <div class="navbar-inner">
          <a class="brand" href="index.php">Eliza</a>
        </div>
      </div>
      <div class="content">
				<div id="test_results">
					<p>Now we check availability of Eliza's directories. Eliza must have access
					to few directories for storing its data.</p>
					<p>Checking directories...</p>
					<?php
						foreach ($requiredRights as $directory=>$availableExpected) {
							$isAvailable = Helpers::isPathAvailable($directory);
							$directoryName = end(explode(DIRECTORY_SEPARATOR, trim($directory,'/')));
							echo $isAvailable ? '"'.$directoryName.'" is Available. ' : '"'.$directoryName.'" is Not Available. ';
							echo ($isAvailable === $availableExpected) ? '<span class="success">OK</span>' :
								'<span class="fail">Failed</span>';
							echo '<br />';
						}
					?>
				</div>
				<a href="install.php?run=1" style="width: 120px; font-size: 18px; height: 20px; margin-top: 20px; padding-top: 5px;" class="btn btn-primary">Run Eliza</a>
				<a href="#" style="width: 120px; font-size: 18px; height: 20px; margin: 0px 0 0 20px; padding-top: 5px;" class="btn btn-primary">Refresh</a>
      </div>
      <div class="footer">
        <div style="margin: 0 auto; margin-top: 20px; width: 500px; text-align: center;">
          <p>Eliza - Open Source Unit and Acceptance testing framework</p>
          <p>Eliza is on <a target="_blank" href="https://github.com/SnowHall/Eliza">GitHub</a></p>
          <p><a href="http://snowhall.com" target="_blank">2013 &copy; SnowHall Ltd</a></p>
        </div>
      </div>
    </div>
  </body>
</html>

