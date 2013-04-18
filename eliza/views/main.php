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
          <ul class="nav">
            <li class="<?php echo empty($_GET['r']) ? 'active' : '' ?>" ><a href="index.php">Tests</a></li>
            <li class="<?php echo (!empty($_GET['r']) && $_GET['r'] == 'group/index') ? 'active' : '' ?>" ><a href="<?php echo $this->url('group/index'); ?>">Groups</a></li>
            <li class="<?php echo (!empty($_GET['r']) && $_GET['r'] == 'task/index') ? 'active' : '' ?>"><a href="<?php echo $this->url('task/index'); ?>">Tasks</a></li>
            <li class="<?php echo (!empty($_GET['r']) && $_GET['r'] == 'history/index') ? 'active' : '' ?>"><a href="<?php echo $this->url('history/index'); ?>">History</a></li>
            <li class="<?php echo (!empty($_GET['r']) && $_GET['r'] == 'main/reports') ? 'active' : '' ?>"><a href="<?php echo $this->url('main/reports'); ?>">Reports</a></li>
            <li class="<?php echo (!empty($_GET['r']) && $_GET['r'] == 'settings/index') ? 'active' : '' ?>"><a href="<?php echo $this->url('settings/index'); ?>">Settings</a></li>
            <li class="<?php echo (!empty($_GET['r']) && $_GET['r'] == 'main/help') ? 'active' : '' ?>"><a href="<?php echo $this->url('main/help'); ?>">Help</a></li>
          </ul>
        </div>
      </div>
      <div class="content">
        <?php echo $content; ?>
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