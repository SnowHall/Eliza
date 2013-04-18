<?php $form = new Form('Task','',array('id'=>'task-create')); ?>
<?php echo $form->begin() ?>
  <?php if (!empty($errors)): ?>
    <div class="errors alert alert-block">
      <?php echo implode('<br />',$errors); ?>
    </div>
  <?php endif; ?>

  <div class="main-options block">
    <h3>Main Settings</h3>

    <div class="field-row <?php echo !empty($errors['name']) ? 'error' : '' ?>">
      <?php $valueName = isset($task['name']) ? $task['name'] : ''  ?>
      <?php echo $form->getTextField('Task[name]',$valueName,array('label'=>'Task name:','required'=>true)); ?>
    </div>

    <div class="field-row <?php echo !empty($errors['email']) ? 'error' : '' ?>" >
      <div class="report-check">
        <?php $sendEmail = isset($task['sendEmail']) ? $task['sendEmail'] : null  ?>
        <?php echo $form->getCheckbox('Task[sendEmail]', '1', array(
          'id'=>'sendEmail',
          'label'=>'E-mail reports',
          'checked'=>$sendEmail,
        )); ?>
        <?php /*<input type="checkbox" <?php echo $valueEmail || isset($_POST['Task']['sendEmail']) ? 'checked="checked"' : '' ?>
               name="Task[sendEmail]" id="sendEmail" value="1" /> */?>
        <span>Send reports by email</span>
      </div>
      <?php $valueEmail = isset($task['email']) ? $task['email'] : Config::getValue('admin_email')  ?>
      <?php echo $form->getTextField('Task[email]',$valueEmail,array(
        'label'=>false,
        'id'=>'emailInput',
        'disabled'=>!isset($_POST['Task']['sendEmail']) ? "disabled" : null
      )); ?>
      <div class="field-note">You can add few emails divided by ";"</div>
    </div>
  </div>

  <div class="sheduler block">
      <h3>Sheduler</h3>
      <div class="periods">
        <?php $valuePeriods = isset($task['periods']) ? $task['periods'] : 'daily' ?>
        <?php echo $form->getRadioButtonList('Task[periods]', $valuePeriods, array('hourly'=>'Hourly','daily'=>'Daily','weekly'=>'Weekly','monthly'=>'Monthly')); ?>
      </div>
      <div id="hourly" class="run-type">
        <span>Run every </span>
          <?php $valueHourlyHours = isset($task['hourly_hours']) ? $task['hourly_hours'] : '1' ?>
          <?php echo $form->getTextField('Task[hourly_hours]',$valueHourlyHours,array(
            'label'=>false,
          )); ?>
        <span> hours</span><br />
        <span>Run In </span>
          <?php $valueHourlyMinutes = isset($task['hourly_minutes']) ? $task['hourly_minutes'] : '2' ?>
          <?php echo $form->getDropdownList('Task[hourly_minutes]', $valueHourlyMinutes, Date::generateMinutesList()); ?>
        <span> mins</span>
      </div>
      <div id="daily" style="display: block;" class="run-type">
         <?php $valueDailyDays = isset($task['daily_days']) ? unserialize($task['daily_days']) : array('Sun','Mon','Tue','Wed','Thu','Fri','Sat'); ?>
         <?php echo $form->getCheckBoxList('Task[daily_days]', $valueDailyDays, Date::getWeekDaysList(), array(
            'position'=>'horizontal',
          )) ?>
        <span>Start Time: </span>
        <?php $valueDailyRuntime = isset($task['daily_runtime']) ? $task['daily_runtime'] : '00:00' ?>
        <?php echo $form->getTextField('Task[daily_runtime]',$valueDailyRuntime,array(
            'label'=>false,
            'style'=>'width:80px;'
        )); ?>
      </div>
      <div id="weekly" class="run-type">
        <?php $valueWeeklyDays = isset($task['weekly_days']) ? $task['weekly_days'] : 'Mon' ?>
        <?php echo $form->getRadioButtonList('Task[weekly_days]', $valueWeeklyDays, Date::getWeekDaysList(), array(
          'position'=>'horizontal',
        )) ?>
        <span>Start Time: </span>
        <?php $valueWeeklyRuntime = isset($task['weekly_runtime']) ? $task['weekly_runtime'] : '00:00' ?>
        <?php echo $form->getTextField('Task[weekly_runtime]',$valueWeeklyRuntime,array(
            'label'=>false,
            'style'=>'width: 80px;'
        )); ?>
      </div>
      <div id="monthly" class="run-type">
        <span>Day: </span>
        <?php $valueMonthlyDays = isset($task['monthly_days']) ? $task['monthly_days'] : '1'  ?>
        <?php echo $form->getDropdownList('Task[monthly_days]', $valueMonthlyDays, Date::generateDaysList()); ?>
        <br />
        <span>Start Time: </span>
        <?php $valueMonthlyRuntime = isset($task['monthly_runtime']) ? $task['monthly_runtime'] : '00:00' ?>
        <?php echo $form->getTextField('Task[monthly_runtime]',$valueMonthlyRuntime,array(
            'label'=>false,
            'style'=>'width: 80px;'
        )); ?>
      </div>
 </div>

 <div class="task-tests block">
    <h3>Assigned tests</h3>
    <div class="field-row test-assign <?php echo !empty($errors['tests']) ? 'error' : '' ?>">
        <input type="checkbox" id="checkAllTests" />&nbsp;Check all<hr />
        <?php $valueTests = isset($task['tests']) ? $task['tests'] : null ?>
        <?php echo $form->getCheckBoxList('Task[tests]', $valueTests, Test::getTestsCheckboxList($tests)) ?>
    </div>
 </div>

 <?php if ($groups): ?>
    <div class="task-tests block">
      <h3>Assigned groups</h3>
      <div class="field-row group-assign <?php echo !empty($errors['groups']) ? 'error' : '' ?>">
        <input type="checkbox" id="checkAllGroups" />&nbsp;Check all<hr />
        <?php $valueGroups = isset($task['groups']) ? $task['groups'] : null ?>
        <?php echo $form->getCheckBoxList('Task[groups]', $valueGroups, Group::getGroupsCheckboxList($groups)) ?>
      </div>
    </div>
 <?php endif; ?>

  <?php $valueTaskId = !empty($taskId) ? $taskId : '' ?>
  <?php echo $form->getHiddenField('Task[taskId]', $valueTaskId) ?>

  <?php $valueCreated = isset($task['created']) ? base64_encode($task['created']) : ''; ?>
  <?php echo $form->getHiddenField('Task[created]', $valueCreated) ?>

  <?php $valueLastUpdate = isset($task['lastUpdate']) ? base64_encode($task['lastUpdate']) : ''; ?>
  <?php echo $form->getHiddenField('Task[lastUpdate]', $valueLastUpdate) ?>

  <div class="field-row note">
    You need run <em>cron.php</em> file for tasks running.
  </div>

  <?php echo $form->getButton(!empty($taskId) ? 'Save' : 'Create', array('class'=>'btn', 'type'=>'submit')) ?>
<?php echo $form->end() ?>