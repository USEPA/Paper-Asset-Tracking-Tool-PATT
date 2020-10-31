<?php

final class WPSCExportTicketBackend {
  
  function wpsc_admin_localize_script($localize_script_data){
    $localize_script_data['please_select_at_least_one_column'] = __('Please select at least one column!','wpsc-export-ticket');
    $localize_script_data['export_ticket'] = __('Export Ticket','wpsc-export-ticket');
    return $localize_script_data;
  }
    
  function export_btnAfterDefaultFilter(){
    include WPSC_EXP_DIR . 'includes/admin/wpsc_get_export_file.php';    
  }
  
  function get_export_ticket_field(){    
    include WPSC_EXP_DIR . 'includes/ajax/get_export_ticket_field.php';
    die();
  }
  
  function set_export_ticket(){
    include WPSC_EXP_DIR . 'includes/ajax/set_export_ticket.php';
    die();
  }
  
  function setting_pillsExportTicket(){
    include WPSC_EXP_DIR . 'includes/admin/setting_pillsExportTicket.php';
  }
  
  function get_export_ticket_settings(){
    include WPSC_EXP_DIR . 'includes/ajax/get_export_ticket_settings.php';
    die();
  }
  
  function set_export_ticket_settings(){
    include WPSC_EXP_DIR . 'includes/ajax/set_export_ticket_settings.php';
    die();
  }
  
  // Add-on installed or not for licensing
  function is_add_on_installed($flag){
    return true;
  }
  
  // Print license functionlity for this add-on
  function addon_license_area(){
    include WPSC_EXP_DIR . 'includes/addon_license_area.php';
  }
  
  // Activate Export Ticket license
  function license_activate(){
    include WPSC_EXP_DIR . 'includes/license_activate.php';
    die();
  }
  
  // Deactivate Export Ticket license
  function license_deactivate(){
    include WPSC_EXP_DIR . 'includes/license_deactivate.php';
    die();
  }
  
  /*
    set export menu list order
   */
  function set_export_list_order(){
    include WPSC_EXP_DIR . 'includes/ajax/set_export_list_order.php';
    die();
  }
  
  /**
   *  Add agent only fields in load order
   */
  function add_agentonly_field($term_id){
    global $wpdb;
    $load_order = $wpdb->get_var("select max(CAST(meta_value as UNSIGNED)) as load_order from {$wpdb->prefix}termmeta WHERE meta_key='wpsc_export_ticket_list_order'");
    add_term_meta ($term_id, 'wpsc_export_ticket_list_order', ++$load_order);
  }
  
  /*
  * Add ticket fields    
   */
  function add_ticket_form_field($term_id){
    global $wpdb;
    $load_order = $wpdb->get_var("select max(CAST(meta_value as UNSIGNED)) as load_order from {$wpdb->prefix}termmeta WHERE meta_key='wpsc_export_ticket_list_order'");
    add_term_meta ($term_id, 'wpsc_export_ticket_list_order', ++$load_order);
  }

  // Add export button in report active customers
  function active_customers(){
    ?>
    <button onclick="set_active_customers_export();return false;" style="margin-top:23px;" class="btn btn-sm btn-default"><?php echo _e('Export','wpsc-export-ticket');?></button>
    <script type="text/javascript" >
      function set_active_customers_export(){
         var data = {
           action: 'set_active_customers_export',
      
         };
        jQuery.post(wpsc_admin.ajax_url, data, function(response) {
          var obj = jQuery.parseJSON( response );
          window.open(obj.url_to_export,'_blank');
          wpsc_modal_close();    
          });
      }              
      
    </script>
    <?php
  }

  function set_active_customers_export(){
     include WPSC_EXP_DIR . 'includes/ajax/set_active_customers_export.php';
    die();
  }

  // after delete custom field
  function after_delete_custom_field($field_id){
    $wpsc_export_ticket_list = get_option('wpsc_export_ticket_list');
     $term = get_term_by('id',$field_id,'wpsc_ticket_custom_fields');
     if(in_array($term->slug,$wpsc_export_ticket_list)){
       $wpsc_export_ticket_list = array_diff($wpsc_export_ticket_list,array($term->slug));
       update_option('wpsc_export_ticket_list', $wpsc_export_ticket_list);
     }
  }
}
  