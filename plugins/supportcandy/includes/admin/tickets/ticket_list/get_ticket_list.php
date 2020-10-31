<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;
if (!$current_user->ID){

	die();
}

$filter = $wpscfunction->get_current_filter();
$wpsc_appearance_ticket_list = get_option('wpsc_appearance_ticket_list');
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');

// Initialize meta query
$meta_query = array(
	'relation' => 'AND',
);

if ( !is_multisite() || !is_super_admin($current_user->ID)) {
	// Initialie restrictions. Everyone should able to see their own tickets.
	$restrict_rules = array(
		'relation' => 'OR',
		array(
			'key'            => 'customer_email',
			'value'          => $current_user->user_email,
			'compare'        => '='
		),
	);

	if ($current_user->has_cap('wpsc_agent') ) {
		
		$post_per_page     = get_option('wpsc_tl_agent_no_of_tickets');
		$agent_permissions = $wpscfunction->get_current_agent_permissions();
		$current_agent_id  = $wpscfunction->get_current_user_agent_id();
		
		if(!$current_agent_id) die();
		
		if ($agent_permissions['view_unassigned'] && $filter['label']!='mine') {
			$restrict_rules[] = array(
				'key'            => 'assigned_agent',
				'value'          => 0,
				'compare'        => '='
			);
		}
		
		if ($agent_permissions['view_assigned_me']) {
			$restrict_rules[] = array(
				'key'            => 'assigned_agent',
				'value'          => $current_agent_id,
				'compare'        => '='
			);
		}
		
		if ($agent_permissions['view_assigned_others'] && $filter['label']!='mine') {
			$restrict_rules[] = array(
				'key'            => 'assigned_agent',
				'value'          => array(0,$current_agent_id),
				'compare'        => 'NOT IN'
			);
		}
		
		$restrict_rules = apply_filters('wpsc_tl_agent_restrict_rules',$restrict_rules);		
	} else {
		
		$post_per_page = get_option('wpsc_tl_customer_no_of_tickets');
		
		$restrict_rules = apply_filters('wpsc_tl_customer_restrict_rules',$restrict_rules);	
			
	}

	$wpsc_ticket_public_mode = get_option('wpsc_ticket_public_mode');

	if( !$current_user->has_cap('wpsc_agent') && $wpsc_ticket_public_mode){
		$restrict_rules[] = array(
			'key'            => 'active',
			'value'          => 1,
			'compare'        => '='
		);
	}

	$meta_query[] = $restrict_rules;
	
} else {
	
		$post_per_page = get_option('wpsc_tl_agent_no_of_tickets');
	
}

// Merge default filter label
if($filter['query']){
	$meta_query = array_merge($meta_query, $filter['query']);
}

// Select offset for page number
$offset = ($filter['page']-1)*$post_per_page;
?>

<form id="frm_additional_filters" action="javascript:wpsc_set_custom_filter();" method="post">
	<div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 wpsc_ticket_search_box" style="margin-bottom:20px;padding:0;">
		<input type="text" id="wpsc_ticket_search" class="form-control" name="custom_filter[s]" value="<?php echo trim($filter['custom_filter']['s'])?>" autocomplete="off" placeholder="<?php _e('Search...','supportcandy')?>">
		<i class="fa fa-search wpsc_search_btn wpsc_search_btn_sarch"></i>
		<i class="fa fa-caret-down wpsc_search_btn wpsc_search_btn_filter" onclick="show_custom_filters();"></i>
		<input type="submit" name="submit" style="position: fixed; top: -1000px;" />
		<div class="wpsp_custom_filter_container" style="display:none;">
			
			<div class="row wpsp_filter_body" style="">
					<?php
					if ($current_user->has_cap('wpsc_agent')) {
						$fields = get_terms([
							'taxonomy'   => 'wpsc_ticket_custom_fields',
							'hide_empty' => false,
							'orderby'    => 'meta_value_num',
							'meta_key'	 => 'wpsc_filter_agent_load_order',
							'order'    	 => 'ASC',
							'meta_query' => array(
								'relation' => 'AND',
						    array(
						      'key'       => 'wpsc_allow_ticket_filter',
						      'value'     => '1',
						      'compare'   => '='
						    ),
						    array(
						      'key'       => 'wpsc_agent_ticket_filter_status',
						      'value'     => '1',
						      'compare'   => '='
						    )
							),
						]);
					}
					else {
						$fields = get_terms([
							'taxonomy'   => 'wpsc_ticket_custom_fields',
							'hide_empty' => false,
							'orderby'    => 'meta_value_num',
							'meta_key'	 => 'wpsc_filter_customer_load_order',
							'order'    	 => 'ASC',
							'meta_query' => array(
								'relation' => 'AND',
						    array(
						      'key'       => 'wpsc_allow_ticket_filter',
						      'value'     => '1',
						      'compare'   => '='
						    ),
						    array(
						      'key'       => 'wpsc_customer_ticket_filter_status',
						      'value'     => '1',
						      'compare'   => '='
						    )
							),
						]);
					}
					foreach ( $fields as $field ){
						$label       = get_term_meta( $field->term_id, 'wpsc_tf_label', true);
						$filter_type = get_term_meta( $field->term_id, 'wpsc_ticket_filter_type', true);
						$wpsc_tf_type = get_term_meta( $field->term_id,'wpsc_tf_type', true);
						   if ($filter_type=='string' || $filter_type=='number') {
							if($field->slug == 'ticket_id'){
								$field->slug = 'id';
							}
							?>
							<div id="tf_<?php echo $field->slug?>" class="form-group col-sm-12">
								<label><?php echo htmlentities($label)?></label>
								<input type="text" data-field="<?php echo $field->slug?>" class="form-control wpsc_search_autocomplete" placeholder="<?php _e('Search...','supportcandy')?>">
								<ul class="wpsp_filter_display_container">
								
									<?php if(isset($filter['custom_filter'][$field->slug])):
										if(isset($filter['custom_filter'][$field->slug]) && apply_filters('wpsc_add_ticket_meta',true,$field->slug)){
											$meta_query[] = array(
												'key'     => $field->slug,
												'value'   => $filter['custom_filter'][$field->slug],
												'compare' => 'IN'
											);
										}else {
											$meta_query = apply_filters('wpsc_get_tickets_meta', $meta_query,$field->slug,$filter['custom_filter'][$field->slug]);
										}
									?>
									<?php foreach ($filter['custom_filter'][$field->slug] as $key => $value):?>
										<li class="wpsp_filter_display_element">
											<div class="flex-container">
												<div class="wpsp_filter_display_text">
													<?php echo $wpscfunction->get_tf_value_filter_label($field->slug,$value)?>
													<input type="hidden" name="custom_filter[<?php echo $field->slug?>][]" value="<?php echo $value?>">
												</div>
												<div class="wpsp_filter_display_remove" onclick="wpsc_remove_filter(this);">
													<i class="fa fa-times"></i>
												</div>
											</div>
										</li>
									<?php endforeach;?>
								<?php endif;?>								
								</ul>
							</div>
							<?php
						} elseif($filter_type=='datetime') {
							if( isset($filter['custom_filter'][$field->slug]['from']) && $filter['custom_filter'][$field->slug]['from'] && isset($filter['custom_filter'][$field->slug]['to']) && $filter['custom_filter'][$field->slug]['to'] ){
								$meta_query[] = array(
									'key'     => $field->slug,
									'value'   => array( 
										$filter['custom_filter'][$field->slug]['from'],
 									  $filter['custom_filter'][$field->slug]['to'],
									),
									'compare' => 'BETWEEN',
									'type' => 'DATE',
								);
							}
							
							?>
							<div class="row form-group">
								<label style="width:100%;padding-left:15px;"><?php echo htmlentities($label)?></label>
								<div class="col-sm-6">
									<input type="text" autocomplete="off" class="form-control wpsc_datetime" name="custom_filter[<?php echo $field->slug?>][from]" value="<?php echo isset($filter['custom_filter'][$field->slug]['from'])?$filter['custom_filter'][$field->slug]['from']:'';?>" placeholder="<?php _e('From','supportcandy')?>">
								</div>
								<div class="col-sm-6">
									<input type="text" autocomplete="off" class="form-control wpsc_datetime" name="custom_filter[<?php echo $field->slug?>][to]" value="<?php echo isset($filter['custom_filter'][$field->slug]['to'])?$filter['custom_filter'][$field->slug]['to']:'';?>" placeholder="<?php _e('To','supportcandy')?>">
								</div>
							</div>
							<?php
							
						}elseif($wpsc_tf_type=='21'){
							$time_format = get_term_meta($field->term_id, 'wpsc_time_format',true);
							if($time_format == '12'){
								$tr = 'hh:mm:ss tt';
							}elseif($time_format == '24'){
								$tr = 'HH:mm:ss';
							}

							if( isset($filter['custom_filter'][$field->slug]['from']) && $filter['custom_filter'][$field->slug]['from'] && isset($filter['custom_filter'][$field->slug]['to']) && $filter['custom_filter'][$field->slug]['to'] ){
								if($time_format == '12'){
									$from = date("H:i:s",strtotime($filter['custom_filter'][$field->slug]['from']));
									$to   = date("H:i:s",strtotime($filter['custom_filter'][$field->slug]['to']));
								}elseif($time_format == '24'){
									$from = $filter['custom_filter'][$field->slug]['from'];
									$to = $filter['custom_filter'][$field->slug]['to'];
								}
								$meta_query[] = array(
									'key'     => $field->slug,
									'value'   => array( 
										$from,
 									  	$to,
									),
									'compare' => 'BETWEEN',
									'type' => 'DATE',
								);
							}
							
							?>
							<div class="row form-group">
								<label style="width:100%;padding-left:15px;"><?php echo htmlentities($label)?></label>
								<div class="col-sm-6">
									<input type="text" autocomplete="off" class="form-control <?php echo $field->slug?>" name="custom_filter[<?php echo $field->slug?>][from]" value="<?php echo isset($filter['custom_filter'][$field->slug]['from'])?$filter['custom_filter'][$field->slug]['from']:'';?>" placeholder="<?php _e('From','supportcandy')?>">
								</div>
								<div class="col-sm-6">
									<input type="text" autocomplete="off" class="form-control <?php echo $field->slug?>" name="custom_filter[<?php echo $field->slug?>][to]" value="<?php echo isset($filter['custom_filter'][$field->slug]['to'])?$filter['custom_filter'][$field->slug]['to']:'';?>" placeholder="<?php _e('To','supportcandy')?>">
								</div>
							</div>
							<script>
								 jQuery('.<?php echo $field->slug?>').timepicker({
									timeFormat : '<?php echo $tr ?>'
		 						}); 
							</script>
							

							<?php	
						}elseif($wpsc_tf_type == '6'){
							$dr = "";
						    $date_range = get_term_meta($field->term_id, 'wpsc_date_range',true);
						    if($date_range == 'future'){
								$dr = "minDate: 0";
						   	}elseif($date_range == 'past'){
								$dr = "maxDate: 0";
							}
							if( isset($filter['custom_filter'][$field->slug]['from']) && $filter['custom_filter'][$field->slug]['from'] && isset($filter['custom_filter'][$field->slug]['to']) && $filter['custom_filter'][$field->slug]['to'] ){
									$meta_query[] = array(
										'key'     => $field->slug,
										'value'   => array( 
											get_date_from_gmt($wpscfunction->calenderDateFormatToDateTime($filter['custom_filter'][$field->slug]['from'])),
											get_date_from_gmt($wpscfunction->calenderDateFormatToDateTime($filter['custom_filter'][$field->slug]['to'])),
										),
										'compare' => 'BETWEEN',
										'type' => 'DATE',
									);

								}
							
								?>
								<div class="row form-group">
									<label style="width:100%;padding-left:15px;"><?php echo htmlentities($label)?></label>
									<div class="col-sm-6">
										<input type="text" autocomplete="off" class="form-control <?php echo $field->slug ?> " name="custom_filter[<?php echo $field->slug?>][from]" value="<?php echo isset($filter['custom_filter'][$field->slug]['from'])?$filter['custom_filter'][$field->slug]['from']:'';?>" placeholder="<?php _e('From','supportcandy')?>">
									</div>
									<div class="col-sm-6">
										<input type="text" autocomplete="off" class="form-control <?php echo $field->slug ?> " name="custom_filter[<?php echo $field->slug?>][to]" value="<?php echo isset($filter['custom_filter'][$field->slug]['to'])?$filter['custom_filter'][$field->slug]['to']:'';?>" placeholder="<?php _e('To','supportcandy')?>">
									</div>
								</div>
									
							<script>
							
							jQuery( ".<?php echo $field->slug?>").datepicker({
							dateFormat : '<?php echo get_option('wpsc_calender_date_format')?>',
							showAnim : 'slideDown',
							changeMonth: true,
							changeYear: true,
							yearRange: "-100:+100",
							<?php echo $dr?>

							});
						
						   </script>
						   
							
						
						<?php
						}else {
							if( isset($filter['custom_filter'][$field->slug]['from']) && $filter['custom_filter'][$field->slug]['from'] && isset($filter['custom_filter'][$field->slug]['to']) && $filter['custom_filter'][$field->slug]['to'] ){
								$meta_query[] = array(
									'key'     => $field->slug,
									'value'   => array( 
										get_date_from_gmt($wpscfunction->calenderDateFormatToDateTime($filter['custom_filter'][$field->slug]['from'])),
										get_date_from_gmt($wpscfunction->calenderDateFormatToDateTime($filter['custom_filter'][$field->slug]['to'])),
									),
									'compare' => 'BETWEEN',
									'type' => 'DATE',
								);
							}
						
							?>
							<div class="row form-group">
								<label style="width:100%;padding-left:15px;"><?php echo htmlentities($label)?></label>
								<div class="col-sm-6">
									<input type="text" autocomplete="off" class="form-control wpsc_date" name="custom_filter[<?php echo $field->slug?>][from]" value="<?php echo isset($filter['custom_filter'][$field->slug]['from'])?$filter['custom_filter'][$field->slug]['from']:'';?>" placeholder="<?php _e('From','supportcandy')?>">
								</div>
								<div class="col-sm-6">
									<input type="text" autocomplete="off" class="form-control wpsc_date" name="custom_filter[<?php echo $field->slug?>][to]" value="<?php echo isset($filter['custom_filter'][$field->slug]['to'])?$filter['custom_filter'][$field->slug]['to']:'';?>" placeholder="<?php _e('To','supportcandy')?>">
								</div>
							</div>
							<?php
							
						}
						
					}
					?>
		  </div>
		     <script>
				    jQuery(document).ready(function(){
					
			           jQuery( ".wpsc_date" ).datepicker({
					        dateFormat : '<?php echo get_option('wpsc_calender_date_format')?>',
							showAnim : 'slideDown',
							changeMonth: true,
							changeYear: true,
							yearRange: "-100:+100",
						
					});
				
				       jQuery('.wpsc_datetime').datetimepicker({
						 dateFormat : '<?php echo get_option('wpsc_calender_date_format')?>',
						  showAnim : 'slideDown',
							changeMonth: true,
			        changeYear: true,
						 timeFormat: 'HH:mm:ss'
					 });

					jQuery( ".wpsc_search_autocomplete" ).autocomplete({
			      minLength: 0,
			      appendTo: jQuery('.wpsc_search_autocomplete').parent(),
			      source: function( request, response ) {
			        var term = request.term;
			        request = {
			          action: 'wpsc_tickets',
			          setting_action : 'filter_autocomplete',
			          term : term,
								field : jQuery(this.element).data('field'),
			        }
			        jQuery.getJSON( wpsc_admin.ajax_url, request, function( data, status, xhr ) {
			          response(data);
			        });
			      },
						select: function (event, ui) {
			        var html_str = '<li class="wpsp_filter_display_element">'
															+'<div class="flex-container">'
																+'<div class="wpsp_filter_display_text">'
																	+ui.item.label
																	+'<input type="hidden" name="custom_filter['+ui.item.slug+'][]" value="'+ui.item.flag_val+'">'
																+'</div>'
																+'<div class="wpsp_filter_display_remove" onclick="wpsc_remove_filter(this);"><i class="fa fa-times"></i></div>'
															+'</div>'
														+'</li>';
							jQuery('#tf_'+ui.item.slug+' .wpsp_filter_display_container').append(html_str);
							jQuery(this).val(''); return false;
			      }
			    }).focus(function() {
							jQuery(this).autocomplete("search", "");
					});
				});
				function wpsc_set_save_ticket_filter(){  
				  var filter_name = jQuery('#wpsc_filter_label').val().trim();
				  if (filter_name.length == 0) {
				    jQuery('#wpsc_filter_label').val('').focus();
				    return;
				  }
				  var dataform = new FormData(jQuery('#frm_additional_filters')[0]);
				  dataform.append('filter_name', filter_name);
				  dataform.append('action', 'wpsc_tickets');
				  dataform.append('setting_action', 'set_save_ticket_filter');
				  jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','supportcandy')?>');
				  jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
				  jQuery.ajax({
				    url: wpsc_admin.ajax_url,
				    type: 'POST',
				    data: dataform,
				    processData: false,
				    contentType: false
				  })
				  .done(function (response_str) {    
				    wpsc_modal_close();      
				    wpsc_get_ticket_list();    
				  });      
				}
			</script>
			
			<div class="row wpsp_filter_footer" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_footer_bg_color']?> !important;">
				<div class="col-sm-12">
					<button type="submit" id="wpsc_load_apply_filter_btn" class="btn wpsp_filter_btn" style="background-color:<?php echo $wpsc_appearance_ticket_list['wpsc_apply_filter_btn_bg_color']?> !important;color:<?php echo $wpsc_appearance_ticket_list['wpsc_apply_filter_btn_text_color']?> !important;border-color:<?php echo $wpsc_appearance_ticket_list['wpsc_apply_filter_btn_border_color']?> !important;"><?php _e('Apply Filter','supportcandy')?></button>
					<button type="button" id="wpsc_save_ticket_filter_btn" onclick="wpsc_get_save_ticket_filter();" class="btn wpsp_filter_btn" style="background-color:<?php echo $wpsc_appearance_ticket_list['wpsc_save_filter_btn_bg_color']?> !important;color:<?php echo $wpsc_appearance_ticket_list['wpsc_save_filter_btn_text_color']?> !important;border-color:<?php echo $wpsc_appearance_ticket_list['wpsc_save_filter_btn_border_color']?> !important;"><?php _e('Save Filter','supportcandy')?></button>
					<button type="button" id="wpsc_load_close_filter" onclick="wpsc_close_custom_filter();" class="btn wpsp_filter_btn" style="background-color:<?php echo $wpsc_appearance_ticket_list['wpsc_close_filter_btn_bg_color']?> !important;color:<?php echo $wpsc_appearance_ticket_list['wpsc_close_filter_btn_text_color']?> !important;border-color:<?php echo $wpsc_appearance_ticket_list['wpsc_close_filter_btn_border_color']?> !important;"><?php _e('Close','supportcandy')?></button>
				</div>
			</div>
		</div>
		<input type="hidden" name="filter" value="all">
	</div>
	
	<input type="hidden" name="action" value="wpsc_tickets">
	<input type="hidden" name="setting_action" value="set_custom_filter">
	<input type="hidden" id="wpsc_pg_no" name="page_no" value="<?php echo htmlentities($filter['page'])?>">
	<input type="hidden" id="wpsc_th_orderby" name="orderby" value="<?php echo htmlentities($filter['orderby'])?>">
	<input type="hidden" id="wpsc_th_order" name="order" value="<?php echo htmlentities($filter['order'])?>">
	
</form>

<?php
$search = '';
$is_search = trim($filter['custom_filter']['s']) ? true : false;
if($is_search){
	$search = trim($filter['custom_filter']['s']);
}

$active = 1;
if ($filter['label'] == 'deleted') {
	$active = 0;
}

$meta_query[] = array(
	'key'     => 'active',
	'value'   => $active,
	'compare' => '='
);

$orderby      = sanitize_text_field($filter['orderby']=='ticket_id'?'id':$filter['orderby']);
$order        = sanitize_text_field($filter['order']);
$current_page = sanitize_text_field($filter['page']);
$select_str   = 'SQL_CALC_FOUND_ROWS DISTINCT t.*';
$sql          = $wpscfunction->get_sql_query( $select_str, $meta_query, $search, $orderby, $order, $post_per_page, $current_page );
$tickets      = $wpdb->get_results($sql);
$total_items  = $wpdb->get_var("SELECT FOUND_ROWS()");
$ticket_list  = json_decode(json_encode($tickets), true);
$total_pages  = ceil($total_items/$post_per_page);

if( $total_items<=$current_page*$post_per_page){
 $no_of_tickets = $total_items;
}
else {
 $no_of_tickets = $current_page*$post_per_page;
}

if ($current_user->has_cap('wpsc_agent')) {
	$ticket_list_items = get_terms([
		'taxonomy'   => 'wpsc_ticket_custom_fields',
		'hide_empty' => false,
		'orderby'    => 'meta_value_num',
		'meta_key'	 => 'wpsc_tl_agent_load_order',
		'order'    	 => 'ASC',
		'meta_query' => array(
			'relation' => 'AND',
	    array(
	      'key'       => 'wpsc_allow_ticket_list',
	      'value'     => '1',
	      'compare'   => '='
	    ),
	    array(
	      'key'       => 'wpsc_agent_ticket_list_status',
	      'value'     => '1',
	      'compare'   => '='
	    )
		),
	]);
} else {
	$ticket_list_items = get_terms([
		'taxonomy'   => 'wpsc_ticket_custom_fields',
		'hide_empty' => false,
		'orderby'    => 'meta_value_num',
		'meta_key'	 => 'wpsc_tl_customer_load_order',
		'order'    	 => 'ASC',
		'meta_query' => array(
			'relation' => 'AND',
	    array(
	      'key'       => 'wpsc_allow_ticket_list',
	      'value'     => '1',
	      'compare'   => '='
	    ),
	    array(
	      'key'       => 'wpsc_customer_ticket_list_status',
	      'value'     => '1',
	      'compare'   => '='
	    )
		),
	]);
}
?>
<script>
var link = true;
</script>
<div class="col-sm-6 col-sm-offset-6 col-xs-12 wpsc_ticket_count" style="margin-top:-20px !important;text-align:right;font-size:12px;padding-right:0;">
		<?php
		if(apply_filters('wpsc_show_ticket_count',true)){
			if( $total_items > $post_per_page ){?>
				 <strong><?php echo ($current_page*$post_per_page-$post_per_page)+1;?></strong>-<strong><?php echo htmlentities($no_of_tickets);?></strong> <?php _e('of' ,'supportcandy'); ?> <strong><?php echo htmlentities($total_items);?></strong> <?php ($total_items >1)?_e('Tickets','supportcandy'):_e('Ticket','supportcandy');?>
				<?php	
			} else {
		     ?>
				   <strong><?php echo htmlentities($total_items);?> <?php ($total_items >1 ) ? _e('Tickets','supportcandy'): _e('Ticket','supportcandy');?> </strong>
			<?php 
			 }
		}
	 ?>
</div>
<table id="tbl_wpsc_ticket_list" class="table" >
  <tr>
    <th class="wpsc_th_check_all" style="background-color:<?php echo $wpsc_appearance_ticket_list['wpsc_ticket_list_header_bg_color']?> !important;color:<?php echo $wpsc_appearance_ticket_list['wpsc_ticket_list_header_text_color']?> !important;"><input id="chk_all_ticket_list" onchange="toggle_list_checkboxes(this);" type="checkbox" /></th>
		<?php
		foreach ($ticket_list_items as $list_item) {
			$label         = get_term_meta( $list_item->term_id, 'wpsc_tf_label', true);
			$allow_orderby = get_term_meta( $list_item->term_id, 'wpsc_allow_orderby', true);						
			?>
			<th class="wpsc_th_<?php echo $list_item->slug ?>"onclick="<?php echo $allow_orderby ? 'wpsc_header_sort(\''.$list_item->slug.'\')' : ''?>" style="background-color:<?php echo $wpsc_appearance_ticket_list['wpsc_ticket_list_header_bg_color']?> !important;color:<?php echo $wpsc_appearance_ticket_list['wpsc_ticket_list_header_text_color']?> !important;">
				<?php 
				_e($label,'supportcandy');
				if( $filter['orderby']==$list_item->slug && $filter['order'] =='ASC' ){
						?> <i class="fa fa-caret-down"></i><?php
				} else if( $filter['orderby']==$list_item->slug && $filter['order'] =='DESC' ){
						?> <i class="fa fa-caret-up"></i><?php
				}
				?>
			</th>
			<?php
		}
		?>
		<?php do_action('wpsc_print_ticket_list_table_header'); ?>
  </tr>
	
	<?php
	
	include_once WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/class-ticket-list-field-format.php';
	$format = new WPSC_Ticket_List_Field();
		if($ticket_list){
			foreach($ticket_list as $ticket){
				?>
				<tr class="wpsc_tl_row_item" data-id="<?php echo $ticket['id']?>" onclick="if(link)wpsc_get_individual_ticket(this);">
					<td onmouseover="link=false;" onmouseout="link=true;">
						<input type="checkbox" name="chk_ticket_list_item[]" class="chk_ticket_list_item" onchange="toggle_ticket_list_actions();" value="<?php echo $ticket['id']; ?>"/>
					</td>
					<?php
					foreach ($ticket_list_items as $list_item) {
						?>
						<td style="<?php echo $list_item->slug == 'ticket_subject' || $list_item->slug == 'assigned_agent' ? 'white-space: normal;' : ''?>"><?php echo $format->print_field($list_item,$ticket);?></td>
						<?php
					}
					?>
					<?php do_action('wpsc_print_fields_in_ticket_list',$ticket); ?>
				</tr>
				<?php
			}
			$current_page = sanitize_text_field($filter['page']);
			}
			else{
				?>
				<tr>
					<td colspan="4"><?php _e('No tickets found!','supportcandy')?></td>
				</tr>
				<?php
			}
			?>
		</table>

		<?php 
		$wpsc_tl_row_item_css = 'background-color:'.$wpsc_appearance_ticket_list['wpsc_ticket_list_item_mo_bg_color'].' !important;color:'.$wpsc_appearance_ticket_list['wpsc_ticket_list_item_mo_text_color'].' !important;';
		?>
		<style type="text/css">
			.wpsc_tl_row_item:hover{
				<?php echo htmlentities($wpsc_tl_row_item_css)?>
			}
		</style>
		<?php
		if($ticket_list) : ?>	
		<div class="row wpsc_pagination_prev_next_btn" style="margin-bottom:20px;">
	  	  <div class="col-md-4 col-md-offset-4 wpsc_ticket_list_nxt_pre_page" style="text-align: center;">
		   <?php 
		   if($total_pages != 1){ ?>
           		<button class="btn btn-default btn-sm" <?php echo $filter['page']==1? 'disabled' : ''?> onclick="wpsc_ticket_first_page();"><i class="fas fa-fast-backward" aria-hidden="true"></i></button>  
		   		<button class="btn btn-default btn-sm" <?php echo $filter['page']==1? 'disabled' : ''?> onclick="wpsc_ticket_prev_page();"><i class="fas fa-step-backward" aria-hidden="true"></i></button>
		   		<strong><?php echo $current_page ?></strong> <?php _e('of','supportcandy')?> <strong><?php echo $total_pages?></strong> <?php _e('Pages','supportcandy') ?>
		   		<button class="btn btn-default btn-sm" <?php echo $filter['page']==$total_pages? 'disabled' : ''?> onclick="wpsc_ticket_next_page();"><i class="fas fa-step-forward" aria-hidden="true"></i></button>
		   		<button class="btn btn-default btn-sm" onclick="wpsc_last_ticket_page();" <?php echo $filter['page']==$total_pages? 'disabled' : ''?>><i class="fas fa-fast-forward" aria-hidden="true"></i></button>
		   	<?php 
			} ?>
		  </div>
		</div>
		<?php endif; ?>

<script>
	jQuery(document).ready(function() {
		jQuery('#wpsc_ticket_search').on("keypress", function(e) {
			if (e.keyCode == 13) {
				jQuery('#wpsc_pg_no').val('1');
			}
		});
		
		jQuery('#wpsc_load_apply_filter_btn').on("click", function(e) {
			jQuery('#wpsc_pg_no').val('1');
		});
	});
	<?php do_action('wpsc_print_ext_js_ticket_list');	?>
 
	function wpsc_last_ticket_page(){
		var page_no = parseInt(jQuery('#wpsc_pg_no').val().trim());
		var last_page=<?php echo json_encode($total_pages); ?>;
		if(page_no != last_page){
			page_no = last_page;
			jQuery('#wpsc_pg_no').val(page_no);
			wpsc_set_custom_filter();
		}  
	}

	function wpsc_ticket_first_page(){
		var page_no = parseInt(jQuery('#wpsc_pg_no').val().trim());
		if(page_no > 1){ 
			page_no = 1;
			jQuery('#wpsc_pg_no').val(page_no);
			wpsc_set_custom_filter();
		}
	}

</script>