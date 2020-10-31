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
		}

		/**
		 * Add fields for upload associated document
		 */
		public static function new_request_associated_documents() {
			?>
			<div class="row create_ticket_fields_container">
				<div class="col-sm-12">
					<ul class="nav nav-tabs">
						<li role="presentation" class="tab active" id="wpsc_recall_sla_chan_destruct_auth" onclick="wpsc_change_tab( this, 'approval_destruction_authorization' );"><a href="javascript:void(0);"><?php esc_html_e( 'Destruction Authorization', 'pattracking' ); ?></a></li> <a href="#" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo Patt_Custom_Func::helptext_tooltip('help-change-shipping'); ?>"><i class="far fa-question-circle"></i></a>
						<li role="presentation" class="tab" id="wpsc_recall_sla_chan_litigation_letter" onclick="wpsc_change_tab(this,'approval_litigation_letter');"><a href="javascript:void(0);"><?php esc_html_e( 'Litigation Letter', 'pattracking' ); ?></a></li>
						<li role="presentation" class="tab" onclick="wpsc_change_tab(this,'approval_congressional');"><a href="javascript:void(0);"><?php esc_html_e( 'Congressional', 'pattracking' ); ?></a></li>
						<li role="presentation" class="tab" onclick="wpsc_change_tab(this,'approval_foia');"><a href="javascript:void(0);"><?php esc_html_e( 'FOIA', 'pattracking' ); ?></a></li>
					</ul>


					<div id="approval_destruction_authorization" class="tab_content visible">
						<h4><?php esc_html_e( 'Destruction Authorization', 'pattracking' ); ?></h4>
						<div class="dropzone" id="destr-autho-dropzone">
							<div class="fallback">
								<input name="destruction_authorization_files" type="file" id="destruction_authorization_files" />
							</div>
						</div>
					</div>

					<div id="approval_litigation_letter" class="tab_content hidden">
						<h4><?php esc_html_e( 'Litigation Letter', 'pattracking' ); ?></h4>

						<div class="dropzone" id="litigation-letter-dropzone">
							<div class="fallback">
								<input name="litigation_letter_files" type="file" id="litigation_letter_files" />
							</div>
						</div>
					</div>

					<div id="approval_congressional" class="tab_content hidden">
						<h4><?php esc_html_e( 'Congressional', 'pattracking' ); ?></h4>

						<div class="dropzone" id="congressional-dropzone">
							<div class="fallback">
								<input name="congressional_files" type="file" id="congressional_files" />
							</div>
						</div>
					</div>

					<div id="approval_foia" class="tab_content hidden">
						<h4><?php esc_html_e( 'FOIA', 'pattracking' ); ?></h4>

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

			if ( is_array( $associated_files['destruction_authorization_files'] ) && count( $associated_files['destruction_authorization_files'] ) > 0 ) {

				add_filter( 'upload_dir', 'Wppatt_Request_Approval_Widget::change_destruction_auth_upload_dir' );
				add_filter( 'intermediate_image_sizes_advanced', 'Wppatt_Request_Approval_Widget::remove_thumbnail_generation' );

				$destruction_authorization_files = isset( $associated_files['destruction_authorization_files'] ) ? $associated_files['destruction_authorization_files'] : array();

				$_FILES = array();

				$attach_ids   = array();

				foreach ( $destruction_authorization_files['name'] as $key => $file ) {
					$_FILES[ $file . '_' . $key ] = array(
						'name'     => $file,
						'type'     => $destruction_authorization_files['type'][ $key ],
						'tmp_name' => $destruction_authorization_files['tmp_name'][ $key ],
						'error'    => $destruction_authorization_files['error'][ $key ],
						'size'     => $destruction_authorization_files['size'][ $key ],
					);

					$attachment_id = media_handle_upload( $file . '_' . $key, 0 );
					if ( ! is_wp_error( $attachment_id ) ) {
						update_post_meta( $attachment_id, 'folder', 'destruction-authorizations' );
						array_push( $attach_ids, $attachment_id );
					}
				}

				$approval_flag = ( count( $attach_ids ) === intval( Patt_Custom_Func::get_accession_count( $request_id ) ) ? 1 : 0 );

				$wpdb->update( "{$wpdb->prefix}wpsc_ticket", array( 'destruction_approval' => $approval_flag ), array( 'id' => $request_id ), array( '%d' ), array( '%d' ) );

				$wpdb->insert(
					$wpdb->prefix . 'wpsc_ticketmeta',
					array(
						'ticket_id'  => $request_id,
						'meta_key'   => 'destruction_authorizations_image',
						'meta_value' => json_encode( $attach_ids ),
					)
				);

				remove_filter( 'upload_dir', 'Wppatt_Request_Approval_Widget::change_destruction_auth_upload_dir' );
				remove_filter( 'intermediate_image_sizes_advanced', 'Wppatt_Request_Approval_Widget::remove_thumbnail_generation' );
			}

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

			// In ticket meta, set commma seperated dropzone name if file will be set 
			$are_these_documents_used_for_the_following = implode( ',' , array_keys( $associated_files ) );
			$are_these_documents_used_for_the_following = str_replace( '_files', '', $are_these_documents_used_for_the_following );
			$are_these_documents_used_for_the_following = ( '' !== $are_these_documents_used_for_the_following ) ? $are_these_documents_used_for_the_following : 'N/A'; 

			$wpdb->insert(
				$wpdb->prefix . 'wpsc_ticketmeta',
				array(
					'ticket_id'  => $request_id,
					'meta_key'   => 'are-these-documents-used-for-the-following',
					'meta_value' => $are_these_documents_used_for_the_following,
				)
			);

		}
	}

	/**
	 * Calling init function to activate hooks and filters.
	 */
	Wppatt_New_Request_Litigation_Letter::init();
}
