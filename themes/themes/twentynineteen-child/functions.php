<?php
/**
 * Functions - Twentynineteen Child theme custom functions
 */

/**
 * Remove double load of bootstrap js on folder files details page and memphisdocuments plugin.
 */
 
add_action('wp_print_scripts','dequeue_bootstrap');

function dequeue_bootstrap() {
global $pagenow;
 if ( (($pagenow == 'admin.php') && ($_GET['page'] == 'filedetails')) || (($pagenow == 'admin.php') && ($_GET['page'] == 'memphis-documents.php')) ) {
   wp_dequeue_script( 'bootstrap-cdn-js' );
 }
}

/**
 * Loads the parent stylesheet.
 */

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

/**
 * Change Admin Media Menu Label
 */
function patt_admin_media_menu_rename() {
     global $menu; // Global to get menu array
     global $submenu; // Global to get submenu array
     $menu[10][0] = 'Documents'; // Change name of Media to Documents
     $submenu['upload.php'][5][0] = 'All Document Items'; // Change name of Library to All Document Items
}
add_action( 'admin_menu', 'patt_admin_media_menu_rename' );

/****************************************************************************************************************/










/**
 * Remove Wordpress Logo and Admin submenu items
 */
function admin_bar_remove_items() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'wp-logo' );
    $wp_admin_bar->remove_menu('view-site');
    $wp_admin_bar->remove_menu('dashboard');
    $wp_admin_bar->remove_menu('themes');
    $wp_admin_bar->remove_menu('widgets');
    $wp_admin_bar->remove_menu('menus');
    $wp_admin_bar->remove_menu('customize');
    $wp_admin_bar->remove_menu('new-content');
    $wp_admin_bar->remove_menu('updates');
    $wp_admin_bar->remove_menu('comments');
}
add_action( 'wp_before_admin_bar_render', 'admin_bar_remove_items', 0 );

/**
 * Add ERMD Footer.
 */
  
function remove_footer_admin () 
{
    echo '<span id="footer-thankyou">For technical support please contact ERMD: <a href="mailto:ecms@epa.gov" style="Color:#0B4656">ecms@epa.gov</a></span>';
}
 
add_filter('admin_footer_text', 'remove_footer_admin');



/**
 * Change default login page, remove dashboard and vist homepage link
 */
 
add_action( 'admin_menu', 'remove_dashboard', 99 );
function remove_dashboard(){
   remove_menu_page( 'index.php' ); //dashboard
}

function admin_default_page() {
   return home_url() . "/wp-admin/admin.php?page=wpsc-tickets"; //redirect URL
}
add_filter('login_redirect', 'admin_default_page'); 

add_action( 'admin_bar_menu', 'customize_my_wp_admin_bar', 80 );
function customize_my_wp_admin_bar( $wp_admin_bar ) {

    $site_node = $wp_admin_bar->get_node('site-name');

    //Change link
    $site_node->href = home_url() . "/wp-admin/admin.php?page=wpsc-tickets";

    //Update Node.
    $wp_admin_bar->add_node($site_node);

}

/**
 * Redirect access to WP dashboard to request dashboard
 */
 
add_action('load-index.php', function(){
    if(get_current_screen()->base == 'dashboard')
        wp_redirect(home_url() ."/wp-admin/admin.php?page=wpsc-tickets");
});

/**
 * Re-order navigational items
 */
 
function wpse_custom_menu_order( $menu_ord ) {
    if ( !$menu_ord ) return true;

    return array(
        'rwpm_inbox', // Messages
        'wpsc-tickets', // PATT Dashboard
        'edit.php?post_type=wpsc_canned_reply', // PATT Canned reply
        'upload.php', // Media
        'memphis-documents.php', // Attachments
        'users.php', // Users
        'separator1', // First separator
        'edit.php', // Posts
        'edit.php?post_type=page', // Pages
        'edit-comments.php', // Comments
        'link-manager.php', // Links
        'separator2', // Second separator
        'themes.php', // Appearance
        'plugins.php', // Plugins
        'tools.php', // Tools
        'options-general.php', // Settings
        'activity_log_page', // Activity Log
        'separator-last', // Last separator
    );
}

/**
 * Security add-on fixes to address vulnerabilities
 */

add_action('init', 'myStartSession', 1);
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');

function myStartSession() {
    if(!session_id()) {
        session_start();
    }
}

function myEndSession() {
    session_destroy ();
}

function IsResourceLocal($url){
    if( empty( $url ) ){ return false; }
    $urlParsed = parse_url( $url );
    $host = $urlParsed['host'];
    if( empty( $host ) ){ 
    /* maybe we have a relative link like: /wp-content/uploads/image.jpg */
    /* add absolute path to begin and check if file exists */
    $doc_root = $_SERVER['DOCUMENT_ROOT'];
    $maybefile = $doc_root.$url;
    /* Check if file exists */
    $fileexists = file_exists ( $maybefile );
    if( $fileexists ){
        /* maybe you want to convert to full url? */
        return true;        
        }
     }
    /* strip www. if exists */
    $host = str_replace('www.','',$host);
    $thishost = $_SERVER['HTTP_HOST'];
    /* strip www. if exists */
    $thishost = str_replace('www.','',$thishost);
    if( $host == $thishost ){
        return true;
        }
    return false;
}

function possibly_redirect(){
 global $pagenow;
 if( 'wp-login.php' == $pagenow && IsResourceLocal($_GET['redirect_to'])) {
  wp_redirect(home_url() ."/wp-admin/admin.php?page=wpsc-tickets");
  exit();
 }
}

//add_action('init','possibly_redirect');


function glue_login_redirect() {
    global $redirect_to;
    if (!isset($_GET['redirect_to']) || !IsResourceLocal($_GET['redirect_to'])) {
        $redirect_to = home_url() ."/wp-admin/admin.php?page=wpsc-tickets";
    }
    else{
        $redirect_to = $_GET['redirect_to'];
    }
}

//add_action( 'login_form' , 'glue_login_redirect' );

/**
 * Removes the "Trash" link on the individual post's "actions" row on the posts
 * edit page.
 */
add_filter( 'post_row_actions', 'remove_row_actions_post', 10, 2 );
function remove_row_actions_post( $actions, $post ) {
    if( $post->post_type === 'wpsc_canned_reply' ) {
        unset( $actions['clone'] );
        unset( $actions['trash'] );
    }
    return $actions;
}

    global $pagenow;
    
    if ( 'edit.php' == $pagenow && $_GET['post_type'] == 'wpsc_canned_reply') {
        add_action( 'admin_head', 'remove_bulk_delete' );
        function remove_bulk_delete() {
            $style = '';
            $style .= '<style type="text/css">';
            $style .= '#delete-action, .bulkactions option[value=trash] {display: none;}';
            $style .= '</style>';

            echo $style;
        }
        }

add_action( 'admin_head', function () {
    $current_screen = get_current_screen();

    // Hides the "Move to Trash" link on the post edit page.
    if ( 'post' === $current_screen->base &&
    'wpsc_canned_reply' === $current_screen->post_type ) :
    ?>
        <style>#delete-action { display: none; }</style>
    <?php
    endif;
} );
        
add_action('wp_trash_post', 'restrict_post_deletion');
function restrict_post_deletion($post_id) {
    if( get_post_type($post_id) === 'wpsc_canned_reply' ) {
      wp_die('The system message you were trying to delete is protected.');
    }
}

add_filter( 'custom_menu_order', 'wpse_custom_menu_order', 10, 1 );
add_filter( 'menu_order', 'wpse_custom_menu_order', 10, 1 );
if(function_exists('add_db_table_editor')){
add_db_table_editor('title=Record Schedule Editor&table=wpqa_epa_record_schedule');
add_db_table_editor('title=File Folder Details&table=wpqa_wpsc_epa_folderdocinfo');
add_db_table_editor('title=Box editor&table=wpqa_wpsc_epa_boxinfo');
add_db_table_editor('title=Error Log&table=wpqa_epa_error_log');
add_db_table_editor('title=Shipping Table&table=wpqa_wpsc_epa_shipping_tracking');
}