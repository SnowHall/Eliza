<div class="config-index">
  <?php if ($message = Flash::get('success')): ?>
    <div class="alert alert-success" style="display: block;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <?php echo $message ?>
    </div>
  <?php endif; ?>
  <?php $modules = Module::getAvailableModules(); ?>
  <?php echo $this->render('modules/index',array('modules'=>$modules)); ?>
  <h2>Common settings</h2>
  <form action="" method="post" name="ConfigForm" id="config-form">
    <div class="option" id="urls">
      <label>Test URLs</label>
      <p><a href="javascript:void(0);" onclick="addUrlInput()"><span class="icon-plus"></span> Add url</a></p>
      <table>
        <?php if ($config['urls']): ?>
          <?php foreach($config['urls'] as $key=>$url): ?>
            <tr>
              <td class="radio"><input type="radio" name="defaultUrl" value="<?php echo $key ?>" <?php echo $key == '0' ? 'checked="checked"' : ''?> /></td>
              <td>
                <input type="text" name="urls[]" value="<?php echo $url ?>" />
                <?php if ($key != '0'): ?>
                  <a class="urlRemove" href="javascript:void(0);">Remove</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td class="radio"><input type="radio" name="defaultUrl" value="0" checked="checked" /></td>
            <td>
              <input type="text" name="urls[]" value="<?php echo Config::getValue('host',''); ?>" />
            </td>
          </tr>
        <?php endif; ?>
      </table>
    </div>
    <div class="option">
      <label>Admin Email</label>
      <input type="text" name="ConfigForm[admin_email]"
             value="<?php echo Helpers::getFormValue($_POST['ConfigForm'],'admin_email',Config::getValue('admin_email')) ?>" />
    </div>
    <div class="option">
      <label>History store time</label>
      <input style="width: 40px;" type="text" name="ConfigForm[history_store_time]"
             value="<?php echo Helpers::getFormValue($_POST['ConfigForm'],'history_store_time',Config::getValue('history_store_time')) ?>" /> days
      <div class="field-note">Empty value - store all history, 0 - not store history.</div>
    </div>
    <div class="option">
      <label>Timezone</label>
      <select type="text" name="ConfigForm[timezone]">
        <?php foreach($timezones as $key=>$name): ?>
          <option value="<?php echo $key ?>" <?php echo $key == Helpers::getFormValue($_POST['ConfigForm'],'timezone',Config::getValue('timezone')) ? 'selected="selected"' : '' ?> ><?php echo $name ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" name="ConfigForm[ok]" class="btn">Save</button>
  </form>
</div>