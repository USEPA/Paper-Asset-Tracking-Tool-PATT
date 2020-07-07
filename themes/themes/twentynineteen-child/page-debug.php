<?php
/**
 * Template Name: Debug File
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

get_header();
global $wpdb;
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			This is a test template in child theme.
			<br/>

<?php
	echo '<hr/>';

			echo "PATT BOX ID to PATT REQUEST ID<br/>";
		$patt_request_id = Patt_Custom_Func::convert_box_request_id('0000001-1');
		echo $patt_request_id;
		
		
		echo "Update reacall user data<br/>";

		$data = [
			'recall_id' => 19,			
			'user_id' => [4,5,6,2,7]
		];

		$date_updated = Patt_Custom_Func::update_recall_user_by_id($data);
		echo '<pre>';
		print_r($date_updated);
		echo '</pre>';
		echo '<hr/>';
		
		
		echo "Get Box Details By ID<br/>";
		//$box_array = Patt_Custom_Func::get_box_file_details_by_id('0000238-1');
		//$box_array = Patt_Custom_Func::get_box_file_details_by_id('0000001-1');
		$box_array = Patt_Custom_Func::get_box_file_details_by_id('0000001-1-02-1');
		echo '<pre>';
		print_r($box_array);
		echo '</pre>';
		echo '<hr/>';
		
		echo "Insert recall data<br/>";

		$num = rand ( 10000 , 99999 ); // $ticket_id
		$str_length = 7;
		$recall_id = substr("000000{$num}", -$str_length);

		$data = [
			// 'recall_id' => "$recall_id",
			'box_id' => 7,
			'folderdoc_id' => 4,
			'program_office_id' => 2,
			'shipping_tracking_id' => 24,
			'record_schedule_id' => 1,
			'user_id' => 2, //[2,5,67,5]
			'recall_status_id' => 5,
			'expiration_date' => '2020-06-02 00:00:00',
			'request_date' => '2020-06-02 00:00:00',
			'request_receipt_date' => '2020-06-02 00:00:00',
			'return_date' => '2020-06-02 00:00:00',
			'updated_date' => '2020-06-02 00:00:00',
			'comments' => 'dfbdfbd',
		];

		//$recall_insert_id = Patt_Custom_Func::insert_recall_data($data);
		echo '<pre>';
		print_r($recall_insert_id);
		echo '</pre>';
		echo '<hr/>';


		echo "Get all recall data<br/>";
		/**
		 * Passing single recall ID, multiple IDs, status id, programe office id or digitization center
		 */
		
		//$where = [ 'recall_id' => '0000105' ]; //folder
		//$where = [ 'recall_id' => '0000096' ]; //box - working
		$where = [ 'recall_id' => '0000110' ]; //box
/*
		$where = [
			// 'id' => 19, 
			// 'id' => [19, 20], 
			// 'recall_id' => 19, 
			'recall_id' => ['0000001', '0000002']
			// 'recall_status_id' => 5,
			// 'program_office_id' => 2,
			// 'digitization_center' => 'East'  ,
			'filter' => [
				'records_per_page' => 3,
				'paged' => 2,
				'orderby' => 'recall_id',
				'order' => 'ASC'
			]
		]; 
*/
		$recall_array = Patt_Custom_Func::get_recall_data($where);
		echo '<pre>';
		print_r($recall_array);
		echo '</pre>';
		echo '<hr/>';


		echo "Get shipping data by recall ids<br/>";
		/**
		 * Passing single recall ID, multiple IDs, status id, programe office id or digitization center
		 */
		$where = [
			'recallrequest_id' => 1, 
			// 'recallrequest_id' => ['5', '6']
		]; 
		$shipping_data = Patt_Custom_Func::get_shipping_data_by_recall_id($where);
		echo '<pre>';
		print_r($shipping_data);
		echo '</pre>';
		echo '<hr/>';


		
		echo "Insert shipping data<br/>";
		$data = [
			'ticket_id' => 7,
			'recallrequest_id' => 4,
			'company_name' => 2,
			'tracking_number' => 24,
			'status' => 1
		];

		$shipping_insert_id = Patt_Custom_Func::add_shipping_data($data);
		echo '<pre>';
		print_r($shipping_insert_id);
		echo '</pre>';
		echo '<hr/>';		


		echo "Delete shipping tracking records by recall ID<br/>";

		$where = [
			'recallrequest_id' => 4,
			// 'recallrequest_id' => ['5', '6']
		];
		$shipping_data_array = Patt_Custom_Func::delete_shipping_data_by_recall_id( $where);
		echo '<pre>';
		print_r($shipping_data_array);
		echo '</pre>';
		echo '<hr/>';

				
		echo "Get shipping data by recall ids<br/>";
		/**
		 * Passing single recall ID, multiple IDs, status id, programe office id or digitization center
		 */
		$where = [
			// 'recallrequest_id' => 1, 
			'recallrequest_id' => ['5', '6']
		]; 
		$shipping_data = Patt_Custom_Func::get_shipping_data_by_recall_id($where);
		echo '<pre>';
		print_r($shipping_data);
		echo '</pre>';
		echo '<hr/>';

		echo "Update request date - recall ID<br/>";
		$update = [
			'request_date' => '2020-05-05 00:00:00'
		];
		$where = [
			'id' => 3
		];
		$recall_array = Patt_Custom_Func::update_recall_dates($update, $where);
		echo '<pre>';
		print_r($recall_array);
		echo '</pre>';
		echo '<hr/>';

		echo "Get return primary key by returnid<br/>";
		/**
		 * Get return primary key by returnid
		 */
		$where = [
			'return_id' => 'ytvybft567747467'
		]; 
		$return_data = Patt_Custom_Func::get_primary_id_by_retunid($where);
		echo '<pre>';
		print_r($return_data);
		echo '</pre>';
		echo '<hr/>';


		echo "Get ticket id by returnid or recall_id<br/>";
		/**
		 * Get ticket id by returnid or recall_id
		 */
		$where = [
			'return_id' => 34,
			// 'recallrequest_id' => 1
		]; 
		$ticket_id_by = Patt_Custom_Func::get_ticket_id_by($where);
		echo '<pre>';
		print_r($ticket_id_by);
		echo '</pre>';
		echo '<hr/>';

		echo "Update request date - recall ID<br/>";
		$update = [
			'request_date' => '2020-05-05 00:00:00'
		];
		$where = [
			'id' => 3
		];
		$recall_array = Patt_Custom_Func::update_recall_dates($update, $where);
		echo '<pre>';
		print_r($recall_array);
		echo '</pre>';
		echo '<hr/>';

		echo "Update received date - recall ID<br/>";
		$update = [
			'request_receipt_date' => '2020-05-05 00:00:00'
		];
		$where = [
			'id' => 3
		];
		$recall_array = Patt_Custom_Func::update_recall_dates($update, $where);
		echo '<pre>';
		print_r($recall_array);
		echo '</pre>';
		echo '<hr/>';


		echo "Update return date - recall ID<br/>";
		$update = [
			'return_date' => '2020-05-05 00:00:00'
		];
		$where = [
			'id' => 3
		];
		$recall_array = Patt_Custom_Func::update_recall_dates($update, $where);
		echo '<pre>';
		print_r($recall_array);
		echo '</pre>';
		echo '<hr/>';


		echo "Auto-assign Digitization Center<br/>";
		$box_array = Patt_Custom_Func::get_default_digitization_center(238);
		print_r($box_array);
		echo '<hr/>';
		
		echo "Obtain array of Box ID's <br/>";
		$box_array = Patt_Custom_Func::fetch_box_id(1);
		print_r($box_array);
		echo '<hr/>';
		
		echo "Obtain array of Program Offices<br/>";
		$po_array = Patt_Custom_Func::fetch_program_office_array();
		print_r($po_array);
		echo '<hr/>';
		
		
		echo "Convert PATT Request ID to DB ID<br/>";
		$GLOBALS['id'] = $_GET['id'];
		echo $request_id;
		echo Patt_Custom_Func::convert_request_id($GLOBALS['id']);
		echo '<hr/>';
		
		echo "Obtain array of Box Information for frontend/backend Request Details page <br/>";
		$box_details_array = Patt_Custom_Func::fetch_box_details(1);
		print_r($box_details_array);
		echo '<hr/>';

		echo "Function to obtain location value from database <br/>";
        $box_location = Patt_Custom_Func::fetch_location(1);
		print_r($box_location);
		echo '<hr/>';

		echo "Function to obtain program office from database <br/>";
        $box_program_office = Patt_Custom_Func::fetch_program_office(1);
		print_r($box_program_office);
		echo '<hr/>';

        echo 'Function to obtain shelf from database <br/>';
        $box_shelf = Patt_Custom_Func::fetch_shelf(1);
		print_r($box_shelf);
		echo '<hr/>';

        echo 'Function to obtain bay from database <br/>';
		$box_bay = Patt_Custom_Func::fetch_bay(1);
		print_r($box_bay);
		echo '<hr/>';

        echo 'Function to obtain create month and year from database <br/>';
		$box_date = Patt_Custom_Func::fetch_create_date(1);
		print_r($box_date);
		echo '<hr/>';

        echo 'Function to obtain box count <br/>';
		$box_count = Patt_Custom_Func::fetch_box_count(1);
		print_r($box_count);
		echo '<hr/>';

        echo 'Function to obtain request key <br/>';
		$request_key = Patt_Custom_Func::fetch_request_key(1);
		print_r($request_key);
		echo '<hr/>';

        echo 'Function to obtain request ID <br/>';
		$num = Patt_Custom_Func::fetch_request_id(1);
		print_r($num);
		echo '<hr/>';

        echo 'Function to array of Box IDs <br/>';
		$box_array = Patt_Custom_Func::fetch_box_id_a('dddgd,dg4541,4544rhh');
		print_r($box_array);
		echo '<hr/>';



?>

			<?php

			/* Start the Loop */
			// while ( have_posts() ) :
			// 	the_post();

			// 	get_template_part( 'template-parts/content/content', 'page' );

			// 	// If comments are open or we have at least one comment, load up the comment template.
			// 	if ( comments_open() || get_comments_number() ) {
			// 		comments_template();
			// 	}

			// endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
