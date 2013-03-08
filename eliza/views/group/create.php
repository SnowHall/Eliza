<div class="group-create">
    <h2>Create group</h2>
    <form id="group-create" name="Group" action="" method="POST">
      <div class="control-group <?php echo $errors['name'] ? 'error' : '' ?>">
        <label class="control-label" for="Group[name]">Group name:<span class="required">*</span></label>
        <div class="controls">
          <input type="text" name="Group[name]" value='<?php echo $_POST['Group']['name'] ? htmlspecialchars($_POST['Group']['name']) : '' ?>' /><br />
          <?php if ($errors['name']): ?>
            <span class="help-inline">Please fill the field.</span>
          <?php endif; ?>
        </div>
      </div>

      <button type="submit" class="btn"/>Create</button>
    </form>
</div>