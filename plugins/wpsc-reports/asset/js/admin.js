function get_ticket_stats_report(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_rp_new_ticket_reports').addClass('active');
  jQuery('.wpsc_report_setting_pill').html(wpsc_admin.loading_html);
  var data = {
    action: 'get_ticket_stats_report',
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    jQuery('.wpsc_report_setting_pill').html(response_str);
  });
}

function wpsc_ticket_stat_report_graph(){
  jQuery('#wpsc_all_graph').html('');
  var dataform = new FormData(jQuery('#frm_reports_filters')[0]);
  var date_filter =  jQuery('#wpsc_ticket_stats_month_filters').val();
  dataform.append('date_filter',date_filter);
  var custom_date_start = jQuery('#wpsc_ticket_stats_custom_date_start').val();
  var custom_date_end   = jQuery('#wpsc_ticket_stats_custom_date_end').val();
  dataform.append('custom_date_start',custom_date_start);
  dataform.append('custom_date_end',custom_date_end);
  jQuery('#wpsc_all_graph').html(wpsc_admin.loading_html);
  jQuery.ajax({
    url: wpsc_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    jQuery('#wpsc_all_graph').html(response_str);
  });
    
}
function get_first_response_time_reports(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_rp_first_response_time_reports').addClass('active');
  jQuery('.wpsc_report_setting_pill').html(wpsc_admin.loading_html)
  var data = {
    action: 'get_first_response_time_reports',
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    jQuery('.wpsc_report_setting_pill').html(response_str);
  });
}




function wpsc_get_tickets_stats_reports_graph(){
  jQuery('#wpsc_new_ticket_graph').html('');
  var date = jQuery('#wpsc_new_ticket_month_filters').val();
  var data = {
    action : 'get_tickets_stats_reports_graph',
    date : date
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    jQuery('.wpsc_new_ticket_graph').html(response_str);
  });
}


function wpsc_get_first_response_time_reports_graph(){
  jQuery('#wpsc_first_response_time_graph').html('');
  var dataform = new FormData(jQuery('#frm_first_response_time_filters')[0]);
  date_filter =  jQuery('#wpsc_first_response_time_month_filters').val();
  dataform.append('date_filter',date_filter);
  var custom_date_start = jQuery('#wpsc_first_response_time_custom_date_start').val();
  var custom_date_end   = jQuery('#wpsc_first_response_time_custom_date_end').val();
  dataform.append('custom_date_start',custom_date_start);
  dataform.append('custom_date_end',custom_date_end);
  jQuery('#wpsc_first_response_time_graph').html(wpsc_admin.loading_html);
  jQuery.ajax({
    url: wpsc_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    jQuery('#wpsc_first_response_time_graph').html(response_str);
  });
}

function get_category_report(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_rp_category_reports').addClass('active');
  jQuery('.wpsc_report_setting_pill').html(wpsc_admin.loading_html)
  var data = {
    action: 'get_category_report',
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    jQuery('.wpsc_report_setting_pill').html(response_str);
  });
}

function wpsc_ticket_category_report_graph(){
  jQuery('#wpsc_category_graph').html('');
  var dataform = new FormData(jQuery('#frm_category_reports')[0]);
  date_filter =  jQuery('#wpsc_ticket_category_month_filters').val();
  dataform.append('date_filter',date_filter);
  var custom_date_start = jQuery('#wpsc_ticket_category_custom_date_start').val();
  var custom_date_end   = jQuery('#wpsc_ticket_category_custom_date_end').val();
  dataform.append('custom_date_start',custom_date_start);
  dataform.append('custom_date_end',custom_date_end);
  jQuery('#wpsc_category_graph').html(wpsc_admin.loading_html);
  jQuery.ajax({
    url: wpsc_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    jQuery('#wpsc_category_graph').html(response_str);
  });
}

function get_all_dropdown_report(term_id){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#' + term_id ).addClass('active');
  jQuery('.wpsc_report_setting_pill').html(wpsc_admin.loading_html)
  var data = {
    action: 'get_all_dropdown_report',
    term_id : term_id
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    jQuery('.wpsc_report_setting_pill').html(response_str);
  });
}

function wpsc_custom_field_dropdown_report_graph(){
  jQuery('#wpsc_dropdown_graph').html('');
  var dataform = new FormData(jQuery('#frm_dropdown_reports')[0]);
  date_filter =  jQuery('#wpsc_custom_field_dropdown_month_filters').val();
  dataform.append('date_filter',date_filter);
  var custom_date_start = jQuery('#wpsc_custom_field_dropdown_custom_date_start').val();
  var custom_date_end   = jQuery('#wpsc_custom_field_dropdown_custom_date_end').val();
  dataform.append('custom_date_start',custom_date_start);
  dataform.append('custom_date_end',custom_date_end);
  jQuery('#wpsc_dropdown_graph').html(wpsc_admin.loading_html);
  jQuery.ajax({
    url: wpsc_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    jQuery('#wpsc_dropdown_graph').html(response_str);
  });
}

function get_all_checkbox_report(term_id){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#' + term_id).addClass('active');
  jQuery('.wpsc_report_setting_pill').html(wpsc_admin.loading_html)
  var data = {
    action: 'get_all_checkbox_report',
    term_id : term_id
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    jQuery('.wpsc_report_setting_pill').html(response_str);
  });
}

function wpsc_custom_field_checkbox_report_graph(){
  jQuery('#wpsc_checkbox_graph').html('');
  var dataform = new FormData(jQuery('#frm_checkbox_reports')[0]);
  date_filter =  jQuery('#wpsc_custom_field_checkbox_month_filters').val();
  dataform.append('date_filter',date_filter);
  var custom_date_start = jQuery('#wpsc_custom_field_checkbox_custom_date_start').val();
  var custom_date_end   = jQuery('#wpsc_custom_field_checkbox_custom_date_end').val();
  dataform.append('custom_date_start',custom_date_start);
  dataform.append('custom_date_end',custom_date_end);
  jQuery('#wpsc_checkbox_graph').html(wpsc_admin.loading_html);
  jQuery.ajax({
    url: wpsc_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    jQuery('#wpsc_checkbox_graph').html(response_str);
  });
}

function get_all_radio_button_report(term_id){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#' + term_id).addClass('active');
  jQuery('.wpsc_report_setting_pill').html(wpsc_admin.loading_html)
  var data = {
    action: 'get_all_radio_button_report',
    term_id : term_id
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    jQuery('.wpsc_report_setting_pill').html(response_str);
  });
}

function wpsc_custom_field_radio_button_report_graph(){
  jQuery('#wpsc_radio_button_graph').html('');
  var dataform = new FormData(jQuery('#frm_radio_button_reports')[0]);
  date_filter =  jQuery('#wpsc_custom_field_radio_button_month_filters').val();
  dataform.append('date_filter',date_filter);
  var custom_date_start = jQuery('#wpsc_custom_field_radio_button_custom_date_start').val();
  var custom_date_end   = jQuery('#wpsc_custom_field_radio_button_custom_date_end').val();
  dataform.append('custom_date_start',custom_date_start);
  dataform.append('custom_date_end',custom_date_end);
  jQuery('#wpsc_radio_button_graph').html(wpsc_admin.loading_html);
  jQuery.ajax({
    url: wpsc_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    jQuery('#wpsc_radio_button_graph').html(response_str);
  });
}

function get_active_customers_report(){
  jQuery('#wpsc_active_customers_list').html(wpsc_admin.loading_html)
  var data = {
    action: 'get_active_customers_report',
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    jQuery('#wpsc_active_customers_list').html(response_str);
  });
}

function get_dashboard_report(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_rp_dashboard_reports').addClass('active');
  jQuery('.wpsc_report_setting_pill').html(wpsc_admin.loading_html)
  var data = {
    action: 'get_dashboard_report',
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    jQuery('.wpsc_report_setting_pill').html(response_str);
  });
}

function get_active_customers_filter(){
  var dataform = new FormData(jQuery('#frm_active_customers_reports')[0]);
  dataform.append('action','get_active_customers_filter');
  jQuery('#wpsc_active_customers_list').html(wpsc_admin.loading_html);
  jQuery.ajax({
    url: wpsc_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    get_active_customers_report();
  });
}

function wpsc_active_customers_prev_page(){
	
	var page_no = parseInt(jQuery('#wpsc_pg_no').val().trim());
  if( page_no > 1){
			page_no--;
			jQuery('#wpsc_pg_no').val(page_no);
			get_active_customers_filter();
	}
}

function wpsc_active_customers_next_page(){
	
	var page_no = parseInt(jQuery('#wpsc_pg_no').val());
	if( page_no){
			page_no++;
			jQuery('#wpsc_pg_no').val(page_no);
			get_active_customers_filter();
	}
}

function set_active_customers_default_filter(){
  jQuery('#wpsc_active_customers_list').html(wpsc_admin.loading_html);
  var data = {
    action: 'set_active_customers_default_filter',
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    get_active_customers_report();
  });
}

function wpsc_get_recalculate_first_response_time(){
  jQuery('#wpsc_first_response_time_graph').html('');
  var dataform = new FormData(jQuery('#frm_first_response_time_filters')[0]);
  dataform.append('action','get_recalculate_first_response_time');
  date_filter =  jQuery('#wpsc_first_response_time_month_filters').val();
  dataform.append('date_filter',date_filter);
  var custom_date_start = jQuery('#wpsc_first_response_time_custom_date_start').val();
  var custom_date_end   = jQuery('#wpsc_first_response_time_custom_date_end').val();
  dataform.append('custom_date_start',custom_date_start);
  dataform.append('custom_date_end',custom_date_end);
  jQuery('#wpsc_first_response_time_graph').html(wpsc_admin.loading_html);
  jQuery.ajax({
    url: wpsc_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    jQuery('#wpsc_first_response_time_graph').html(response_str);
    wpsc_get_first_response_time_reports_graph();
  });
}

function get_active_customers_settings(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_rp_active_customers_reports').addClass('active');
  jQuery('.wpsc_report_setting_pill').html(wpsc_admin.loading_html)
  var data = {
    action: 'get_active_customers_settings',
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    jQuery('.wpsc_report_setting_pill').html(response_str);
  });
}

function wpsc_get_recalculate_ticket_stats() {
  jQuery('#wpsc_all_graph').html('');
  var dataform = new FormData(jQuery('#frm_reports_filters')[0]);
  dataform.append('action', 'get_recalculate_ts');
  var date_filter = jQuery('#wpsc_ticket_stats_month_filters').val();
  dataform.append('date_filter', date_filter);
  var custom_date_start = jQuery('#wpsc_ticket_stats_custom_date_start').val();
  var custom_date_end = jQuery('#wpsc_ticket_stats_custom_date_end').val();
  dataform.append('custom_date_start', custom_date_start);
  dataform.append('custom_date_end', custom_date_end);
  jQuery('#wpsc_all_graph').html(wpsc_admin.loading_html);
  jQuery.ajax({
    url: wpsc_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    jQuery('#wpsc_all_graph').html(response_str);
    wpsc_ticket_stat_report_graph();
  });
}
