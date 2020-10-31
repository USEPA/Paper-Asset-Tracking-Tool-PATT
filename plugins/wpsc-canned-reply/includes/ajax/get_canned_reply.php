<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpdb,$post;

if (!$current_user->ID) die();

$wpsc_appearance_modal_window = get_option('wpsc_modal_window');

$args = array(
	'post_type'      => 'wpsc_canned_reply',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'meta_query' => array(
		'relation' => 'OR',
		array(
			'key'     => 'wpsc_agent_visibility',
			'value'   => '1',
			'compare' => '='
		),
		array(
			'key'     => 'wpsc_author',
			'value'   => $current_user->ID,
			'compare' => '='
		),
	),
);

$posts = get_posts($args);
ob_start();
?>
<div class="table-responsive">
	<table id="tbl_templates"   class="table table-striped table-bordered"  cellspacing="5" cellpadding="5">
  <thead>
  	<tr>
    <th><?php _e('Tag','wpsc-canned-reply')?></th>
    <th><?php _e('Category','wpsc-canned-reply')?></th>
		<th><?php _e('Actions','wpsc-canned-reply')?></th>
  </tr>
	</thead>
	<tbody>
	<?php
	
	foreach ($posts as $field) {			
		?>
		<tr>
	    <td class="wpsc_tag_td" onclick="wpsc_insert_editor_text_data('<?php echo $field->ID ?>')"><?php echo $field->post_title?></td>	   
				<?php 				
				$categories = get_the_terms($field->ID, "wpsc_canned_reply_categories");				
        if( ! empty( $categories ) ) {					
				 $category_name= array();					
	          foreach ($categories as $category) {										  
	            $category_name[] = $category->name;						
	          }					
					?><td><?php echo $category_name = implode(',', $category_name);?></td><?php					
        }else {
            $category_name = __('Uncategorized','wpsc-canned-reply');
						?><td><?php echo $category_name;?></td><?php
          }
				?>			
			<td>
				<?php
				$author = get_post_meta($field->ID,'wpsc_author',true);
				if($current_user->ID == $author){
					?>
					<i onclick="wpsc_delete_canned_reply(<?php echo $field->ID; ?>)" class="far fa-trash-alt thread_action_icon" aria-hidden="true" data-toggle="tooltip" data-placement="left"></i>
					<?php
				}
				?>
			</td>
	  </tr>
		<?php
	}
	?>
	</tbody>
</table>
</div>
<link rel="stylesheet" type="text/css" href="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.js';?>"></script>
<script>
 jQuery(document).ready(function() {
	 jQuery('#tbl_templates').DataTable({
		 "aLengthMenu": [[4, 8, 12, -1], [4, 8, 12, "All"]]		
		});
} );

function wpsc_delete_canned_reply(post_id){
  if(confirm("Are you sure to delete this canned reply?")){
    var data = {
      action: 'delete_canned_reply',      
      post_id : post_id
    };  
    jQuery.post(wpsc_admin.ajax_url, data, function(response) {
      wpsc_get_canned_reply();
    });
  }
}

function wpsc_insert_editor_text_data(post_id){  
  var data = {
    action: 'wpsc_insert_canned_reply',
    post_id: post_id	     
  };  
  jQuery.post(wpsc_admin.ajax_url, data, function(response) {      
    var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
	if (is_tinymce) {
		tinymce.activeEditor.execCommand('mceInsertContent', false, response);
	} else {

		var $txt = jQuery(".wpsc_textarea");
    	var caretPos = $txt[0].selectionStart;
    	var textAreaTxt = $txt.val();
    	$txt.val(textAreaTxt.substring(0, caretPos) + response + textAreaTxt.substring(caretPos) );
	}  
    wpsc_modal_close();
  }); ;       
}
</script>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;" onclick="wpsc_modal_close();"><?php _e('Close','wpsc-canned-reply');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
?>
