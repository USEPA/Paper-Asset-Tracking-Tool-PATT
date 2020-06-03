# PATT - Paper Asset Tracking Tool Plugins for Wordpress
## Support Candy Core Modification Documentation
### Determine database modifications by doing a diff merge on /supportcandy/includes/class-wpsc-install.php
### Copy over language .pot, .po and .mo files
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

if(in_array($request_status, array('3', '4', '5', '63')) && $assigned_agent != '')
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

                          if(in_array($request_status, array('3', '4', '5', '63')) && $assigned_agent != '') 
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
ABOVE
```
add_submenu_page(
'wpsc-tickets',
__( 'Ticket List', 'supportcandy' ),
```
ADD
```
// PATT Menu Items
do_action('wpsc_add_submenu_page');
$agent_permissions = $wpscfunction->get_current_agent_permissions();
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
{
do_action('wpsc_add_admin_page');
}
// END PATT Menu Items
```
COMMENT OUT
```
 // PATT do_action('wpsc_add_submenu_page');
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
### Fix display of digitization center. May be superseded by Aaron’s Code.
###### /supportcandy/includes/admin/tickets/ticket_list/class-ticket-list-field-format.php
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

global $wpdb;

$ticket_id = $this->ticket['id'];
          
$box_details = $wpdb->get_results(
"SELECT wpqa_terms.name as digitization_center
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id
INNER JOIN wpqa_terms ON  wpqa_terms.term_id = wpqa_wpsc_epa_storage_location.digitization_center
WHERE wpqa_wpsc_epa_boxinfo.ticket_id = '" . $ticket_id . "'"
			);
			$array = [];
			foreach ($box_details as $info) {
			    array_push($array, $info->digitization_center);
			}
			$unique = array_unique($array);
			$unique_string = implode(",",$unique);
			
			if(empty($unique) || $unique_string == '') {
			echo 'Unassigned';
			} else {
			echo $unique_string;
			}
        }
//PATT END
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
