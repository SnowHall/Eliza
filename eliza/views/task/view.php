<div class="task-view">
  <?php if($task): ?>
    <h3><?php echo $task['name']; ?></h3>
    <div>
      <p><span class="field">Name: </span><?php echo $task['name']; ?></p>
    </div>
    <div>
      <p><span class="field">Period: </span>
		  <?php
					switch ($task['periods'])
					{
						case 'hourly':
							$period = 'Every '.$task['hourly_hours'].' hours at '.$task['hourly_minutes'].' minutes';
							break;

						case 'daily':
							$period = 'Every '.implode(', ',unserialize($task['daily_days'])).' at '.$task['daily_runtime'];
							break;

						case 'weekly':
							$period = 'Every week in '.$task['weekly_days'].' at '.$task['weekly_runtime'];
							break;

						case 'monthly':
							$period = 'Every month in '.$task['monthly_days'].' day at '.$task['monthly_runtime'];
							break;
					}
					echo $period;
       ?></p>
    </div>
		<div>
			<p><span class="field">Last Run: </span><?php echo $task['lastUpdate'] == 'not run' ? 'not run yet' : date('M j \a\t H:i',$task['lastUpdate']); ?></p>
	  </div>
    <div>
      <p><span class="field">Send Email: </span><?php echo $task['sendEmail'] ? 'Enabled' : 'Disabled'; ?></p>
    </div>
    <div>
      <p><span class="field">Email: </span><?php echo !empty($task['sendEmail']) ? str_replace(';', '; ', $task['email']) : '&ndash;'; ?></p>
    </div>
		<div>
      <p><span class="field">Assigned Tests: </span>
			<?php if(!empty($task['tests'])): ?>
				<?php foreach($task['tests'] as $test): ?>
					<span class="task-test"><?php echo $test ?><a onclick="removeTestFromTask(<?php echo "'".$id."','".$test."'" ?>)" class="close">x</a></span>
				<?php endforeach; ?>
			<?php else: ?>
				&ndash;
		  <?php endif; ?>
			</p>
	  </div>
		<div style="margin-top: 30px;">
		<p><span class="field">Assigned Groups: </span>
			<?php if (!empty($task['groups'])): ?>

				<?php foreach ($task['groups'] as $groupKey): ?>
					<span class="task-test"><?php echo $groups[$groupKey]['name'] ?><a onclick="removeGroupFromTask(<?php echo "'".$id."','".$groupKey."'" ?>)" class="close">x</a></span>
				<?php endforeach; ?>

			<?php else: ?>
				&ndash;
			<?php endif; ?>
	  </p>
	  </div>
  <?php else: ?>
    <p>Choose task for watching.</p>
  <?php endif; ?>
</div>