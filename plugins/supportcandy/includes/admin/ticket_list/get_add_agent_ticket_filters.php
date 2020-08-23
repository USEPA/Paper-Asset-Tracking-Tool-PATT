<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$fields = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
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
      'compare'   => '!='
    )
	),
]);

ob_start();
?>
<div class="form-group">
  <label for="wpsc_al_item"><?php _e('Select Field','supportcandy');?></label>
  <select id="wpsc_al_item" class="form-control" name="wpsc_al_item">
    <?php foreach ( $fields as $field ) :
  		$label = get_term_meta( $field->term_id, 'wpsc_tf_label', true);
  		?>
      <option value="<?php echo $field->term_id?>"><?php echo htmlentities($label)?></option>
  	<?php endforeach;?>
  </select>
</div>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_add_agent_ticket_filters();"><?php _e('Submit','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
