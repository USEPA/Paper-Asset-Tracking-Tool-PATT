<?php

final class WPSCExportTicketFrontend {
  
  function wpsc_admin_localize_script($localize_script_data){
    $localize_script_data['please_select_at_least_one_column'] = __('Please select at least one column!','wpsc-export-ticket');
    $localize_script_data['export_ticket'] = __('Export Ticket','wpsc-export-ticket');
    return $localize_script_data;
  }
  
}
?>