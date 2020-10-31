<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$is_addons = apply_filters( 'wpsc_is_add_on_installed', false );
if($is_addons) {
  return;
}

?>

<div class="bootstrap-iso">

  <div class="row" style="padding: 10px;background-color: #94A0E4;margin: 10px 0;width: 99%;border-radius: 4px;">
    
		<div class="col-md-12">
			<div style="width:145px;float:left;">
	      <a href="https://supportcandy.net/">
	        <img src="<?php echo WPSC_PLUGIN_URL.'asset/images/supportcandy_logo.png'?>">
	      </a>
	    </div>
	    <div class="col-md-10" style="font-size:18px;text-align:left;color:#fff; padding: 5px 0;">
	      Access premium features at extremely affordable price with our <strong><a target="_blank" style="color: #fff;" href="https://supportcandy.net/pricing/">Premium Plans</a></strong>.<br><br>
	      <small><i>(This ad will get automatically removed as soon as at least one add-on is installed.)</i></small>
	    </div>
		</div>
    
  </div>

</div>