<html>
  <head>
    <meta charset="UTF-8" />
    <title>Eliza - acceptance testing</title>
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
            <li><a href="index.php">Tests</a></li>
            <li><a href="<?php echo url('group/index'); ?>">Groups</a></li>
            <li><a href="<?php echo url('task/index'); ?>">Tasks</a></li>
            <li><a href="<?php echo url('history/index'); ?>">History</a></li>
            <li><a href="<?php echo url('main/reports'); ?>">Reports</a></li>
            <li><a href="<?php echo url('settings/index'); ?>">Settings</a></li>
            <li><a href="<?php echo url('main/help'); ?>">Help</a></li>
          </ul>
        </div>
      </div>
      <div class="content">
        <?php echo $content; ?>
      </div>
    </div>
  </body>
</html>