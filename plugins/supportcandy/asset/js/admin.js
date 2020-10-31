
/*
 * General Settings
 */
function wpsc_get_general_settings(){
  
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_settings_general').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_settings',
    setting_action : 'get_general_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
  
}

function wpsc_set_general_settings(){
  
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_general_settings')[0]);
  
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
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });
  
}

function wpsc_set_terms_and_condition_settings(){
  
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_terms_and_cond_settings')[0]);  
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
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });
  
}

/*
 * Category Settings
 */
function wpsc_get_category_settings(){
  
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_settings_category').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_settings',
    setting_action : 'get_category_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
  
}

/*
 * Status Settings
 */
function wpsc_get_status_settings(){
  
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_settings_status').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_settings',
    setting_action : 'get_status_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
  
}

function wpsc_get_priority_settings(){
  
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_settings_priority').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_settings',
    setting_action : 'get_priority_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
  
}

function wpsc_get_ticket_widget_settings() {
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_settings_ticket_widget').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data={
    action: 'wpsc_settings',
    setting_action:'get_ticket_widget_settings' 
  };
  jQuery.post(wpsc_admin.ajax_url,data,function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
  
}

function wpsc_get_thank_you_settings(){
  
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_settings_thank_you').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_settings',
    setting_action : 'get_thankyou_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
  
}

function wpsc_get_agent_roles(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_settings_agent_roles').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_settings',
    setting_action : 'get_agent_roles'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}

function wpsc_get_templates(){
  
  wpsc_modal_open(wpsc_admin.templates);
  var data = {
    action: 'wpsc_settings',
    setting_action : 'get_templates'
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    jQuery('#wpsc_popup_body').html(response.body);
    jQuery('#wpsc_popup_footer').html(response.footer);
    jQuery('#wpsc_cat_name').focus();
  });
  
}

function wpsc_set_thankyou_settings(){
  
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_thankyou_settings')[0]);
  
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
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });
  
}

function wpsc_insert_editor_text( text_to_insert ){
  var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
  if (is_tinymce) {
    tinymce.activeEditor.execCommand('mceInsertContent', false, text_to_insert);
  } else {

    var $txt = jQuery(".wpsc_textarea");
    var caretPos = $txt[0].selectionStart;
    var textAreaTxt = $txt.val();
    $txt.val(textAreaTxt.substring(0, caretPos) + text_to_insert + textAreaTxt.substring(caretPos) );
  }  
  wpsc_modal_close();
}

function wpsc_get_ticket_form_fields(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_ticket_custom_fields').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_custom_fields',
    setting_action : 'get_ticket_form_fields'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });  
}

function wpsc_add_field_condition(){
  var wpsc_tf_vcf = jQuery('#wpsc_tf_vcf').val().trim();
  if (!wpsc_tf_vcf) {
    jQuery('#wpsc_tf_vcf').focus();
    return;
  }
  var wpsc_tf_vco = jQuery('#wpsc_tf_vco').val().trim();
  if (!wpsc_tf_vco) {
    jQuery('#wpsc_tf_vco').focus();
    return;
  }
  
  var flag = true;
  jQuery('#wpsc_tf_condition_container').find('input').each(function(){
    var current_condition = ''+wpsc_tf_vcf+'--'+wpsc_tf_vco+'';
    if(jQuery(this).val() == current_condition ){
      flag = false;
    }
  });
  
  if(flag){
    var selected_field_label = jQuery('#wpsc_tf_vcf option:selected').text();
    var selected_option_label = jQuery('#wpsc_tf_vco option:selected').text();
    var str_html = ''
    +'<li class="wpsp_filter_display_element">'
      +'<div class="flex-container">'
        +'<div class="wpsp_filter_display_text">'
          +selected_field_label+': '+selected_option_label
          +'<input type="hidden" name="wpsp_tf_condition[]" value="'+wpsc_tf_vcf+'--'+wpsc_tf_vco+'" />'
        +'</div>'
        +'<div class="wpsp_filter_display_remove" onclick="wpsc_remove_filter(this);"><i class="fa fa-times"></i></div>'
      +'</div>'
    +'</li>';
    
    jQuery('#wpsc_tf_condition_container').append(str_html);
  }
  
  jQuery('#wpsc_tf_vcf').val('');
  jQuery('#wpsc_tf_vco').html('');
  
}

function wpsc_remove_filter(e){
  jQuery(e).parent().parent().remove();
}

function wpsc_get_agentonly_fields(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_agentonly_fields').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_custom_fields',
    setting_action : 'get_agentonly_fields'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });  
}

function wpsc_get_agent_ticket_list(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_agent_ticket_list').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_ticket_list',
    setting_action : 'get_agent_ticket_list'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });  
}

function wpsc_get_customer_ticket_list(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_customer_ticket_list').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_ticket_list',
    setting_action : 'get_customer_ticket_list'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}

function wpsc_get_agent_ticket_filters(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_agent_ticket_filters').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_ticket_list',
    setting_action : 'get_agent_ticket_filters'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}

function wpsc_get_customer_ticket_filters(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_customer_ticket_filters').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_ticket_list',
    setting_action : 'get_customer_ticket_filters'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}

function wpsc_get_ticket_list_additional_settings(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_ticket_list_additional_settings').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_ticket_list',
    setting_action : 'get_ticket_list_additional_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}

function set_ticket_list_additional_settings(){
  var check = false;
  jQuery('#wpsc_agent_unresolved_statuses').find('input:checked').each(function(){
    check=true;
  });  

  if(!check){
    alert(wpsc_admin.unresolve_agent); 
    return;   
  }
  
  var check=false;
  jQuery('#wpsc_customer_unresolved_statuses').find('input:checked').each(function(){
       check=true;
  });
    
  if(!check){
     alert(wpsc_admin.unresolve_customer); 
     return;   
  }
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_general_settings')[0]);
 
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
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });
  
}

function wpsc_get_support_agents(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_support_agents').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_support_agents',
    setting_action : 'get_support_agents'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}

function wpsc_get_en_general_setting(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_en_setting_general').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_email_notifications',
    setting_action : 'get_en_general_setting'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}

function wpsc_set_en_general_settings(){
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_general_settings')[0]);
  
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
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}

function wpsc_get_en_ticket_notifications(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_en_ticket_notifications').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_email_notifications',
    setting_action : 'get_en_ticket_notifications'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}

function wpsc_get_add_ticket_notification(){
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  var data = {
    action: 'wpsc_email_notifications',
    setting_action : 'get_add_ticket_notification'
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}

function wpsc_set_add_ticket_notification(){
  
  var conditions = wpsc_condition_parse('wpsc_add_en_conditions');
  if(!wpsc_condition_validate(conditions)) {
    alert('Incorrect Conditions');
    return;
  }
  
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_general_settings')[0]);
  
  dataform.append('conditions',JSON.stringify(conditions));
  
  jQuery.ajax({
    url: wpsc_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    } else {
      jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
    wpsc_get_en_ticket_notifications();
  });
}

function wpsc_get_edit_ticket_notification(term_id){
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  var data = {
    action: 'wpsc_email_notifications',
    setting_action : 'get_edit_ticket_notification',
    term_id : term_id
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}

function wpsc_set_edit_ticket_notification(){
  
  var conditions = wpsc_condition_parse('wpsc_edit_en_conditions');
  if(!wpsc_condition_validate(conditions)) {
    alert('Incorrect Conditions');
    return;
  }
  
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_general_settings')[0]);
  
  dataform.append('conditions',JSON.stringify(conditions));
  
  jQuery.ajax({
    url: wpsc_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
    wpsc_get_en_ticket_notifications();
  });
}

function wpsc_clone_ticket_notification(term_id){
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  var data = {
    action: 'wpsc_email_notifications',
    setting_action : 'clone_ticket_notification',
    term_id : term_id
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    } else {
      jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
    wpsc_get_en_ticket_notifications();
  });
}

function wpsc_delete_ticket_notification(term_id){
  if(confirm(wpsc_admin.are_you_sure)){
    jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
    var data = {
      action: 'wpsc_email_notifications',
      setting_action : 'delete_ticket_notification',
      term_id : term_id
    };
    jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
      var response = JSON.parse(response_str);
      if (response.sucess_status=='1') {
        jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
      } else {
        jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
      }
      jQuery('#wpsc_alert_success').slideDown('fast',function(){});
      setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
      wpsc_get_en_ticket_notifications();
    });
  }
}

function wpsc_get_cron_setup_settings(){
  
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_settings_cron_setup').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_settings',
    setting_action : 'get_cron_setup_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
  
}

/*
 * Appearance General Settings
 */
function wpsc_get_appearance_general_settings(){
  
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_appearance_general').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_appearance_settings',
    setting_action : 'get_appearance_general_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
  
}

function wpsc_set_appearance_general_settings(){
  
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_appearance_general_settings')[0]);
  
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
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });
  
}

/*
 * Appearance General reset Settings 
 */
function wpsc_reset_default_general_settings() {
  
  var data = {
    action: 'wpsc_appearance_settings',
    setting_action : 'get_reset_default_general_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
    wpsc_get_appearance_general_settings()
  });
}

/*
 * Appearance Ticket List Settings
 */
function wpsc_get_appearance_ticket_list(){
  
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_appearance_ticket_list').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_appearance_settings',
    setting_action : 'get_appearance_ticket_list'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
  
}

function wpsc_set_appearance_ticket_list_settings(){
  
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_appearance_ticket_list_settings')[0]);
  
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
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}

/*
 * Appearance Ticket List reset Settings 
 */
function wpsc_reset_default_ticket_list_settings() {

  var data = {
    action: 'wpsc_appearance_settings',
    setting_action : 'get_reset_default_ticket_list_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
    wpsc_get_appearance_ticket_list()
  });
}

/*
 * Appearance individual Ticket Page Settings
 */
function wpsc_get_appearance_individual_ticket(){
  
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_appearance_individual_ticket').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_appearance_settings',
    setting_action : 'get_appearance_individual_ticket_page'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}

function wpsc_set_appearance_individual_ticket_settings(){
  
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_appearance_individual_ticket_settings')[0]);
  
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
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}

/*
 * Appearance individual Ticket reset Settings 
 */
function wpsc_reset_default_individual_ticket_settings() {

  var data = {
    action: 'wpsc_appearance_settings',
    setting_action : 'get_reset_default_individual_ticket_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
    wpsc_get_appearance_individual_ticket()
  });
}


/*
 * Appearance Create Ticket Page Settings
 */
function wpsc_get_appearance_create_ticket(){
  
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_appearance_create_ticket').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_appearance_settings',
    setting_action : 'get_appearance_create_ticket'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}


function wpsc_set_appearance_create_ticket_settings(){
  
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_appearance_create_ticket_settings')[0]);
  
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
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}

function wpsc_get_appearance_login_form(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_appearance_login_form').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_appearance_settings',
    setting_action : 'get_appearance_login_form'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}

function wpsc_set_appearance_login_form(){
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_appearance_login_form')[0]);
  
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
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}
/*
 * Appearance Create Ticket reset Settings 
 */
function wpsc_reset_default_create_ticket_settings() {

  var data = {
    action: 'wpsc_appearance_settings',
    setting_action : 'get_reset_default_create_ticket_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
    wpsc_get_appearance_create_ticket()
  });
}
/*
 * Appearance Madal Window  Settings
 */
function wpsc_get_appearance_madal_window(){
  
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_appearance_modal_window').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_appearance_settings',
    setting_action : 'get_appearance_modal_window'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}

function wpsc_set_appearance_modal_window_settings(){
  
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_appearance_modal_window_settings')[0]);
  
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
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}

function wpsc_get_appearance_signup(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_appearance_signup_form').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_appearance_settings',
    setting_action : 'get_appearance_signup'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}

function wpsc_set_appearance_sign_up(){
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_appearnce_signup_settings')[0]);
  
  jQuery.ajax({
    url: wpsc_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  
  .done(function (response_str) 
  {
    var response = JSON.parse(response_str);
    jQuery('.wpsc_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}
/*
 * Appearance Modal Window reset Settings 
 */
function wpsc_reset_default_modal_window_settings() {

  var data = {
    action: 'wpsc_appearance_settings',
    setting_action : 'get_reset_default_modal_window_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
    wpsc_get_appearance_madal_window()
  });
}

function wpsc_get_terms_and_condition_settings(){
  
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_settings_term_and_conditions').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_settings',
    setting_action : 'get_terms_and_condition_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
  
}

function wpsc_reset_appearance_signup_form(){
  var data = {
    action: 'wpsc_appearance_settings',
    setting_action : 'get_reset_default_signup_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') 
    {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
    wpsc_get_appearance_signup();
  });
}

function wpsc_get_advanced_settings(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_advanced_settings').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  var data = {
    action: 'wpsc_settings',
    setting_action : 'get_advanced_settings'
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });  
}

function wpsc_set_advanced_settings(){
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_advanced_settings')[0]);  
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
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }else {
      jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
      jQuery('#wpsc_alert_error').slideDown('fast',function(){});
      setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });  
}

function wpsc_get_captcha_settings(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_captcha_settings').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  var data = {
    action: 'wpsc_settings',
    setting_action : 'get_captcha_settings'
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });  
}

function wpsc_set_captcha_settings(){
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_captcha_settings')[0]);  
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
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });  
}

function wpsc_get_rest_api_settings(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_rest_settings').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  var data = {
    action: 'wpsc_settings',
    setting_action : 'get_rest_api_settings'
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });  
}

function wpsc_set_rest_api_settings(){
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_rest_api_settings')[0]);  
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
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });  
}

function wpsc_custom_ticket_number(){
  jQuery('.wpsc_submit_wait_1').show();
  var new_count = document.getElementById("wpsc_custom_ticket_count").value;
  var data = {
    action: 'wpsc_settings',
    setting_action : 'custom_start_ticket_number',
    new_count : new_count
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    var response = JSON.parse(response);
    jQuery('.wpsc_submit_wait_1').hide();
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    } else {
      alert(response.msg);
      return;
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}

/**
 * Adds new condition element.
 */
function wpsc_add_new_condition(id) {
  jQuery('#'+ id +' .wpsc_conditions_container').append(jQuery('.wpsc_condition_template').html());
}

/**
 * Remove condition element.
 */
function wpsc_remove_condition(obj){
  jQuery(obj).parent().parent().remove();
}

/**
 * Change condition option.
 */
function wpsc_condition_change(obj){
  
    jQuery(obj).parent().parent().find('.wpsc_condition_compare_container').first().html('');
    jQuery(obj).parent().parent().find('.wpsc_condition_value_container').first().html('');
    
    var key = jQuery(obj).val();
    var has_options = jQuery(obj).find(':selected').first().data('hasoptions');
    
    if( has_options == 1 ){
      
      var data = {
        action: 'wpsc_custom_fields',
        setting_action : 'get_conditional_options',
        key : key
      };
      jQuery.post(wpsc_admin.ajax_url, data, function(response) {
        
          jQuery(obj).parent().parent().find('.wpsc_condition_compare_container').first().html(jQuery('.wpsc_cond_compare_dd_template').html());
          jQuery(obj).parent().parent().find('.wpsc_condition_value_container').first().html(jQuery('.wpsc_cond_val_dd_template').html());
          jQuery(obj).parent().parent().find('.wpsc_condition_value').first().html(response);
        
      });
      
    } else {
      
        jQuery(obj).parent().parent().find('.wpsc_condition_compare_container').first().html(jQuery('.wpsc_cond_compare_tf_template').html());
        jQuery(obj).parent().parent().find('.wpsc_condition_value_container').first().html(jQuery('.wpsc_cond_val_tf_template').html());
      
    }
  
}

/**
 * Return array of conditions found for condition element of given id
 * @param  {String} id Condition element id
 * @return {Array}  conditions parsed in an array
 */
function wpsc_condition_parse(id){
  
    var conditions = new Array();
    
    jQuery('#'+id).find('.wpsc_condition_element').each(function(){
      
        var field    = jQuery(this).find('.wpsc_condition_field').first().val().trim();
        var compare  = '';
        var cond_val = '';
        
        if( field != '' ){
          compare = jQuery(this).find('.wpsc_condition_compare').first().val().trim();
          cond_val = jQuery(this).find('.wpsc_condition_value').first().val().trim();
        }
        
        conditions.push( { field: field, compare: compare, cond_val: cond_val } );
      
    });
    
    return conditions;
  
}

/**
 * Validate whether conditions are entered correctly or not
 * @param  {Array} conditions Array contains condition objects
 * @return {Boolean} True or False
 */
function wpsc_condition_validate(conditions){
  
    var is_correct = true;
    if( conditions.length > 0 ){
      jQuery.each( conditions, function( key, condition ){
        if( condition.field =='' || condition.cond_val =='' ){
          is_correct = false;
          return;
        }
      });
    }
    
    return is_correct;
  
}

function wpsc_get_conditional_options(e){
  jQuery('#wpsc_tf_vco').html('');
  var field_id = jQuery(e).val();
  var data = {
    action: 'wpsc_custom_fields',
    setting_action : 'get_conditional_options_fields',
    field_id : field_id
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('#wpsc_tf_vco').html(response);
  });  
}

function wpsc_get_ticket_list_advanced_settings(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_ticket_list_advanced_settings').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_ticket_list',
    setting_action : 'get_ticket_list_advanced_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}

function set_ticket_list_advanced_settings(){
  var check = false;
  
  jQuery('#wpsc_agent_close_statuses').find('input:checked').each(function () {
    check = true;
  });  

 if(!check){
   alert(wpsc_admin.agent_close_statuses); 
   return;   
  }
  
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_ticket_list_advanced_settings')[0]);
  
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
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });
  
}

function wpsc_get_attachment_settings(){
  
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_settings_attachment').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_settings',
    setting_action : 'get_attachment_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}

function wpsc_set_attachment_settings(){
  
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_attachment_settings')[0]);
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
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });
  
}

function wpsc_get_tinymce_settings(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_tinymce_settings').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_settings',
    setting_action : 'get_tinymce_settings'
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    jQuery('.wpsc_setting_col2').html(response);
  });
}

function wpsc_set_tinymce_settings(){

  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_get_tinymce_settings')[0]);
  
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
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}