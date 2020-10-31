<?php
function mdocs_load_modals() {
	global $post;
	if(is_admin()) {
		load_preview_modal();
		load_ratings_modal();
		load_add_update_modal();
		load_share_modal();
		load_description_modal();
		//load_versions_modal();
		load_batch_edit_modal();
		load_batch_move_modal();
		load_batch_delete_modal();
		load_manage_version_modal();
	} elseif (isset($post) && has_shortcode( $post->post_content, 'mdocs' ) && $post->post_type != 'mdocs-posts' || isset($post) && has_shortcode( $post->post_content, 'mdocs_upload_btn' ) && $post->post_type != 'mdocs-posts') {
		load_preview_modal();
		load_ratings_modal();
		load_add_update_modal();
		load_share_modal();
		load_description_modal();
		load_versions_modal();
		load_batch_edit_modal();
		load_batch_move_modal();
		load_batch_delete_modal();
	}
}
function load_add_update_modal() {
	?>
	<div class="modal fade mdocs-modal" id="mdocs-add-update" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php _e('Close','memphis-documents-library'); ?></span></button>
					<div class="mdocs-add-update-body" id="mdocs-add-update-aria">
						<?php mdocs_uploader(is_admin()); ?>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','memphis-documents-library'); ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php
}
function load_description_modal() {
	?>
	<div class="modal fade mdocs-modal" id="mdocs-description-preview" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-body">
					
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php _e('Close','memphis-documents-library'); ?></span></button>
					<div class="mdocs-description-preview-body mdocs-modal-body mdocs-post"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','memphis-documents-library'); ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php
}
function load_preview_modal() {
	?>
	<div class="modal fade mdocs-modal" id="mdocs-file-preview" tabindex="-1" role="dialog" aria-hidden="true" >
		<div class="modal-dialog modal-lg" style="height: 100% !important;">
			<div class="modal-content">
				<div class="modal-body">
					<h4 id="mdocs-file-preview-aria"></h4>
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php _e('Close','memphis-documents-library'); ?></span></button>
					<div class="mdocs-file-preview-body mdocs-modal-body"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','memphis-documents-library'); ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function load_ratings_modal() {
	?>
	<div class="modal fade mdocs-modal" id="mdocs-rating" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<h4 id="mdocs-ratings-aria"></h4>
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php _e('Close','memphis-documents-library'); ?></span></button>
					<div class="mdocs-ratings-body mdocs-modal-body" ></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','memphis-documents-library'); ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php
}
function load_share_modal() {
	?>
	<div class="modal fade mdocs-modal" id="mdocs-share" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<h4 id="mdocs-share-aria"></h4>
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php _e('Close','memphis-documents-library'); ?></span></button>
					<div class="mdocs-share-body mdocs-modal-body" ></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','memphis-documents-library'); ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php
}
function load_batch_edit_modal() {
	?>
	<div class="modal fade mdocs-modal" id="mdocs-batch-edit" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php _e('Close','memphis-documents-library'); ?></span></button>
					<div class="mdocs-batch-edit-body  mdocs-batch-body mdocs-modal-body" id="mdocs-batch-edit-aria"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" id="test-test-test" onclick="mdocs_batch_edit_save();"><?php _e('Save', 'memphis-documents-library'); ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','memphis-documents-library'); ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php
}
function load_batch_move_modal() {
	?>
	<div class="modal fade mdocs-modal" id="mdocs-batch-move" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php _e('Close','memphis-documents-library'); ?></span></button>
					<div class="mdocs-batch-move-body mdocs-batch-body mdocs-modal-body" id="mdocs-batch-move-aria"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" onclick="mdocs_batch_move_save();"><?php _e('Move', 'memphis-documents-library'); ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','memphis-documents-library'); ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php
}
function load_batch_delete_modal() {
	?>
	<div class="modal fade mdocs-modal" id="mdocs-batch-delete" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php _e('Close','memphis-documents-library'); ?></span></button>
					<div class="mdocs-batch-delete-body mdocs-batch-body mdocs-modal-body" id="mdocs-batch-delete-aria"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" onclick="mdocs_batch_delete_save();"><?php _e('Delete', 'memphis-documents-library'); ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','memphis-documents-library'); ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php
}
function load_versions_modal() {
	?>
	<div class="modal fade mdocs-modal" id="mdocs-versions" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php _e('Close','memphis-documents-library'); ?></span></button>
					<div class="mdocs-versions-body mdocs-modal-body" id="mdocs-versions-aria"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','memphis-documents-library'); ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php
}
function load_manage_version_modal() {
	?>
	<div class="modal fade mdocs-modal" id="mdocs-manage-versions" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-body">
					
					<div class="mdocs-manage-versions-body mdocs-modal-body" id="mdocs-manage-versions-aria"></div>
				</div>
				<div class="modal-footer">
					
				</div>
			</div>
		</div>
	</div>
	<?php
}
?>