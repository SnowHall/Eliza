<div class="index">
    <?php if ($message = Flash::get('success')): ?>
      <div class="alert"><?php echo $message ?></div>
    <?php endif; ?>
    <h2>Modules List</h2>
    <form name="modules" method="post" action="<?php echo $this->url('modules/index') ?>">
      <table class="table table-hover table-bordered">
        <thead>
          <td>Enabled</td>
          <td>Module Name</td>
          <td>Version</td>
          <td>Description</td>
          <td>Operations</td>
        </thead>
        <tbody>
          <?php foreach ($modules as $key=>$options): ?>
            <?php
              $moduleClass = $key.'Module';
              $module = new $moduleClass;
              $hasOptions = $module->options() ? true : false;
            ?>
            <tr class="test-item">
              <td style="text-align: center"><input type="checkbox" name="modules[<?php echo $key ?>]" <?php
                echo $options['enabled'] ? ' checked="checked" ' : '' ?> /></td>
              <td class="title span3">
                <?php echo isset($options['info']['name']) ? $options['info']['name'] : ucfirst($key); ?>
              </td>
              <td><?php echo isset($options['info']['version']) ? $options['info']['version'] : '' ?></td>
              <td><?php echo isset($options['info']['description']) ? $options['info']['description'] : '' ?></td>
              <td class="run">
                <?php /*<a title="Help" href="<?php echo $this->url('help/index'); ?>"><span style="font-weight: bold; font-size: 16px; color: #000;" class="icon">?</span>*/ ?>
                <?php if ($hasOptions): ?>
                  <a title="Configuration" onclick="showModuleConfigPopup('<?php echo $key; ?>')" href="javascript:void(0);"><span class="icon-wrench"></span></a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div>
        <input type="submit" value="Save changes" class="btn" />
      </div>
    </form>
</div>
<div class="config-modal"></div>