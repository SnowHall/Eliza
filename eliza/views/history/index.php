<div class="history">
  <a href="<?php echo url('history/clear') ?>" class="confirm">
    <button class="btn">Clear History</button>
  </a>
  <?php if ($history): ?>
    <table class="table table-striped">
      <thead>
        <td>
          <div class="entry">
            <span class="result result-title">Status</span>
            <span class="title">Name</span>
            <span class="date">Execution Date</span>
            <span class="exec-time">Execution Time</span>
            <span></span>
          </div>
        </td>
      </thead>
      <?php foreach($history as $key=>$entry): ?>
        <tr>
          <td>
            <div class="entry">
              <span class="result result-test <?php echo $entry->error ? 'error' : 'success' ?>"><?php
                echo $entry->error ? 'Failed' : 'Success' ?>
              </span>
              <span class="title"><a href="<?php echo url('test/view',array('test'=>'auth')) ?>"><?php echo ucfirst($entry->name) ?></a></span>
              <span class="date"><?php echo date('j M Y, H:i',strtotime($entry->date)) ?></span>
              <span class="date"><?php echo round($entry->executionTime,2).'s' ?></span>
              <span><a class="more" href="javascript:void(0);">Show More</a></span>
              <div class="more-info-<?php echo $key ?>">
                 <?php //echo History::markResult(nl2br($entry->procces)); ?>
              </div>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php else: ?>
    <p>History is empty.</p>
  <?php endif; ?>
</div>