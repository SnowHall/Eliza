<div class="index">
    <div class="alert"></div>
    <h2>Tests List</h2>
    <div class="create-btn"><a class="btn" href="<?php echo $this->url('test/create'); ?>">Create Test</a></div>
    <div style="clear:both;"></div>
    <table class="table table-hover table-bordered">
      <thead>
        <td>Test Name</td>
        <td class="run">Run</td>
        <td>&nbsp;</td>
      </thead>
      <tbody>
        <?php foreach ($tests as $test): ?>
          <tr class="test-item">
            <td class="title span10">
              <?php echo $test; ?>
              <?php if (is_array($queue) && in_array($test,$queue)): ?>
                <span class="queue-progress">(In Progress...)</span>
              <?php endif ?>
            </td>
            <td class="run">
              <?php if (count(Config::getValue('urls')) > 1): ?>
                <a title="Run Now" onclick="getRunTestPopup('<?php echo $test ?>'); return false;" href="#"><span class="icon-play"></span>
                <a title="Add to queue" onclick="getAddToQueuePopup('<?php echo $test; ?>')" href="javascript:void(0);"><span class="icon-time"></span></a>
             <?php else: ?>
                <a title="Run Now" onclick="run_test('<?php echo $this->url('test/run',array('ajax'=>1,'test'=>$test)); ?>'); return false;" href="#"><span class="icon-play"></span>
                <a title="Add to queue" onclick="addTestToQueue('<?php echo $this->url('test/addqueue',array('ajax'=>1,'test'=>$test)); ?>')" href="javascript:void(0);"><span class="icon-time"></span></a>
              <?php endif; ?>
              <a title="Debug" href="<?php echo $this->url('test/debug',array('test'=>$test)) ?>"><span class="icon-wrench"></span></a>
            </td>
            <td class="buttons">
              <a title="View test" href="<?php echo $this->url('test/view',array('test'=>$test)) ?>"><span class="icon-search"></span></a>
              <a title="Delete test" href="<?php echo $this->url('test/delete',array('test'=>$test)) ?>"><span class="icon-remove"></span></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div id="test_results"></div>
</div>
<div class="add-test-modal"></div>