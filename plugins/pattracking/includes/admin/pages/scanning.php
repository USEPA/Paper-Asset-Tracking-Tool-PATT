<?php
    
    if ( ! defined( 'ABSPATH' ) ) {
    	exit; /* Exit if accessed directly */
    }
    
    // Code to add ID lookup
    global $current_user, $wpscfunction, $wpdb;
    
    $agent_permissions = $wpscfunction->get_current_agent_permissions();
    
    $GLOBALS['id'] = $_GET['id'];
    
    $id = $GLOBALS['id'];
    $dash_count = substr_count($id, '-');
    
    $general_appearance = get_option('wpsc_appearance_general_settings');
    $action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';
    $wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');
    $edit_btn_css = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_border_color'].'!important';
    
    echo '<link rel="stylesheet" type="text/css" href="' . WPPATT_PLUGIN_URL . 'includes/admin/css/scan-table.css"/>';
    
?>


<script>

    var outputArray = [];

    var boxid_values = [];
    var scanid_values = [];
    
    const reg_physicalLocation = /^\d{1,3}A_\d{1,3}B_\d{1,3}S_\d{1,3}P$/gi;
    
    const reg_boxid = /^\d{7}-\d{1,}$/i;

    const reg_cartid = /(\bcid-\d\d-e\b|\bcid-\d\d-w\b)|(\bcid-\d\d-east\scui\b|\bcid-\d\d-west\scui\b)|(\bcid-\d\d-east\b|\bcid-\d\d-west\b)|(\bcid-\d\d-eastcui\b|\bcid-\d\d-westcui\b)/gim;   

    const reg_stagingarea = /^\b(sa-e|sa-w)\b$/i;  

    const reg_scanning = /^\b(SCN-\d\d-e|SCN-\d\d-w)\b$/i; 
    
    var POST_count = 0;
    
    jQuery(document).ready(function() {

        
          jQuery("textarea#boxid-textarea").focus();
 
            jQuery('#submitbtn').css('border', '');
            jQuery('#submitbtn').css('box-shadow', '');

            
            /* 
                 $('#element').donetyping(callback[, timeout=1000])
                 Fires callback when a user has finished typing. This is determined by the time elapsed
                 since the last keystroke and timeout parameter or the blur event--whichever comes first.
                   @callback: function to be called when even triggers
                   @timeout:  (default=1000) timeout, in ms, to to wait before triggering event if not
                              caused by blur.
                 Requires jQuery 1.7+
            */

            ;(function(jQuery){
                (jQuery).fn.extend({
                    donetyping: function(callback,timeout){
                        timeout = timeout || 1e3; // 1 second default timeout
                        var timeoutReference,
                            doneTyping = function(el){
                                if (!timeoutReference) return;
                                timeoutReference = null;
                                callback.call(el);
                            };
                        return this.each(function(i,el){
                            var $el = (jQuery)(el);
                            // Chrome Fix (Use keyup over keypress to detect backspace)
                            $el.is('textarea') && $el.on('keyup keypress paste',function(e){
                                // This catches the backspace button in chrome, but also prevents
                                // the event from triggering too preemptively. Without this line,
                                // using tab/shift+tab will make the focused element fire the callback.
                                if (e.type=='keyup' && e.keyCode!=8) return;
                                
                                // 6/26/2020 - Function to add a newline if the return key (13) is pressed
                                var code = e.keyCode ? e.keyCode : e.which;
                                if (e.type=='keyup' && code == 13) jQuery('textarea#boxid-textarea').val(jQuery('textarea#boxid-textarea').val().trim() + '\n'); 
                                if (e.type=='keyup' && code == 13) return;

                                // Check if timeout has been set. If it has, "reset" the clock and
                                // start over again.
                                if (timeoutReference) clearTimeout(timeoutReference);
                                timeoutReference = setTimeout(function(){
                                    // if we made it here, our timeout has elapsed. Fire the
                                    // callback
                                    doneTyping(el);
                                }, timeout);
                            }).on('blur',function(){
                                // If we can, fire the event since we're leaving the field
                                doneTyping(el);
                            });
                        });
                    }
                });
            })(jQuery);
            
            jQuery('textarea#boxid-textarea').donetyping(function(){

                text = jQuery('textarea#boxid-textarea').val();
        		lines = text.split(/\r|\r\n|\n/);
        		lines_arr = new Array(); 
      		
        		jQuery.each(lines,function(index, last_boxid_val){
    
                    if (reg_boxid.test(last_boxid_val)){
            			if (jQuery.inArray(last_boxid_val, lines_arr) ==-1){
            				lines_arr.push(last_boxid_val);
            				
            			}else{
            			    
                            alert( last_boxid_val + " is a duplicate.");
            			}
            		}else{

            		    if (last_boxid_val == ""){
            		    }else{
                            alert( last_boxid_val + " is a invalid BoxID.");
            		    }
            		}
        		});  
            });

            jQuery('input#scan-input').donetyping(function(){
                
                var text_vals = jQuery(this).val().toLowerCase();

                  //  jQuery.each(text_vals.split(/[\s,]+/).filter(Boolean), function(index, last_scan_val) {

                            if (reg_cartid.test(text_vals)){
                                
                                scanid_values.push(text_vals);

                            }else if(reg_stagingarea.test(text_vals)){
                                
                                 scanid_values.push(text_vals);
                                
                            }else if(reg_scanning.test(text_vals)){
                                
                                 scanid_values.push(text_vals);

                            }else if(reg_physicalLocation.test(text_vals)){
                                
                                scanid_values.push(text_vals);

                            }else{                 
                                alert(text_vals + " is invalid.");
                            }
                   // });
            });

            jQuery('input#scan-input').focus(function(){
                jQuery(this).addClass("focused");
            });
            jQuery('input#scan-input').blur(function(){
                jQuery(this).removeClass("focused");
            }); 

            jQuery('#next-to-scan-btn').on('click', function (e) {
                e.preventDefault();
                
                jQuery(this).next().find('#submitbtn').focus();
                jQuery('#submitbtn').css('border', '');
                jQuery('#submitbtn').css('box-shadow', '');
                
                var textarea = jQuery('#scan-input');
                textarea.focus();

            });
                
 
                
            var counter=0;
            jQuery('form').on('submit',  function(e) {
                e.preventDefault();

                jQuery(this).next().find('#submitbtn').focus();
                jQuery('#submitbtn').css('border', '');
                jQuery('#submitbtn').css('box-shadow', '');
                
                var scanid_uniq = jQuery('input#scan-input').val();
                text = jQuery('textarea#boxid-textarea').val();
        		lines = text.split(/\r|\r\n|\n/);
        		boxid_uniq = new Array(); 
        		
        		jQuery.each(lines,function(index, last_boxid_val){
            			if (jQuery.inArray(last_boxid_val, boxid_uniq) ==-1)
            				boxid_uniq.push(last_boxid_val)
        		});     
        		
        		/* Remove empty array elements */
                boxid_uniq = boxid_uniq.filter(item => item);

                    if(boxid_uniq.length > 0 && scanid_uniq.length > 0){

                        POST_count++;

                        if(POST_count >= 1){
    
                            jQuery.post(
                                        '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_scan_location.php',{ 
                                                postvarsboxid: boxid_uniq,
                                                postvarslocation: scanid_uniq
                                            }, 
                                                function (data) {
                                                    
                                                    /* 9/24/20 - Only show eight messages on the client by counting visible wpsc_thread_log 
                                                       10/2/20 - changed display, inline to display, inline-block in .confirmation tag */
                                                    var numMessages = jQuery('.wpsc_thread_log').length;
                                                
                                                    if(!alert(data)){
                            
                                                        var response = data;
                                                        
                                                        if(numMessages <= 8){
                                                            var confmessage = '<div class="wpsc_thread_log" style="width: 100%;background-color:#D6EAF8 !important;color:#000000 !important;border-color:#C3C3C3 !important;">' + response + '</small></i></div>';
                                                        }else{
                                                            var confmessage = '<div class="wpsc_thread_log" style="display: none;width: 100%;background-color:#D6EAF8 !important;color:#000000 !important;border-color:#C3C3C3 !important;">' + response + '</small></i></div>';
                                                        }       
                                                            
                                                        jQuery('.confirmation').append(confmessage);
                                                        jQuery('.confirmation').css('display', 'inline-block');  

                                                    }else{
                                                        
                                                        /* 9/24/20 - Only show eight messages on the client by counting visible wpsc_thread_log */
                                                        if(numMessages <= 8){
                                                            var response = "Not Updated. There was problem updating the database. Please try again.";
                                                            var confmessage = '<div class="wpsc_thread_log" style="width: 100%;background-color:#D6EAF8 !important;color:#000000 !important;border-color:#C3C3C3 !important;">' + response + '</small></i></div>';
                                                        }else{
                                                            var response = "Not Updated. There was problem updating the database. Please try again.";
                                                            var confmessage = '<div class="wpsc_thread_log" style="display: none;width: 100%;background-color:#D6EAF8 !important;color:#000000 !important;border-color:#C3C3C3 !important;">' + response + '</small></i></div>';
                                                        } 
                                                        
                                                        jQuery('.confirmation').append(confmessage);
                                                        jQuery('.confirmation').css('display', 'inline-block'); 
                                                     }
                                        });
                        }
                    }else{
                        
                        /* 9/24/20 - Only show eight messages on the client by counting visible wpsc_thread_log */
                        var numMessages = jQuery('.wpsc_thread_log').length;
                        
                        if(numMessages <= 8){
                            
                            var response = "The BoxID is missing or there are no Location Scan(s) to submit.";
                            var confmessage = '<div class="wpsc_thread_log" id="missing_boxid" style="width: 100%;background-color:#D6EAF8 !important;color:#000000 !important;border-color:#C3C3C3 !important;">Please try again. ' + response + '</small></i></div>';
                        }else{
                            
                            var response = "The BoxID is missing or there are no Location Scan(s) to submit.";
                            var confmessage = '<div class="display: none;wpsc_thread_log" id="missing_boxid" style="width: 100%;background-color:#D6EAF8 !important;color:#000000 !important;border-color:#C3C3C3 !important;">Please try again. ' + response + '</small></i></div>';
                        }
                        
                        if(counter<=0){
                            jQuery('.confirmation').append(confmessage);
                            jQuery('.confirmation').css('display', 'inline-block');  
                            counter++;
                        }

                    }
                    
                    return false;
            });

            /*    This code will determine when a code has been either entered manually or
                entered using a scanner.
                It assumes that a code has finished being entered when one of the following
                events occurs:
                    • The enter key (keycode 13) is input
                    • The input has a minumum length of text and loses focus
                    • Input stops after being entered very fast 
            */
            
            /* handle a key value being entered by either keyboard or scanner */
            jQuery('.input').keypress(function (e) {
                /* restart the timer */
                if (timing) {
                    clearTimeout(timing);
                }
                
                /* Enter key was entered */
                if (e.which == 13) {
                    
                    /* don't submit the form */
                    e.preventDefault();
                    
                    /* has the user finished entering manually? */
                    if (jQuery(this).val().length >= minChars){
                        userFinishedEntering = true; // incase the user pressed the enter key
                        inputComplete(this);
                    }
                }else {
                    /* some other key value was entered */
                    
                    // could be the last character
                    inputStop = performance.now();
                    lastKey = e.which;
                    
                    /* don't assume it's finished just yet */
                    userFinishedEntering = false;
                    
                    /* is this the first character? */
                    if (!inputStart) {
                        firstKey = e.which;
                        inputStart = inputStop;
                        
                        /* watch for a loss of focus */
                        jQuery('body').on('blur', this, inputBlur(jQuery(this)));
                    }
                    
                    /* start the timer again */ 
                    timing = setTimeout(inputTimeoutHandler, 500);
                }
            });
            
            jQuery('#resetscanbtn').on('click', function(e) {
                e.preventDefault();
                
                jQuery(this).next().find('#submitbtn').focus();
                jQuery('#submitbtn').css('border', '');
                jQuery('#submitbtn').css('box-shadow', '');
                jQuery('#scan-input').val(''); 
                scanid_values.splice(0, scanid_values.length);
            });
                  
           jQuery('#resetboxidbtn').on('click', function(e) {
                e.preventDefault(); 

                jQuery(this).next().find('#submitbtn').focus();
                jQuery('#submitbtn').css('border', '');
                jQuery('#submitbtn').css('box-shadow', '');
                jQuery('textarea#boxid-textarea').val('');
                boxid_values.splice(0, boxid_values.length);

            });
            
            jQuery('textarea#boxid-textarea').keypress(function(event){
                
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if(keycode == '13'){
                    
                    jQuery('textarea#boxid-textarea').val(jQuery('textarea#boxid-textarea').val().trim() + '\n');
                }
            });
            
            function removeusingSet(arr) { 
                let outputArray = Array.from(new Set(arr)) 
                return outputArray 
            } 
          
    });      
    
            var inputStart, inputStop, firstKey, lastKey, timing, userFinishedEntering;
            var minChars = 3;
            
            /* Assume that a loss of focus means the value has finished being entered */
            function inputBlur(id){
                clearTimeout(timing);
                if (id.val.length >= minChars){
                    userFinishedEntering = true;
                    inputComplete(jQuery(id));
                }
            }
              
            /* Assume that it is from the scanner if it was entered really fast */
            function isScannerInput(id) {
                return (((inputStop - inputStart) / id.val.length) < 15);
            }
            
            /* Determine if the user is just typing slowly */
            function isUserFinishedEntering(id){
                return !isScannerInput(id) && userFinishedEntering;
            }
            
            function inputTimeoutHandler(id){
                /* stop listening for a timer event */
                clearTimeout(timing);
                /* if the value is being entered manually and hasn't finished being entered */
                if (!isUserFinishedEntering(jQuery(id)) || id.val.length < 3) {
                    /* keep waiting for input */
                    return;
                }
                else{
                    reportValues(jQuery(id));
                }
            }
            
            /* here we decide what to do now that we know a value has been completely entered */
            function inputComplete(id){
                /* stop listening for the input to lose focus */
                jQuery('body').off('blur', jQuery(id) , inputBlur(jQuery(id)));
                /* report the results */
                reportValues(jQuery(id));
            }
            
            function reportValues(id) {
                
                if (!inputStart) {

                    jQuery(id).focus().select();
                } else {

                    jQuery(id).focus().select();
                    inputStart = null;
                }
            }


            /* 9/16/2020 - Added Load More functionality below */
            if (jQuery(".wpsc_thread_log")[0]){
            jQuery(".logtitle").addClass('display');
            }
            
            if (jQuery(".wpsc_thread_log")[7]){
            jQuery(".load").addClass('display');
            }
            
            jQuery(function () {
                
                /* JM - 9/24/20 - Show eight log results upon page entry */
                jQuery(".wpsc_thread_log").slice(0, 8).addClass('display');
                
                jQuery("#loadMore").on('click', function (e) {
                    e.preventDefault();
                    
                    /* JM - 9/24/20 - Remove the Display: hidden style from the wpsc_thread_log */
                    jQuery(".wpsc_thread_log").css({"display": "inline-block"});
                    
                    jQuery(".wpsc_thread_log:hidden").slice(0, 8).addClass('display');
                    if (jQuery(".wpsc_thread_log:hidden").length == 0) {
                        jQuery("#load").fadeOut('slow');
                    }
                    jQuery('html,body').animate({
                        scrollTop: jQuery(this).offset().top
                    }, 1500);
                });
            });
            
            jQuery(window).scroll(function () {
                if (jQuery(this).scrollTop() > 50) {
                    jQuery('.totop a').fadeIn();
                } else {
                    jQuery('.totop a').fadeOut();
                }
            });

</script>
<style>
	#scan-input {
	    padding: 0 30px !important;
	}
	
	/* 9/15/2020 - Adding See more button functionality
	
	                  *****BUG*****
	   9/17/2020 - Discovered this is hiding message updates on the dashboard 
	   */
	.wpsc_thread_log {
	    
        /*display:none;*/
        width: 100%;
        background-color: #fff;
        padding: 0px;
    }
    .logtitle {
        display:none;
        padding-left: 0px !important;
        margin-bottom: 10px;
        overflow: hidden;
    }
    .load {
        display:none;
        padding-left: 0px !important;
        text-align: center;
        overflow: hidden;
    }
    .display {
    	display: inline-block;
    }
    .totop {
        position: fixed;
        bottom: 10px;
        right: 20px;
    }
    .totop a {
        display: none;
    }
    
    #loadMore {
        float: left;
        padding: 5px;
        margin-bottom: 20px; 
        background-color: #33739E;
        color: #fff;
        border-width: 0 1px 1px 0;
        border-style: solid;
        border-color: #fff;
        box-shadow: 0 1px 1px #ccc;
        transition: all 600ms ease-in-out;
        -webkit-transition: all 600ms ease-in-out;
        -moz-transition: all 600ms ease-in-out;
        -o-transition: all 600ms ease-in-out;
    }
    #loadMore:hover {
        background-color: #fff;
        color: #33739E;
    }
</style>
<div class="bootstrap-iso">
        
    <div>
        <H3>Barcode Scanning</H3>
    </div>

    <div id="wpsc_tickets_container" class="row" style="border-color:#1C5D8A !important;"><div class="row wpsc_tl_action_bar" style="background-color:#1C5D8A !important;">
        <div class="row wpsc_tl_action_bar" style="background-color:#1C5D8A !important;">
            <div class="col-sm-12">            
                <button class="btn btn-sm pull-right" type="button" id="wpsc_sign_out" onclick="window.location.href='http://086.info/wordpress3/wp-login.php?action=logout&amp;redirect_to=http%3A%2F%2F086.info%2Fwordpress3%2Fsupport-ticket%2F&amp;_wpnonce=fe1da2483c'" style=" background-color:#FF5733 !important;color:#FFFFFF !important;">
                    <i class="fas fa-sign-out-alt">
                    </i>    
                        Log Out
                </button>
            </div>  
        </div>
    </div>
       
<?php

if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent')){
         /* echo "This is a message for managers of PATT.";
         
            echo '<label for="boxid-textarea">'; 
            echo '</label>';
        */
         
echo ' <div class="row" style="background-color:#FFFFFF !important;color:#000000 !important;"> ';
	echo '<form id="scanform" action="#" method="post"> ';
		echo ' <div class="col-sm-4 col-md-3 wpsc_sidebar individual_ticket_widget"> ';
			echo ' <div class="row" id="wpsc_status_widget" style="background-color:#FFFFFF !important;color:#000000 !important;border-color:#C3C3C3 !important;"> ';
				echo ' <h4 class="widget_header"><i class="fa fa-archive"></i> Assignment</h4>';
				echo ' <hr class="widget_divider">';
				echo ' <div class="wpsp_sidebar_labels">Enter one or more Box IDs:<br>';
					echo '<div class="column" id="container-boxidinfo-border" style="background-color:#FFFFFF !important;color:#2C3E50 !important;border-color:#C3C3C3 !important;" >';
						echo '<div class="justified" style="text-align:center">';
							echo '<div class="dvboxid-layer" style="width: 100%;">';
								echo '<div tabindex="-1">';
									echo '<br/>';
									echo '<textarea id="boxid-textarea" class="input" name="boxid-textarea" pattern="(\d\d\d\d\d\d\d-\d{1,})" title="The Box ID must consist of {<7 numbers>-<any number of digits>}." rows="15" cols="15">';
									echo '</textarea>';  
								echo '</div>';
								echo '<br/>';
								echo '<input id="resetboxidbtn" name="resetboxidbtn" type="button" class="btn" value="Reset" />';
								echo '<input id="next-to-scan-btn" name="next-to-scan-btn" class="btn clnext" type="button" value="Next" />';
							echo '</div>';
						echo '</div>';
					echo '</div>';

					echo '<br><br>';
					
				echo '</div>';
			echo ' </div>'; 
		echo ' </div>';

		echo ' <div class="col-sm-8 col-md-9 wpsc_it_body" style="padding: 12px" > ';
			echo '<div class="row" id="scan_input_row">';
				echo '<div id="container-scaninfo-border" style="background-color:#FFFFFF !important;color:#2C3E50 !important;border-color:#C3C3C3 !important;" >';
					echo '<div class="justified" style="text-align:center; height: 37px;">';
						echo '<div class="dvscanner-layer" style="width:100%" tabindex="1">';
							echo '<div class="scaninput-bar">';
							    echo '<div class="header-left">';
								echo '</div>';
								    echo '
                                        <div>
                                            <div class="col-xs-10">
                                                <input type="text" id="scan-input" class="form-control" value="" name="scan-input" autocomplete="off" placeholder="Location...">
                                                <span><i class="fa fa-map-marker wpsc_search_btn wpsc_search_btn_sarch" aria-hidden="true"></i></span>
                                            </div>
                                            <div class="col-1">
                                                <div class="input-group">
                                                    <input class="btn" id="resetscanbtn" name="resetscanbtn" type="button" value="Reset" />
                                                    <input class="btn" action="#" id="submitbtn" name="submitbtn" type="submit" value="Submit" />
                                                </div>
                                            </div>
                                        </div>';

    							echo '</div>';
    							echo '<br/>';
    							echo '<div style="text-align:center">';
							echo '</div>';
						echo '</div>';
					echo '</div>';
    			echo '</div>';
                echo '<br/>';
    			echo '<div id="submit-scan-border" style="background-color:#FFFFFF !important;color:#2C3E50 !important;border-color:#C3C3C3 !important;" >';
    				echo '<div class="justified" style="display:flex;text-align:center">';
    					echo '<div class="dvsubmission_layer" style="display: inline-block;width:100%">';
    
    						echo '<div style="text-align:center" tabindex="2">'; 
    							echo '<h4>';
    							echo 'Submission Status: ';
    							echo '</h4>';
    							
    						echo '</div>'; 	
    						    echo '<br/>';
    					/*	echo '<div id="dvconfirmation" class="confirmation">'; */
    					/* 9/15/2020 - Adding See more button functionality  */
    						echo '<div id="dvconfirmation col-md-8 col-md-offset-2 load" class="confirmation wpsc_thread_log">';
    						
    						
                            echo '</div>';
                            echo '<a href="#" id="loadMore"><i class="fas fa-sync"></i> Load More</a>';
    					echo '</div>';
    				echo '</div>';
	            echo '</div>';
	        echo '</div>'; 
	        // JM - 9/23/20 - Added closing Div tag
	    echo '</div>';   
	echo '</form>';
echo '</div>';
}?>
</div>








