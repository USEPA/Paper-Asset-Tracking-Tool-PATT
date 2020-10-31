
/*
 * General Settings
 */
function wppatt_get_general_settings(){
  
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_settings_general').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_settings',
    setting_action : 'get_general_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
  
}

function wppatt_set_general_settings(){
  
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_general_settings')[0]);
  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });
  
}

function wppatt_set_terms_and_condition_settings(){
  
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_terms_and_cond_settings')[0]);  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });
  
}

/*
 * Category Settings
 */
function wppatt_get_category_settings(){
  
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_settings_category').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_settings',
    setting_action : 'get_category_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
  
}

/*
 * Status Settings
 */
function wppatt_get_status_settings(){
  
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_settings_status').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_settings',
    setting_action : 'get_status_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
  
}

function wppatt_get_priority_settings(){
  
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_settings_priority').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_settings',
    setting_action : 'get_priority_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
  
}

function wppatt_get_ticket_widget_settings() {
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_settings_ticket_widget').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data={
    action: 'wppatt_settings',
    setting_action:'get_ticket_widget_settings' 
  };
  jQuery.post(wppatt_admin.ajax_url,data,function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
  
}

function wppatt_get_thank_you_settings(){
  
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_settings_thank_you').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_settings',
    setting_action : 'get_thankyou_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
  
}

function wppatt_get_agent_roles(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_settings_agent_roles').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_settings',
    setting_action : 'get_agent_roles'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}

function wppatt_get_templates(){
  
  wppatt_modal_open(wppatt_admin.templates);
  var data = {
    action: 'wppatt_settings',
    setting_action : 'get_templates'
  };
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    jQuery('#wppatt_popup_body').html(response.body);
    jQuery('#wppatt_popup_footer').html(response.footer);
    jQuery('#wppatt_cat_name').focus();
  });
  
}

function wppatt_set_thankyou_settings(){
  
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_thankyou_settings')[0]);
  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });
  
}

function wppatt_insert_editor_text( text_to_insert ){
  var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
  if (is_tinymce) {
    tinymce.activeEditor.execCommand('mceInsertContent', false, text_to_insert);
  } else {

    var $txt = jQuery(".wppatt_textarea");
    var caretPos = $txt[0].selectionStart;
    var textAreaTxt = $txt.val();
    $txt.val(textAreaTxt.substring(0, caretPos) + text_to_insert + textAreaTxt.substring(caretPos) );
  }  
  wppatt_modal_close();
}

function wppatt_get_ticket_form_fields(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_ticket_custom_fields').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_custom_fields',
    setting_action : 'get_ticket_form_fields'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });  
}

function wppatt_add_field_condition(){
  var wppatt_tf_vcf = jQuery('#wppatt_tf_vcf').val().trim();
  if (!wppatt_tf_vcf) {
    jQuery('#wppatt_tf_vcf').focus();
    return;
  }
  var wppatt_tf_vco = jQuery('#wppatt_tf_vco').val().trim();
  if (!wppatt_tf_vco) {
    jQuery('#wppatt_tf_vco').focus();
    return;
  }
  
  var flag = true;
  jQuery('#wppatt_tf_condition_container').find('input').each(function(){
    var current_condition = ''+wppatt_tf_vcf+'--'+wppatt_tf_vco+'';
    if(jQuery(this).val() == current_condition ){
      flag = false;
    }
  });
  
  if(flag){
    var selected_field_label = jQuery('#wppatt_tf_vcf option:selected').text();
    var selected_option_label = jQuery('#wppatt_tf_vco option:selected').text();
    var str_html = ''
    +'<li class="wpsp_filter_display_element">'
      +'<div class="flex-container">'
        +'<div class="wpsp_filter_display_text">'
          +selected_field_label+': '+selected_option_label
          +'<input type="hidden" name="wpsp_tf_condition[]" value="'+wppatt_tf_vcf+'--'+wppatt_tf_vco+'" />'
        +'</div>'
        +'<div class="wpsp_filter_display_remove" onclick="wppatt_remove_filter(this);"><i class="fa fa-times"></i></div>'
      +'</div>'
    +'</li>';
    
    jQuery('#wppatt_tf_condition_container').append(str_html);
  }
  
  jQuery('#wppatt_tf_vcf').val('');
  jQuery('#wppatt_tf_vco').html('');
  
}

function wppatt_remove_filter(e){
  jQuery(e).parent().parent().remove();
}

function wppatt_get_agentonly_fields(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_agentonly_fields').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_custom_fields',
    setting_action : 'get_agentonly_fields'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });  
}

function wppatt_get_agent_ticket_list(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_agent_ticket_list').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_ticket_list',
    setting_action : 'get_agent_ticket_list'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });  
}

function wppatt_get_customer_ticket_list(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_customer_ticket_list').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_ticket_list',
    setting_action : 'get_customer_ticket_list'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}

function wppatt_get_agent_ticket_filters(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_agent_ticket_filters').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_ticket_list',
    setting_action : 'get_agent_ticket_filters'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}

function wppatt_get_customer_ticket_filters(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_customer_ticket_filters').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_ticket_list',
    setting_action : 'get_customer_ticket_filters'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}

function wppatt_get_ticket_list_additional_settings(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_ticket_list_additional_settings').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_ticket_list',
    setting_action : 'get_ticket_list_additional_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}

function set_ticket_list_additional_settings(){
  var check = false;
  jQuery('#wppatt_agent_unresolved_statuses').find('input:checked').each(function(){
    check=true;
  });  

  if(!check){
    alert(wppatt_admin.unresolve_agent); 
    return;   
  }
  
  var check=false;
  jQuery('#wppatt_customer_unresolved_statuses').find('input:checked').each(function(){
       check=true;
  });
    
  if(!check){
     alert(wppatt_admin.unresolve_customer); 
     return;   
  }
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_general_settings')[0]);
 
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });
  
}

function wppatt_get_support_agents(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_support_agents').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_support_agents',
    setting_action : 'get_support_agents'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}

function wppatt_get_en_general_setting(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_en_setting_general').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_email_notifications',
    setting_action : 'get_en_general_setting'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}

function wppatt_set_en_general_settings(){
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_general_settings')[0]);
  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}

function wppatt_get_en_ticket_notifications(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_en_ticket_notifications').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_email_notifications',
    setting_action : 'get_en_ticket_notifications'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}

function wppatt_get_add_ticket_notification(){
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  var data = {
    action: 'wppatt_email_notifications',
    setting_action : 'get_add_ticket_notification'
  };
  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}

function wppatt_set_add_ticket_notification(){
  
  var conditions = wppatt_condition_parse('wppatt_add_en_conditions');
  if(!wppatt_condition_validate(conditions)) {
    alert('Incorrect Conditions');
    return;
  }
  
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_general_settings')[0]);
  
  dataform.append('conditions',JSON.stringify(conditions));
  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    } else {
      jQuery('#wppatt_alert_error .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
    wppatt_get_en_ticket_notifications();
  });
}

function wppatt_get_edit_ticket_notification(term_id){
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  var data = {
    action: 'wppatt_email_notifications',
    setting_action : 'get_edit_ticket_notification',
    term_id : term_id
  };
  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}

function wppatt_set_edit_ticket_notification(){
  
  var conditions = wppatt_condition_parse('wppatt_edit_en_conditions');
  if(!wppatt_condition_validate(conditions)) {
    alert('Incorrect Conditions');
    return;
  }
  
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_general_settings')[0]);
  
  dataform.append('conditions',JSON.stringify(conditions));
  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
    wppatt_get_en_ticket_notifications();
  });
}

function wppatt_clone_ticket_notification(term_id){
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  var data = {
    action: 'wppatt_email_notifications',
    setting_action : 'clone_ticket_notification',
    term_id : term_id
  };
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    } else {
      jQuery('#wppatt_alert_error .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
    wppatt_get_en_ticket_notifications();
  });
}

function wppatt_delete_ticket_notification(term_id){
  if(confirm(wppatt_admin.are_you_sure)){
    jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
    var data = {
      action: 'wppatt_email_notifications',
      setting_action : 'delete_ticket_notification',
      term_id : term_id
    };
    jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
      var response = JSON.parse(response_str);
      if (response.sucess_status=='1') {
        jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
      } else {
        jQuery('#wppatt_alert_error .wppatt_alert_text').text(response.messege);
      }
      jQuery('#wppatt_alert_success').slideDown('fast',function(){});
      setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
      wppatt_get_en_ticket_notifications();
    });
  }
}

function wppatt_get_cron_setup_settings(){
  
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_settings_cron_setup').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_settings',
    setting_action : 'get_cron_setup_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
  
}

/*
 * Appearance General Settings
 */
function wppatt_get_appearance_general_settings(){
  
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_appearance_general').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_appearance_settings',
    setting_action : 'get_appearance_general_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
  
}

function wppatt_set_appearance_general_settings(){
  
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_appearance_general_settings')[0]);
  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });
  
}

/*
 * Appearance General reset Settings 
 */
function wppatt_reset_default_general_settings() {
  
  var data = {
    action: 'wppatt_appearance_settings',
    setting_action : 'get_reset_default_general_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
    wppatt_get_appearance_general_settings()
  });
}

/*
 * Appearance Ticket List Settings
 */
function wppatt_get_appearance_ticket_list(){
  
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_appearance_ticket_list').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_appearance_settings',
    setting_action : 'get_appearance_ticket_list'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
  
}

function wppatt_set_appearance_ticket_list_settings(){
  
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_appearance_ticket_list_settings')[0]);
  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}

/*
 * Appearance Ticket List reset Settings 
 */
function wppatt_reset_default_ticket_list_settings() {

  var data = {
    action: 'wppatt_appearance_settings',
    setting_action : 'get_reset_default_ticket_list_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
    wppatt_get_appearance_ticket_list()
  });
}

/*
 * Appearance individual Ticket Page Settings
 */
function wppatt_get_appearance_individual_ticket(){
  
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_appearance_individual_ticket').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_appearance_settings',
    setting_action : 'get_appearance_individual_ticket_page'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}

function wppatt_set_appearance_individual_ticket_settings(){
  
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_appearance_individual_ticket_settings')[0]);
  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}

/*
 * Appearance individual Ticket reset Settings 
 */
function wppatt_reset_default_individual_ticket_settings() {

  var data = {
    action: 'wppatt_appearance_settings',
    setting_action : 'get_reset_default_individual_ticket_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
    wppatt_get_appearance_individual_ticket()
  });
}


/*
 * Appearance Create Ticket Page Settings
 */
function wppatt_get_appearance_create_ticket(){
  
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_appearance_create_ticket').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_appearance_settings',
    setting_action : 'get_appearance_create_ticket'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}


function wppatt_set_appearance_create_ticket_settings(){
  
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_appearance_create_ticket_settings')[0]);
  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}

function wppatt_get_appearance_login_form(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_appearance_login_form').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_appearance_settings',
    setting_action : 'get_appearance_login_form'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}

function wppatt_set_appearance_login_form(){
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_appearance_login_form')[0]);
  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}
/*
 * Appearance Create Ticket reset Settings 
 */
function wppatt_reset_default_create_ticket_settings() {

  var data = {
    action: 'wppatt_appearance_settings',
    setting_action : 'get_reset_default_create_ticket_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
    wppatt_get_appearance_create_ticket()
  });
}
/*
 * Appearance Madal Window  Settings
 */
function wppatt_get_appearance_madal_window(){
  
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_appearance_modal_window').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_appearance_settings',
    setting_action : 'get_appearance_modal_window'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}

function wppatt_set_appearance_modal_window_settings(){
  
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_appearance_modal_window_settings')[0]);
  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}

function wppatt_get_appearance_signup(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_appearance_signup_form').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_appearance_settings',
    setting_action : 'get_appearance_signup'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}

function wppatt_set_appearance_sign_up(){
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_appearnce_signup_settings')[0]);
  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  
  .done(function (response_str) 
  {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}
/*
 * Appearance Modal Window reset Settings 
 */
function wppatt_reset_default_modal_window_settings() {

  var data = {
    action: 'wppatt_appearance_settings',
    setting_action : 'get_reset_default_modal_window_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
    wppatt_get_appearance_madal_window()
  });
}

function wppatt_get_terms_and_condition_settings(){
  
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_settings_term_and_conditions').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_settings',
    setting_action : 'get_terms_and_condition_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
  
}

function wppatt_reset_appearance_signup_form(){
  var data = {
    action: 'wppatt_appearance_settings',
    setting_action : 'get_reset_default_signup_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    if (response.sucess_status=='1') 
    {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
    wppatt_get_appearance_signup();
  });
}

function wppatt_get_advanced_settings(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_advanced_settings').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  var data = {
    action: 'wppatt_settings',
    setting_action : 'get_advanced_settings'
  };
  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });  
}

function wppatt_set_advanced_settings(){
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_advanced_settings')[0]);  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }else {
      jQuery('#wppatt_alert_error .wppatt_alert_text').text(response.messege);
      jQuery('#wppatt_alert_error').slideDown('fast',function(){});
      setTimeout(function(){ jQuery('#wppatt_alert_error').slideUp('fast',function(){}); }, 3000);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });  
}

function wppatt_get_captcha_settings(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_captcha_settings').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  var data = {
    action: 'wppatt_settings',
    setting_action : 'get_captcha_settings'
  };
  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });  
}

function wppatt_set_captcha_settings(){
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_captcha_settings')[0]);  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });  
}

function wppatt_get_rest_api_settings(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_rest_settings').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  var data = {
    action: 'wppatt_settings',
    setting_action : 'get_rest_api_settings'
  };
  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });  
}

function wppatt_set_rest_api_settings(){
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_rest_api_settings')[0]);  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });  
}

function wppatt_custom_ticket_number(){
  jQuery('.wppatt_submit_wait_1').show();
  var new_count = document.getElementById("wppatt_custom_ticket_count").value;
  var data = {
    action: 'wppatt_settings',
    setting_action : 'custom_start_ticket_number',
    new_count : new_count
  };
  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    var response = JSON.parse(response);
    jQuery('.wppatt_submit_wait_1').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    } else {
      alert(response.msg);
      return;
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}

/**
 * Adds new condition element.
 */
function wppatt_add_new_condition(id) {
  jQuery('#'+ id +' .wppatt_conditions_container').append(jQuery('.wppatt_condition_template').html());
}

/**
 * Remove condition element.
 */
function wppatt_remove_condition(obj){
  jQuery(obj).parent().parent().remove();
}

/**
 * Change condition option.
 */
function wppatt_condition_change(obj){
  
    jQuery(obj).parent().parent().find('.wppatt_condition_compare_container').first().html('');
    jQuery(obj).parent().parent().find('.wppatt_condition_value_container').first().html('');
    
    var key = jQuery(obj).val();
    var has_options = jQuery(obj).find(':selected').first().data('hasoptions');
    
    if( has_options == 1 ){
      
      var data = {
        action: 'wppatt_custom_fields',
        setting_action : 'get_conditional_options',
        key : key
      };
      jQuery.post(wppatt_admin.ajax_url, data, function(response) {
        
          jQuery(obj).parent().parent().find('.wppatt_condition_compare_container').first().html(jQuery('.wppatt_cond_compare_dd_template').html());
          jQuery(obj).parent().parent().find('.wppatt_condition_value_container').first().html(jQuery('.wppatt_cond_val_dd_template').html());
          jQuery(obj).parent().parent().find('.wppatt_condition_value').first().html(response);
        
      });
      
    } else {
      
        jQuery(obj).parent().parent().find('.wppatt_condition_compare_container').first().html(jQuery('.wppatt_cond_compare_tf_template').html());
        jQuery(obj).parent().parent().find('.wppatt_condition_value_container').first().html(jQuery('.wppatt_cond_val_tf_template').html());
      
    }
  
}

/**
 * Return array of conditions found for condition element of given id
 * @param  {String} id Condition element id
 * @return {Array}  conditions parsed in an array
 */
function wppatt_condition_parse(id){
  
    var conditions = new Array();
    
    jQuery('#'+id).find('.wppatt_condition_element').each(function(){
      
        var field    = jQuery(this).find('.wppatt_condition_field').first().val().trim();
        var compare  = '';
        var cond_val = '';
        
        if( field != '' ){
          compare = jQuery(this).find('.wppatt_condition_compare').first().val().trim();
          cond_val = jQuery(this).find('.wppatt_condition_value').first().val().trim();
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
function wppatt_condition_validate(conditions){
  
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

function wppatt_get_conditional_options(e){
  jQuery('#wppatt_tf_vco').html('');
  var field_id = jQuery(e).val();
  var data = {
    action: 'wppatt_custom_fields',
    setting_action : 'get_conditional_options_fields',
    field_id : field_id
  };
  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('#wppatt_tf_vco').html(response);
  });  
}

function wppatt_get_ticket_list_advanced_settings(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_ticket_list_advanced_settings').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_ticket_list',
    setting_action : 'get_ticket_list_advanced_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}

function set_ticket_list_advanced_settings(){
  
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_ticket_list_advanced_settings')[0]);
  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });
  
}

function wppatt_get_attachment_settings(){
  
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_settings_attachment').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_settings',
    setting_action : 'get_attachment_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}

function wppatt_set_attachment_settings(){
  
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_frm_attachment_settings')[0]);
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });
  
}

function wppatt_get_tinymce_settings(){
  jQuery('.wppatt_setting_pills li').removeClass('active');
  jQuery('#wppatt_tinymce_settings').addClass('active');
  jQuery('.wppatt_setting_col2').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_settings',
    setting_action : 'get_tinymce_settings'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_setting_col2').html(response);
  });
}

function wppatt_set_tinymce_settings(){

  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_get_tinymce_settings')[0]);
  
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wppatt_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wppatt_alert_success .wppatt_alert_text').text(response.messege);
    }
    jQuery('#wppatt_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wppatt_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}