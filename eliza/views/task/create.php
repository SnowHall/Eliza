<div class="task-form">
    <h2>Create task</h2>
    <?php
      echo $this->render('task/_form', array(
          'tests'=>$tests,
          'groups'=>$groups,
          'errors'=>$errors
      ));
    ?>
</div>
<script>
  $(document).ready(function() {
    $('#sendEmail').click(function(){
     if ($('#sendEmail').is(':checked')) $('#emailInput').removeAttr('disabled');
     else $('#emailInput').attr('disabled','disabled');
   });

   $('#checkAllTests').click(function(){
      $(".test-assign input[type='checkbox']").attr('checked', $('#checkAllTests').is(':checked'));
   });

   $('#checkAllGroups').click(function(){
      $(".group-assign input[type='checkbox']").attr('checked', $('#checkAllGroups').is(':checked'));
   });

   if ($('#sendEmail').is(':checked')) $('#emailInput').removeAttr('disabled');
   else $('#emailInput').attr('disabled','disabled');

    sheldueType = $("input:radio[name='Task[periods]']:checked").val();
    $('.run-type').css('display','none');
    if (sheldueType === 'hourly') { $('#hourly').css('display','block'); }
    else if (sheldueType === 'daily') { $('#daily').css('display','block'); }
    else if (sheldueType === 'weekly') { $('#weekly').css('display','block'); }
    else if (sheldueType === 'monthly') { $('#monthly').css('display','block'); }
  });
</script>