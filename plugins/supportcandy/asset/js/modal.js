
jQuery(document).ready(function(){
  
  jQuery('#wpsc_popup_background,.wpsc_popup_close').click(function(){
    wpsc_modal_close();
  });
  
  jQuery(document).keyup(function(e){
    if (e.keyCode == 27) { 
      wpsc_modal_close();
    }
  });
  
});

function wpsc_modal_open(title){
  jQuery('#wpsc_popup_title h3').text(title);
  jQuery('#wpsc_popup_body').html(wpsc_admin.loading_html);
  jQuery('.wpsc_popup_action').hide();
  jQuery('#wpsc_popup_container,#wpsc_popup_background').show();
}

function wpsc_modal_close(){
  jQuery('#wpsc_popup_container,#wpsc_popup_background').hide();
}

function wpsc_modal_close_thread(tinymce_toolbar){
  
  jQuery('#wpsc_popup_container,#wpsc_popup_background').hide();
  var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
  if(is_tinymce){
    tinymce.init({
      selector:'#wpsc_reply_box',
      body_id: 'wpsc_reply_box',
      menubar: false,
      statusbar: false,
      autoresize_min_height: 150,
      wp_autoresize_on: true,
      plugins: [
          'wpautoresize lists link image directionality'
      ],
      toolbar:  tinymce_toolbar.join() +' | wpsc_templates',
      branding: false,
      autoresize_bottom_margin: 20,
      browser_spellcheck : true,
      relative_urls : false,
      remove_script_host : false,
      convert_urls : true
    });
  }
}
