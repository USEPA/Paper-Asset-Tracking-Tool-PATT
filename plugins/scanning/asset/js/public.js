
function wppatt_init(wppatt_setting_action,attrs){
  
  jQuery('#wppatt_tickets_container').html(wppatt_admin.loading_html);
  var data = {
    action: 'wppatt_tickets',
    setting_action : wppatt_setting_action
  };
  
   jQuery.each( attrs, function( key, value ) {
     data[key] = value;
   });
  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('#wppatt_tickets_container').html(response);
  });
  
}

function wppatt_signup_user(){
  
  jQuery('#wppatt_tickets_container').html(wppatt_admin.loading_html);
  //wppatt_doScrolling('#wppatt_tickets_container',1000);

  var data = {
    action: 'wppatt_tickets',
    setting_action : 'sign_up_user'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('#wppatt_tickets_container').html(response);
  });
}

function wppatt_get_ticket_list(){
  var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
  if(is_tinymce){
    var description = tinyMCE.activeEditor.getContent().trim();
    if(description.length != 0){
      if(!confirm(wppatt_admin.warning_message)) {
        return;
      }
    }
  }
  
  jQuery('#wppatt_tickets_container').html(wppatt_admin.loading_html);

  var data = {
    action: 'wppatt_tickets',
    setting_action : 'ticket_list'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('#wppatt_tickets_container').html(response);
  });
  if (is_tinymce) {
    tinyMCE.activeEditor.setContent('');
  }
}

function wppatt_get_individual_ticket(e){
    
  var ticket_id = jQuery(e).data('id');
  wppatt_open_ticket(ticket_id);

}

function wppatt_open_ticket(ticket_id){
  jQuery('#wppatt_tickets_container').html(wppatt_admin.loading_html);
  //wppatt_doScrolling('#wppatt_tickets_container',1000);
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'individual_ticket',
    ticket_id : ticket_id
  };
  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('#wppatt_tickets_container').html(response);
  });
}

function wppatt_get_create_ticket(){
  
  var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
  if (jQuery('#wppatt_individual_new_ticket_btn').is(":visible") && is_tinymce){
    var description = tinyMCE.activeEditor.getContent().trim();
    if(description.length != 0){
      if(!confirm(wppatt_admin.warning_message)) {
        return;
      }
    }
  }
  
  jQuery('#wppatt_tickets_container').html(wppatt_admin.loading_html);

  var data = {
    action: 'wppatt_tickets',
    setting_action : 'create_ticket'
  };

  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('#wppatt_tickets_container').html(response);
  });
  
  if(is_tinymce) tinyMCE.activeEditor.setContent('');
}

function wppatt_create_ticket_init(wppatt_setting_action,attrs){
  
  jQuery('#wppatt_tickets_container').html(wppatt_admin.loading_html);
  var data = {
    action: 'wppatt_tickets',
    setting_action : wppatt_setting_action,
  };
  
  jQuery.each( attrs, function( key, value ) {
    data[key] = value;
  });
  
  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('#wppatt_tickets_container').html(response);
    jQuery('.wppatt_tl_action_bar').hide();
    jQuery('#wppatt_insert_macros').hide();
  });
}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function validateURL(url){
    var re = /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/;
    return re.test(url);
}

function attachment_cancel( obj ){
    jQuery(obj).parent().remove();
}

function wppatt_doScrolling(element, duration) {
	var startingY = window.pageYOffset;
  var elementY = wppatt_getElementY(element);
  var targetY = document.body.scrollHeight - elementY < window.innerHeight ? document.body.scrollHeight - window.innerHeight : elementY;
	var diff = targetY - startingY;
  var easing = function (t) { return t<.5 ? 4*t*t*t : (t-1)*(2*t-2)*(2*t-2)+1 }
  var start;
  if (!diff) return;
  window.requestAnimationFrame(function step(timestamp) {
    if (!start) start = timestamp;
    var time = timestamp - start;
		var percent = Math.min(time / duration, 1);
    percent = easing(percent);
    window.scrollTo(0, startingY + diff * percent);
    if (time < duration) {
      window.requestAnimationFrame(step);
    }
  });
}

function wppatt_getElementY(query) {
  return window.pageYOffset + document.querySelector(query).getBoundingClientRect().top
}

function show_custom_filters(){
  jQuery('.wpsp_custom_filter_container').show();
}

function wppatt_close_custom_filter(){
  jQuery('.wpsp_custom_filter_container').hide();
}

function wppatt_get_save_ticket_filter(){
  wppatt_modal_open(wppatt_admin.save_filter);
  var data = {
    action: 'wppatt_tickets',
    setting_action: 'get_save_ticket_filter'
  };
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    jQuery('#wppatt_popup_body').html(response.body);
    jQuery('#wppatt_popup_footer').html(response.footer);
  });
}
function wppatt_set_saved_filter(key){
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'set_saved_filter',
    key: key
  };
  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    wppatt_get_ticket_list();
  });
}

function wppatt_delete_saved_filter(key){
  if( !confirm(wppatt_admin.are_you_sure) ){
    return;
  }
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'delete_saved_filter',
    key: key
  };
  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    wppatt_get_ticket_list();
  });
}

function wppatt_set_default_filter(label){
  jQuery('.checkbox_depend').addClass('hidden');
  
  jQuery('.wpsp_sidebar_labels').removeClass('active');
  jQuery('.wpsp_sidebar_labels.'+label).addClass('active');
  jQuery('.wppatt_ticket_list_container').html(wppatt_admin.loading_html);
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'set_default_filter',
    label: label
  };
  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    wppatt_get_tickets();
  });
}

function wppatt_get_tickets(){
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'get_ticket_list'
  };
  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('.wppatt_ticket_list_container').html(response);
  });
}

function wppatt_set_custom_filter(){
  var dataform = new FormData(jQuery('#frm_additional_filters')[0]);
  jQuery('.wppatt_ticket_list_container').html(wppatt_admin.loading_html);
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    wppatt_get_tickets();
    //wppatt_doScrolling('#wppatt_tickets_container',1000);
  });
}

function wppatt_header_sort(slug){
  var orderby = jQuery('#wppatt_th_orderby').val().trim();
  var order   = jQuery('#wppatt_th_order').val().trim();
  if (orderby == slug) {
    orderby = slug;
    order   = (order == wppatt_admin.asc ? wppatt_admin.desc: wppatt_admin.asc);
  } else {
    orderby = slug;
    order   = wppatt_admin.asc;
  }
  jQuery('#wppatt_th_orderby').val(orderby);
  jQuery('#wppatt_th_order').val(order);
  jQuery('#wppatt_pg_no').val('1');
  wppatt_set_custom_filter();
}


function toggle_list_checkboxes(obj){
	if(jQuery(obj).is(':checked')){
    jQuery('.chk_ticket_list_item:enabled').prop('checked',true);
	}else{
		jQuery('.chk_ticket_list_item:enabled').prop('checked',false);
	}
  toggle_ticket_list_actions();
}

function wppatt_ticket_next_page(){
  var page_no = parseInt(jQuery('#wppatt_pg_no').val().trim());
  if( page_no){
      page_no++;
      jQuery('#wppatt_pg_no').val(page_no);
      wppatt_set_custom_filter();
  }
}

function wppatt_ticket_prev_page(){
  var page_no = parseInt(jQuery('#wppatt_pg_no').val().trim());
  if( page_no > 1){
      page_no--;
      jQuery('#wppatt_pg_no').val(page_no);
      wppatt_set_custom_filter();
  }
}

function toggle_ticket_list_actions(){
  
  var checked = jQuery('#tbl_wppatt_ticket_list').find('.chk_ticket_list_item:checked');
  if(checked.length==0){
      jQuery('.checkbox_depend').addClass('hidden');
  } else {
      //jQuery('.checkbox_depend').removeClass('hidden');
      var class_list=jQuery('.wpsp_sidebar_labels.active').prop('class');
      var index=class_list.indexOf('deleted');
      
      if(index!=-1){
        //deleted filter active
        jQuery('.checkbox_depend').addClass('hidden');
        jQuery('#btn_delete_permanently_bulk_ticket').removeClass('hidden');
      }else{
        //other filters
        jQuery('.checkbox_depend').removeClass('hidden');
        jQuery('#btn_delete_permanently_bulk_ticket').addClass('hidden');
        jQuery('#btn_restore_tickets').addClass('hidden');
      }
      
  }
}

function show_custom_filters(){
     jQuery('.wpsp_custom_filter_container').toggle('show');
}

function wppatt_get_agent_setting(){
  wppatt_modal_open(wppatt_admin.agent_setting);
  var data = {
     action: 'wppatt_support_agents',
     setting_action : 'get_agent_setting'
   };
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
     var response = JSON.parse(response_str);          
     jQuery('#wppatt_popup_body').html(response.body);
     jQuery('#wppatt_popup_footer').html(response.footer);
 });
}

function wppatt_set_agent_setting(){
    var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
    if (is_tinymce){
      var agent_setting = tinyMCE.get('wppatt_agent_signature').getContent().trim();
    }else{
      var agent_setting = jQuery('#wppatt_agent_signature').val();
   }
   var dataform = new FormData(jQuery('#wppatt_frm_agent_setting')[0]);
   dataform.append('wppatt_agent_signature', agent_setting);
   dataform.append('action', 'wppatt_support_agents');
   dataform.append('setting_action', 'set_agent_setting');
   wppatt_modal_close();
   jQuery('.wppatt_ticket_list_container').html(wppatt_admin.loading_html);
   jQuery.ajax({
     url: wppatt_admin.ajax_url,
     type: 'POST',
     data: dataform,
     processData: false,
     contentType: false
   })
   .done(function (response_str) {  
      wppatt_get_ticket_list();    
   });   
   if (is_tinymce) {
     tinyMCE.activeEditor.setContent('');
   }  
}
 
function wppatt_get_bulk_change_status(){
  wppatt_modal_open(wppatt_admin.change_ticket_status);
  var checked = jQuery('#tbl_wppatt_ticket_list').find('.chk_ticket_list_item:checked');
  if(checked.length!=0){
    var values = jQuery('.chk_ticket_list_item:checked').map(function(){return this.value;}).get();
    var ticket_id = String(values);
    var data = {
      action: 'wppatt_tickets',
      setting_action : 'get_bulk_change_status',
      ticket_id: ticket_id
    }
    jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
      var response = JSON.parse(response_str);
      jQuery('#wppatt_popup_body').html(response.body);
      jQuery('#wppatt_popup_footer').html(response.footer);
    });
  }
}

function wppatt_set_bulk_change_status(){
  var dataform = new FormData(jQuery('#frm_ticket_change_status')[0]);
  wppatt_modal_close();
  jQuery('.wppatt_ticket_list_container').html(wppatt_admin.loading_html);
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    toggle_ticket_list_actions();
    wppatt_get_tickets();
  });
}

function wppatt_get_delete_bulk_ticket(){
    wppatt_modal_open(wppatt_admin.delete_tickets);
    var checked = jQuery('#tbl_wppatt_ticket_list').find('.chk_ticket_list_item:checked');
    if(checked.length!=0){
        var values = jQuery('.chk_ticket_list_item:checked').map(function () {
          return this.value;
        }).get();

        var ticket_id=String(values);

        var data = {
            action: 'wppatt_tickets',
            setting_action : 'get_delete_bulk_ticket',
            ticket_id: ticket_id
        }

        jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
         var response = JSON.parse(response_str);
         jQuery('#wppatt_popup_body').html(response.body);
         jQuery('#wppatt_popup_footer').html(response.footer);
       });
    }
}

function wppatt_get_bulk_assign_agent(){
    wppatt_modal_open(wppatt_admin.assign_agent);
    var checked = jQuery('#tbl_wppatt_ticket_list').find('.chk_ticket_list_item:checked');

    if(checked.length!=0){
       var values = jQuery('.chk_ticket_list_item:checked').map(function () {
       return this.value;
       }).get();

       var ticket_id=String(values);

       var data = {
         action: 'wppatt_tickets',
         setting_action : 'get_bulk_assign_agent',
         ticket_id: ticket_id
       }

       jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
        var response = JSON.parse(response_str);
        jQuery('#wppatt_popup_body').html(response.body);
        jQuery('#wppatt_popup_footer').html(response.footer);
       });
    }
}

function wppatt_set_delete_bulk_ticket(){
  var dataform = new FormData(jQuery('#frm_delete_bulk_ticket')[0]);
  wppatt_modal_close();
  jQuery('.wppatt_ticket_list_container').html(wppatt_admin.loading_html);
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    toggle_ticket_list_actions();
    wppatt_get_tickets();
  });
}

function wppatt_set_bulk_assign_agent(){
   var dataform = new FormData(jQuery('#frm_bulk_assigned_agents')[0]);
   wppatt_modal_close();
   jQuery('.wppatt_ticket_list_container').html(wppatt_admin.loading_html);
   jQuery.ajax({
     url: wppatt_admin.ajax_url,
     type: 'POST',
     data: dataform,
     processData: false,
     contentType: false
   })
   .done(function (response_str) {
     toggle_ticket_list_actions();
     wppatt_get_tickets();
   });

}

function wppatt_ticket_thread_expander_toggle(obj){
    var height = parseInt(jQuery(obj).parent().find('.thread_messege').height());
    if( height === 100 ){
        jQuery(obj).parent().find('.thread_messege').height('auto');
        jQuery(obj).text(wppatt_admin.view_less);
    } else {
        jQuery(obj).parent().find('.thread_messege').height(100);
        jQuery(obj).text(wppatt_admin.view_more);
    }
}

function wppatt_get_change_ticket_status(ticket_id){
  wppatt_modal_open(wppatt_admin.change_ticket_status);
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'get_change_ticket_status',
    ticket_id: ticket_id
  }
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    jQuery('#wppatt_popup_body').html(response.body);
    jQuery('#wppatt_popup_footer').html(response.footer);
  });
}
    
function wppatt_get_change_ticket_fields(ticket_id){
  wppatt_modal_open('Change Request Fields');
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'get_change_ticket_fields',
    ticket_id: ticket_id
  }
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) { 
    var response = JSON.parse(response_str);  
    jQuery('#wppatt_popup_body').html(response.body);
    jQuery('#wppatt_popup_footer').html(response.footer);
  });
}

function wppatt_set_change_ticket_status(ticket_id){
  
  if (wppatt_check_desc_empty()) {
    if(!confirm(wppatt_admin.warning_message)) {
      wppatt_modal_close();
      return;
    }
  }
  var dataform = new FormData(jQuery('#frm_get_ticket_change_status')[0]);
  wppatt_modal_close();
  jQuery('.wppatt_reply_widget').html(wppatt_admin.loading_html);
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
     wppatt_open_ticket(ticket_id);
  });  
  
}

function wppatt_get_change_assign_agent(ticket_id){
  wppatt_modal_open('Assign Agent'); 
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'get_change_assign_agent',
    ticket_id: ticket_id
  }
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) { 
    var response = JSON.parse(response_str);    
    jQuery('#wppatt_popup_body').html(response.body);
    jQuery('#wppatt_popup_footer').html(response.footer);
  });
}

function wppatt_get_delete_ticket(ticket_id){
  wppatt_modal_open('Delete Ticket'); 
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'get_delete_ticket',
    ticket_id: ticket_id
  }
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) { 
    var response = JSON.parse(response_str);    
    jQuery('#wppatt_popup_body').html(response.body);
    jQuery('#wppatt_popup_footer').html(response.footer);
  });
}

function wppatt_set_change_assign_agent(ticket_id){
  
  if (wppatt_check_desc_empty()) {
    if(!confirm(wppatt_admin.warning_message)) {
      wppatt_modal_close();
      return;
    }
  }
  
  var dataform = new FormData(jQuery('#frm_get_ticket_assign_agent')[0]);
  wppatt_modal_close();
  jQuery('.wppatt_reply_widget').html(wppatt_admin.loading_html);
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
     wppatt_open_ticket(ticket_id);
  });  
}

function wppatt_get_edit_thread(ticket_id,thread_id){

  wppatt_modal_open(wppatt_admin.edit_this_thread);
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'get_edit_thread',
    ticket_id:  ticket_id,
    thread_id : thread_id
  }
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    jQuery('#wppatt_popup_body').html(response.body);
    jQuery('#wppatt_popup_footer').html(response.footer);
  });
}

function wppatt_get_close_ticket(ticket_id){
  wppatt_modal_open(wppatt_admin.close_ticket);
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'get_close_ticket',
    ticket_id: ticket_id
  }
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    jQuery('#wppatt_popup_body').html(response.body);
    jQuery('#wppatt_popup_footer').html(response.footer);
  });
}

function wppatt_get_clone_ticket(ticket_id){
  wppatt_modal_open(wppatt_admin.clone_ticket);
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'get_clone_ticket',
    ticket_id: ticket_id
  }
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    jQuery('#wppatt_popup_body').html(response.body);
    jQuery('#wppatt_popup_footer').html(response.footer);
  });
}

function wppatt_set_edit_thread(ticket_id){
  
   var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
   if(is_tinymce){
      var description = tinyMCE.get('wppatt_therad_edit').getContent().trim();
   }else{
     var description = jQuery('#wppatt_therad_edit').val();
   }
   var dataform = new FormData(jQuery('#frm_edit_thread')[0]);
   dataform.append('body', description);
   dataform.append('action', 'wppatt_tickets');
   dataform.append('setting_action', 'set_edit_thread');
   wppatt_modal_close();
   jQuery('.wppatt_reply_widget').html(wppatt_admin.loading_html);
   jQuery.ajax({
     url: wppatt_admin.ajax_url,
     type: 'POST',
     data: dataform,
     processData: false,
     contentType: false
   })
   .done(function (response_str) {  
       wppatt_open_ticket(ticket_id);
   });      
}

function wppatt_get_delete_thread(ticket_id,thread_id){
  
  wppatt_modal_open(wppatt_admin.delete_this_thread);
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'get_delete_thread',
    ticket_id:  ticket_id,
    thread_id : thread_id
  }
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    jQuery('#wppatt_popup_body').html(response.body);
    jQuery('#wppatt_popup_footer').html(response.footer);
  });
}

function wppatt_edit_ticket_subject(ticket_id){
  wppatt_modal_open(wppatt_admin.edit_subject);
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'get_edit_ticket_subject',
    ticket_id: ticket_id
  }
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    jQuery('#wppatt_popup_body').html(response.body);
    jQuery('#wppatt_popup_footer').html(response.footer);
  });
}

function wppatt_set_delete_thread(ticket_id){
  var dataform = new FormData(jQuery('#frm_delete_thread')[0]);
  wppatt_modal_close();
  jQuery('.wppatt_reply_widget').html(wppatt_admin.loading_html);
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    wppatt_open_ticket(ticket_id);
  });   
}

function wppatt_set_close_ticket(ticket_id){
  
  if (wppatt_check_desc_empty()) {
    if(!confirm(wppatt_admin.warning_message)) {
      wppatt_modal_close();
      return;
    }
  }
  var dataform = new FormData(jQuery('#frm_close_ticket')[0]);
  jQuery('.wppatt_reply_widget').html(wppatt_admin.loading_html);
  wppatt_modal_close();
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    wppatt_open_ticket(ticket_id);
  });
}

function wppatt_set_edit_ticket_subject(ticket_id){
  var dataform = new FormData(jQuery('#frm_edit_subject')[0]);
  var subject = jQuery('#subject').val();
  jQuery('.wppatt_reply_widget').html(wppatt_admin.loading_html);
  wppatt_modal_close();
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    wppatt_open_ticket(ticket_id);
  });   
}

function wppatt_set_clone_ticket(){
  
  if (wppatt_check_desc_empty()) {
    if(!confirm(wppatt_admin.warning_message)) {
      wppatt_modal_close();
      return;
    }
  }
  var dataform = new FormData(jQuery('#frm_edit_clone_subject')[0]);
  var subject = jQuery('#subject').val();
  jQuery('.wppatt_reply_widget').html(wppatt_admin.loading_html);
  wppatt_modal_close();
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    
    ticket_id = JSON.parse(response_str);
    wppatt_open_ticket(ticket_id);
  });
}

function wppatt_set_delete_ticket(){
  
  if (wppatt_check_desc_empty()) {
    if(!confirm(wppatt_admin.warning_message)) {
      wppatt_modal_close();
      return;
    }
  }
  var dataform = new FormData(jQuery('#frm_delete_ticket')[0]);
   wppatt_modal_close();
    jQuery.ajax({
      url: wppatt_admin.ajax_url,
      type: 'POST',
      data: dataform,
      processData: false,
      contentType: false
    })
    .done(function (response_str) {
       wppatt_get_ticket_list();
    });  
}

function wppatt_get_change_raised_by(ticket_id){
  wppatt_modal_open(wppatt_admin.change_raised_by); 
   var data = {
     action: 'wppatt_tickets',
     setting_action : 'get_change_raised_by',
     ticket_id: ticket_id
   }
   jQuery.post(wppatt_admin.ajax_url, data, function(response_str) { 
     var response = JSON.parse(response_str);  
     jQuery('#wppatt_popup_body').html(response.body);
     jQuery('#wppatt_popup_footer').html(response.footer);
   })
}

function wppatt_set_change_raised_by(ticket_id){
  
  if (wppatt_check_desc_empty()) {
    if(!confirm(wppatt_admin.warning_message)) {
      wppatt_modal_close();
      return;
    }
  }
  var dataform = new FormData(jQuery('#frm_get_ticket_raised_by')[0]);
  if(jQuery('#customer_name').val().trim()==''){
    alert(wppatt_admin.customer_name);
    return;
  }
  
  if(jQuery('#customer_email').val().trim()==''){
    alert(wppatt_admin.customer_email);
    return;
  }
  wppatt_modal_close();
  jQuery('.wppatt_reply_widget').html(wppatt_admin.loading_html);
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
     wppatt_open_ticket(ticket_id);
  });  
}

function wppatt_get_restore_bulk_ticket(){
  wppatt_modal_open(wppatt_admin.restore_deleted_tickets);
  var checked = jQuery('#tbl_wppatt_ticket_list').find('.chk_ticket_list_item:checked');
  if(checked.length!=0){
      var values = jQuery('.chk_ticket_list_item:checked').map(function () {
        return this.value;
      }).get();

      var ticket_id=String(values);

      var data = {
          action: 'wppatt_tickets',
          setting_action : 'get_bulk_restore_ticket',
          ticket_id: ticket_id
      }

      jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
       var response = JSON.parse(response_str);
       jQuery('#wppatt_popup_body').html(response.body);
       jQuery('#wppatt_popup_footer').html(response.footer);
     });
  }
}

function wppatt_set_bulk_restore_ticket(){
   var dataform = new FormData(jQuery('#frm_bulk_restore_ticket')[0]);
   wppatt_modal_close();
   jQuery('.wppatt_ticket_list_container').html(wppatt_admin.loading_html);
   jQuery.ajax({
     url: wppatt_admin.ajax_url,
     type: 'POST',
     data: dataform,
     processData: false,
     contentType: false
   })
   .done(function (response_str) {
     toggle_ticket_list_actions();
     wppatt_get_tickets();
   });

}

function get_restore_ticket(ticket_id){
  wppatt_modal_open(wppatt_admin.restore_ticket);
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'get_restore_ticket',
    ticket_id: ticket_id
  }
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    jQuery('#wppatt_popup_body').html(response.body);
    jQuery('#wppatt_popup_footer').html(response.footer);
  });
}

function wppatt_set_restore_ticket(ticket_id){
  var dataform = new FormData(jQuery('#frm_restore_ticket')[0]);
  jQuery('.wppatt_reply_widget').html(wppatt_admin.loading_html);
  wppatt_modal_close();
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    wppatt_open_ticket(ticket_id);
  });
}

function wppatt_set_change_ticket_fields(ticket_id){  
  
  if (wppatt_check_desc_empty()) {
    if(!confirm(wppatt_admin.warning_message)) {
      wppatt_modal_close();
      return;
    }
  }
  var dataform = new FormData(jQuery('#frm_get_ticket_fields')[0]);
  wppatt_modal_close();
  jQuery('.wppatt_reply_widget').html(wppatt_admin.loading_html);
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
     wppatt_open_ticket(ticket_id);
  });  
}

function wppatt_delete_attached_files(attachment_id,ticket_id,attachment_slug){
  var flag = confirm(wppatt_admin.are_you_sure);
  		if (flag) {
        var data = {
          action:          'wppatt_tickets',
          setting_action:  'set_delete_attached_files',
          attachment_id:   attachment_id,
          ticket_id:       ticket_id,
          attachment_slug: attachment_slug
        }
        jQuery.post(wppatt_admin.ajax_url, data, function(response_str) { 
          jQuery('#attach_'+attachment_id).remove();
          if( jQuery('#frm_get_ticket_fields').is(':visible') && jQuery('#frm_get_ticket_fields .wppatt_attachment_tbl input:hidden[name="'+attachment_slug+'[]"]').length==0){
            jQuery('#frm_get_ticket_fields .wppatt_attachment_tbl').append('<input type="hidden" name="'+attachment_slug+'" value="">');
          }else if(jQuery('#frm_get_agent_fields').is(':visible') && jQuery('#frm_get_agent_fields .wppatt_attachment_tbl input:hidden[name="'+attachment_slug+'[]"]').length==0){
            jQuery('#frm_get_agent_fields .wppatt_attachment_tbl').append('<input type="hidden" name="'+attachment_slug+'" value="">');
          }
        });
    }
}            
function wppatt_get_change_agent_fields(ticket_id){
  wppatt_modal_open(wppatt_admin.change_agent_fields);
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'get_change_agent_fields',
    ticket_id: ticket_id
  }
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    jQuery('#wppatt_popup_body').html(response.body);
    jQuery('#wppatt_popup_footer').html(response.footer);
  });
}

function wppatt_set_change_agent_fields(ticket_id){
  
  if (wppatt_check_desc_empty()) {
    if(!confirm(wppatt_admin.warning_message)) {
      wppatt_modal_close();
      return;
    }
  }
  var dataform = new FormData(jQuery('#frm_get_agent_fields')[0]);
  wppatt_modal_close();
  jQuery('.wppatt_reply_widget').html(wppatt_admin.loading_html);
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
     wppatt_open_ticket(ticket_id);
  });  
}    

function wppatt_signup_user(){
  jQuery('#wppatt_tickets_container').html(wppatt_admin.loading_html);
  
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'sign_up_user'
  };
  jQuery.post(wppatt_admin.ajax_url, data, function(response) {
    jQuery('#wppatt_tickets_container').html(response);
  });
}

function wppatt_set_cron_setup_settings() {
  jQuery('.wppatt_submit_wait').show();
  var dataform = new FormData(jQuery('#wppatt_set_cron_setup_settings')[0]);
  
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

function wppatt_delete_ticket_permanently(ticket_id){
    wppatt_modal_open(wppatt_admin.delete_ticket_permanently);
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'get_delete_ticket_permanently',
    ticket_id: ticket_id
  }
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    jQuery('#wppatt_popup_body').html(response.body);
    jQuery('#wppatt_popup_footer').html(response.footer);
  });
}

function wppatt_set_delete_ticket_permanently(){
  var dataform = new FormData(jQuery('#frm_delete_ticket')[0]);
  jQuery('.wppatt_reply_widget').html(wppatt_admin.loading_html);
  wppatt_modal_close();
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    wppatt_get_ticket_list();
  });
}

function wppatt_get_delete_permanently_bulk_ticket(){
  wppatt_modal_open(wppatt_admin.delete_tickets_permanently);
  var checked = jQuery('#tbl_wppatt_ticket_list').find('.chk_ticket_list_item:checked');
  if(checked.length!=0){
      var values = jQuery('.chk_ticket_list_item:checked').map(function () {
        return this.value;
      }).get();

      var ticket_id =String(values);

      var data = {
          action: 'wppatt_tickets',
          setting_action : 'get_delete_permanently_bulk_ticket',
          ticket_id: ticket_id
      }

      jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
       var response = JSON.parse(response_str);
       jQuery('#wppatt_popup_body').html(response.body);
       jQuery('#wppatt_popup_footer').html(response.footer);
     });
  }
}

function wppatt_set_delete_permanently_bulk_ticket(){
   var dataform = new FormData(jQuery('#frm_bulk_delete_ticket')[0]);
   wppatt_modal_close();
   jQuery('.wppatt_ticket_list_container').html(wppatt_admin.loading_html);
   jQuery.ajax({
     url: wppatt_admin.ajax_url,
     type: 'POST',
     data: dataform,
     processData: false,
     contentType: false
   })
   .done(function (response_str) {
     toggle_ticket_list_actions();
     wppatt_get_ticket_list();
   });
  
}

function wppatt_get_create_thread(ticket_id,thread_id){
  
   wppatt_modal_open(wppatt_admin.create_thread_ticket);
   var data = {
     action: 'wppatt_tickets',
     setting_action : 'get_create_thread',
     ticket_id:  ticket_id,
     thread_id : thread_id
   }
   jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
     var response = JSON.parse(response_str);
     jQuery('#wppatt_popup_body').html(response.body);
     jQuery('#wppatt_popup_footer').html(response.footer);
   });
}

/**
 * wppatt_set_new_ticket_thread 
 */
function wppatt_set_new_ticket_thread(){
  
  var dataform = new FormData(jQuery('#create_ticket_thread')[0]);
  var subject = jQuery('#subject').val();
  jQuery('.wppatt_reply_widget').html(wppatt_admin.loading_html);
  wppatt_modal_close();
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    ticket_id = JSON.parse(response_str);
    wppatt_open_ticket(ticket_id);
  });
}

function wppatt_get_tinymce(selector,body_id){
  jQuery('#visual').addClass('btn btn-primary');
  jQuery('#text').removeClass('btn btn-primary');
  jQuery('#text').addClass('btn btn-default');
  tinymce.init({ 
	  selector:'#'+selector,
	  body_id: body_id,
	  menubar: false,
		statusbar: false,
	  height : '200',
	  plugins: [
	      'lists link image directionality'
	  ],
	  image_advtab: true,
	  toolbar: 'bold italic underline blockquote | alignleft aligncenter alignright | bullist numlist | rtl | link image',
	  branding: false,
	  autoresize_bottom_margin: 20,
	  browser_spellcheck : true,
	  relative_urls : false,
	  remove_script_host : false,
	  convert_urls : true,
		setup: function (editor) {
	  }
	});
}
	
function wppatt_get_textarea(){
  jQuery('#visual').removeClass('btn btn-primary');
  jQuery('#visual').addClass('btn btn-default');
  jQuery('#text').addClass('btn btn-primary');
  tinymce.remove();
}

function wppatt_get_add_ticket_users(ticket_id){
  wppatt_modal_open(wppatt_admin.additional_recipients); 
   var data = {
     action: 'wppatt_tickets',
     setting_action : 'get_add_ticket_users',
     ticket_id: ticket_id
   }
   jQuery.post(wppatt_admin.ajax_url, data, function(response_str) { 
     var response = JSON.parse(response_str);  
     jQuery('#wppatt_popup_body').html(response.body);
     jQuery('#wppatt_popup_footer').html(response.footer);
   })
}

function wppatt_set_add_ticket_users(ticket_id){
  
  if (wppatt_check_desc_empty()) {
    if(!confirm(wppatt_admin.warning_message)) {
      wppatt_modal_close();
      return;
    }
  }
  
  validation = true;
  var email = jQuery('textarea:input[name=wppatt_ticket_et_user]').val().trim();
  if (email) {
    var lines = email.split("\n");
    
    for (var i = 0; i < lines.length; i++) {
      if (!validateEmail(lines[i])) {
        validation = false;
      }
    }
    
    if (!validation) {
      alert(wppatt_admin.validate_email);
      return false;
    }
  }
  
  var dataform = new FormData(jQuery('#frm_get_add_ticket_users')[0]);
  wppatt_modal_close();
  jQuery('.wppatt_reply_widget').html(wppatt_admin.loading_html);
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    wppatt_open_ticket(ticket_id);
  });
}

function wppatt_get_thread_info(ticket_id,thread_id,source){
  
  if(source == 'thread'){
    wppatt_modal_open(wppatt_admin.extra_thread_info);
  }else {
    wppatt_modal_open(wppatt_admin.extra_ticket_info);
  }
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'get_thread_info',
    ticket_id:  ticket_id,
    thread_id : thread_id,
    source : source
  }
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    jQuery('#wppatt_popup_body').html(response.body);
    jQuery('#wppatt_popup_footer').html(response.footer);
  });
}

function wppatt_get_all_tickets_of_user(ticket_id, customer_name){
  wppatt_modal_open(customer_name + ' (' + wppatt_admin.users_all_tickets + ')'); 
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'get_all_tickets_of_user',
    ticket_id: ticket_id
  }
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) { 
    var response = JSON.parse(response_str);  
    jQuery('#wppatt_popup_body').html(response.body);
    jQuery('#wppatt_popup_footer').html(response.footer);
  })
}

function wppatt_text_limit(event,element,limit){
  if(limit != '0'){
    if(element.value.length > limit-1) {
       event.preventDefault();
    }
  }
}

function wppatt_check_desc_empty(){
  var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
  if(is_tinymce){
   var description = tinyMCE.activeEditor.getContent().trim();
  }else {
    var description = jQuery('#wppatt_reply_box').val();
  }
  var flag = false;
  if(description.length != 0){
    var flag = true;
  }
  return flag;
}

function wppatt_thread_attachment_remove(obj,attachment,post_id,ticket_id){
  if( confirm(wppatt_admin.are_you_sure) ){
   var data = {
     action: 'wppatt_tickets',
     setting_action : 'remove_attachment',
     attachment : attachment,
     post_id : post_id
   };
   jQuery.post(wppatt_admin.ajax_url, data, function(response) {
     jQuery(obj).parent('td').parent('tr').remove();
     wppatt_open_ticket(ticket_id);
    });
  }
}

function wppatt_edit_saved_filter(key){
  wppatt_modal_open(wppatt_admin.edit_custom_filter);
  var data = {
    action: 'wppatt_tickets',
    setting_action : 'update_filter',
    key: key
  };
  jQuery.post(wppatt_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    jQuery('#wppatt_popup_body').html(response.body);
    jQuery('#wppatt_popup_footer').html(response.footer);
  });
}

function wppatt_get_update_ticket_filter(){
  if(jQuery('#filter_save_label').val().trim()==''){
    alert(wppatt_admin.filter_title);
    return;
  }
  var dataform = new FormData(jQuery('#frm_edit_additional_custom_filters')[0]);
  jQuery('.wppatt_popup_action').text(wppatt_admin.popup_wait);
  jQuery.ajax({
    url: wppatt_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    wppatt_modal_close();
    wppatt_get_ticket_list();  
  });
}