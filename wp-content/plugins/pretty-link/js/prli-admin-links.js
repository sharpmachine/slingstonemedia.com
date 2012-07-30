function prli_toggle_link_options() {
  if( jQuery('#redirect_type').val() == 'metarefresh' ||
      jQuery('#redirect_type').val() == 'javascript' )
    jQuery('#prli_time_delay').show();
  else
    jQuery('#prli_time_delay').hide();
  
  if( jQuery('#redirect_type').val() == 'cloak' ||
      jQuery('#redirect_type').val() == 'prettybar' ||
      jQuery('#redirect_type').val() == 'metarefresh' ||
      jQuery('#redirect_type').val() == 'javascript' )
    jQuery('#prli_google_analytics').show();
  else
    jQuery('#prli_google_analytics').hide();

  if( jQuery('#redirect_type').val() == 'pixel' )
    jQuery('#prli_target_url').hide();
  else
    jQuery('#prli_target_url').show();
  
  if( jQuery('.prlipro-enable-split-test').prop('checked') )
    jQuery('.prlipro-split-test-goal-link').show();
  else
    jQuery('.prlipro-split-test-goal-link').hide();
}

(function ($) { $(document).ready(function(e){
  prli_toggle_link_options();

  $('#redirect_type').change(function() {
    prli_toggle_link_options();
  });

  $('#param_forwarding').click(function() {
    prli_toggle_link_options();
  });
  
  $('.prlipro-enable-split-test').click(function() {
    prli_toggle_link_options();
  });
  
  // tab swapping
  $('.nav-tab').click(function() {

    // tab is already active. don't do anything
    if( $(this).hasClass( 'nav-tab-active' ) )
      return false;
    
    $('.nav-tab-active').removeClass( 'nav-tab-active' );
    $(this).addClass( 'nav-tab-active' );
    
    if( $(this).attr( 'href' ) == '#options-table' ) {
      $('#options-table').show();
      $('#pro-options-table').hide();
    }
    else {
      $('#options-table').hide();
      $('#pro-options-table').show();
    }
    
    return false;
  });

  $("#add_group_textbox").keypress(function(e) {
    // Apparently 13 is the enter key
    if(e.which == 13) {
      e.preventDefault();
      
      var add_new_group_data = {
        action: 'add_new_prli_group',
        new_group_name: $('#add_group_textbox').val(),
        _prli_nonce: $('#add_group_textbox').attr('prli_nonce')
      };
      
      $.post(ajaxurl, add_new_group_data, function(data) {
        if(data['status']=='success') {
          $('#group_dropdown').append(data['group_option']);
          $('#group_dropdown').val(data['group_id']);
          $('#add_group_textbox').val('');
          $("#add_group_textbox").blur();
          $("#add_group_message").addClass('updated');
          $("#add_group_message").text(data['message']);
          $("#add_group_message").show();
          
          $("#add_group_message").fadeOut(5000, function(e) {
            $("#add_group_message").removeClass('updated');
          });
        }
        else {
          $("#add_group_message").addClass('error');
          $("#add_group_message").text(data['message']);
          
          $("#add_group_message").fadeOut(5000, function(e) {
            $("#add_group_message").removeClass('error');
          });
        }
      });
    }
  });

  $(".defaultText").focus(function(srcc) {
    if ($(this).val() == $(this)[0].title) {
      $(this).removeClass("defaultTextActive");
      $(this).val('');
    }
  });
  
  $(".defaultText").blur(function() {
    if ($(this).val() == "")
    {
      $(this).addClass("defaultTextActive");
      $(this).val($(this)[0].title);
    }
  });
  
  $(".defaultText").blur();
  
  $(".link_row").hover( function() {
      $(this).find(".link_actions").show();
    },
    function() {
      $(this).find(".link_actions").hide();
    });

  $('.prli_bulk_action_apply').click( function() {
    if($('.prli_bulk_action').val()=='edit') {
      if($('.link-action-checkbox:checkbox:checked').length > 0)
        $('#bulk-edit').slideDown('slow');
    }
    else if($('.prli_bulk_action').val()=='delete') {
      var confmsg = $(this).attr('data-confmsg');
      if(confirm(confmsg)) {
        var ids = $('.link-action-checkbox:checkbox:checked').map(function() {
                    return $(this).attr('data-id');
                  }).get().join(',');
        var delurl = $('.prli_bulk_action_apply').attr('data-url') + 
                     window.location.search +
                     '&action=bulk-destroy' +
                     '&_wpnonce=' + $('.prli_bulk_action_apply').attr('data-wpnonce') + 
                     '&ids=' + ids;

        window.location = delurl;
      }
    }
  });

  $('.bulk-edit-cancel').click( function() {
    $('#bulk-edit').slideUp('slow');
  });

  $('.bulk-edit-update').click( function() {
    var ids = $('.link-action-checkbox:checkbox:checked').map(function() {
                return $(this).attr('data-id');
              }).get().join(',');
    var editurl = $('.prli_bulk_action_apply').attr('data-url') + 
                  window.location.search +
                  '&action=bulk-update' +
                  '&_wpnonce=' + $('.prli_bulk_action_apply').attr('data-wpnonce') + 
                  '&ids=' + ids;
    
    $('.bulk-edit-select').each( function() {
      if($(this).val() != '##nochange##')
        editurl += '&' + $(this).attr('name') + '=' + encodeURIComponent($(this).val());
    });
    
    $('.bulk-edit-text').each( function() {
      editurl += '&' + $(this).attr('name') + '=' + encodeURIComponent($(this).val());
    });

    window.location = editurl;
  });

  $('.link-action-checkbox').change( function() {
    if($(this).prop('checked')==false)
      $('.select-all-link-action-checkboxes').prop('checked',false);
    
    $('#bulk-titles').html('');
    $('.link-action-checkbox:checkbox:checked').each( function() {
      var nid = $(this).attr('data-id');
      var ntitle = $(this).attr('data-title');
      $('#bulk-titles').append('<div id="ttle'+nid+'"><a data-id="'+nid+'" class="ntdelbutton" title="Remove From Bulk Edit">X</a>"'+ntitle+'"</div>');
      $('.ntdelbutton').click( function() {
        var nid = $(this).attr('data-id');
        $('.link-action-checkbox[data-id='+nid+']').prop('checked',false);
        $('.link-action-checkbox[data-id='+nid+']').trigger('change');
      });
    });
  });

  // Check all boxes when the select all box is checked, etc
  $('.select-all-link-action-checkboxes').change(function() {
    if($(this).prop('checked'))
      $('.link-action-checkbox').prop('checked',true);
    else
      $('.link-action-checkbox').prop('checked',false);

    $('.link-action-checkbox').trigger('change');
  });

  // Set the correct colspan for the bulk-edit form
  // This is necessary because in some cases, PLP adds a
  // keyword column to this table -- so it has to be dynamic
  $('.prli-edit-table td.colspanchange').attr('colspan', $('table.prli-edit-table thead tr th').length);
});
})(jQuery);
