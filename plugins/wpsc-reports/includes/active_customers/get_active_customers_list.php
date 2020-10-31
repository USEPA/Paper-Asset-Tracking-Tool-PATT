<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpdb, $wpscfunc;

$filter = $wpscfunc->get_current_filter();
$post_per_page  = 10;

$sql   = "select SQL_CALC_FOUND_ROWS customer_email, COUNT(customer_email) as cnt from {$wpdb->prefix}wpsc_ticket";
$where = " WHERE active=1";
if($filter['s']) {
	$search = trim($filter['s']);
	$term 	= '%'.$search.'%';
  	$where.= " AND ( " 
            ."customer_name  LIKE '$term' OR "
            ."customer_email  LIKE '$term' "
        .")" ;

}


$group  = " group by customer_email order by COUNT(customer_email) desc ";

$sql .= $where.$group;

$offset = ($filter['page']-1)*$post_per_page;

$sql.= " LIMIT ".$post_per_page." OFFSET ".$offset;

$results = $wpdb->get_results($sql);

$count  = $wpdb->get_var("SELECT FOUND_ROWS()");

$current_page = sanitize_text_field($filter['page']);

if($count <= $current_page*$post_per_page){
	$no_of_customers = $count;
}else {
	$no_of_customers = $current_page*$post_per_page;
}
$total_pages  = ceil($count/$post_per_page);
if($current_page < $total_pages)
	$current_page+1;
?>
<div id='active_customers_list'>
	<div class="col-sm-6 col-sm-offset-6" style="margin-top:-20px;text-align:right;font-size:12px;padding-right:0;">
		<?php 
		if( $count > $post_per_page ){?>
			<strong><?php echo ($current_page*$post_per_page-$post_per_page)+1;?></strong>-<strong><?php echo htmlentities($no_of_customers);?></strong> of <strong><?php echo htmlentities($count);?></strong> <?php ($count >1)?_e('Customers','wpsc-rp'):_e('Customers','wpsc-rp');?>
		<?php	
		} else {
		 ?>
		 <strong><?php echo htmlentities($count);?> <?php ($count >1 ) ? _e('Customers','wpsc-rp'): _e('Customers','wpsc-rp');?> </strong>
		 <?php 
	 }
	 ?>
	</div>
	<table id="active_customers_table" class="table table-bordered">
		<tr>
			<th><?php _e('Rank','wpsc-rp')?></th>
			<th><?php _e('Name','wpsc-rp')?></th>
			<th><?php _e('Email','wpsc-rp')?></th>
			<th><?php _e('No of Tickets','wpsc-rp')?></th>
		</tr>
		
		<?php 
		$record = 1;
		foreach ($results as $result) {
			$rank = (($current_page-1) * 10 ) + $record;
			$user = get_user_by( 'email', $result->customer_email);
			if ( ! empty( $user ) ) {
				$name = $user->display_name;
			}else{
				$name = $wpdb->get_var("SELECT customer_name FROM {$wpdb->prefix}wpsc_ticket WHERE customer_email='".$result->customer_email."' ORDER BY id DESC LIMIT 1");
			}
			?>
			<tr>
				<td><?php echo $rank; ?></td>
				<td><?php echo $name; ?></td>
				<td><?php echo $result->customer_email; ?></td>
				<td><?php echo $result->cnt; ?></td>
			</tr>
			<?php
			$record++;
		} 
		?>
	</table>
	<?php
	if($results) : 
	?>
	<div class="row" style="margin-bottom:20px;">
		<div class="col-md-4 col-md-offset-4 wpsc_ticket_list_nxt_pre_page" style="text-align: center;">
				<button class="btn btn-default btn-sm" <?php echo $filter['page']==1? 'disabled' : ''?> onclick="wpsc_active_customers_prev_page();"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i></button>
				<strong><?php echo $current_page ?></strong> <?php _e('of','wpsc-rp')?> <strong><?php echo $total_pages?></strong> <?php _e('Pages','wpsc-rp') ?>
				<button class="btn btn-default btn-sm" <?php echo $filter['page']==$total_pages? 'disabled' : ''?> onclick="wpsc_active_customers_next_page();"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i></button>
		</div>
	</div>	
	<?php endif; ?>
</div>
