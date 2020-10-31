<?php
/**
 * Class to manage request approval widget
 *
 * @package pattracking
 */

if ( ! class_exists( 'Wppatt_Request_Approval_Widget' ) ) {
	/**
	 * Class to manage the request approval widget
	 */
	class Wppatt_Request_Approval_Widget {
		/**
		 * Add hooks and filters for the request approval widget
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function init() {
			// Add Approval Widget.
			add_action( 'admin_enqueue_scripts', __CLASS__ . '::request_enqueue_script' );
			add_action( 'wpsc_after_ticket_widget', __CLASS__ . '::approval_widget' );
			add_action( 'wp_ajax_wpsc_get_approval_details', __CLASS__ . '::get_approval_details' );
			add_action( 'wp_ajax_wpsc_set_approval_widget', __CLASS__ . '::set_approval_widget' );
			add_action( 'wp_ajax_wpsc_delete_destruction_authorization', __CLASS__ . '::delete_destruction_authorization' );
			add_action( 'wp_ajax_wpsc_delete_litigation_letter', __CLASS__ . '::delete_litigation_letter' );
			add_action( 'wp_ajax_wpsc_delete_congressional', __CLASS__ . '::delete_congressional' );
			add_action( 'wp_ajax_wpsc_delete_foia', __CLASS__ . '::delete_foia' );

			add_filter( 'media_folder_list', __CLASS__ . '::media_folder_list_callback', 10, 1 );
		}

		/**
		 * Add approval widget file's Folder in media folder list
		 *
		 * @param Array $folders Folders as array.
		 * @return Array Folders as array.
		 */
		public static function media_folder_list_callback( $folders ) {

			array_push( $folders, array( 'name' => 'destruction-authorizations' ) );
			array_push( $folders, array( 'name' => 'litigation-letters' ) );
			array_push( $folders, array( 'name' => 'congressional' ) );
			array_push( $folders, array( 'name' => 'foia' ) );
			array_push( $folders, array( 'name' => 'box-list' ) );
			array_push( $folders, array( 'name' => 'backups' ) );

			return $folders;
		}

		/**
		 * Request Approval Widget Script
		 */
		public static function request_enqueue_script() {
			wp_enqueue_script( 'request-approval-widget-js', WPPATT_PLUGIN_URL . 'asset/js/request_approval_widget.js', array(), time(), true );
			wp_enqueue_style( 'request-approval-widget-style', WPPATT_PLUGIN_URL . 'asset/css/request_approval_widget.css', array(), time(), false );
		}

		/**
		 * Added function to create a Approval widget
		 *
		 * @param Integer $post_id Post Id as Integer.
		 */
		public static function approval_widget( $post_id ) {
			global $wpdb;

			$wpsc_appearance_individual_ticket_page = get_option( 'wpsc_individual_ticket_page' );
			$edit_btn_css = 'background-color:' . $wpsc_appearance_individual_ticket_page['wpsc_edit_btn_bg_color'] . ' !important;color:' . $wpsc_appearance_individual_ticket_page['wpsc_edit_btn_text_color'] . ' !important;border-color:' . $wpsc_appearance_individual_ticket_page['wpsc_edit_btn_border_color'] . '!important';

			$wpsc_appearance_individual_ticket_page = get_option( 'wpsc_individual_ticket_page' );
			?>
			<div id="wpsc_approval_widget" class="row" style="'background-color:'<?php echo esc_attr( $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color'] ); ?>' !important; color:'<?php echo esc_attr( $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color'] ); ?>' !important; border-color:'<?php echo esc_attr( $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color'] ); ?>' !important;">
				<h4 class='widget_header'>
					<i class="far fa-folder"></i> 
					<?php esc_html_e( 'Assoc. Documents', 'pattracking' ); ?> 
					<button id="wpsc_individual_change_agent_fields" aria-label="Associated Documents edit button" onclick="wpsc_get_approval_details('<?php echo esc_attr( $post_id ); ?>')" class="btn btn-sm wpsc_action_btn" style="<?php echo esc_attr( $edit_btn_css ); ?>" ><i class="fas fa-edit"></i></button>
				</h4>
				<hr style="margin-top: 4px; margin-bottom: 6px" class="widget_devider">

				<ul class="approval-preview-image">
					<?php
					$destr_auth_images = self::get_approval_attached_image( $post_id, 'destruction_authorizations_image' );
					$litig_letter_image = self::get_approval_attached_image( $post_id, 'litigation_letter_image' );
					$congressional_file = self::get_approval_attached_image( $post_id, 'congressional_file' );
					$foia_file = self::get_approval_attached_image( $post_id, 'foia_file' );

					$has_file = false;

					if ( is_array( $destr_auth_images ) && count( $destr_auth_images ) > 0 ) {
						$destruction_counter = 1;
						$dest_content        = '';
						foreach ( $destr_auth_images as $destruction_image ) {
							$dest_content .= '<li>';
								$dest_content .= '<a href="' . $destruction_image . '" target="_blank">';
									$dest_content .= '<label>' . __( 'File ', 'pattracking' ) . $destruction_counter . '</label>';
								$dest_content .= '</a>';
							$dest_content .= '</li>';
							$destruction_counter++;
						}

						if ( '' !== $dest_content ) {
							$has_file = true;
							?>
								<li>
									<strong><?php esc_html_e( 'Destruction Authorization', 'pattracking' ); ?></strong> <a href="#" aria-label="Destruction Authorization" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo Patt_Custom_Func::helptext_tooltip('help-destruction-authorization-tab'); ?>"><i class="far fa-question-circle"></i></a>
								</li>
								<?php echo $dest_content; // phpcs:ignore ?>
							<?php
						}
					}

					if ( is_array( $litig_letter_image ) && count( $litig_letter_image ) > 0 ) {
						$litigation_counter = 1;
						$litigation_content = '';
						foreach ( $litig_letter_image as $litigation_image ) {
							$litigation_content .= '<li>';
								$litigation_content .= '<a href="' . $litigation_image . '" target="_blank">';
									$litigation_content .= '<label>' . __( 'File ', 'pattracking' ) . $litigation_counter . '</label>';
								$litigation_content .= '</a>';
							$litigation_content .= '</li>';
							$litigation_counter++;
						}

						if ( '' !== $litigation_content ) {
							$has_file = true;
							?>
								<li><strong><?php esc_html_e( 'Litigation Letter', 'pattracking' ); ?></strong> <a href="#" aria-label="Litigation Letter" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo Patt_Custom_Func::helptext_tooltip('help-litigation-letter-tab'); ?>"><i class="far fa-question-circle"></i></a>
								</li>
								<?php echo $litigation_content; // phpcs:ignore ?>
							<?php
						}
					}

					if ( is_array( $congressional_file ) && count( $congressional_file ) > 0 ) {
						$congressional_counter = 1;
						$congressional_content = '';
						foreach ( $congressional_file as $file ) {
							$congressional_content .= '<li>';
								$congressional_content .= '<a href="' . $file . '" target="_blank">';
									$congressional_content .= '<label>' . __( 'File ', 'pattracking' ) . $congressional_counter . '</label>';
								$congressional_content .= '</a>';
							$congressional_content .= '</li>';
							$congressional_counter++;
						}

						if ( '' !== $congressional_content ) {
							$has_file = true;
							?>
								<li><strong><?php esc_html_e( 'Congressional', 'pattracking' ); ?></strong> <a href="#" aria-label="Congressional" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo Patt_Custom_Func::helptext_tooltip('help-congressional-tab'); ?>"><i class="far fa-question-circle"></i></a>
								</li>
								<?php echo $congressional_content; // phpcs:ignore ?>
							<?php
						}
					}

					if ( is_array( $foia_file ) && count( $foia_file ) > 0 ) {
						$foia_counter = 1;
						$foia_content = '';
						foreach ( $foia_file as $file ) {
							$foia_content .= '<li>';
								$foia_content .= '<a href="' . $file . '" target="_blank">';
									$foia_content .= '<label>' . __( 'File ', 'pattracking' ) . $foia_counter . '</label>';
								$foia_content .= '</a>';
							$foia_content .= '</li>';
							$foia_counter++;
						}

						if ( '' !== $foia_content ) {
							$has_file = true;
							?>
								<li><strong><?php esc_html_e( 'FOIA', 'pattracking' ); ?></strong> <a href="#" aria-label="FOIA" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo Patt_Custom_Func::helptext_tooltip('help-foia-tab'); ?>"><i class="far fa-question-circle"></i></a>
								</li>
								<?php echo $foia_content; // phpcs:ignore ?>
							<?php
						}
					}

					if ( ! $has_file ) {
						?>
						<li>
							<label><?php esc_html_e( 'No Associated Documents', 'pattracking' ); ?></label>
						</li>
						<?php
					}
					?>
				</ul>
			</div>                
			<?php
		}

		/**
		 * Added function to get approval image
		 *
		 * @param Integer $request_id request id Id as Integer.
		 * @param Integer $meta_key meta key as string.
		 */
		public static function get_approval_attached_image( $request_id, $meta_key ) {

			global $wpdb;

			$ticket_field = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsc_ticketmeta WHERE ticket_id= %d AND meta_key = %s", array( $request_id, $meta_key ) ) );

			$images = array();
			if ( ! empty( $ticket_field ) && isset( $ticket_field->id ) && isset( $ticket_field->meta_value ) ) {

				$ticket_meta_value = json_decode( $ticket_field->meta_value, true );

				array_walk(
					$ticket_meta_value,
					function ( $value, $key ) use ( &$images ) {
						$image_url = wp_get_attachment_url( $value );

						$images[ $value ] = $image_url;
					}
				);

			}

			return $images;
		}

		/**
		 * Added function to get approval widget details
		 */
		public static function get_approval_details() {

			global $wpdb;

			$ticket_id   = isset( $_POST['ticket_id'] ) ? intval( $_POST['ticket_id'] ) : 0; //phpcs:ignore

			$destr_auth_images = self::get_approval_attached_image( $ticket_id, 'destruction_authorizations_image' );
			$litig_letter_image = self::get_approval_attached_image( $ticket_id, 'litigation_letter_image' );
			$congressional_file = self::get_approval_attached_image( $ticket_id, 'congressional_file' );
			$foia_file = self::get_approval_attached_image( $ticket_id, 'foia_file' );

			$total_require_file = intval( Patt_Custom_Func::get_accession_count( $ticket_id ) );

			ob_start();
			?>
			<form id="approval_widget_form" method="post" action="javascript:wpsc_set_approval_widget();" enctype="multipart/form-data">

				<div id="wpsc_popup_body">
					
					<ul class="nav nav-tabs">
						<li role="presentation" class="tab active" id="wpsc_recall_sla_chan_destruct_auth" onclick="wpsc_change_tab( this, 'approval_destruction_authorization' );"><a href="javascript:void(0);"><?php esc_html_e( 'Destruction Authorization', 'pattracking' ); ?> </a></li>
						<li role="presentation" class="tab" id="wpsc_recall_sla_chan_litigation_letter" onclick="wpsc_change_tab(this,'approval_litigation_letter');"><a href="javascript:void(0);"><?php esc_html_e( 'Litigation Letter', 'pattracking' ); ?></a></li>
						<li role="presentation" class="tab" onclick="wpsc_change_tab(this,'approval_congressional');"><a href="javascript:void(0);"><?php esc_html_e( 'Congressional', 'pattracking' ); ?></a></li>
						<li role="presentation" class="tab" onclick="wpsc_change_tab(this,'approval_foia');"><a href="javascript:void(0);"><?php esc_html_e( 'FOIA', 'pattracking' ); ?></a></li>
					</ul>


					<div id="approval_destruction_authorization" class="tab_content visible"> 
						<h4><?php esc_html_e( 'Destruction Authorization', 'pattracking' ); ?> </h4>
						<span class="destruction_auth_instruction"><div class='alert-warning alert'><?php echo esc_attr( $total_require_file . ' Files needed for Destruction Authorization.' ); ?></div></span>
						<div class="dropzone" id="destr-autho-dropzone">
							<div class="fallback">
								<input name="destruction_authorization_files" type="file" id="destruction_authorization_files" />
							</div>
						</div>

						<?php

						if ( is_array( $destr_auth_images ) && count( $destr_auth_images ) > 0 ) {
							$counter = 1;
							foreach ( $destr_auth_images as $key => $value ) {
								if ( '' !== $value ) {
									?>
									<div class="preview-image image_<?php echo esc_attr( $key ); ?>">
										<a href="<?php echo $value; ?>" target="_blank"><?php echo __( 'File ', 'pattracking' ) . ' ' . $counter; ?></a>
										<span class="delete-image" title="Delete Image" onclick="wpsc_delete_approval_widget( 'wpsc_delete_destruction_authorization', <?php echo esc_attr( $ticket_id ); ?> , <?php echo esc_attr( $key ); ?> );">
											<i class="fa fa-trash"></i>
										</span>
									</div>
									<?php
									$counter++;
								}
							}
						}
						?>
					</div>

					<div id="approval_litigation_letter" class="tab_content hidden">
						<h4><?php esc_html_e( 'Litigation Letter', 'pattracking' ); ?></h4>

						<div class="dropzone" id="litigation-letter-dropzone">
							<div class="fallback">
								<input name="litigation_letter_files" type="file" id="litigation_letter_files" />
							</div>
						</div>

						<?php
						if ( is_array( $litig_letter_image ) && count( $litig_letter_image ) > 0 ) {
							$counter = 1;
							foreach ( $litig_letter_image as $key => $value ) {
								if ( '' !== $value ) {
									?>
									<div class="preview-image image_<?php echo esc_attr( $key ); ?>">
										<a href="<?php echo $value; ?>" target="_blank"><?php echo __( 'File ', 'pattracking' ) . ' ' . $counter; ?></a>
										<span class="delete-image" title="Delete Image" onclick="wpsc_delete_approval_widget( 'wpsc_delete_litigation_letter', <?php echo esc_attr( $ticket_id ); ?> , <?php echo esc_attr( $key ); ?> );">
											<i class="fa fa-trash"></i>
										</span>
									</div>
									<?php
									$counter++;
								}
							}
						}
						?>
					</div>

					<div id="approval_congressional" class="tab_content hidden">
						<h4><?php esc_html_e( 'Congressional', 'pattracking' ); ?></h4>

						<div class="dropzone" id="congressional-dropzone">
							<div class="fallback">
								<input name="congressional_files" type="file" id="congressional_files" />
							</div>
						</div>

						<?php
						if ( is_array( $congressional_file ) && count( $congressional_file ) > 0 ) {
							$counter = 1;
							foreach ( $congressional_file as $key => $value ) {
								if ( '' !== $value ) {
									?>
									<div class="preview-image image_<?php echo esc_attr( $key ); ?>">
										<a href="<?php echo $value; ?>" target="_blank"><?php echo __( 'File ', 'pattracking' ) . ' ' . $counter; ?></a>
										<span class="delete-image" title="Delete Image" onclick="wpsc_delete_approval_widget( 'wpsc_delete_congressional', <?php echo esc_attr( $ticket_id ); ?> , <?php echo esc_attr( $key ); ?> );">
											<i class="fa fa-trash"></i>
										</span>
									</div>
									<?php
									$counter++;
								}
							}
						}
						?>
					</div>

					<div id="approval_foia" class="tab_content hidden">
						<h4><?php esc_html_e( 'FOIA', 'pattracking' ); ?></h4>

						<div class="dropzone" id="foia-dropzone">
							<div class="fallback">
								<input name="foia_files" type="file" id="foia_files" />
							</div>
						</div>

						<?php
						if ( is_array( $foia_file ) && count( $foia_file ) > 0 ) {
							$counter = 1;
							foreach ( $foia_file as $key => $value ) {
								if ( '' !== $value ) {
									?>
									<div class="preview-image image_<?php echo esc_attr( $key ); ?>">
										<a href="<?php echo $value; ?>" target="_blank"><?php echo __( 'File ', 'pattracking' ) . ' ' . $counter; ?></a>
										<span class="delete-image" title="Delete Image" onclick="wpsc_delete_approval_widget( 'wpsc_delete_foia', <?php echo esc_attr( $ticket_id ); ?> , <?php echo esc_attr( $key ); ?> );">
											<i class="fa fa-trash"></i>
										</span>
									</div>
									<?php
									$counter++;
								}
							}
						}
						?>
					</div>

					<input name="request_id" value="<?php echo esc_attr( $ticket_id ); ?>" type="hidden" />
					<input type="hidden" name="action" value="wpsc_set_approval_widget" />
				</div>
				<div id="wpsc_popup_footer">
					<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php esc_html_e( 'Close', 'wpsc-export-ticket' ); ?></button>

					<button type="submit" class="btn btn-success"><?php esc_html_e( 'Save Changes', 'pattracking' ); ?></button>
					
				</div>
				<?php wp_nonce_field( 'approval_widget_form_submit', 'approval_widget_generate_nonce' ); ?>
			</form>
			<?php
			$content = ob_get_clean();
			$output = array(
				'content'   => $content,
			);

			echo json_encode( $output );
			die();

		}

		/**
		 * Added function to set approval destruction authorization data
		 */
		public static function set_approval_widget() {

			global $wpdb, $wpscfunction;

			if ( isset( $_POST['approval_widget_generate_nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['approval_widget_generate_nonce'] ) ), 'approval_widget_form_submit' ) ) {
				wp_die( 'This Site is protected!!' );
			}

			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';

			$request_id = isset( $_POST['request_id'] ) ? sanitize_text_field( wp_unslash( $_POST['request_id'] ) ) : '';

			$destruction_authorization_files = isset( $_FILES['destruction_authorization_files'] ) ? $_FILES['destruction_authorization_files'] : array();
			$litigation_letter_files = isset( $_FILES['litigation_letter_files'] ) ? $_FILES['litigation_letter_files'] : array();
			$congressional_files = isset( $_FILES['congressional_files'] ) ? $_FILES['congressional_files'] : array();
			$foia_files = isset( $_FILES['foia_files'] ) ? $_FILES['foia_files'] : array();

			$_FILES = array();

			$attach_ids  = array();
			$ticket_field = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsc_ticketmeta WHERE ticket_id= %d AND meta_key = 'destruction_authorizations_image'", $request_id ) );

			if ( ! empty( $ticket_field ) && isset( $ticket_field->id ) && isset( $ticket_field->meta_value ) ) {
				$ticket_meta_value = json_decode( $ticket_field->meta_value, true );
				if ( ! empty( $ticket_meta_value ) ) {
					$attach_ids = array_merge( $attach_ids, $ticket_meta_value );
				}
			}

			$destruction_approval_warning = '';
			if ( count( $attach_ids ) !== intval( Patt_Custom_Func::get_accession_count( $request_id ) ) ) {
				$destruction_approval_warning = 'Destruction Authorization ' . intval( Patt_Custom_Func::get_accession_count( $request_id ) ) . ' files required, ' . count( $attach_ids ) . ' files found.';
			}

			if ( is_array( $destruction_authorization_files ) && count( $destruction_authorization_files ) > 0 ) {
				add_filter( 'upload_dir', __CLASS__ . '::change_destruction_auth_upload_dir' );
				add_filter( 'intermediate_image_sizes_advanced', __CLASS__ . '::remove_thumbnail_generation' );

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

				$destruction_approval_status = ( count( $attach_ids ) === intval( Patt_Custom_Func::get_accession_count( $request_id ) ) ? 1 : 0 );
				$wpdb->update( "{$wpdb->prefix}wpsc_ticket", array( 'destruction_approval' => $destruction_approval_status ), array( 'id' => $request_id ), array( '%d' ), array( '%d' ) );

				if ( 0 === $destruction_approval_status ) {
					$destruction_approval_warning = 'Destruction Authorization ' . intval( Patt_Custom_Func::get_accession_count( $request_id ) ) . ' files required, ' . count( $attach_ids ) . ' files found.';
				}

				if ( empty( $ticket_field ) && ! isset( $ticket_field->id ) ) {

					$wpdb->insert(
						$wpdb->prefix . 'wpsc_ticketmeta',
						array(
							'ticket_id'  => $request_id,
							'meta_key'   => 'destruction_authorizations_image',
							'meta_value' => json_encode( $attach_ids ),
						)
					);
				} else {
					$wpscfunction->update_ticket_meta( $request_id, 'destruction_authorizations_image', array( 'meta_value' => json_encode( $attach_ids ) ) );
				}

				remove_filter( 'upload_dir', __CLASS__ . '::change_destruction_auth_upload_dir' );
				remove_filter( 'intermediate_image_sizes_advanced', __CLASS__ . '::remove_thumbnail_generation' );
			}

			if ( is_array( $litigation_letter_files ) && count( $litigation_letter_files ) > 0 ) {
				add_filter( 'upload_dir', __CLASS__ . '::change_litig_letter_upload_dir' );
				add_filter( 'intermediate_image_sizes_advanced', __CLASS__ . '::remove_thumbnail_generation' );

				$attach_ids  = array();

				$ticket_field = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsc_ticketmeta WHERE ticket_id= %d AND meta_key = 'litigation_letter_image'", $request_id ) );

				if ( ! empty( $ticket_field ) && isset( $ticket_field->id ) && isset( $ticket_field->meta_value ) ) {
					$ticket_meta_value = json_decode( $ticket_field->meta_value, true );
					if ( ! empty( $ticket_meta_value ) ) {
						$attach_ids = array_merge( $attach_ids, $ticket_meta_value );
					}
				}

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

				if ( empty( $ticket_field ) && ! isset( $ticket_field->id ) ) {
					$wpdb->insert(
						$wpdb->prefix . 'wpsc_ticketmeta',
						array(
							'ticket_id'  => $request_id,
							'meta_key'   => 'litigation_letter_image',
							'meta_value' => json_encode( $attach_ids ),
						)
					);
				} else {
					$wpscfunction->update_ticket_meta( $request_id, 'litigation_letter_image', array( 'meta_value' => json_encode( $attach_ids ) ) );
				}

				remove_filter( 'upload_dir', __CLASS__ . '::change_litig_letter_upload_dir' );
				remove_filter( 'intermediate_image_sizes_advanced', __CLASS__ . '::remove_thumbnail_generation' );
			}

			if ( is_array( $congressional_files ) && count( $congressional_files ) > 0 ) {
				add_filter( 'upload_dir', __CLASS__ . '::change_congressional_upload_dir' );
				add_filter( 'intermediate_image_sizes_advanced', __CLASS__ . '::remove_thumbnail_generation' );

				$attach_ids  = array();

				$ticket_field = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsc_ticketmeta WHERE ticket_id= %d AND meta_key = 'congressional_file'", $request_id ) );

				if ( ! empty( $ticket_field ) && isset( $ticket_field->id ) && isset( $ticket_field->meta_value ) ) {
					$ticket_meta_value = json_decode( $ticket_field->meta_value, true );
					if ( ! empty( $ticket_meta_value ) ) {
						$attach_ids = array_merge( $attach_ids, $ticket_meta_value );
					}
				}

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

				if ( empty( $ticket_field ) && ! isset( $ticket_field->id ) ) {
					$wpdb->insert(
						$wpdb->prefix . 'wpsc_ticketmeta',
						array(
							'ticket_id'  => $request_id,
							'meta_key'   => 'congressional_file',
							'meta_value' => json_encode( $attach_ids ),
						)
					);
				} else {
					$wpscfunction->update_ticket_meta( $request_id, 'congressional_file', array( 'meta_value' => json_encode( $attach_ids ) ) );
				}

				remove_filter( 'upload_dir', __CLASS__ . '::change_congressional_upload_dir' );
				remove_filter( 'intermediate_image_sizes_advanced', __CLASS__ . '::remove_thumbnail_generation' );
			}

			if ( is_array( $foia_files ) && count( $foia_files ) > 0 ) {
				add_filter( 'upload_dir', __CLASS__ . '::change_foia_upload_dir' );
				add_filter( 'intermediate_image_sizes_advanced', __CLASS__ . '::remove_thumbnail_generation' );

				$attach_ids  = array();

				$ticket_field = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsc_ticketmeta WHERE ticket_id= %d AND meta_key = 'foia_file'", $request_id ) );

				if ( ! empty( $ticket_field ) && isset( $ticket_field->id ) && isset( $ticket_field->meta_value ) ) {
					$ticket_meta_value = json_decode( $ticket_field->meta_value, true );
					if ( ! empty( $ticket_meta_value ) ) {
						$attach_ids = array_merge( $attach_ids, $ticket_meta_value );
					}
				}

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

				if ( empty( $ticket_field ) && ! isset( $ticket_field->id ) ) {
					$wpdb->insert(
						$wpdb->prefix . 'wpsc_ticketmeta',
						array(
							'ticket_id'  => $request_id,
							'meta_key'   => 'foia_file',
							'meta_value' => json_encode( $attach_ids ),
						)
					);
				} else {
					$wpscfunction->update_ticket_meta( $request_id, 'foia_file', array( 'meta_value' => json_encode( $attach_ids ) ) );
				}

				remove_filter( 'upload_dir', __CLASS__ . '::change_foia_upload_dir' );
				remove_filter( 'intermediate_image_sizes_advanced', __CLASS__ . '::remove_thumbnail_generation' );
			}

			$response = array(
				'sucess_status' => 1,
				'messege'       => 'Saved Successfully.',
				'destruction_approval_warning' => $destruction_approval_warning,
			);

			echo wp_json_encode( $response );
			die();
		}


		/**
		 * Change upload path
		 *
		 * @param  Array $dir Upload directory information as array.
		 */
		public static function change_destruction_auth_upload_dir( $dir ) {

			$dir['path']   = $dir['basedir'] . '/destruction-authorizations';
			$dir['url']    = $dir['baseurl'] . '/destruction-authorizations';
			$dir['subdir'] = '/destruction-authorizations';

			return $dir;
		}

		/**
		 * Change upload path
		 *
		 * @param  Array $dir Upload directory information as array.
		 */
		public static function change_litig_letter_upload_dir( $dir ) {

			$dir['path']   = $dir['basedir'] . '/litigation-letters';
			$dir['url']    = $dir['baseurl'] . '/litigation-letters';
			$dir['subdir'] = '/litigation-letters';

			return $dir;
		}

		/**
		 * Change upload path
		 *
		 * @param  Array $dir Upload directory information as array.
		 */
		public static function change_congressional_upload_dir( $dir ) {

			$dir['path']   = $dir['basedir'] . '/congressional';
			$dir['url']    = $dir['baseurl'] . '/congressional';
			$dir['subdir'] = '/congressional';

			return $dir;
		}


		/**
		 * Change upload path
		 *
		 * @param  Array $dir Upload directory information as array.
		 */
		public static function change_foia_upload_dir( $dir ) {

			$dir['path']   = $dir['basedir'] . '/foia';
			$dir['url']    = $dir['baseurl'] . '/foia';
			$dir['subdir'] = '/foia';

			return $dir;
		}

		/**
		 * Delete Destruction Authorization image
		 */
		public static function delete_destruction_authorization() {

			global $wpscfunction, $wpdb;

			$request_id = isset( $_POST['request_id'] ) ? sanitize_text_field( wp_unslash( $_POST['request_id'] ) ) : ''; //phpcs:ignore

			$attachment_id = isset( $_POST['attachment_id'] ) ? sanitize_text_field( wp_unslash( $_POST['attachment_id'] ) ) : ''; //phpcs:ignore

			wp_delete_attachment( $attachment_id );

			$ticket_field = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsc_ticketmeta WHERE ticket_id= %d AND meta_key = 'destruction_authorizations_image'", $request_id ) );

			$ticket_field_metavalue = json_decode( $ticket_field->meta_value, true );

			$meta_key = array_search( $attachment_id, $ticket_field_metavalue );

			if ( false !== $meta_key ) {
				unset( $ticket_field_metavalue[ $meta_key ] );
				$wpscfunction->update_ticket_meta( $request_id, 'destruction_authorizations_image', array( 'meta_value' => json_encode( $ticket_field_metavalue ) ) );

				$destruction_approval_status = ( count( $ticket_field_metavalue ) === intval( Patt_Custom_Func::get_accession_count( $request_id ) ) ? 1 : 0 );

				$wpdb->update( "{$wpdb->prefix}wpsc_ticket", array( 'destruction_approval' => $destruction_approval_status ), array( 'id' => $request_id ), array( '%d' ), array( '%d' ) );

				echo wp_json_encode(
					array(
						'sucess_status' => 1,
						'messege'       => 'Destruction Authorization file deleted successfully.',
					)
				);
			} else {
				echo wp_json_encode(
					array(
						'sucess_status' => 0,
						'messege'       => 'Error deleting file.',
					)
				);
			}
			die();
		}

		/**
		 * Delete Litigation Letter image
		 */
		public static function delete_litigation_letter() {

			global $wpscfunction, $wpdb;

			$request_id = isset( $_POST['request_id'] ) ? sanitize_text_field( wp_unslash( $_POST['request_id'] ) ) : ''; //phpcs:ignore
			$attachment_id = isset( $_POST['attachment_id'] ) ? sanitize_text_field( wp_unslash( $_POST['attachment_id'] ) ) : ''; //phpcs:ignore

			wp_delete_attachment( $attachment_id );

			$ticket_field = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsc_ticketmeta WHERE ticket_id= %d AND meta_key = 'litigation_letter_image'", $request_id ) );

			$ticket_field_metavalue = json_decode( $ticket_field->meta_value, true );

			$meta_key = array_search( $attachment_id, $ticket_field_metavalue );

			if ( false !== $meta_key ) {
				unset( $ticket_field_metavalue[ $meta_key ] );
				$wpscfunction->update_ticket_meta( $request_id, 'litigation_letter_image', array( 'meta_value' => json_encode( $ticket_field_metavalue ) ) );

				if ( count( $ticket_field_metavalue ) === 0 ) {
					$wpdb->update( "{$wpdb->prefix}wpsc_ticket", array( 'freeze_approval' => 0 ), array( 'id' => $request_id ), array( '%d' ), array( '%d' ) );
				}

				echo wp_json_encode(
					array(
						'sucess_status' => 1,
						'messege'       => 'Litigation Letter file deleted successfully.',
					)
				);
			} else {
				echo wp_json_encode(
					array(
						'sucess_status' => 0,
						'messege'       => 'Error deleting file.',
					)
				);
			}

			die();
		}


		/**
		 * Delete Congressional Files
		 */
		public static function delete_congressional() {

			global $wpscfunction, $wpdb;

			$request_id = isset( $_POST['request_id'] ) ? sanitize_text_field( wp_unslash( $_POST['request_id'] ) ) : ''; //phpcs:ignore
			$attachment_id = isset( $_POST['attachment_id'] ) ? sanitize_text_field( wp_unslash( $_POST['attachment_id'] ) ) : ''; //phpcs:ignore

			wp_delete_attachment( $attachment_id );

			$ticket_field = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsc_ticketmeta WHERE ticket_id= %d AND meta_key = 'congressional_file'", $request_id ) );

			$ticket_field_metavalue = json_decode( $ticket_field->meta_value, true );

			$meta_key = array_search( $attachment_id, $ticket_field_metavalue );

			if ( false !== $meta_key ) {
				unset( $ticket_field_metavalue[ $meta_key ] );
				$wpscfunction->update_ticket_meta( $request_id, 'congressional_file', array( 'meta_value' => json_encode( $ticket_field_metavalue ) ) );

				if ( count( $ticket_field_metavalue ) === 0 ) {
					$wpdb->update( "{$wpdb->prefix}wpsc_ticket", array( 'congressional_approval' => 0 ), array( 'id' => $request_id ), array( '%d' ), array( '%d' ) );
				}

				echo wp_json_encode(
					array(
						'sucess_status' => 1,
						'messege'       => 'Congressional file deleted successfully.',
					)
				);
			} else {
				echo wp_json_encode(
					array(
						'sucess_status' => 0,
						'messege'       => 'Error deleting file.',
					)
				);
			}

			die();
		}

		/**
		 * Delete FOIA Files
		 */
		public static function delete_foia() {

			global $wpscfunction, $wpdb;

			$request_id = isset( $_POST['request_id'] ) ? sanitize_text_field( wp_unslash( $_POST['request_id'] ) ) : ''; //phpcs:ignore
			$attachment_id = isset( $_POST['attachment_id'] ) ? sanitize_text_field( wp_unslash( $_POST['attachment_id'] ) ) : ''; //phpcs:ignore

			wp_delete_attachment( $attachment_id );

			$ticket_field = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsc_ticketmeta WHERE ticket_id= %d AND meta_key = 'foia_file'", $request_id ) );

			$ticket_field_metavalue = json_decode( $ticket_field->meta_value, true );

			$meta_key = array_search( $attachment_id, $ticket_field_metavalue );

			if ( false !== $meta_key ) {
				unset( $ticket_field_metavalue[ $meta_key ] );
				$wpscfunction->update_ticket_meta( $request_id, 'foia_file', array( 'meta_value' => json_encode( $ticket_field_metavalue ) ) );

				if ( count( $ticket_field_metavalue ) === 0 ) {
					$wpdb->update( "{$wpdb->prefix}wpsc_ticket", array( 'foia_approval' => 0 ), array( 'id' => $request_id ), array( '%d' ), array( '%d' ) );
				}

				echo wp_json_encode(
					array(
						'sucess_status' => 1,
						'messege'       => 'FOIA file deleted successfully.',
					)
				);
			} else {
				echo wp_json_encode(
					array(
						'sucess_status' => 0,
						'messege'       => 'Error deleting file.',
					)
				);
			}

			die();
		}

		/**
		 * Remove thumbnail generation
		 */
		public static function remove_thumbnail_generation() {
			return array();
		}

	}

	/**
	 * Calling init function to activate hooks and filters.
	 */
	Wppatt_Request_Approval_Widget::init();
}
