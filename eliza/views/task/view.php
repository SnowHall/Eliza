<div class="task-view">
  <?php if($task): ?>
    <h3><?php echo $task['name']; ?></h3>
    <div>
      <p><span class="field">Name: </span><?php echo $task['name']; ?></p>
    </div>
    <div>
      <p><span class="field">Period: </span><?php echo $task['period']; ?></p>
    </div>
    <div>
      <p><span class="field">Send email: </span><?php echo $task['sendEmail'] ? 'Enabled' : 'Disabled'; ?></p>
    </div>
    <div>
      <p><span class="field">Email: </span><?php echo $task['email']; ?></p>
    </div>
    <?php if(isset($task['tests'])): ?>
      <div>
        <p><span class="field">Assignee tests: </span>
        <?php foreach($task['tests'] as $test): ?>
          <span class="task-test"><?php echo $test ?><a onclick="removeTestFromTask(<?php echo "'".$id."','".$test."'" ?>)" class="close">x</a></span>
        <?php endforeach; ?>
        </p>
      </div>
    <?php endif; ?>
  <?php else: ?>
    <p>Choose task for watching.</p>
  <?php endif; ?>
</div>