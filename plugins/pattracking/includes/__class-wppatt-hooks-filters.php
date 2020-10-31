<?php
/**
 * Exit if accessed directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Patt_HooksFilters' ) ) {

	/**
	 * Class to manage the ticket request
	 */
	class Patt_HooksFilters {

		/**
		 * Get things started
		 *
		 * @access  public
		 * @since   1.0
		 */
		public function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'wpsc_print_ext_js_create_ticket' ) );

			// Print HTML In Request Form - Location: /home/acy3/public_html/wordpress3/wp-content/plugins/supportcandy/includes/admin/tickets/create_ticket/load_create_ticket.php.
			add_action( 'print_listing_form_block', array( $this, 'print_listing_form_block' ) );

			// Not required.
			// add_action( 'patt_custom_imports_tickets', array( $this, 'patt_custom_imports_tickets' ) );.

			// Not required.
			// add_action( 'patt_print_js_functions_create', array( $this, 'patt_print_js_functions_create' ) );.

			// Print Scripts - Location: /home/acy3/public_html/wordpress3/wp-content/plugins/supportcandy/includes/admin/tickets/tickets.php.
			// Not required.
			// add_action( 'admin_footer', array( $this, 'patt_print_js_tickets_page' ) );.

			// Location: /home/acy3/public_html/wordpress3/wp-content/plugins/supportcandy/includes/functions/create_ticket.php.
			add_action( 'patt_process_boxinfo_records', array( $this, 'patt_process_boxinfo_records' ) );

			// Move uploaded file.
			add_action( 'wp_ajax_move_excel_file', array( $this, 'move_excel_file' ) );
			add_action( 'wp_ajax_nopriv_move_excel_file', array( $this, 'move_excel_file' ) );

		}

		/**
		 * Assign a new folder for box list excel file
		 *
		 * @param Array $param Upload directory information as array.
		 */
		public function wpai_set_custom_upload_folder( $param ) {
			$mydir         = '/box-list';
			$param['path'] = $param['basedir'] . $mydir;
			$param['url']  = $param['baseurl'] . $mydir;
			return $param;
		}

		/**
		 * After file upload, move it from the temp to custom directory
		 */
		public function move_excel_file() {

			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			$uploadedfile = ! empty( $_FILES['file'] ) ? $_FILES['file'] : array();
			$file_name = ! empty( $_FILES['file']['name'] ) ? basename( $_FILES['file']['name'] ) : '';
			$time = gmdate( 'd-m-Y' ) . '-' . time();
			$fty = $time . '-' . $file_name;
			$upload_overrides = array(
				'test_form' => false,
				'unique_filename_callback' => $fty,
			);

			add_filter( 'upload_dir', array( $this, 'wpai_set_custom_upload_folder' ) );
			// $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

			$attachment_id = media_handle_upload( 'file', 0 );
			if ( ! is_wp_error( $attachment_id ) ) {
				update_post_meta( $attachment_id, 'folder', 'box-list' );
				array_push( $attach_ids, $attachment_id );

				$request_page = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : '';
				echo 'File Upload Successfully -> ' . $request_page;
			} else {
				echo $movefile['error'];
			}

			remove_filter( 'upload_dir', array( $this, 'wpai_set_custom_upload_folder' ) );

			die();
		}

		/**
		 * Add custom JS.
		 */
		public function wpsc_print_ext_js_create_ticket() {

			wp_enqueue_style( 'datatable-style', WPSC_PLUGIN_URL . 'asset/lib/DataTables/datatables.min.css', array(), time(), false );
			wp_enqueue_script( 'datatable-js', WPSC_PLUGIN_URL . 'asset/lib/DataTables/datatables.min.js', array(), time(), true );

			wp_enqueue_style( 'tagsinput-style', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.3/jquery.tagsinput.css', array(), time(), false );
			wp_enqueue_script( 'tagsinput-js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.3/jquery.tagsinput.js', array(), time(), true );

			wp_enqueue_style( 'datatable-checkbox-style', '//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css', array(), time(), false );
			wp_enqueue_script( 'datatable-checkbox-js', '//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js', array(), time(), true );

			wp_enqueue_style( 'dropzone-style', WPPATT_PLUGIN_URL . 'asset/css/dropzone.min.css', array(), time(), false );
			wp_enqueue_script( 'dropzone-js', WPPATT_PLUGIN_URL . 'asset/js/dropzone.min.js', array(), time(), true );

			wp_enqueue_script( 'xlsx-full-js', 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.14.5/xlsx.full.min.js', array(), time(), true );

			wp_enqueue_script( 'save-ticket-boxlist-js', WPPATT_PLUGIN_URL . 'asset/js/ticket_box_list_save.js', array(), time(), true );

			wp_localize_script(
				'save-ticket-boxlist-js',
				'attachment_info',
				array(
					'max_filesize' => get_option( 'wpsc_attachment_max_filesize' ),
					'close_image' => WPSC_PLUGIN_URL . 'asset/images/close.png',
				)
			);
		}

		/**
		 * Save Boxlist Records
		 *
		 * @param Array $data box list data as array.
		 */
		public function patt_process_boxinfo_records( $data ) {
			global $wpdb, $wpscfunction;

			$ticket_id = $data['ticket_id'];
			$str_length = 7;
			$request_id = substr( "000000{$ticket_id}", -$str_length );

			$wpdb->update( $wpdb->prefix . 'wpsc_ticket', array( 'request_id' => $request_id ), array( 'id' => $ticket_id ) );

			// New BoxInfo Code.
			$boxinfodata = stripslashes( $data['box_info'] );
			$boxinfo_array = json_decode( $boxinfodata, true );

			$box = '';
			$row_counter = 1;

			foreach ( $boxinfo_array as $boxinfo ) {
				$box_id = $request_id . '-' . $boxinfo['Box'];
				if ( $box !== $boxinfo['Box'] ) {
					$record_schedule_number_break = explode( ':', $boxinfo['Record Schedule & Item Number'] );
					$record_schedule_number = trim( str_replace( array( '[', ']' ), '', $record_schedule_number_break[0] ) );

					$program_office_break = explode( ':', $boxinfo['Program Office'] );
					$program_office_id = trim( $program_office_break[0] );

					$epa_contact = $boxinfo['EPA Contact'];

					$lan_id = Patt_Custom_Func::lan_id_check( $epa_contact, $request_id );
					$lan_json = Patt_Custom_Func::lan_id_to_json( $lan_id );

					$boxarray = array(
						'box_id' => $box_id,
						'ticket_id' => $ticket_id,
						// 'location' => $boxinfo["Location"],
						// 'bay' => '1',
						'storage_location_id' => $this->get_new_storage_location_row_id(),
						'location_status_id' => 1,
						'lan_id' => $lan_id,
						'lan_id_details' => $lan_json,
						'program_office_id' => $this->get_programe_office_id( $program_office_id ),
						'record_schedule_id' => $this->get_record_schedule_id( $record_schedule_number ),
						'date_created' => gmdate( 'Y-m-d H:i:s' ),
						'date_updated' => gmdate( 'Y-m-d H:i:s' ),
					);

					$boxinfo_id = $this->create_new_boxinfo( $boxarray );
					$this->add_boxinfo_meta( $boxinfo_id, 'assigned_agent', '0' );
					$this->add_boxinfo_meta( $boxinfo_id, 'prev_assigned_agent', '0' );
					$box = $boxinfo['Box'];

					if ( 0 === $boxinfo_id ) {
						// if, Box not inserted, delete the ticket.
						$delete_ticket = apply_filters( 'request_ticket_delete', $ticket_id );

						ob_start();
						?>
						<div class="col-sm-12 ticket-error-msg">
							<?php esc_html_e( 'Error entering box information. Ticket not generated.', 'pattracking' ); ?>
						</div>
						<?php
						$ticket_error_message = ob_get_clean();

						$response = array(
							'redirct_url'    => '',
							'thank_you_page' => $ticket_error_message,
						);

						echo json_encode( $response );
						die();
					}
				}

				$index_level = strtolower( $boxinfo['Index Level'] ) == 'file' ? 2 : 1;
				$essential_record = 'Yes' == $boxinfo['Essential Record'] ? '00' : '01';
				$docinfo_id = $request_id . '-' . $boxinfo['Box'] . '-' . str_pad( $index_level, 2, '0', STR_PAD_LEFT ) . '-' . $row_counter;
				$folderdocarray = array(
					'folderdocinfo_id' => $docinfo_id,
					'title' => $boxinfo['Title'],
					'date' => gmdate( 'Y-m-d H:i:s' ),
					'author' => "{$boxinfo['Author']}",
					'addressee' => "{$boxinfo['Addressee']}",
					'record_type' => "{$boxinfo['Record Type']}",
					'site_name' => "{$boxinfo['Site Name']}",
					'site_id' => "{$boxinfo['Site ID #']}",
					'close_date' => "{$boxinfo['Close Date']}",
					'epa_contact_email' => '{}',
					'access_type' => "{$boxinfo['Access Type']}",
					'source_format' => "{$boxinfo['Source Format']}",
					// 'rights' => "{$boxinfo['Rights']}",
					// 'contract_number' => "{$boxinfo['Contract #']}",
					// 'grant_number' => "{$boxinfo['Grant #']}",
					'folder_identifier' => "{$boxinfo['Folder Identifier']}",
					// 'file_name' => '',
					// 'file_location' => '',
					// 'freeze' => 1,
					'index_level' => $index_level,
					'box_id' => $boxinfo_id,
					'essential_record' => "{$essential_record}",
					'date_created' => gmdate( 'Y-m-d H:i:s' ),
					'date_updated' => gmdate( 'Y-m-d H:i:s' ),
				);
				if ( 'Litigation' == $data['ticket_useage'] ) {
					$folderdocarray['freeze'] = 1;
				}

				$folderdocinfo_id = $this->create_new_folderdocinfo( $folderdocarray );

				$row_counter++;
			}
			// End of New BoxInfo Code.
		}

		/**
		 * Get storage location row id
		 */
		public function get_new_storage_location_row_id() {
			global $wpdb;
			$table = $wpdb->prefix . 'wpsc_epa_storage_location';
			$data = array(
				'digitization_center' => 666,
				'aisle' => 0,
				'bay' => 0,
				'shelf' => 0,
				'position' => 0,
			);
			$format = array( '%s', '%d', '%d', '%d', '%d' );
			$wpdb->insert( $table, $data, $format );
			return $wpdb->insert_id;
		}

		/**
		 * Get storage location row id
		 *
		 * @param String $record_schedule_number Record schedule number as string.
		 */
		public function get_record_schedule_id( $record_schedule_number ) {

			global $wpdb;
			$programe_office_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}epa_record_schedule WHERE Record_Schedule_Number = %s ", $record_schedule_number ) );

			return $programe_office_id;
		}

		/**
		 * Get program office id
		 *
		 * @param String $office_id_key office id key as string.
		 */
		public function get_programe_office_id( $office_id_key ) {

			global $wpdb;
			$programe_office_id = $wpdb->get_var( $wpdb->prepare( "SELECT office_code FROM {$wpdb->prefix}wpsc_epa_program_office WHERE office_acronym = %s ", $office_id_key ) );

			return $programe_office_id;
		}

		/**
		 * Adds ticketmeta for BoxInfo
		 *
		 * @param Integer $boxinfo_id box info id as Integer.
		 * @param String  $meta_key meta key as string.
		 * @param String  $meta_value meta value as string.
		 */
		public function add_boxinfo_meta( $boxinfo_id, $meta_key, $meta_value ) {
			global $wpdb;
			$wpdb->insert(
				$wpdb->prefix . 'wpsc_epa_boxmeta',
				array(
					'box_id' => $boxinfo_id,
					'meta_key' => $meta_key,
					'meta_value' => $meta_value,
				)
			);
		}

		/**
		 * Create a folderdocinfo record
		 *
		 * @param Array $folderdocarray folder doc array as array.
		 */
		public function create_new_folderdocinfo( $folderdocarray ) {
			global $wpdb;
			$wpdb->insert( $wpdb->prefix . 'wpsc_epa_folderdocinfo', $folderdocarray );
			$folderdocinfo_id = $wpdb->insert_id;
			return $folderdocinfo_id;
		}

		/**
		 * Create a boxinfo record
		 *
		 * @param Array $boxarray box info as array.
		 */
		public function create_new_boxinfo( $boxarray ) {
			global $wpdb;
			$wpdb->insert( $wpdb->prefix . 'wpsc_epa_boxinfo', $boxarray );
			$boxinfo_id = $wpdb->insert_id;
			return $boxinfo_id;
		}

		/**
		 * Box list form html
		 *
		 * @param array $field field info as array.
		 */
		public function print_listing_form_block( $field ) {

			if ( 'ticket_category' == $field->name ) {
				?>
				<!-- Beginning of new datatable -->
				<div class="box-body table-responsive" id="boxdisplaydiv"
					style="width:100%;padding-bottom: 20px;padding-right:20px;padding-left:20px;margin: 0 auto;">
					<label class="wpsc_ct_field_label">Box List <span style="color:red;">*</span></label>

					<!-- DropZone File Grag Drop Uploader -->
					<div id="dzBoxUpload" class="dropzone">
						<div class="fallback">
							<input name="file" type="file" />
						</div>
						<div class="dz-default dz-message">
							<button class="dz-button" type="button">Drop your file here to upload (xlsx files allowed)</button>
						</div>
					</div>
					<div style="margin: 10px 0 10px;" id="attach_16" class="row spreadsheet_container"></div>

					<table style="display:none;margin-bottom:0;" id="boxinfodatatable" class="table table-striped table-bordered nowrap">
						<thead style="margin: 0 auto !important;">
							<tr>
								<th>Box</th>
								<th>Folder Identifier</th>
								<th>Title</th>
								<th>Date</th>
								<th>Author</th>
								<th>Addressee</th>
								<th>Record Type</th>
								<th>Record Schedule & Item Number</th>
								<th>Site Name</th>
								<th>Site ID #</th>
								<th>Close Date</th>
								<th>EPA Contact</th>
								<th>Access Type</th>
								<th>Source Format</th>
								<th>Program Office</th>
								<th>Index Level</th>
								<th>Essential Record</th>
								<!-- <th>Rights</th> -->
							</tr>
						</thead>
					</table>

					<!-- O L D  F I L E  U P L O A D E R         
					<div class="row attachment_link">
						<span onclick="wpsc_spreadsheet_upload('attach_16','spreadsheet_attachment');">Attach spreadsheet</span>
					</div>
					-->
					<!-- File Upload Validation -->
					<input type="hidden" id="file_upload_cr" name="file_upload_cr" value="0" />
				</div>

				<!-- End of new datatable -->
				<?php
			}
		}
	}

	new Patt_HooksFilters();
}
