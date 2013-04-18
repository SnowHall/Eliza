<div class="test-create">
    <h2>Debug test "<?php echo $test ?>"</h2>
    <div class="alert"></div>
    <a onclick="run_test('<?php echo $this->url('test/run',array('ajax'=>1,'test'=>$test,'debug'=>true)); ?>'); return false;" href="#">
       <button class="btn">Run test</button>
    </a>
    <div id="test_results"></div>
</div>