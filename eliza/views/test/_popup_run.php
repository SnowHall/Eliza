<div class="modal-head"><span>Choose Url</span><a class="close" onclick="$('.add-test-modal').hide();">x</a></div>
<div class="modal-body">
  <div>
    <select id="testUrlSelect">
      <?php foreach($urls as $url): ?>
        <option value="<?php echo $url ?>"><?php echo $url ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div>
    <a class="btn" href="javascript:void(0);" onclick="run_test('<?php echo $this->url('test/run',array('ajax'=>1,'test'=>$test)); ?>'); $('.add-test-modal').hide(); return false;" >Run</a>
  </div>
</div>