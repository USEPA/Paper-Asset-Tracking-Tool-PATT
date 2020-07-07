<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$wpsc_captcha = get_option('wpsc_captcha',0);
$wpsc_recaptcha_type = get_option('wpsc_recaptcha_type');
$wpsc_get_secret_key = get_option('wpsc_get_secret_key');
$wpsc_guest_can_upload_files = get_option('wpsc_guest_can_upload_files');

if($wpsc_captcha){
	if($wpsc_recaptcha_type){
		$captcha_key =  isset($_COOKIE) && isset($_COOKIE['wpsc_secure_code']) ? intval($_COOKIE['wpsc_secure_code']) : 0;
		if( !isset($_POST['captcha_code']) || !wp_verify_nonce($_POST['captcha_code'],$captcha_key) ){
		    die(__('Cheating huh?', 'supportcandy'));
		}
	}
	else {
		if(isset($_POST['g-recaptcha-response'])){
			$captcha=$_POST['g-recaptcha-response'];
		}
		if(!$captcha){
			die(__('Cheating huh?', 'supportcandy'));
		}
		$secretKey = $wpsc_get_secret_key;
		$ip = $_SERVER['REMOTE_ADDR'];
		$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
		$responseKeys = json_decode($response,true);
			if(intval($responseKeys["success"]) !== 1) {
				die(__('Cheating huh?', 'supportcandy'));
			} 
	}
	setcookie('wpsc_secure_code','123');
}

global $current_user, $wpscfunction;

$args = array();

// Customer name
if(is_user_logged_in() && !$current_user->has_cap('wpsc_agent') ){
	$customer_name = $current_user->display_name;
}else{
	$customer_name = isset($_POST['customer_name']) ? sanitize_text_field($_POST['customer_name']) : '';
}
$args['customer_name'] = $customer_name;

// Customer email
if(is_user_logged_in() && !$current_user->has_cap('wpsc_agent') ){
	$customer_email = $current_user->user_email;
	$args['customer_email']	= $customer_email;
}else{
	$customer_email = isset($_POST['customer_email']) ? sanitize_text_field($_POST['customer_email']) : '';
	$args['customer_email'] = $customer_email;
}


// CR - Add data to request
$boxinfodata = $_POST["boxinfo"];
$args['box_info'] = $boxinfodata;


// Subject
$ticket_subject = isset($_POST['ticket_subject']) ? sanitize_text_field($_POST['ticket_subject']) : '';
if($ticket_subject) $args['ticket_subject'] = $ticket_subject;

// Description
$ticket_description = isset($_POST['ticket_description']) ? wp_kses_post(htmlspecialchars_decode($_POST['ticket_description'], ENT_QUOTES)) : '';
if($ticket_description) $args['ticket_description'] = $ticket_description;
if(is_user_logged_in() || $wpsc_guest_can_upload_files ){
	$description_attachment = isset($_POST['desc_attachment']) ? $_POST['desc_attachment'] : array();
	if($description_attachment) $args['desc_attachment'] = $description_attachment;
}

// Category
$ticket_category = isset($_POST['ticket_category']) ? intval($_POST['ticket_category']) : '';
if($ticket_category) $args['ticket_category'] = $ticket_category;

// Priority
$ticket_priority = isset($_POST['ticket_priority']) ? intval($_POST['ticket_priority']) : '';
if($ticket_priority) $args['ticket_priority'] = $ticket_priority;

// Custom fields
$fields = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'meta_key'	 => 'wpsc_tf_load_order',
	'order'    	 => 'ASC',
	'meta_query' => array(
		'relation' => 'AND',
		array(
      'key'       => 'agentonly',
      'value'     => '0',
      'compare'   => '='
    ),
		array(
      'key'       => 'wpsc_tf_type',
      'value'     => '0',
      'compare'   => '>'
    ),
	),
]);
foreach ($fields as $field) {
	$tf_type = get_term_meta( $field->term_id, 'wpsc_tf_type', true);
	switch ($tf_type) {
		case '1':	
		case '2':
		case '4':
		case '6':
		case '7':
		case '8':
		case '18':
			$text = isset($_POST[$field->slug]) ? sanitize_text_field($_POST[$field->slug]) : '';
			if($text) $args[$field->slug] = $text;
			break;
			
		case '3':
		case '10':
			$arrVal = isset($_POST[$field->slug]) ? $_POST[$field->slug] : array();
			if($arrVal) $args[$field->slug] = $wpscfunction->sanitize_array($arrVal);
			break;
			
		case '5':
			$text = isset($_POST[$field->slug]) ? wp_kses_post(htmlspecialchars_decode($_POST[$field->slug], ENT_QUOTES)) : '';
			if($text) $args[$field->slug] = $text;
			break;
			
		case '9':
			$number = isset($_POST[$field->slug]) ? intval($_POST[$field->slug]) : '';
			if($number) $args[$field->slug] = $number;
			break;
		
		case '21':
			$text = isset($_POST[$field->slug]) ? sanitize_text_field($_POST[$field->slug]) : '';
			if($text) $args[$field->slug] = date("H:i:s " ,strtotime($text));
			break;

		default:	
			$args = apply_filters('wpsc_after_create_ticket_custom_field',$args,$field,$tf_type);
			break;		
	}
}

$args = apply_filters( 'wpsc_before_create_ticket_args', $args);

$ticket_id = $wpscfunction->create_ticket($args);
$thankyou_html = $wpscfunction->replace_macro(get_option('wpsc_thankyou_html'),$ticket_id);
$thankyou_html = apply_filters('wpsc_after_thankyou_page_button',$thankyou_html,$ticket_id);


/**
 * CR - ADDING NEW URL 
*/
$num = $ticket_id;
$str_length = 7;
$padded_request_id = substr("000000{$num}", -$str_length);
$request_link = site_url().'/data/?id='.$padded_request_id;
$thankyou_html = str_replace('{request_id}', $padded_request_id, $thankyou_html);
$thankyou_html .= '<p><a target="_blank" href="'.$request_link .'">'.$request_link .'</a></p>';

ob_start();
?>
<div class="col-sm-12" style="margin-top:20px;">
	<?php echo html_entity_decode(stripslashes($thankyou_html))?>
</div>
<?php
$thankyou_html = ob_get_clean();

$response = array(
  'redirct_url'    => get_option('wpsc_thankyou_url'),
  'thank_you_page' => $thankyou_html,
);

echo json_encode($response);