<div class="modal-head"><span>Add test</span><a class="close" onclick="$('.add-test-modal').hide();">x</a></div>
<div class="modal-body">
  <?php if ($tests): ?>
    <form action="<?php echo $this->url('group/addtest') ?>" name="addTest" method="POST">
      <div>
        <select name="test-name">
          <?php foreach($tests as $test): ?>
            <option value="<?php echo $test ?>"><?php echo $test ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <input type="hidden" name="group" id="group-id" value="<?php echo $_POST['group'] ?>" />
      </div>
      <div>
        <button type="submit" class="btn">Add</button>
      </div>
    </form>
  <?php else: ?>
    <p>You add all tests in this group.</p>
  <?php endif; ?>
</div>