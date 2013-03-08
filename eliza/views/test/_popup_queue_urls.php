<div class="modal-head"><span>Choose Url</span><a class="close" onclick="$('.add-test-modal').hide();">x</a></div>
<div class="modal-body">
    <select id="testUrlSelect">
      <?php foreach($urls as $url): ?>
        <option value="<?php echo $url ?>"><?php echo $url ?></option>
      <?php endforeach; ?>
    </select>
    <a class="btn" href="javascript:void(0);" onclick="addTestToQueue('<?php echo url('test/addqueue',array('ajax'=>1,'test'=>$test)); ?>'); $('.add-test-modal').hide(); return false;" >Add</a>
</div>