$(document).ready(function() {
    $('.confirm').click(function(){
      action = $(this).html().replace(/<\/?[^>]+>/g, '').trim();
      if (!action) action = 'do this';
      if(confirm('Do you really want to '+action+'?')) return true;
      else return false;
    });

    $('.history .entry .more').click(function(){
      infoBlock = $(this).parent().parent().find("div[class^='more-info-']");
      matches = infoBlock.attr('class').match(/\d+/g);
      id = matches[0];

      /*$('.entry .more-info').each(function(index,el){$(el).hide(300);});*/
      if ($(this).html() == 'Show More') {
        $(this).html('Openning...');
        link = $(this);
        if ($('.more-info-'+id).html().replace(/^\s+|\s+$/g, '').length == 0) {
          jQuery.ajax({
            type: 'POST',
            url: 'index.php?r=history/getmore&ajax=1',
            data: ({
              'id': id
            }),
            success: function(data){
              infoBlock.html(data);
              infoBlock.show(300);
              link.html('Hide');
            },
            error: function(data, msg){
              alert(msg);
            }
          });
        }
        else {
          infoBlock.show(300);
          $(this).html('Hide');
        }
      }
      else {
        $(this).html('Show More');
        infoBlock.hide(300);
      }
   });

   $(document).on('click','a.urlRemove',function(){
     $(this).parent().parent().remove();
   });

   $(document).on('click','button.close',function(){
     $('div.alert').hide();
   });

   /*$('.executionType').click(function(){
     if ($(this).val() == 'intime') {
       $('.period input:not(.executionType)').each(function(index,el){$(el).attr('disabled','disabled')});
       $('.intime input:not(.executionType)').each(function(index,el){$(el).removeAttr('disabled')});
     }
     else {
       $('.intime input:not(.executionType)').each(function(index,el){$(el).attr('disabled','disabled')});
       $('.period input:not(.executionType)').each(function(index,el){$(el).removeAttr('disabled')});
     }
   });*/

   $("input:radio[name='Task[periods]']").click(function(){
     $('.run-type').css('display','none');
     if ($(this).val() === 'hourly') { $('#hourly').css('display','block'); }
     else if ($(this).val() === 'daily') { $('#daily').css('display','block'); }
     else if ($(this).val() === 'weekly') { $('#weekly').css('display','block'); }
     else if ($(this).val() === 'monthly') { $('#monthly').css('display','block'); }
   });
});

function getRunTestPopup(test) {
  jQuery.ajax({
      type: 'POST',
      url: 'index.php?r=test/getrunpopup&ajax=1',
      data: ({
        test: test
      }),
      success: function(data){
         $('.add-test-modal').html(data);
         $('.add-test-modal').show(300);
      },
      error: function(data, msg){
        alert(data);
      }
 });
}

function run_test(url,testUrl) {
  var result = $('#test_results');
  result.html('<img src="assets/img/wait.gif">');

  testUrl = $('#testUrlSelect').val();

  jQuery.ajax({
      type: "GET",
      url: url,
      dataType: 'json',
      data: ({
        responseFormat: 'json',
        testUrl : testUrl
      }),
      success:  function(data) {
        if (data.error || data == '') {
          $('.alert').addClass('alert-error');
          $('.alert').html('<button type="button" class="close" data-dismiss="alert">×</button>Test not accepted!');
        }
        else {
          $('.alert').addClass('alert-success');
          $('.alert').html('<button type="button" class="close" data-dismiss="alert">×</button>Test accepted!');
        }
        $('.alert').show('200');
        result.html(data.response);
      },
      error: function(data, message) {
        result.html(data);
      }
 });
}

function addTestPopup(group) {
  jQuery.ajax({
      type: 'POST',
      url: 'index.php?r=group/getaddpopup&ajax=1',
      data: ({
        group: group
      }),
      success: function(data){
         $('.add-test-modal').html(data);
         $('.add-test-modal').show(300);
      },
      error: function(data, msg){
        alert(msg);
      }
 });
}

function removeTestFromGroup(id, test) {
  if (confirm('Do you want remove test from group?'))
  {
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?r=group/removetest&ajax=1',
      data: ({
        group: id,
        test: test
      }),
      success: function(data){
         window.location.reload();
      },
      error: function(data, msg){
        alert(msg);
      }
    });
  }
}

function removeTestFromTask(id, test) {
  if (confirm('Do you want remove test from task?'))
  {
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?r=task/removetest&ajax=1',
      data: ({
        task: id,
        test: test
      }),
      success: function(data){
         window.location.reload();
      },
      error: function(data, msg){
        alert(msg);
      }
    });
  }
}

function removeGroupFromTask(id, group) {
  if (confirm('Do you want remove group from task?'))
  {
    jQuery.ajax({
      type: 'POST',
      url: 'index.php?r=task/removegroup&ajax=1',
      data: ({
        task: id,
        group: group
      }),
      success: function(data){
         window.location.reload();
      },
      error: function(data, msg){
        alert(msg);
      }
    });
  }
}

function getAddToQueuePopup(test) {
  jQuery.ajax({
      type: 'POST',
      url: 'index.php?r=test/getaddqueuepopup&ajax=1',
      data: ({
        test: test
      }),
      success: function(data){
         $('.add-test-modal').html(data);
         $('.add-test-modal').show(300);
      },
      error: function(data, msg){
        alert(data);
      }
 });
}

function addTestToQueue (url, testUrl) {
  testUrl = $('#testUrlSelect').val();

  jQuery.ajax({
    type: 'POST',
    url: url,
    data: ({
        testUrl: testUrl
    }),
    success: function(data){
      window.location.reload();
    },
    error: function(data, msg){
      alert(msg);
    }
  });
}

function addUrlInput() {
  lastValue = parseInt($('#config-form #urls input[type^=radio]:last').val()) + 1;
  $('#config-form #urls tr:last').after('<tr><td class="radio"><input type="radio" name="defaultUrl" value="'+lastValue+'" /></td><td><input type="text" name="urls[]" /><a class="urlRemove" href="javascript:void(0);">Remove</a></td></tr>');
}

function showModuleConfigPopup(module) {
  jQuery.ajax({
      type: 'POST',
      url: 'index.php?r=modules/getconfigpopup&ajax=1',
      data: ({
        module: module
      }),
      success: function(data){
         $('.config-modal').html(data);
         $('.config-modal').show(300);
      },
      error: function(data, msg){
        alert(data);
      }
 });
}