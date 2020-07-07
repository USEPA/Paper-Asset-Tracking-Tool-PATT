
jQuery(document).ready(function(){
  
  jQuery('#wppatt_popup_background,.wppatt_popup_close').click(function(){
    wppatt_modal_close();
  });
  
  jQuery(document).keyup(function(e){
    if (e.keyCode == 27) { 
      wppatt_modal_close();
    }
  });
  
});

function wppatt_modal_open(title){
  jQuery('#wppatt_popup_title h3').text(title);
  jQuery('#wppatt_popup_body').html(wppatt_admin.loading_html);
  jQuery('.wppatt_popup_action').hide();
  jQuery('#wppatt_popup_container,#wppatt_popup_background').show();
}

function wppatt_modal_close(){
  jQuery('#wppatt_popup_container,#wppatt_popup_background').hide();
}

function wppatt_modal_close_thread(tinymce_toolbar){
  
  jQuery('#wppatt_popup_container,#wppatt_popup_background').hide();
  var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
  if(is_tinymce){
    tinymce.init({
      selector:'#wppatt_reply_box',
      body_id: 'wppatt_reply_box',
      menubar: false,
      statusbar: false,
      autoresize_min_height: 150,
      wp_autoresize_on: true,
      plugins: [
          'wpautoresize lists link image directionality'
      ],
      toolbar:  tinymce_toolbar.join() +' | wppatt_templates',
      branding: false,
      autoresize_bottom_margin: 20,
      browser_spellcheck : true,
      relative_urls : false,
      remove_script_host : false,
      convert_urls : true
    });
  }
}
