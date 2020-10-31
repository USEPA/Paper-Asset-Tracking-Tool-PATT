function get_sla_overdue_report(){
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_rp_sla_reports').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html)
  var data = {
    action: 'get_sla_overdue_report',
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    jQuery('.wpsc_report_setting_pill').html(response_str);
  });
}

function wpsc_sla_report_graph(){
  jQuery('#wpsc_sla_graph').html('');
  var dataform = new FormData(jQuery('#frm_sla_reports')[0]);
  date_filter =  jQuery('#wpsc_sla_month_filters').val();
  dataform.append('date_filter',date_filter);
  var custom_date_start = jQuery('#wpsc_sla_custom_date_start').val();
  var custom_date_end   = jQuery('#wpsc_sla_custom_date_end').val();
  dataform.append('custom_date_start',custom_date_start);
  dataform.append('custom_date_end',custom_date_end);
  jQuery('#wpsc_sla_graph').html(wpsc_admin.loading_html);
  jQuery.ajax({
    url: wpsc_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    jQuery('#wpsc_sla_graph').html(response_str);
  });
}
