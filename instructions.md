# PATT - Paper Asset Tracking Tool Plugins for Wordpress
## Support Candy Core Modification Documentation
### Determine database modifications by doing a diff merge on /supportcandy/includes/class-wpsc-install.php
### Copy over language .pot, .po and .mo files
### Global find and replace: manage_options and edit_published_posts
### Remove the clone ticket button. 
###### /supportcandy/includes/admin/tickets/individual_ticket/load_individual_ticket.php
REMOVE
```
<?php if ($wpscfunction->has_permission('add_note',$ticket_id) && $ticket_status):?>
<button type="button" id="wpsc_individual_clone_btn" onclick="wpsc_get_clone_ticket(<?php echo $ticket_id ?>)" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="far fa-clone"></i> <?php _e('Clone','supportcandy')?></button>
<?php endif;?>
```
					    
### Format the request id on the ticket page. 
###### /supportcandy/includes/admin/tickets/individual_ticket/load_individual_ticket.php
FIND
```
[<?php echo get_option('wpsc_ticket_alice').$ticket_id?>]
```
REPLACE WITH
```[Request # <?php
//PATT BEGIN
$num = $ticket_id;
$str_length = 7;
$padded_request_id = substr("000000{$num}", -$str_length);
echo $padded_request_id; 
//PATT END ?>]
```
### Remove subject from ticket page 
###### /supportcandy/includes/admin/tickets/individual_ticket/load_individual_ticket.php
REMOVE
```
<?php echo stripcslashes($ticket['ticket_subject']); ?>
<?php if ($wpscfunction->has_permission('change_ticket_fields',$ticket_id) && $ticket_status):?>
<button id="wpsc_individual_edit_ticket_subject" onclick="wpsc_edit_ticket_subject(<?php echo $ticket_id;?>)" class="btn btn-sm wpsc_action_btn" style="<?php echo $edit_btn_css ?>"><i class="fas fa-edit"></i></button>
<?php endif;?>
```
### Allow for ticket widget to display properly at the end of the default widgets.
###### /supportcandy/includes/admin/tickets/individual_ticket/load_individual_ticket.php
MOVE
```
<?php do_action( 'wpsc_after_ticket_widget', $ticket_id)?> inside wpsc_sidebar.individual_ticket_widget div.
<?php do_action( 'wpsc_after_ticket_widget', $ticket_id)?>
</div>
```
### …Add functionality to the ticket page
###### /supportcandy/includes/admin/tickets/individual_ticket/load_individual_ticket.php
FIND
```
<?php do_action('wpsc_load_individual_ticket'); ?>
<?php
$flag = false;
if((in_array('register_user',$wpsc_allow_rich_text_editor) && !$current_user->has_cap('wpsc_agent')) && $rich_editing){
$flag = true;
}elseif($current_user->has_cap('wpsc_agent') && $rich_editing){
$flag = true;
}
```
ADD BELOW
```
//PATT BEGIN
$assigned_agent = $wpscfunction->get_ticket_meta( $ticket_id, assigned_agent, true);
$request_data = $wpscfunction->get_ticket($ticket_id);
$request_status = $request_data['ticket_status'];

if(in_array($request_status, array('3', '4', '5', '670', '63')) && $assigned_agent != '')
{
$wpscfunction->change_status($ticket_id, 64);
}
//PATT END
```
### Add status, request fields and recipients widget to requester view.
###### /supportcandy/includes/admin/tickets/individual_ticket/load_individual_ticket.php
REMOVE
```
!$current_user->has_cap('wpsc_agent') && 
```
FROM
```
			if ( $wpsc_ticket_widget_type && (in_array($role_id,$wpsc_ticket_widget_role) || (in_array('customer',$wpsc_ticket_widget_role)) || (is_super_admin($current_user->ID) && is_multisite() ) ) ) {
				$flag = true;
			}
```
ADD
```
&& $wpscfunction->has_permission('change_agentonly_fields',$ticket_id)
```
TO 
```
<?php if ($wpscfunction->has_permission('change_status',$ticket_id) && $ticket_status):?>
```
### Add hook for box list table on request page.
###### /supportcandy/includes/admin/tickets/individual_ticket/load_individual_ticket.php
ADD
```
<?php /*PATT BEGIN*/ do_action('wpsc_before_request_id',$ticket_id); /*PATT END*/ ?>
<br />
<br />
```
BELOW
```
<div class="row wpsc_it_subject_widget">
<h4>
<?php if(apply_filters('wpsc_show_hide_ticket_subject',true)){?>
[Request # <?php
$num = $ticket_id;
$str_length = 7;
$padded_request_id = substr("000000{$num}", -$str_length);
echo $padded_request_id; ?>]
<?php } ?>
</h4>
</div>
```
### Add status, request fields, and recipients widget to requestor view
###### /supportcandy/includes/admin/tickets/ticket_list/load_list.php
FIND
```
<?php if ($wpscfunction->has_permission('assign_agent')):?>
    	<button type="button" class="btn btn-sm wpsc_btn_bulk_action wpsc_action_btn checkbox_depend hidden" id="btn_assign_agents" onclick="wpsc_get_bulk_assign_agent();" style="<?php echo $action_default_btn_css?>"><i class="fas fa-users"></i> <?php _e('Assign Agent','supportcandy')?></button>
		<?php endif;?>
```
ADD
```
&& $wpscfunction->has_permission('change_agentonly_fields',$ticket_id)
```
FIND
```
<?php echo !$current_user->has_cap('wpsc_agent'); if ($wpscfunction->has_permission('change_status')):?>
    	<button type="button" class="btn btn-sm wpsc_btn_bulk_action wpsc_action_btn checkbox_depend hidden" id="btn_change_statuses" onclick="wpsc_get_bulk_change_status()" style="<?php echo $action_default_btn_css?>"><i class="fa fa-arrow-circle-right"></i> <?php _e('Change Status','supportcandy')?></button>
		<?php endif;?>
```
ADD
```
&& $wpscfunction->has_permission('change_agentonly_fields',$ticket_id)
```
### Change title of "Change Ticket Fields" modal window.
###### /supportcandy/asset/js/public.js
FIND
```
wpsc_modal_open('Change Ticket Fields');
```
REPLACE
```
wpsc_modal_open('Change Request Fields');
```
### Allow for new icons like shipping icons.
###### Updated FontAwesome Library \supportcandy\asset\lib\font-awesome from 5.1.0 to version 5.12.1

### Advanced filter on PATT now searches by request_id instead of ticket_id
###### /supportcandy/includes/admin/tickets/ticket_list/get_ticket_list.php
FIND
```
if($field->slug == 'ticket_id'){
$field->slug = 'id';
}
```
REPLACE
```
$field->slug = 'id';
```
WITH
```
//PATT BEGIN
$field->slug = 'request_id';
//PATT END
```
### Removed subject from request dashboard by executing the following SQL:
```
UPDATE `wpqa_termmeta` SET `meta_value` = '0' WHERE `wpqa_termmeta`.`meta_id` = 94;
set wpsc_allow_ticket_list for the subject term id 14 from 1 to 0.
Format the request id on the request dashboard.
```
###### /supportcandy/includes/admin/tickets/ticket_list/class-ticket-list-field-format.php
FIND
```
case 'id':
```
REPLACE WITH
```
case 'id':
//PATT BEGIN
$num = $ticket['id'];
$str_length = 7;
$padded_request_id = substr("000000{$num}", -$str_length);
echo $padded_request_id;
break;
//PATT END
```
### Automatically change status to assigned when staff is assigned to a request.
###### /supportcandy/includes/admin/tickets/ticket_list/class-ticket-list-field-format.php
FIND
```
function print_field($list_item,$ticket){
                global $wpscfunction;
                $this->list_item = $list_item;
                $this->ticket    = $ticket;
                $get_all_meta_keys = $wpscfunction->get_all_meta_keys();
```
ADD BELOW
```
//PATT BEGIN
$assigned_agent = $wpscfunction->get_ticket_meta( $ticket['id'], assigned_agent, true);
$request_data = $wpscfunction->get_ticket($ticket['id']);
$request_status = $request_data['ticket_status'];

                          if(in_array($request_status, array('3', '4', '5', '670', '63')) && $assigned_agent != '') 
                          {
                            $wpscfunction->change_status($ticket['id'], 64);
                          }
//PATT END
```
### Removed location, subject and description from request form:
###### /supportcandy/includes/admin/tickets/create_ticket/class-ticket-form-field-format.php
COMMENT OUT
```
//PATT $this->print_ticket_subject($field);
//PATT $this->print_ticket_category($field);
```
Change ticket_description case from 
```
$this->print_ticket_description($field);
```
to
```
$this->print_ticket_desc($field);
```
Add the following function above
```
function print_text_field($field){
//PATT BEGIN
function print_ticket_desc($field){
			?>
          	<input type="hidden" id="<?php echo $this->slug;?>" name="<?php echo $this->slug;?>" value="Request Created: <?php date_default_timezone_set('US/Eastern'); echo date("m/d/Y"); ?>" />
          <?php
        }
//PATT END
```
### CHANGE database values of meta_value for term_id 14, 15, 16 for meta_key wpsc_tf_required from 1 to 0
```
SELECT * FROM `wpqa_termmeta` WHERE `meta_key` LIKE 'wpsc_tf_required'
```
### Remove Description on create ticket.
###### /supportcandy/includes/admin/tickets/create_ticket/load_create_ticket.php
COMMENT OUT
```
			if($wpsc_desc_status){
				if($flag){
				?>
					//PATT var description = tinyMCE.get('ticket_description').getContent().trim();
					//PATT dataform.append('ticket_description', description);
					//PATT is_tinymce = true;
				<?php
				}else{
					?>
					//PATT var description = jQuery('#ticket_description').val();
					//PATT dataform.append('ticket_description',description);
					//PATT is_tinymce = false;
					<?php
				}
			}
```
COMMENT OUT
```
			<?php  if($wpsc_desc_status){ ?>
				//PATT if(is_tinymce) tinyMCE.activeEditor.setContent('');
			<?php } ?>
```
### Changed get_option to display no text
###### /supportcandy/includes/functions/create_ticket.php
FIND and REMOVE wpsc_default_ticket_category from
```
$default_category = get_option('wpsc_default_ticket_category');
```
// Category 
```
$default_category = get_option(''); 
```
### Allow request id to be passed to the ticket page.
###### /supportcandy/includes/admin/tickets/tickets.php 
REPLACE
```
wpsc_init(wpsc_setting_action,attrs);
```
WITH
```
<?php /*PATT BEGIN*/ $GLOBALS['id'] = $_GET['id']; if (!empty($GLOBALS['id']) && preg_match("/^[0-9]{7}$/", $GLOBALS['id'])) { ?>
wpsc_open_ticket(<?php echo Patt_Custom_Func::convert_request_id($GLOBALS['id']); ?>);
<?php } else { ?>
wpsc_init(wpsc_setting_action,attrs);
<?php } /*PATT END*/ ?>
```
### Modify menu to allow the addition of items to be displayed to digitization staff/administrators only.
###### /supportcandy/includes/class-wpsc-admin.php
BELOW
```
      add_submenu_page(
        'wpsc-tickets',
        __( 'Ticket List', 'supportcandy' ),
        __( 'Tickets', 'supportcandy' ),
        'wpsc_agent',
        'wpsc-tickets',
        array($this,'tickets')
      );
```
REPLACE
```
// PATT Menu Items
do_action('wpsc_add_submenu_page');
$agent_permissions = $wpscfunction->get_current_agent_permissions();
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
{
do_action('wpsc_add_admin_page');
}
// END PATT Menu Items

      add_submenu_page(
        'wpsc-tickets',
        __( 'Support Agents', 'supportcandy' ),
        __( 'Support Agents', 'supportcandy' ),
        'edit_published_posts',
        'wpsc-support-agents',
        array($this,'support_agents')
      );
      add_submenu_page(
        'wpsc-tickets',
        __( 'Custom Fields', 'supportcandy' ),
        __( 'Custom Fields', 'supportcandy' ),
        'edit_published_posts',
        'wpsc-custom-fields',
        array($this,'custom_fields')
      );
      add_submenu_page(
        'wpsc-tickets',
        __( 'Ticket List', 'supportcandy' ),
        __( 'Ticket List', 'supportcandy' ),
        'edit_published_posts',
        'wpsc-ticket-list',
        array($this,'ticket_list')
      );
      add_submenu_page(
        'wpsc-tickets',
        __( 'Email Notifications', 'supportcandy' ),
        __( 'Email Notifications', 'supportcandy' ),
        'edit_published_posts',
        'wpsc-email-notifications',
        array($this,'email_notifications')
      );
      add_submenu_page(
        'wpsc-tickets',
        __( 'Appearance', 'supportcandy' ),
        __( 'Appearance', 'supportcandy' ),
        'edit_published_posts',
        'wpsc-appearance',
        array($this,'appearance_settings')
      );
      
      //PATT do_action('wpsc_add_submenu_page');
      
      add_submenu_page(
        'wpsc-tickets',
        __( 'Settings', 'supportcandy' ),
        __( 'Settings', 'supportcandy' ),
        'edit_published_posts',
        'wpsc-settings',
        array($this,'settings')
      );
      add_submenu_page(
        'wpsc-tickets',
        __( 'License', 'supportcandy' ),
        __( 'License', 'supportcandy' ),
        'edit_published_posts',
        'wpsc-license',
        array($this,'licenses')
      );
      add_submenu_page(
        'wpsc-tickets',
        __( 'Addons', 'supportcandy' ),
        __( 'Addons', 'supportcandy' ),
        'edit_published_posts',
        'wpsc-add-ons',
        array($this,'addons')
      );
    }
  // PATT  
```
COMMENT OUT
```
 // PATT do_action('wpsc_add_submenu_page');
```
### Disable status based on validation, assignment of digitization staff and box destruction.
###### /supportcandy/includes/admin/tickets/individual_ticket/get_change_ticket_status.php
BELOW
```<form id="frm_get_ticket_change_status" method="post">```
ADD
```
<?php
//PATT BEGIN
$get_assigned = $wpdb->get_row("SELECT sum(meta_value) as assigned_val FROM wpqa_wpsc_ticketmeta WHERE ticket_id = '" . $ticket_id . "' AND meta_key = 'assigned_agent' ORDER BY ticket_id DESC");
$assigned_val = $get_assigned->assigned_val.',';

$get_sum_total = $wpdb->get_row("select sum(a.total_count) as sum_total_count
    from (
SELECT (SELECT count(id) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id) as total_count FROM wpqa_wpsc_epa_boxinfo as a INNER JOIN wpqa_wpsc_ticket as b ON a.ticket_id = b.id WHERE b.id = '" . $ticket_id . "'
    ) a");
$sum_total_val = $get_sum_total->sum_total_count;

$get_sum_validation = $wpdb->get_row("select sum(a.validation) as sum_validation
    from (
SELECT (SELECT sum(validation = 1) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id) as validation FROM wpqa_wpsc_epa_boxinfo as a INNER JOIN wpqa_wpsc_ticket as b ON a.ticket_id = b.id WHERE b.id = '" . $ticket_id . "'
    ) a");
$sum_validation = $get_sum_validation->sum_validation;

$get_sum_destruction = $wpdb->get_row("select count(id) as count_destruction
    from wpqa_wpsc_epa_boxinfo where ticket_id = '" . $ticket_id . "' and box_destroyed = 1");
$count_destruction = $get_sum_destruction->count_destruction;

$get_sum_boxes = $wpdb->get_row("select count(id) as box_count
    from wpqa_wpsc_epa_boxinfo where ticket_id = '" . $ticket_id . "'");
$count_boxes = $get_sum_boxes->box_count;

$validated = '';
$assigned = '';
$destruction = '';

if($sum_total_val == $sum_validation) {
$validated = 1;
} else {
$validated = 0;
}

$validated_array = array(66,68,67,69);

if($assigned_val > 0) {
$assigned = 1;
} else {
$assigned = 0;
}

$assigned_array = array(3,4,670,5,63);

if($count_boxes == $count_destruction) {
$destruction = 1;
} else {
$destruction = 0;
}

$destruction_array = array(3,4,670,5,63,64,672,671,65,6,673,674,66);
//PATT END
?>
```
BELOW
```$selected = $status_id == $status->term_id ? 'selected="selected"' : '';```
ADD
```
//PATT BEGIN
$disabled = '';
if (in_array($status->term_id, $validated_array) && $validated == 0) {
    $disabled = 'disabled';
}
if (in_array($status->term_id, $assigned_array) && $assigned == 1) {
    $disabled = 'disabled';
}
if (in_array($status->term_id, $destruction_array) && $destruction == 1) {
    $disabled = 'disabled';
}
//PATT END
```
ADD
```
$disabled
```
TO
```
echo '<option '.$selected.' value="'.$status->term_id.'" '.$disabled.'>
```
### Enable auto-assignment functionality in the change status modal window.
###### /supportcandy/includes/admin/tickets/individual_ticket/get_change_ticket_status.php AND
###### /supportcandy/includes/admin/tickets/individual_ticket/get_bulk_change_status.php
ADD
```
<script>
// PATT BEGIN
jQuery(document).ready(function() {
jQuery(".wpsc_popup_action").click(function () {
jQuery.post(
'<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/auto_assignment.php',{
postvartktid: '<?php echo $ticket_id ?>',
postvardcname: jQuery("[name=category]").val()
},
function (response) {
if(jQuery("select[name='category']").val()) {
alert(response);
}
});
});
});
// PATT END
</script>
```
BELOW
```
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_set_change_ticket_status(<?php echo htmlentities($ticket_id)?>);"><?php _e('Save','supportcandy');?></button>
```
### Remove display of category
###### /supportcandy/includes/admin/tickets/individual_ticket/load_individual_ticket.php
COMMENT OUT THE FOLLOWING:
```
<!--PATT BEGIN <div class="wpspsidebar_labels"><strong><?php _e('Category','supportcandy')?>:</strong> <?php echo $wpsc_custom_category_localize['custom_category'.$category_id] ?> </div> PATT END-->
```
### Wordpress 3 reference fix
###### /supportcandy/includes/admin/tickets/ticket_list/get_ticket_list.php
FIND
```
$format = new WPSC_Ticket_List_Field();
```
ADD BELOW
```
//PATT
$subfolder_path = site_url( '', 'relative');
```
REPLACE
```
onclick="if(link)wpsc_get_individual_ticket(this);"
```
WITH
```
onclick="if(link)window.location.replace('<?php echo $subfolder_path; ?>/wp-admin/admin.php?page=wpsc-tickets&id=<?php echo Patt_Custom_Func::convert_request_db_id($ticket['id']); ?>');"
```
### Fix all logs displaying on a request page to limit it to the first 10.
###### /supportcandy/includes/admin/tickets/individual_ticket/load_individual_ticket.php
COMMENT OUT
```
/* PATT BEGIN
				if ( $thread_type == 'log' && apply_filters('wpsc_thread_log_visibility',$current_user->has_cap('wpsc_agent')) && $wpscfunction->has_permission('view_log',$ticket_id)):
					?>
					<div class="col-md-8 col-md-offset-2 wpsc_thread_log" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_logs_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_logs_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_logs_border_color']?> !important;">
		          <?php 
							if($wpsc_thread_date_format == 'timestamp'){
								$date = sprintf( __('reported %1$s','supportcandy'), $wpscfunction->time_elapsed_timestamp($thread->post_date_gmt) );
							}else{
								$date = sprintf( __('reported %1$s','supportcandy'), $wpscfunction->time_elapsed_string($thread->post_date_gmt) );
							}
							echo $reply ?> <i><small><?php echo $date ?></small></i>
		      </div>
					<?php
				endif;
PATT END */
```
### On delete of a request ensure all appropriate tables are updated related to box location.
###### /supportcandy/includes/admin/tickets/individual_ticket/set_delete_ticket_permanently.php AND
###### /supportcandy/includes/admin/tickets/individual_ticket/set_delete_permanently_bulk_ticket.php
ADD ABOVE
```
$wpdb->delete($wpdb->prefix.'wpsc_ticket', array( 'id' => $ticket_id));
```
//PATT BEGIN
```
$get_associated_boxes = $wpdb->get_results("
SELECT id, storage_location_id FROM wpqa_wpsc_epa_boxinfo 
WHERE ticket_id = '" . $ticket_id . "'
");

foreach ($get_associated_boxes as $info) {
		$associated_box_ids = $info->id;
		$associated_storage_ids = $info->storage_location_id;
		
		$box_details = $wpdb->get_row(
"SELECT 
digitization_center,
aisle,
bay,
shelf,
position
FROM wpqa_wpsc_epa_storage_location
WHERE id = '" . $associated_storage_ids . "'"
			);
			
			$box_storage_digitization_center = $box_details->digitization_center;
			$box_storage_aisle = $box_details->aisle;
			$box_storage_bay = $box_details->bay;
			$box_storage_shelf = $box_details->shelf;
			$box_sotrage_shelf_id = $box_storage_aisle . '_' . $box_storage_bay . '_' . $box_storage_shelf;

$box_storage_status = $wpdb->get_row(
"SELECT 
occupied,
remaining
FROM wpqa_wpsc_epa_storage_status
WHERE shelf_id = '" . $box_sotrage_shelf_id . "'"
			);

$box_storage_status_occupied = $box_storage_status->occupied;
$box_storage_status_remaining = $box_storage_status->remaining;
$box_storage_status_remaining_added = $box_storage_status->remaining + 1;

if ($box_storage_status_remaining <= 4) {
$table_ss = 'wpqa_wpsc_epa_storage_status';
$ssr_update = array('remaining' => $box_storage_status_remaining_added);
$ssr_where = array('shelf_id' => $box_sotrage_shelf_id, 'digitization_center' => $box_storage_digitization_center);
$wpdb->update($table_ss , $ssr_update, $ssr_where);
}

if($box_storage_status_remaining == 4){
$sso_update = array('occupied' => 0);
$sso_where = array('shelf_id' => $box_sotrage_shelf_id, 'digitization_center' => $box_storage_digitization_center);
$wpdb->update($table_ss , $sso_update, $sso_where);
}

		$wpdb->delete($wpdb->prefix.'wpsc_epa_storage_location', array( 'id' => $associated_storage_ids));
		$wpdb->delete($wpdb->prefix.'wpsc_epa_boxinfo', array( 'id' => $associated_box_ids));
	}
//PATT END
```
### Digitization center auto categorization. Applies to status editor within requests screen only.
###### /supportcandy/includes/admin/tickets/individual_ticket/get_change_ticket_status.php
FIND
```
$selected = $category_id == $category->term_id ? 'selected="selected"' : '';
```
REPLACE
```
//PATT
$selected = Patt_Custom_Func::get_default_digitization_center($ticket_id) == $category->term_id ? 'selected="selected"' : '';
```
### Only display digitization center selector when nothing has been initialy assigned.
###### /supportcandy/includes/admin/tickets/individual_ticket/get_change_ticket_status.php
ADD $wpdb to global
```
global $current_user,$wpscfunction,$wpdb;
```
FIND
```
	<div class="form-group">
		<label for="wpsc_default_ticket_category"><?php _e('Ticket Category','supportcandy');?></label>
```
ADD ABOVE
```
<?php
//PATT BEGIN
$box_details = $wpdb->get_results(
"SELECT wpqa_terms.term_id as digitization_center, location_status_id as location
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id
INNER JOIN wpqa_terms ON  wpqa_terms.term_id = wpqa_wpsc_epa_storage_location.digitization_center
WHERE wpqa_wpsc_epa_boxinfo.ticket_id = '" . $ticket_id . "'"
			);
$dc_array = array();
$pl_array = array();
foreach ($box_details as $info) {
$dc_details = $info->digitization_center;
$physical_location = $info->location;
array_push($dc_array, $dc_details);
array_push($pl_array, $physical_location);
}

if (count(array_keys($dc_array, '666')) == count($dc_array) && !in_array('-99999', $pl_array) && !in_array(6, $pl_array)) {
//PATT END
?>
```
FIND
```
		echo '<option '.$selected.' value="'.$category->term_id.'">'.$wpsc_custom_category_localize['custom_category_'.$category->term_id].'</option>';
			endforeach;
			?>
		</select>
	</div>
```
ADD BELOW
```
<?php
//PATT BEGIN
} else {
//PATT END

echo '<input type="hidden" name="category" value="'.Patt_Custom_Func::get_default_digitization_center($ticket_id).'">';

//PATT BEGIN
}
//PATT END
?>
```
FIND
```
onclick="wpsc_set_change_ticket_status(<?php echo htmlentities($ticket_id)?>);
```
ADD AFTER
```
wpsc_open_ticket(<?php echo htmlentities($ticket_id)?>);
```
### Fix Search to accept QR Code and Request ID
###### /supportcandy/includes/functions/get_sql_query.php
FIND
```
if($search){
```
ADD BELOW
```
//PATT BEGIN
if (strpos($search, "id")!==false){
parse_str(parse_url($search)['query'], $params);
$num = $params['id'];
$str_length = 7;
$search = substr("000000{$num}", -$str_length);
}
//PATT END
```
FIND
```
."t.id  LIKE '$term' OR "
```
REPLACE
```
//PATT BEGIN
."t.request_id  LIKE '$term' OR "
//PATT END
```
### Fix Advanced Search to accept QR Code and Basic Search to clear url when QR is scanned.
###### /supportcandy/includes/admin/tickets/ticket_list/get_ticket_list.php
FIND
```
jQuery( ".wpsc_search_autocomplete" ).autocomplete({
```
ADD ABOVE
```
//PATT BEGIN

jQuery( '#tf_request_id .wpsc_search_autocomplete' ).after( "<sub>Use a barcode reader or type a Request ID and <strong>press enter.</strong></sub>" );

jQuery( '#tf_request_id .wpsc_search_autocomplete' ).autocomplete({
			      disabled: true
			        });
			        
jQuery(function() {
  jQuery('#tf_request_id .wpsc_search_autocomplete').on('keyup', function(event) {
    var url_string = jQuery(this).val();
    var matches = /id=([^&#=]*)/.exec(url_string);
    if (matches !== null) {
       var paramid = matches[1].replace(/[\n\r]+/g, ' ').replace(/\s{2,}/g,' ').replace(/^\s+|\s+$/,'') ; 
    } else {
       var paramid = jQuery(this).val();
    }
    if (url_string.includes('id=')) {
      jQuery(this).val(paramid);
    }

  })
});

		jQuery('#tf_request_id .wpsc_search_autocomplete').on('keypress', function(e) {
			if (e.keyCode == 13) {
			    e.preventDefault();
                e.stopPropagation();
                var paramid = jQuery(this).val();
    var matches = /^\d{7}$/.exec(paramid);

    if (matches !== null) {
			        var html_str = '<li class="wpsp_filter_display_element">'
															+'<div class="flex-container">'
																+'<div class="wpsp_filter_display_text">'
																	+paramid
																	+'<input type="hidden" name="custom_filter[request_id][]" value="'+paramid+'">'
																+'</div>'
																+'<div class="wpsp_filter_display_remove" onclick="wpsc_remove_filter(this);"><i class="fa fa-times"></i></div>'
															+'</div>'
														+'</li>';
							jQuery('#tf_request_id .wpsp_filter_display_container').append(html_str);
							jQuery(this).val(''); return false;
    }
			}
		});
//PATT END
```
FIND
```
jQuery('#wpsc_load_apply_filter_btn').on("click", function(e) {
```
ADD ABOVE
```
//PATT BEGIN		
jQuery('#wpsc_ticket_search').on('keyup', function(event) {
    var url_string = jQuery(this).val();
    var matches = /id=([^&#=]*)/.exec(url_string);
    if (matches !== null) {
       var paramid = matches[1]; 
    } else {
       var paramid = jQuery(this).val();
    }
    if (url_string.includes('id=')) {
      jQuery(this).val(paramid);
    }
});
//PATT END
```
### Ensure Request Page refreshes when an agent is assigned so that the status auto updates
###### /supportcandy/includes/admin/tickets/individual_ticket/get_change_assign_agent.php
FIND INSIDE ONCLICK
```
wpsc_set_change_assign_agent(<?php echo htmlentities($ticket_id) ?>);
```
ADD AFTER
```
wpsc_open_ticket(<?php echo htmlentities($ticket_id)?>);
```
### Box List Ingestion Changes
###### /supportcandy/includes/admin/tickets/create_ticket/load_create_ticket.php
FIND
```
$form_field->print_field($field);
```
ADD ABOVE
```
do_action('print_listing_form_block', $field);
```
###### /supportcandy/includes/admin/tickets/create_ticket/load_create_ticket.php
FIND
```
<script type="text/javascript">
jQuery(document).ready(function(){
```
ADD ABOVE
```
<?php do_action('patt_custom_imports_tickets', WPSC_PLUGIN_URL); ?>
```
###### /supportcandy/includes/admin/tickets/create_ticket/load_create_ticket.php
FIND
```
function wpsc_submit_ticket() {
```
ADD ABOVE
```
<?php do_action('patt_print_js_functions_create'); ?>
```
###### /supportcandy/includes/admin/tickets/create_ticket/load_create_ticket.php
FIND
```
if (validation) {
```
ADD BELOW
```
//New get DataTable data in the form of an
var data = jQuery('#boxinfodatatable').DataTable().rows().data().toArray();
var data = JSON.stringify(jQuery('#boxinfodatatable').toJson());
var dataform = new FormData(jQuery('#wpsc_frm_create_ticket')[0]);
dataform.append('boxinfo', data);
```
###### /supportcandy/includes/admin/tickets/create_ticket/submit_ticket.php
FIND
```
// Subject
$ticket_subject = isset($_POST['ticket_subject']) ? sanitize_text_field($_POST['ticket_subject']) : '';
```
ADD ABOVE
```
$boxinfodata = $_POST["boxinfo"];
$args['box_info'] = $boxinfodata;
```
###### /supportcandy/includes/functions/create_ticket.php
FIND
```
$ticket_id = $wpscfunction->create_new_ticket($values);
```
ADD BELOW
```
$data['ticket_id'] = $ticket_id;
$data['box_info'] = $args["box_info"];
do_action('patt_process_boxinfo_records', $data);
```
### Location Filtering Functionality
#### /supportcandy/includes/admin/tickets/ticket_list/get_ticket_list.php
FIND
```
	$meta_query[] = array(	
	'key'     => 'active',	
	'value'   => $active,	
	'compare' => '='	
);
```
ADD BELOW
```
$meta_query = apply_filters('get_ticket_list_meta_query', $meta_query); // PATT BEGIN - Location Filtering - PATT END	
```
FIND
```
$sql          = $wpscfunction->get_sql_query( $select_str, $meta_query, $search, $orderby, $order, $post_per_page, $current_page );
```
ADD BELOW
```
$sql		  = apply_filters('get_ticket_list_sql', $sql, $meta_query); // PATT BEGIN - Location Filtering - PATT END	
```
##### /supportcandy/includes/admin/tickets/ticket_list/class-ticket-list-field-format.php
REPLACE 
```
        function print_ticket_category(){
          $category = get_term_by('id',$this->val,'wpsc_categories');
					$wpsc_custom_category_localize = get_option('wpsc_custom_category_localize');
          echo $wpsc_custom_category_localize['custom_category_'.$this->val];
        }
```
WITH
```
//PATT BEGIN	
        function print_ticket_category(){	
          $category = get_term_by('id',$this->val,'wpsc_categories');	
		  $wpsc_custom_category_localize = get_option('wpsc_custom_category_localize');	
          //echo $wpsc_custom_category_localize['custom_category_'.$this->val];	
          	
          do_action('pattracking_print_ticket_category', $this); // PATT BEGIN - Location Filtering - PATT END	
        }	
//PATT END
```
##### /supportcandy/includes/admin/tickets/ticket_list/filters/set_default_filter.php
FIND
```
	setcookie('wpsc_ticket_filter',json_encode($filter));
```
ADD ABOVE
```
$filter = apply_filters('ticket_filter_the_filter',$filter, $order_key, $order); // PATT BEGIN - Location Filtering, allows sidebar filters - PATT END	
```

##### /supportcandy/includes/admin/tickets/individual_ticket/load_individual_ticket.php
FIND ALL INSTANCES OF
```
<?php if($current_user->has_cap('wpsc_agent')): ?>
```
REMOVE LINE DIRECTLY BELOW

##### /wpsc-canned-reply/includes/admin/wpsc_submit_canned_reply_post.php
FIND
```
<button type="button" onclick="javascript:wpsc_submit_canned_reply_post();" class="btn" style="<?php echo $canned_reply_btn_css?>">
```
ADD ABOVE
```
<?php
$agent_permissions = $wpscfunction->get_current_agent_permissions();
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
{
?>
```
ADD TO LAST LINE
```
<?php
}
?>
```

##### Reset counts in DB
UPDATE wpqa_usermeta
SET meta_value = 'a:1:{s:16:"unresolved_agent";i:0;}'
WHERE meta_key LIKE '%%_label_counts%%'

##### Allow users with the Author role in Wordpress edit access
Download "Multifind", a program that allows multiple files to be edited at once. Zip supportcandy folder in wordpress3 and extract in Multifind. Find "manage_options" and replace with "edit_published_posts". Zip the supportcandy folder you have just edited and upload to wordpress3 and extract into wordpress3.  
**Note:** manage_options is a Wordpress function that restricts access to Wordpress admins, Supportcandy has restricted most pages to Wordpress admins.

**Note:** The re-scan ID is different in dev and staging. 743 vs 763.

plugins/supportcandy/includes/class-wpsc-admin.php
        __( 'Support', 'supportcandy' ),
        __( 'Support','supportcandy'),
	
	Remove count in left navigation
Default all files to frozen when RLO selects litigation on New Request screen	
/plugins/pattracking/includes/class-wppatt-hooks-filters.php
ADD:
if($data["ticket_useage"] == 'Litigation') {
   $folderdocarray['freeze'] = 1;
}
AFTER
$folderdocarray = array(...

/plugins/supportcandy/includes/admin/tickets/create_ticket/submit_ticket.php
ADD: 
$useagedata = $_POST["are-these-documents-used-for-the-following"];
$args['ticket_useage'] = $useagedata;

AFTER:
$boxinfodata = $_POST["boxinfo"];
$args['box_info'] = $boxinfodata;

/plugins/supportcandy/includes/functions/create_ticket.php
ADD:
$data['ticket_useage'] = $args["ticket_useage"];

AFTER:
$data['ticket_id'] = $ticket_id;
$data['box_info'] = $args["box_info"];

##### Remove the close button from the request details page
comment out this line:
```
<button type="button" id="wpsc_individual_close_btn" onclick="wpsc_get_close_ticket(<?php echo $ticket_id?>)" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-check"></i> <?php _e('Close','supportcandy')?></button>
```


##### Restrict access to the critical status
IN /wp-content/plugins/supportcandy/includes/admin/tickets/individual_ticket/get_change_ticket_status.php

FIND $wpsc_custom_priority_localize = get_option('wpsc_custom_priority_localize');

ADD BELOW
//PATT BEGIN
$agent_permissions = $wpscfunction->get_current_agent_permissions();
$agent_permissions['label'];
//PATT END

FIND
foreach ( $priorities as $priority ) :

ADD ABOVE
			//PATT BEGIN
			$key = array_search('Critical', array_column($priorities, 'name'));
			if ($agent_permissions['label'] == 'Agent')
            {
                 unset($priorities[$key]);
            }
			//PATT END
FIND
echo '<option '.$selected.' value="'.$priority->term_id.'">'.$wpsc_custom_priority_localize['custom_priority_'.$priority->term_id].'</option>';	

REPLACE
				//PATT BEGIN
				if ($priority->term_id == 9) {
				echo '<option '.$selected.' style="background: #FFD0C2; font-weight: bold;" value="'.$priority->term_id.'">'.$wpsc_custom_priority_localize['custom_priority_'.$priority->term_id].'</option>';
				} else {
				echo '<option '.$selected.' value="'.$priority->term_id.'">'.$wpsc_custom_priority_localize['custom_priority_'.$priority->term_id].'</option>';				
				}
				//PATT END
##### Redo Delete functions to ensure that only requests in a cancelled status or all boxes in a dispositioned status are deleted
IN /wp-content/plugins/supportcandy/includes/admin/tickets/individual_ticket/load_individual_ticket.php

FIND
    	<button type="button" id="wpsc_individual_delete_btn" onclick="wpsc_get_delete_ticket(<?php echo $ticket_id ?>);" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-trash"></i> <?php _e('Delete','supportcandy')?></button>

REPLACE
    	<button type="button" id="wpsc_individual_delete_btn" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-trash"></i> <?php _e('Delete','supportcandy')?></button>
	
FIND
// Submit note
	function wpsc_submit_reply( save_type ){
ADD ABOVE
	//PATT BEGIN
//delete button
jQuery('#wpsc_individual_delete_btn').on('click', function(e){
     var form = this;
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/delete_request.php',{
postvarsrequest_id : <?php echo $ticket_id;?>
}, 
   function (response) {
      //if(!alert(response)){
      
       wpsc_modal_open('Delete Request');
		  var data = {
		    action: 'wpsc_delete_request',
		    response_data: response
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  }); 
		  		  wpsc_open_ticket(<?php echo $ticket_id?>);
      //}
   });
});
	//PATT END
	
IN /wp-content/plugins/supportcandy/includes/class-wpsc-actions.php

FIND
function load_actions() {

ADD UNDER
      //PATT BEGIN
      add_action( 'wpsc_after_recycle', array($this,'recycle_request'), 10, 1 );
      //PATT END
FIND
// Submit reply actions
    function submit_reply($thread_id) {
ADD ABOVE
    //PATT BEGIN
        // Recycle request
    function recycle_request ( $ticket_id){
      global $wpscfunction, $current_user;
      if($current_user->ID){
        $log_str = sprintf( __('Request has been moved to the recycle bin by %1$s','supportcandy'), '<strong>'. $current_user->display_name .'</strong>');
      } else {
        $log_str = sprintf( __('Request has been moved to the recycle bin by %1$s','supportcandy'), '<strong>'.$current_user->display_name.'</strong>' );
      }
      $args = array(
        'ticket_id'      => $ticket_id,
        'reply_body'     => $log_str,
        'thread_type'    => 'log'
      );
      $args = apply_filters( 'wpsc_thread_args', $args );
      $wpscfunction->submit_ticket_thread($args);
    }
    //PATT END