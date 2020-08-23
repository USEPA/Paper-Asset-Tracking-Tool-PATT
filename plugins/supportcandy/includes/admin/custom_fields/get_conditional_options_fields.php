<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$field_id = isset($_POST) && isset($_POST['field_id']) ? intval($_POST['field_id']) : 0;
if (!$field_id) {exit;}

$custom_field =  get_term_by('id', $field_id, 'wpsc_ticket_custom_fields');
$custom_field_type = get_term_meta( $custom_field->term_id, 'wpsc_tf_type', true);

if($custom_field_type=='0'){
  
  if ($custom_field->slug=='ticket_category') {
    
    $categories = get_terms([
    	'taxonomy'   => 'wpsc_categories',
    	'hide_empty' => false,
    	'orderby'    => 'meta_value_num',
    	'order'    	 => 'ASC',
    	'meta_query' => array('order_clause' => array('key' => 'wpsc_category_load_order')),
    ]);
    ?>
    <option value=""><?php _e('Select option','supportcandy');?></option>
    <?php foreach ( $categories as $category ) :?>
      <option value="<?php echo $category->term_id?>"><?php echo $category->name?></option>
    <?php endforeach;
    
  }
  
  if ($custom_field->slug=='ticket_priority') {
    
    $priorities = get_terms([
    	'taxonomy'   => 'wpsc_priorities',
    	'hide_empty' => false,
    	'orderby'    => 'meta_value_num',
    	'order'    	 => 'ASC',
    	'meta_query' => array('order_clause' => array('key' => 'wpsc_priority_load_order')),
    ]);
    ?>
    <option value=""><?php _e('Select option','supportcandy');?></option>
    <?php foreach ( $priorities as $priority ) :?>
      <option value="<?php echo $priority->term_id?>"><?php echo $priority->name?></option>
    <?php endforeach;
    
  }
	
	if ($custom_field->slug=='ticket_status') {
    
    $statuses = get_terms([
    	'taxonomy'   => 'wpsc_statuses',
    	'hide_empty' => false,
    	'orderby'    => 'meta_value_num',
    	'order'    	 => 'ASC',
    	'meta_query' => array('order_clause' => array('key' => 'wpsc_status_load_order')),
    ]);
    ?>
    <option value=""><?php _e('Select option','supportcandy');?></option>
    <?php foreach ( $statuses as $status ) :?>
      <option value="<?php echo $status->term_id?>"><?php echo $status->name?></option>
    <?php endforeach;
    
  }
  
} else {
  
  $wpsc_tf_options = get_term_meta( $custom_field->term_id, 'wpsc_tf_options', true);
  if (!$wpsc_tf_options) {exit;}
  ?>
  <option value=""><?php _e('Select option','supportcandy');?></option>
  <?php foreach ( $wpsc_tf_options as $option ) :?>
    <option value="<?php echo str_replace('"', "&quot;", stripcslashes($option))?>"><?php echo stripcslashes($option)?></option>
  <?php endforeach;
  
}
