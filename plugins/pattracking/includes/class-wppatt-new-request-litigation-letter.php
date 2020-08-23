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

			add_action( 'pattracking_request_litigation_letter', __CLASS__ . '::litigation_letter' );
			add_action( 'wpsc_ticket_created', __CLASS__ . '::set_new_request_form_approval' );
		}

		/**
		 * Change upload path
		 */
		public static function litigation_letter() {
			?>
			<div class="row create_ticket_fields_container">
				<div class="col-sm-12 litigation-letter-dropzone">
					<label class="wpsc_ct_field_label"><?php esc_html_e( 'Litigation Letter', 'pattracking' ); ?><span class="red-text"> * </span></label>
					<div action="" class="dropzone" id="litigation-letter-dropzone">
						<div class="fallback">
							<input name="litigation_letter_files[]" type="file" id="litigation_letter_files"  />
						</div>
					</div>
				</div>	
				<div class="col-sm-12 congressional-dropzone">
					<label class="wpsc_ct_field_label"><?php esc_html_e( 'Congressional', 'pattracking' ); ?><span class="red-text"> * </span></label>
					<div action="" class="dropzone" id="congressional-dropzone">
						<div class="fallback">
							<input name="congressional_files[]" type="file" id="congressional_files"  />
						</div>
					</div>
				</div>
				<div class="col-sm-12 foia-dropzone">
					<label class="wpsc_ct_field_label"><?php esc_html_e( 'FOIA', 'pattracking' ); ?><span class="red-text"> * </span></label>
					<div action="" class="dropzone" id="foia-dropzone">
						<div class="fallback">
							<input name="foia_files[]" type="file" id="foia_files"  />
						</div>
					</div>
				</div>					
			</div>

			<?php
		}

		/**
		 * Set Request Form Approval data
		 */
		public static function set_new_request_form_approval() {

			global $current_user, $wpdb, $wpscfunction;

			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';

			$ticket_field = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsc_ticket  ORDER BY id DESC LIMIT 1" ) );

			if ( is_array( $_FILES['litigation_letter_files'] ) && count( $_FILES['litigation_letter_files'] ) > 0 ) {

				add_filter( 'upload_dir', 'Wppatt_Request_Approval_Widget::change_litig_letter_upload_dir' );
				add_filter( 'intermediate_image_sizes_advanced', 'Wppatt_Request_Approval_Widget::remove_thumbnail_generation' );

				$litigation_letter_files = isset( $_FILES['litigation_letter_files'] ) ? $_FILES['litigation_letter_files'] : array();

				$_FILES = array();

				$request_id = isset( $ticket_field->id ) ? sanitize_text_field( wp_unslash( $ticket_field->id ) ) : '';
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

			} else if ( is_array( $_FILES['congressional_files'] ) && count( $_FILES['congressional_files'] ) > 0 ) {

				add_filter( 'upload_dir', 'Wppatt_Request_Approval_Widget::change_congressional_upload_dir' );
				add_filter( 'intermediate_image_sizes_advanced', 'Wppatt_Request_Approval_Widget::remove_thumbnail_generation' );

				$congressional_files = isset( $_FILES['congressional_files'] ) ? $_FILES['congressional_files'] : array();

				$_FILES = array();

				$request_id = isset( $ticket_field->id ) ? sanitize_text_field( wp_unslash( $ticket_field->id ) ) : '';
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

			} else if ( is_array( $_FILES['foia_files'] ) && count( $_FILES['foia_files'] ) > 0 ) {

				add_filter( 'upload_dir', 'Wppatt_Request_Approval_Widget::change_foia_upload_dir' );
				add_filter( 'intermediate_image_sizes_advanced', 'Wppatt_Request_Approval_Widget::remove_thumbnail_generation' );

				$foia_files = isset( $_FILES['foia_files'] ) ? $_FILES['foia_files'] : array();

				$_FILES = array();

				$request_id = isset( $ticket_field->id ) ? sanitize_text_field( wp_unslash( $ticket_field->id ) ) : '';
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
