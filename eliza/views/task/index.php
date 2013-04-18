<div class="group">
  <a href="<?php echo $this->url('task/create') ?>">
    <button class="btn">Create Task</button>
  </a>
  <div class="alert"></div>
  <?php if ($tasks): ?>
    <table class="table table-striped">
      <thead>
        <td>Name</td>
        <td>Assigned Tests</td>
        <td>Assigned Groups</td>
        <td>Period</td>
        <td>Email</td>
        <td>Last Run</td>
        <td></td>
      </thead>
      <?php foreach($tasks as $id=>$task): ?>
        <tr>
          <td class="span2"><?php echo $task['name'] ?></td>
          <td class="span2">
            <?php
              if (isset($task['tests'])) {
                echo implode(', ',$task['tests']);
              }
              else echo '&ndash;';
            ?>
          </td>
          <td class="span2">
            <?php
              if (!empty($task['groups'])) {
                foreach ($task['groups'] as $groupKey) {
                  $nameGroups[] = $groups[$groupKey]['name'];
                }
                echo implode(', ',$nameGroups);
              }
              else echo '&ndash;';
            ?>
          </td>
          <td class="span2">
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
            ?>
          </td>
          <td class="span2">
            <?php
              echo !empty($task['sendEmail']) ? str_replace(';', '<br />', $task['email']) : '&ndash;';
            ?>
          </td>
          <td class="span2">
            <?php echo $task['lastUpdate'] == 'not run' ? 'not run yet' : date('M j \a\t H:i',$task['lastUpdate']); ?>
          </td>
          <td class="buttons">
            <a href="<?php echo $this->url('task/view',array('id'=>$id)) ?>"><span class="icon-search"></span></a>
            <a title="Edit task" href="<?php echo $this->url('task/edit',array('id'=>$id)) ?>"><span class="icon-edit"></span></a>
            <a class="confirm" href="<?php echo $this->url('task/delete',array('id'=>$id)) ?>"><span class="icon-remove"></span></a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php else: ?>
    <p>There are not added tasks.</p>
  <?php endif; ?>
  <div id="test_results"></div>
</div>
<div class="add-test-modal"></div>