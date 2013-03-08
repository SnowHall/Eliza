<div class="test-create">
    <h2>Create test</h2>
    <form id="test-create" name="test" action="" method="POST">
      <div class="control-group <?php echo $errors['test-name'] ? 'error' : '' ?>">
        <label class="control-label" for="Test[test-name]">Test name:<span class="required">*</span></label>
        <div class="controls">
          <input type="text" name="Test[test-name]" value='<?php echo $_POST['Test']['test-name'] ? htmlspecialchars($_POST['Test']['test-name']) : '' ?>' /><br />
          <?php if ($errors['test-name']): ?>
            <span class="help-inline">Please fill the field.</span>
          <?php endif; ?>
        </div>
      </div>
      <div>
        <label for="Test[test-description]">Test description:</label>
        <div>
          <textarea name="Test[test-description]"><?php echo $_POST['Test']['test-description'] ? htmlspecialchars($_POST['Test']['test-description']) : '' ?></textarea><br />
        </div>
      </div>
      <button type="submit" class="btn"/>Create</button>
    </form>
</div>