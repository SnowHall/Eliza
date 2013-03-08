<div class="config-index">
  <?php if ($message = getFlash('success')): ?>
    <div class="alert alert-success" style="display: block;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <?php echo $message ?>
    </div>
  <?php endif; ?>
  <form action="" method="post" name="ConfigForm" id="config-form">
    <div class="option" id="urls">
      <label>Test URLs</label>
      <p><a href="javascript:void(0);" onclick="addUrlInput()"><span class="icon-plus"></span> Add url</a></p>
      <table>
        <?php if (isset($config['urls']) && !empty($config['urls'])): ?>
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
              <input type="text" name="urls[]" value="<?php echo isset($config['host']) ? $config['host'] : '' ?>" />
            </td>
          </tr>
        <?php endif; ?>
      </table>
    </div>
    <div class="option">
      <label>Admin Email</label>
      <input type="text" name="ConfigForm[admin_email]"
             value="<?php echo getFormValue($_POST['ConfigForm'],'admin_email',$config['admin_email']) ?>" />
    </div>
    <button type="submit" name="ConfigForm[ok]" class="btn">Save</button>
  </form>
</div>