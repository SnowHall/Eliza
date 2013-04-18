<div class="modal-head"><span><?php echo $module['info']['name'] ? $module['info']['name'] : ucfirst($moduleId); ?> :: Configuration</span><a class="close" onclick="$('.config-modal').hide();">x</a></div>
<div class="modal-body">
  <?php $form = new Form('module_config','modules/saveconfig'); ?>
  <?php echo $form->begin() ?>
  <?php foreach ($options as $name=>$option): ?>
    <?php
      $value = null;
      if (isset($module['options'][$name])) $value = $module['options'][$name];
      echo $form->getElement('Options['.$name.']','text',$value);
    ?>
  <?php endforeach; ?>
  <?php echo $form->getElement('isLocal','checkbox','1',array('label'=>'Local configuration','checked'=>$hasLocal ? 'checked' : null))  ?>
  <?php echo $form->getElement('moduleId','hidden',$moduleId) ?>
  <div style="margin-top: 10px;">
    <?php echo $form->getElement('save','submit','Save',array('class'=>'btn')) ?>
  </div>
  <?php echo $form->end() ?>
</div>