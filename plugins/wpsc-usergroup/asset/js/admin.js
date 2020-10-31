function wpsc_get_wpsc_usergroup_settings(){
    jQuery('.wpsc_setting_pills li').removeClass('active');
    jQuery('#wpsc_settings_wpsc_usergroup').addClass('active');
    jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
    
    var data = {
      'action' : 'wpsc_get_company_usergroup_settings'
    };

    jQuery.post(wpsc_admin.ajax_url, data, function(response) {
      jQuery('.wpsc_setting_col2').html(response);
    });
}

function wpsc_add_new_company_settings(){
    var data = {
        'action': 'wpsc_add_new_company_settings'
    };

    jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
      jQuery('.wpsc_setting_col2').html(response_str);
    });
}

function wpsc_delete_usergroup(comp_id){
    if (confirm('Are you sure?')) {
        var data = {
            'action': 'wpsc_delete_company',
            'comp_id':comp_id
        };

        jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
          jQuery('.wpsc_setting_col2').html(response_str);
          wpsc_get_wpsc_usergroup_settings();
        });
    }
}

function wpsc_edit_usergroup(usergroup_id){
    var data = {
        'action'      : 'wpsc_edit_company',
        'usergroup_id':  usergroup_id
    };

    jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
      jQuery('.wpsc_setting_col2').html(response_str);
    });
}

function set_add_new_company_settings(){
  error_flag = true;
  var rows= jQuery('#wpsp_company_selected_users tbody tr').length;
  
  if(jQuery.trim(jQuery('#wpsp_add_usergroup_name').val())==""){
      alert(wpsc_usergroup_data.insert_name);
      error_flag=false;
    }else if(error_flag && rows==0){
      alert(wpsc_usergroup_data.at_least_one_user);
      error_flag=false;
    }else{
      
        jQuery('.wpsc_submit_wait').show();
        var dataform = new FormData(jQuery('#wpsc_frm_add_new_company_settings')[0]);
        
        jQuery.ajax({
          url: wpsc_admin.ajax_url,
          type: 'POST',
          data: dataform,
          processData: false,
          contentType: false
        })
        .done(function (response_str) {
           var response = JSON.parse(response_str);
           jQuery('.wpsc_submit_wait').hide();
           jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
           jQuery('#wpsc_alert_success').slideDown('fast',function(){});
           setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
           wpsc_get_wpsc_usergroup_settings();
        });
  }
}

function set_update_company_settings(){
    jQuery('.wpsc_submit_wait').show();
    var dataform = new FormData(jQuery('#wpsc_frm_update_company_settings')[0]);
    
    jQuery.ajax({
      url: wpsc_admin.ajax_url,
      type: 'POST',
      data: dataform,
      processData: false,
      contentType: false
    })
    .done(function (response_str) {
      wpsc_get_wpsc_usergroup_settings();
    });
}

function wpsc_back_usergroup_list(){
    jQuery('.wpsc_submit_wait').show();
    wpsc_get_wpsc_usergroup_settings();
}