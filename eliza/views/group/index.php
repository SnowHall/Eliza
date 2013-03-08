<div class="group">
  <a href="<?php echo url('group/create') ?>">
    <button class="btn">Create Group</button>
  </a>
  <div class="alert"></div>
  <?php if ($groups): ?>
    <table class="table table-striped">
      <thead>
        <td>Name</td>
        <td>Assigned Tests</td>
        <td></td>
        <td>Run</td>
        <td></td>
      </thead>
      <?php foreach($groups as $id=>$group): ?>
        <tr>
          <td class="span3"><?php echo $group['name'] ?></td>
          <td class="span5">
            <?php echo isset($group['tests']) ? implode(', ',$group['tests']) : 'No tests' ?>
          </td>
          <td class="span2">
            <a href="javascript:void(0);" onclick="addTestPopup('<?php echo $id; ?>')">Add test</a>
          </td>
          <td class="run span1">
            <a onclick="run_test('<?php echo url('test/run',array('ajax'=>'1','group'=>$id)) ?>'); return false;" href="#">
            <span class="icon-play"></span>
          </td>
          <td class="buttons">
            <a href="<?php echo url('group/view',array('id'=>$id)) ?>"><span class="icon-search"></span></a>
            <a class="confirm" href="<?php echo url('group/delete',array('id'=>$id)) ?>"><span class="icon-remove"></span></a>
          </td>

        </tr>
      <?php endforeach; ?>
    </table>
  <?php else: ?>
    <p>There are not added groups.</p>
  <?php endif; ?>
  <div id="test_results"></div>
</div>
<div class="add-test-modal"></div>