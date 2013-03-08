<div class="error">
  <?php if ($code == 404 || $code == 500): ?>
    <p><?php echo $message; ?></p>
  <?php else: ?>
    <p>Error while request!</p>
    <?php echo $_GET['r'].' '.$_GET['ajax'].'  '.$_GET['test']; ?>
  <?php endif;?>
</div>