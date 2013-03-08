<div class="group">
  <a href="<?php echo url('task/create') ?>">
    <button class="btn">Create Task</button>
  </a>
  <div class="alert"></div>
  <?php if ($tasks): ?>
    <table class="table table-striped">
      <thead>
        <td>Name</td>
        <td>Assigned Tests</td>
        <td>Period</td>
        <td>Email</td>
        <td>Last Run</td>
        <td></td>
      </thead>
      <?php foreach($tasks as $id=>$task): ?>
        <tr>
          <td class="span2"><?php echo $task['name'] ?></td>
          <td class="span3">
            <?php
              if (isset($task['tests'])) {
                echo implode(', ',$task['tests']);
              }
              else if (isset($task['groups'])) {
                foreach ($task['groups'] as $groupKey) {
                  $nameGroups[] = $groups[$groupKey]['name'];
                }
                echo implode(', ',$nameGroups);
              }
              else echo 'Has no added tests or groups' ?>
          </td>
          <td class="span2">
            <?php if ($task['executionType'] == 'intime'): ?>
              <?php echo 'At '.$task['runtime'].' every '.($task['intimePeriod'])/(3600*24).' days' ?>
            <?php else: ?>
              <?php echo $task['period'].'h' ?>
            <?php endif; ?>
          </td>
          <td class="span2">
            <?php if($task['sendEmail']): ?>
              <?php echo $task['email']; ?>
            <?php endif; ?>
          </td>
          <td class="span2">
            <?php echo date('M j \a\t H:i',$task['lastUpdate']); ?>
          </td>
          <td class="buttons">
            <a href="<?php echo url('task/view',array('id'=>$id)) ?>"><span class="icon-search"></span></a>
            <a title="Edit task" href="<?php echo url('task/edit',array('id'=>$id)) ?>"><span class="icon-edit"></span></a>
            <a class="confirm" href="<?php echo url('task/delete',array('id'=>$id)) ?>"><span class="icon-remove"></span></a>
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