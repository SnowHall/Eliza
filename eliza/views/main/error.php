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
            <li><a href="index.php?r=group/index">Groups</a></li>
            <li><a href="index.php?r=task/index">Tasks</a></li>
            <li><a href="index.php?r=history/index">History</a></li>
            <li><a href="index.php?r=main/reports">Reports</a></li>
            <li><a href="index.php?r=settings/index">Settings</a></li>
            <li><a href="index.php?r=main/help">Help</a></li>
          </ul>
        </div>
      </div>
      <div class="content">
          <div class="error">
            <?php if ($code == 404 || $code == 500): ?>
              <p><?php echo $message; ?></p>
            <?php else: ?>
              <p>Error while request!</p>
              <?php echo $code.' '.$message.'<br>'; ?>
              <?php echo $_GET['r'].' '.$_GET['ajax'].'  '.$_GET['test']; ?>
            <?php endif;?>
          </div>
      </div>
    </div>
  </body>
</html>