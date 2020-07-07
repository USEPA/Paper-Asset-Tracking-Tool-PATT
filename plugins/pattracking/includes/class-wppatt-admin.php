<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'wppatt_Admin' ) ) :
  
  final class wppatt_Admin {
      
 // constructor
    public function __construct() {
      add_action( 'admin_enqueue_scripts', array( $this, 'loadScripts') );
    }
    
    // Load admin scripts
    public function loadScripts(){
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-autocomplete', '', array('jquery-ui-widget', 'jquery-ui-position'), '1.8.6');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_editor();
        //bootstrap
        wp_enqueue_style('wpsc-bootstrap-css', WPSC_PLUGIN_URL.'asset/css/bootstrap-iso.css?version='.WPSC_VERSION );
        //Font-Awesom
        wp_enqueue_style('wpsc-fa-css', WPSC_PLUGIN_URL.'asset/lib/font-awesome/css/all.css?version='.WPSC_VERSION );
        wp_enqueue_style('wpsc-jquery-ui', WPSC_PLUGIN_URL.'asset/css/jquery-ui.css?version='.WPSC_VERSION );
        //admin scripts
        wp_enqueue_script('wpsc-admin', WPSC_PLUGIN_URL.'asset/js/admin.js?version='.WPSC_VERSION, array('jquery'), null, true);
        wp_enqueue_script('wpsc-public', WPSC_PLUGIN_URL.'asset/js/public.js?version='.WPSC_VERSION, array('jquery'), null, true);
        wp_enqueue_script('wpsc-modal', WPSC_PLUGIN_URL.'asset/js/modal.js?version='.WPSC_VERSION, array('jquery'), null, true);
        wp_enqueue_style('wpsc-public-css', WPSC_PLUGIN_URL . 'asset/css/public.css?version='.WPSC_VERSION );
        wp_enqueue_style('wpsc-admin-css', WPSC_PLUGIN_URL . 'asset/css/admin.css?version='.WPSC_VERSION );
        wp_enqueue_style('wpsc-modal-css', WPSC_PLUGIN_URL . 'asset/css/modal.css?version='.WPSC_VERSION );
        //Datetime picker
        wp_enqueue_script('wpsc-dtp-js', WPSC_PLUGIN_URL.'asset/lib/datetime-picker/jquery-ui-timepicker-addon.js?version='.WPSC_VERSION, array('jquery'), null, true);
        wp_enqueue_style('wpsc-dtp-css', WPSC_PLUGIN_URL . 'asset/lib/datetime-picker/jquery-ui-timepicker-addon.css?version='.WPSC_VERSION );
      if(isset($_REQUEST['page'])) :
        //localize script
        $loading_html = '<div class="wpsc_loading_icon"><img src="'.WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif"></div>';
        $localize_script_data = apply_filters( 'wpsc_admin_localize_script', array(
            'ajax_url'             => admin_url( 'admin-ajax.php' ),
            'loading_html'         => $loading_html
        ));
        wp_localize_script( 'wpsc-admin', 'wpsc_admin', $localize_script_data );
      endif;
    }
    
    // Added function to inject label button
    public function pdflabel_btnAfterClone(){
    include WPPATT_ABSPATH . 'includes/admin/wppatt_get_pdflabel_file.php';    
    }
    
    public function request_boxes_BeforeRequestID(){
    include WPPATT_ABSPATH . 'includes/admin/wppatt_request_boxes.php';    
    }
    
    public function request_hide_logs(){
    include WPPATT_ABSPATH . 'includes/admin/wppatt_request_hide_logs.php';    
    }
    
    public function get_pdf_label_field(){    
    include WPPATT_ABSPATH . 'includes/ajax/get_pdf_label_field.php';
    die();
    }
    
    // Added function to create a shipping ticket widget
    public function shipping_widget( $post_id ) {

	global $current_user, $wpscfunction,$wpdb;
	
	$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
	$ticket_data = $wpscfunction->get_ticket($ticket_id);
	$status_id   	= $ticket_data['ticket_status'];
	$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');
    $edit_btn_css = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_border_color'].'!important';

    $get_shipping_count = $wpdb->get_var('SELECT COUNT(*) FROM ' .$wpdb->prefix .'wpsc_epa_shipping_tracking WHERE ticket_id = ' . $ticket_id );
     
	//if ( ! $current_user->has_cap( 'wpsc_agent' ) ) {	// Only show widget for agents.
	//	return;
	//}

	//echo $status_id;
if ($status_id != 3) {

	$ticket_widget_name = __( 'Shipping', 'supportcandy' );

	$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');

	echo '<div class="row" style="';
	echo 'background-color:' . $wpsc_appearance_individual_ticket_page[ 'wpsc_ticket_widgets_bg_color' ] . ' !important;';
	echo 'color:' . $wpsc_appearance_individual_ticket_page[ 'wpsc_ticket_widgets_text_color' ] . ' !important;';
	echo 'border-color:' . $wpsc_appearance_individual_ticket_page[ 'wpsc_ticket_widgets_border_color' ] . ' !important;';
	echo '">';

	echo '<h4 class="widget_header"><i class="fa fa-truck"></i> ' . $ticket_widget_name . ' <button id="wpsc_individual_change_agent_fields" onclick="wpsc_get_shipping_details(' . $ticket_id .')" class="btn btn-sm wpsc_action_btn" style="' . $edit_btn_css . '" ><i class="fas fa-edit"></i></button></h4>';
	echo '<hr style="margin-top: 4px; margin-bottom: 6px" class="widget_devider">';
	
  if ($get_shipping_count > 0) {
      
    echo '<ul>';


    $shipping_rows = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix .'wpsc_epa_shipping_tracking WHERE ticket_id = ' . $ticket_id . ' ORDER BY id DESC' );
    
    $i = 0;

    foreach( $shipping_rows as $row) {

    $tracking_num = $row->tracking_number;
    $tracking_num_display = mb_strimwidth($tracking_num, 0, 25, "...");
    $company_name = $row->company_name;

    if ($row->shipped == 1) {
        $shipped_status = ' <i class="fa fa-check-circle" style="color:#008000;"></i>';
    } else {
        $shipped_status = '';
    }

switch ($company_name) {
    case "ups":
        echo '<li><i class="fab fa-ups fa-lg"></i> <a href="https://www.ups.com/track?loc=en_US&tracknum=' . $tracking_num . '" target="_blank">'. $tracking_num_display .'</a>' . $shipped_status . '</li>';
        break;
    case "fedex":
        echo '<li><i class="fab fa-fedex fa-lg"></i> <a href="https://www.fedex.com/apps/fedextrack/?tracknumbers=' . $tracking_num . '" target="_blank">'. $tracking_num_display .'</a>' . $shipped_status . '</li>';
        break;
    case "usps":
        echo '<li><i class="fab fa-usps fa-lg"></i> <a href="https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=' . $tracking_num . '" target="_blank">'. $tracking_num_display .'</a>' . $shipped_status . '</li>';
        break;
    case "dhl":
        echo '<li><i class="fab fa-dhl fa-lg"></i> <a href="https://www.logistics.dhl/global-en/home/tracking.html?tracking-id=' . $tracking_num . '" target="_blank">'. $tracking_num_display .'</a>' . $shipped_status . '</li>';
        break;
    default:
        echo $tracking_num;

}
    if (++$i == 10) break;
    }
    echo '</ul>';
     if ($get_shipping_count > 10) {echo '... <i class="fas fa-plus-square"></i> <a href="#" onclick="wpsc_get_shipping_details(' . $ticket_id . ')">[View More]</a><br /><br />';}
  } else {
    echo '<strong>No Tracking Numbers Assigned.</strong><br /><br />';
  }
    ?>

	<script>
		function wpsc_get_shipping_details(ticket_id){
		  wpsc_modal_open('Shipping Details');
		  var data = {
		    action: 'wpsc_get_shipping_details',
		    ticket_id: ticket_id
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  });  
		}
	</script>
	
	</div>
	<?php
	}
}

    public function get_alert_replacement(){    
    include WPPATT_ABSPATH . 'includes/ajax/get_alert_replacement.php';
    die();
    }
    
    public function get_shipping_details(){    
    include WPPATT_ABSPATH . 'includes/ajax/get_shipping_details.php';
    die();
    }

    public function get_inventory_editor(){    
    include WPPATT_ABSPATH . 'includes/ajax/get_inventory_editor.php';
    die();
    }
    
    public function get_digitization_editor(){    
    include WPPATT_ABSPATH . 'includes/ajax/get_digitization_editor.php';
    die();
    }
    
    public function get_folder_file_editor(){    
    include WPPATT_ABSPATH . 'includes/ajax/get_folder_file_editor.php';
    die();
    }
    
    public function get_box_editor(){    
    include WPPATT_ABSPATH . 'includes/ajax/get_box_editor.php';
    die();
    }
    
    public function get_clear_rfid(){    
    include WPPATT_ABSPATH . 'includes/ajax/get_clear_rfid.php';
    die();
    }
    
    public function get_rfid_box_editor(){    
    include WPPATT_ABSPATH . 'includes/ajax/get_rfid_box_editor.php';
    die();
    }
    
    
    // Added function to search for box/folder/file ID in Add Recall page 
    public function recall_search_for_id(){
	    include WPPATT_ABSPATH . 'includes/ajax/get_recall_search_id.php';    
	    die();
    }
    
    // Added function to search for box/folder/file ID in Add Recall page 
    public function recall_submit(){
	    include WPPATT_ABSPATH . 'includes/ajax/submit_recall.php';    
	    die();
    }
    
    // Added function to search and save recall shipping details
    public function recall_get_shipping(){
// 	    include WPPATT_ABSPATH . 'includes/ajax/recall_shipping.php';  
	    include WPPATT_ABSPATH . 'includes/ajax/recall_shipping_multi.php';       
	    die();
    }    
    
    // Added function to search and save recall requestor details
    public function recall_get_requestor(){
	    include WPPATT_ABSPATH . 'includes/ajax/recall_requestor.php';    
	    die();
    }  
    
    // Added function to search and save recall request date
    public function recall_get_date(){
	    include WPPATT_ABSPATH . 'includes/ajax/recall_date.php';    
	    die();
    }  
    
    // Added function to 
    public function recall_status_change(){
	    include WPPATT_ABSPATH . 'includes/ajax/recall_status_change.php';    
	    die();
    }  
    
    // Added function to search and save recall returned date
    public function recall_edit_multi_shipping(){
	    include WPPATT_ABSPATH . 'includes/ajax/recall_shipping_multi.php';    
	    die();
    }  
    
    // Add settings pill for recall statuses 
    public function get_recall_settings(){
	    include WPPATT_ABSPATH . 'includes/admin/pages/get_recall_settings.php';    
	    die();
    }  
    
    // Add settings pill for recall statuses 
    public function set_recall_settings(){
	    include WPPATT_ABSPATH . 'includes/admin/pages/set_recall_settings.php';    
	    die();
    }  
    
    // Add file to cancel recall on recall details page 
    public function recall_cancel(){
	    include WPPATT_ABSPATH . 'includes/ajax/recall_cancel.php';    
	    die();
    }

    

    
    // Added function to search and save recall returned date
    public function ticket_initiate_return(){
	    include WPPATT_ABSPATH . 'includes/ajax/return_editor.php';    
	    die();
    }  

    
    
  }
  
endif;

new wppatt_Admin();
