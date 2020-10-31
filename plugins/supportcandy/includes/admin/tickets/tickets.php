<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$general_appearance = get_option('wpsc_appearance_general_settings');
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');
?>

<div class="bootstrap-iso">
  
   <h3>
    <?php
    $request_id = $_GET['id'];
    if($request_id != '') {
    ?>
    Request Details
    <?php
    } else {
    ?>
    <?php _e('Tickets','supportcandy');?>
    <?php
    }
    ?>
  </h3>
  
  <div id="wpsc_tickets_container" class="row" style="border-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;"></div>
  
  <div id="wpsc_alert_success" class="alert alert-success wpsc_alert" style="display:none;" role="alert">
    <i class="fa fa-check-circle"></i> <span class="wpsc_alert_text"></span>
  </div>
  
  <div id="wpsc_alert_error" class="alert alert-danger wpsc_alert" style="display:none;" role="alert">
    <i class="fa fa-exclamation-triangle"></i> <span class="wpsc_alert_text"></span>
  </div>
  
</div>

<!-- Pop-up snippet start -->
<div id="wpsc_popup_background" style="display:none;"></div>
<div id="wpsc_popup_container" style="display:none;">
  <div class="bootstrap-iso">
    <div class="row">
      <div id="wpsc_popup" class="col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
        <div id="wpsc_popup_title" class="row" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_header_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_header_text_color']?> !important;"><h3><?php _e('Modal Title','supportcandy');?></h3></div>
        <div id="wpsc_popup_body" class="row"><?php _e('I am body!','supportcandy');?></div>
        <div id="wpsc_popup_footer" class="row" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_footer_bg_color']?> !important;">
          <button type="button" class="btn wpsc_popup_close" ><?php _e('Close','supportcandy');?></button>
          <button type="button" class="btn wpsc_popup_action"><?php _e('Save Changes','supportcandy');?></button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Pop-up snippet end -->

<?php
add_action('admin_footer', 'wpsc_page_inline_script');
global $attrs;
$attrs = isset($attr['page'])? $attr['page']:'init';
?>
<script>
  var wpsc_setting_action = '<?php echo $attrs?>';
</script>

<?php
function wpsc_page_inline_script(){
  ?>
  <script>
  <?php
     $url_attrs = array();
     foreach ($_GET as $key => $value) {
       $url_attrs[] = '"'.sanitize_text_field($key).'":"'.sanitize_text_field($value).'"';
     }
     $url_attrs = '{'.implode(',',$url_attrs).'}'
  ?>
       var attrs = <?php echo $url_attrs?>;
       jQuery(document).ready(function(){
         <?php /*PATT BEGIN*/ $GLOBALS['id'] = $_GET['id']; if (!empty($GLOBALS['id']) && preg_match("/^[0-9]{7}$/", $GLOBALS['id'])) { ?>
wpsc_open_ticket(<?php echo Patt_Custom_Func::convert_request_id($GLOBALS['id']); ?>);
<?php } else { ?>
wpsc_init(wpsc_setting_action,attrs);
<?php } /*PATT END*/ ?>
       });
  </script>
  <?php
}
?>