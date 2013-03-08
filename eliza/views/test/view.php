<div class="test-view">
  <?php if($test): ?>
    <div class="test-view">
        <div>
          <p><span class="field">Name: </span><?php echo $test['name']; ?></p>
        </div>
        <div>
          <p><span class="field">File: </span><?php echo $test['file']; ?></p>
        </div>
        <div>
          <p><span class="field">Test: </span><br /><?php echo $test['content']; ?></p>
        </div>
    </div>
  <?php else: ?>
    <p>Choose test for watching.</p>
  <?php endif; ?>
</div>