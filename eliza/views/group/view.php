<div class="group-view">
  <h3><?php echo $group['name']; ?></h3>
  <?php if($group): ?>
    <div class="group-view">
        <div>
          <p><span class="field">Name: </span><?php echo $group['name']; ?></p>
        </div>
        <?php if(isset($group['tests'])): ?>
          <div>
            <p><span class="field">Assignee tests: </span>
            <?php foreach($group['tests'] as $test): ?>
              <span class="group-test"><?php echo $test ?><a onclick="removeTestFromGroup(<?php echo "'".$id."','".$test."'" ?>)" class="close">x</a></span>
            <?php endforeach; ?>
            </p>
          </div>
        <?php endif; ?>
    </div>
  <?php else: ?>
    <p>Choose group for watching.</p>
  <?php endif; ?>
</div>