<div class="task-form">
    <h2>Update task</h2>
    <form id="group-update" name="Task" action="" method="POST">
      <?php if ($errors): ?>
        <div class="errors alert alert-block">
          <?php echo implode('<br />',$errors); ?>
        </div>
      <?php endif; ?>

      <div class="field-row <?php echo $errors['name'] ? 'error' : '' ?>">
        <label class="control-label" for="Task[name]">Task name:<span class="required">*</span></label>
        <input type="text" name="Task[name]" value='<?php echo getFormValue(array($_POST['Task'],$task),'name'); ?>' /><br />
      </div>

      <div class="field-row period <?php echo ($errors['period'] || $errors['starttime']) ? 'error' : '' ?>">
        <label>Time execution:<span class="required">*</span></label>
        <div class="time">Current time is: <?php echo date('Y-m-d H:i',time()); ?></div>
        <input <?php echo ($_POST['Task']['executionType'][0] == 'period' || !isset($_POST['Task']['executionType']) || $task['executionType'] == 'period' || empty($task['executionType'])) ? 'checked="checked"' : '' ?>
          type="radio" value="period" name="Task[executionType][]" class="executionType" />
        <span>Each:</span>
        <input style="width:50px;" type="text" name="Task[period]" value='<?php echo getFormValue(array($_POST['Task'],$task),'period'); ?>' />
        <span>&nbsp;hours</span>
      </div>

      <div class="field-row intime <?php echo $errors['runtime'] ? 'error' : '' ?>">
        <input <?php echo ($_POST['Task']['executionType'][0] == 'intime' || $task['executionType'] == 'intime') ? 'checked="checked"' : '' ?>
          type="radio" value="intime" name="Task[executionType][]" class="executionType" />
        <span class="control-label inline" for="Task[runtime]">Run at:</span>
        <input style="width:80px;" type="text" name="Task[runtime]" value='<?php echo getFormValue(array($_POST['Task'],$task),'runtime','00:00'); ?>' /><br />
        <div class="time-options">
          <div class="option"><input <?php echo ($task['intimePeriod'] == '1' || $_POST['Task']['periodType'][0] == 'daily' || !isset($_POST['Task']['periodType'][0], $task['intimePeriod'])) ? 'checked="checked"' : '' ?>
              type="radio" value="daily" name="Task[periodType][]" /><span> Daily</span></div>
          <div class="option"><input <?php echo ($task['intimePeriod'] == '7' || $_POST['Task']['periodType'][0] == 'weekly') ? 'checked="checked"' : '' ?>
              type="radio" value="weekly" name="Task[periodType][]" /><span> Weekly</span></div>
          <div class="option <?php echo $errors['intimePeriod'] ? 'error' : '' ?>" >
            <input <?php echo ($_POST['Task']['periodType'][0] == 'manual' || (!in_array($task['intimePeriod'],array(1,7)))) && !isset($task['period']) ? 'checked="checked"' : '' ?>
              type="radio" value="manual" name="Task[periodType][]" />
            <span>Each:</span>
            <input style="width:50px;" type="text" name="Task[intimePeriod]" value='<?php echo getFormValue(array($_POST['Task'],$task),'intimePeriod'); ?>' />
            <span>&nbsp;days</span>
          </div>
        </div>
      </div>


      <div class="field-row <?php echo $errors['email'] ? 'error' : '' ?>" >
        <div class="report-check">
          <label>E-mail reports</label>
          <input type="checkbox" <?php echo (isset($_POST['Task']['sendEmail']) || isset($task['sendEmail'])) ? 'checked="checked"' : '' ?>
                 name="Task[sendEmail]" id="sendEmail" value="1" />
          <span>Send reports by email</span>
        </div>
        <input type="text" name="Task[email]" id="emailInput" <?php echo isset($_POST['Task']['sendEmail']) || isset($task['sendEmail']) ? '' : 'disabled="disabled"' ?> value='<?php echo getFormValue(array($_POST['Task'],$task),'email',Config::getValue($config['admin_email'])); ?>' /><br />
      </div>

      <div class="field-row test-assign <?php echo $errors['tests'] ? 'error' : '' ?>">
        <label>Tests:</label>

        <ul>
          <input type="checkbox" id="checkAllTests" />&nbsp;Check all
          <hr />
          <?php foreach($tests as $test): ?>
          <li>
            <input type="checkbox" <?php echo (isset($_POST['Task']['tests']) && in_array($test,$_POST['Task']['tests']) || (isset($task['tests']) && in_array($test,$task['tests']))) ? 'checked="checked"' : '' ?>
                   value="<?php echo $test ?>" name="Task[tests][]" />&nbsp;<?php echo $test ?>
          </li>
          <?php endforeach;  ?>
        </ul>
      </div>

      <?php if ($groups): ?>
        <div class="field-row group-assign <?php echo $errors['groups'] ? 'error' : '' ?>">
          <label>Groups:</label>
          <ul>
            <input type="checkbox" id="checkAllGroups" />&nbsp;Check all
            <hr />
            <?php foreach($groups as $key=>$group): ?>
            <li>
              <input type="checkbox" <?php echo (isset($_POST['Task']['groups']) && in_array($key,$_POST['Task']['groups'])) ? 'checked="checked"' : '' ?>
                    value="<?php echo $key ?>" name="Task[groups][]" />&nbsp;<?php echo $group['name'] ?>
            </li>
            <?php endforeach;  ?>
          </ul>
        </div>
      <?php endif; ?>

      <input type="hidden" name="Task[taskId]" value="<?php echo $taskId ?>" />
      <input type="hidden" name="Task[lastUpdate]" value="<?php echo $task['lastUpdate'] ?>" />

      <div class="field-row note">
        You need run <em>cron.php</em> file for tasks running.
      </div>

      <button type="submit" class="btn"/><?php echo $taskId ? 'Save' : 'Create'; ?></button>
    </form>
</div>