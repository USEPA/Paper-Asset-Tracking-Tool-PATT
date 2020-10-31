<?php
if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly
?>
<?php
global $wpsupportplus, $wpdb;

$s = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';

$sql = "select "
                . "u.ID as id, "
                . "u.display_name as name "
                . "FROM {$wpdb->prefix}users u ";
                
/**
 * Where Part
 */
$where = "WHERE 1=1 ";

if ($s) {

    $where .= 'AND ( ';
    $keywords = explode(' ', $s);
    $search = array();
    foreach ($keywords as $keyword) {
        $search[] = "u.user_login LIKE '%" . $keyword . "%'";
        $search[] = "u.display_name LIKE '%" . $keyword . "%'";
        $search[] = "u.user_email LIKE '%" . $keyword . "%'";
    }
    $where .= implode(' OR ', $search);
    $where .= ' ) ';
}

/**
 * Order By Part
 */
$orderby = 'ORDER BY u.display_name asc ';

/**
 * Limit part
 */
$limit = 'LIMIT 5 OFFSET 0';

/**
 * Combining Query
 */
$sql = $sql . $where . $orderby . $limit;

/**
 * Getting results.
 */
$users = $wpdb->get_results($sql);

$active = TRUE;
foreach ($users as $user){
    $class = $active ? 'wpsp-autocomplete-result-item wpsp-autocomplete-result-item-selected' : 'wpsp-autocomplete-result-item';
    $active =FALSE;
    ?>
    <div id="<?php echo $user->id?>" onclick="wpsp_aut_choose_item(this)" onmouseover="wpsp_aut_item_select(this);" class="<?php echo $class?>"><?php echo $user->name?></div>
    <?php
}
