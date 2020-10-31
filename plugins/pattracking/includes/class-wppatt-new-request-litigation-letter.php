<?php
/**
 * Class to manage new request litigation letter
 *
 * @package pattracking
 */

if ( ! class_exists( 'Wppatt_New_Request_Litigation_Letter' ) ) {
	/**
	 * Class to manage the new request litigation letter
	 */
	class Wppatt_New_Request_Litigation_Letter {
		/**
		 * Add hooks and filters for the new request litigation letter
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function init() {

			add_action( 'pattracking_request_litigation_letter', __CLASS__ . '::new_request_associated_documents' );
			add_action( 'wpsc_ticket_created', __CLASS__ . '::set_new_request_form_approval' );
			add_filter( 'request_ticket_delete', __CLASS__ . '::request_ticket_delete', 10, 1 );
		}

		/**
		 * Ticket delete if error in save box list
		 *
		 * @param Array $ticket_id ticket id as integer.
		 */
		public static function request_ticket_delete( $ticket_id ) {

			global $wpdb, $current_user, $wpscfunction;

			if ( ! ( $current_user->ID && $current_user->has_cap( 'wpsc_agent' ) ) ) {
				exit;
			}

			// Delete associated documents when ticket delete.
			$get_associated_doc_ticket_meta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wpqa_wpsc_ticketmeta WHERE ticket_id = %d AND ( meta_key = 'destruction_authorizations_image' OR meta_key = 'litigation_letter_image' OR meta_key = 'congressional_file' OR meta_key = 'foia_file' ) ", array( $ticket_id ) ), ARRAY_A );

			if ( is_array( $get_associated_doc_ticket_meta ) ) {
				array_walk(
					$get_associated_doc_ticket_meta,
					function ( $value, $key ) use ( &$wpdb ) {

						$wpdb->query( 'DELETE  FROM ' . $wpdb->prefix . 'wpsc_ticketmeta WHERE id = "' . $value['id'] . '"' );
						$attachment_ids = str_replace( '[', '', $value['meta_value'] );
						$attachment_ids = str_replace( ']', '', $attachment_ids );
						$attachment_ids = explode( ',', $attachment_ids );

						if ( is_array( $attachment_ids ) ) {
							array_walk(
								$attachment_ids,
								function( $attachment_id, $k ) {
									// delete from folder, post and post meta table.
									wp_delete_attachment( $attachment_id );
								}
							);
						}

						// Delete from ticket meta table.
						$wpdb->delete( $wpdb->prefix . 'wpsc_ticketmeta', array( 'id' => $value['id'] ) );
					}
				);
			}

			// PATT BEGIN.
			$get_associated_boxes = $wpdb->get_results( $wpdb->prepare( ' SELECT id, storage_location_id FROM wpqa_wpsc_epa_boxinfo WHERE ticket_id = %d ', array( $ticket_id ) ) );

			foreach ( $get_associated_boxes as $info ) {
				$associated_box_ids = $info->id;
				$associated_storage_ids = $info->storage_location_id;

				$box_details = $wpdb->get_row( $wpdb->prepare( 'SELECT digitization_center, aisle, bay, shelf, position FROM wpqa_wpsc_epa_storage_location WHERE id = %d ', array( $associated_storage_ids ) ) );

				$box_storage_digitization_center = $box_details->digitization_center;
				$box_storage_aisle = $box_details->aisle;
				$box_storage_bay = $box_details->bay;
				$box_storage_shelf = $box_details->shelf;
				$box_sotrage_shelf_id = $box_storage_aisle . '_' . $box_storage_bay . '_' . $box_storage_shelf;

				$box_storage_status = $wpdb->get_row( "SELECT occupied, remaining FROM wpqa_wpsc_epa_storage_status WHERE shelf_id = '" . $box_sotrage_shelf_id . "'" );

				$box_storage_status_occupied = $box_storage_status->occupied;
				$box_storage_status_remaining = $box_storage_status->remaining;
				$box_storage_status_remaining_added = $box_storage_status->remaining + 1;

				if ( $box_storage_status_remaining <= 4 ) {
					$table_ss = 'wpqa_wpsc_epa_storage_status';
					$ssr_update = array( 'remaining' => $box_storage_status_remaining_added );
					$ssr_where = array(
						'shelf_id' => $box_sotrage_shelf_id,
						'digitization_center' => $box_storage_digitization_center,
					);
					$wpdb->update( $table_ss, $ssr_update, $ssr_where );
				}

				if ( 4 == $box_storage_status_remaining ) {
					$sso_update = array( 'occupied' => 0 );
					$sso_where = array(
						'shelf_id' => $box_sotrage_shelf_id,
						'digitization_center' => $box_storage_digitization_center,
					);
					$wpdb->update( $table_ss, $sso_update, $sso_where );
				}

				$wpdb->delete( $wpdb->prefix . 'wpsc_epa_storage_location', array( 'id' => $associated_storage_ids ) );
				$wpdb->delete( $wpdb->prefix . 'wpsc_epa_boxinfo', array( 'id' => $associated_box_ids ) );
			}
			// PATT END.

			$wpdb->delete( $wpdb->prefix . 'wpsc_ticket', array( 'id' => $ticket_id ) );

			$args = array(
				'post_type'      => 'wpsc_ticket_thread',
				'post_status'    => array( 'publish', 'trash' ),
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => 'ticket_id',
						'value'   => $ticket_id,
						'compare' => '=',
					),
				),
			);
			$ticket_threads = get_posts( $args );
			if ( $ticket_threads ) {
				foreach ( $ticket_threads as $ticket_thread ) {
					wp_delete_post( $ticket_thread->ID, true );
				}
			}

		}

		/**
		 * Add fields for upload associated document
		 */
		public static function new_request_associated_documents() {
			?>
			<div class="row create_ticket_fields_container request-associated-docs">
				<div class="col-sm-12">
					<h4><?php esc_html_e( 'Associated Documents', 'pattracking' ); ?></h4>
					<ul class="nav nav-tabs">
						<li role="presentation" class="tab active" id="wpsc_recall_sla_chan_litigation_letter" onclick="wpsc_change_tab(this,'approval_litigation_letter');"><a href="javascript:void(0);"><?php esc_html_e( 'Litigation Letter', 'pattracking' ); ?></a></li>
						<li role="presentation" class="tab" onclick="wpsc_change_tab(this,'approval_congressional');"><a href="javascript:void(0);"><?php esc_html_e( 'Congressional', 'pattracking' ); ?></a></li>
						<li role="presentation" class="tab" onclick="wpsc_change_tab(this,'approval_foia');"><a href="javascript:void(0);"><?php esc_html_e( 'FOIA', 'pattracking' ); ?></a></li>
					</ul>

					<div id="approval_litigation_letter" class="tab_content visible">
						<h5><?php esc_html_e( 'Litigation Letter', 'pattracking' ); ?></h5>

						<div class="dropzone" id="litigation-letter-dropzone">
							<div class="fallback">
								<input name="litigation_letter_files" type="file" id="litigation_letter_files" />
							</div>
						</div>
					</div>

					<div id="approval_congressional" class="tab_content hidden">
						<h5><?php esc_html_e( 'Congressional', 'pattracking' ); ?></h5>

						<div class="dropzone" id="congressional-dropzone">
							<div class="fallback">
								<input name="congressional_files" type="file" id="congressional_files" />
							</div>
						</div>
					</div>

					<div id="approval_foia" class="tab_content hidden">
						<h5><?php esc_html_e( 'FOIA', 'pattracking' ); ?></h5>

						<div class="dropzone" id="foia-dropzone">
							<div class="fallback">
								<input name="foia_files" type="file" id="foia_files" />
							</div>
						</div>
					</div>
				</div>
			</div>


			<?php
		}

		/**
		 * Save Request Form associated documents
		 */
		public static function set_new_request_form_approval() {

			global $wpdb, $wpscfunction;

			$associated_files = $_FILES;

			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';

			$ticket_field = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsc_ticket  ORDER BY id DESC LIMIT 1" ) );

			$request_id = isset( $ticket_field->id ) ? sanitize_text_field( wp_unslash( $ticket_field->id ) ) : '';

			if ( is_array( $associated_files['litigation_letter_files'] ) && count( $associated_files['litigation_letter_files'] ) > 0 ) {

				add_filter( 'upload_dir', 'Wppatt_Request_Approval_Widget::change_litig_letter_upload_dir' );
				add_filter( 'intermediate_image_sizes_advanced', 'Wppatt_Request_Approval_Widget::remove_thumbnail_generation' );

				$litigation_letter_files = isset( $associated_files['litigation_letter_files'] ) ? $associated_files['litigation_letter_files'] : array();
				$_FILES = array();

				$attach_ids   = array();

				foreach ( $litigation_letter_files['name'] as $key => $file ) {
					$_FILES[ $file . '_' . $key ] = array(
						'name'     => $file,
						'type'     => $litigation_letter_files['type'][ $key ],
						'tmp_name' => $litigation_letter_files['tmp_name'][ $key ],
						'error'    => $litigation_letter_files['error'][ $key ],
						'size'     => $litigation_letter_files['size'][ $key ],
					);

					$attachment_id = media_handle_upload( $file . '_' . $key, 0 );
					if ( ! is_wp_error( $attachment_id ) ) {
						update_post_meta( $attachment_id, 'folder', 'litigation-letters' );
						array_push( $attach_ids, $attachment_id );
					}
				}

				$approval_flag = ( count( $attach_ids ) > 0 ? 1 : 0 );
				$wpdb->update( "{$wpdb->prefix}wpsc_ticket", array( 'freeze_approval' => $approval_flag ), array( 'id' => $request_id ), array( '%d' ), array( '%d' ) );

				$wpdb->insert(
					$wpdb->prefix . 'wpsc_ticketmeta',
					array(
						'ticket_id'  => $request_id,
						'meta_key'   => 'litigation_letter_image',
						'meta_value' => json_encode( $attach_ids ),
					)
				);

				remove_filter( 'upload_dir', 'Wppatt_Request_Approval_Widget::change_litig_letter_upload_dir' );
				remove_filter( 'intermediate_image_sizes_advanced', 'Wppatt_Request_Approval_Widget::remove_thumbnail_generation' );
			}

			if ( is_array( $associated_files['congressional_files'] ) && count( $associated_files['congressional_files'] ) > 0 ) {

				add_filter( 'upload_dir', 'Wppatt_Request_Approval_Widget::change_congressional_upload_dir' );
				add_filter( 'intermediate_image_sizes_advanced', 'Wppatt_Request_Approval_Widget::remove_thumbnail_generation' );

				$congressional_files = isset( $associated_files['congressional_files'] ) ? $associated_files['congressional_files'] : array();

				$_FILES = array();

				$attach_ids   = array();

				foreach ( $congressional_files['name'] as $key => $file ) {
					$_FILES[ $file . '_' . $key ] = array(
						'name'     => $file,
						'type'     => $congressional_files['type'][ $key ],
						'tmp_name' => $congressional_files['tmp_name'][ $key ],
						'error'    => $congressional_files['error'][ $key ],
						'size'     => $congressional_files['size'][ $key ],
					);

					$attachment_id = media_handle_upload( $file . '_' . $key, 0 );
					if ( ! is_wp_error( $attachment_id ) ) {
						update_post_meta( $attachment_id, 'folder', 'congressional' );
						array_push( $attach_ids, $attachment_id );
					}
				}

				$approval_flag = ( count( $attach_ids ) > 0 ? 1 : 0 );
				$wpdb->update( "{$wpdb->prefix}wpsc_ticket", array( 'congressional_approval' => $approval_flag ), array( 'id' => $request_id ), array( '%d' ), array( '%d' ) );

				$wpdb->insert(
					$wpdb->prefix . 'wpsc_ticketmeta',
					array(
						'ticket_id'  => $request_id,
						'meta_key'   => 'congressional_file',
						'meta_value' => json_encode( $attach_ids ),
					)
				);

				remove_filter( 'upload_dir', 'Wppatt_Request_Approval_Widget::change_congressional_upload_dir' );
				remove_filter( 'intermediate_image_sizes_advanced', 'Wppatt_Request_Approval_Widget::remove_thumbnail_generation' );

			}

			if ( is_array( $associated_files['foia_files'] ) && count( $associated_files['foia_files'] ) > 0 ) {

				add_filter( 'upload_dir', 'Wppatt_Request_Approval_Widget::change_foia_upload_dir' );
				add_filter( 'intermediate_image_sizes_advanced', 'Wppatt_Request_Approval_Widget::remove_thumbnail_generation' );

				$foia_files = isset( $associated_files['foia_files'] ) ? $associated_files['foia_files'] : array();

				$_FILES = array();

				$attach_ids   = array();

				foreach ( $foia_files['name'] as $key => $file ) {
					$_FILES[ $file . '_' . $key ] = array(
						'name'     => $file,
						'type'     => $foia_files['type'][ $key ],
						'tmp_name' => $foia_files['tmp_name'][ $key ],
						'error'    => $foia_files['error'][ $key ],
						'size'     => $foia_files['size'][ $key ],
					);

					$attachment_id = media_handle_upload( $file . '_' . $key, 0 );
					if ( ! is_wp_error( $attachment_id ) ) {
						update_post_meta( $attachment_id, 'folder', 'foia' );
						array_push( $attach_ids, $attachment_id );
					}
				}

				$approval_flag = ( count( $attach_ids ) > 0 ? 1 : 0 );
				$wpdb->update( "{$wpdb->prefix}wpsc_ticket", array( 'foia_approval' => $approval_flag ), array( 'id' => $request_id ), array( '%d' ), array( '%d' ) );

				$wpdb->insert(
					$wpdb->prefix . 'wpsc_ticketmeta',
					array(
						'ticket_id'  => $request_id,
						'meta_key'   => 'foia_file',
						'meta_value' => json_encode( $attach_ids ),
					)
				);

				remove_filter( 'upload_dir', 'Wppatt_Request_Approval_Widget::change_foia_upload_dir' );
				remove_filter( 'intermediate_image_sizes_advanced', 'Wppatt_Request_Approval_Widget::remove_thumbnail_generation' );
			}
		}
	}

	/**
	 * Calling init function to activate hooks and filters.
	 */
	Wppatt_New_Request_Litigation_Letter::init();
}
