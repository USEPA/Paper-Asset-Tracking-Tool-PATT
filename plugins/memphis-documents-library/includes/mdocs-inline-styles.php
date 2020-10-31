<?php
function mdocs_get_inline_css() {
	$num_show = 0;
	$show_array = get_option('mdocs-displayed-file-info');
	foreach($show_array as $value) if(isset($value['show']) && $value['show'] == true) $num_show++;
	$mdocs_font_size = get_option('mdocs-font-size');
	$download_button_color = get_option('mdocs-download-text-color-normal');
	$download_button_bg = get_option('mdocs-download-color-normal'); 
	$download_button_hover_color = get_option('mdocs-download-text-color-hover');
	$download_button_hover_bg = get_option('mdocs-download-color-hover');
	$navbar_bg_color = get_option('mdocs-navbar-bgcolor');
	$navbar_border_color = get_option('mdocs-navbar-bordercolor');
	$navbar_text_color_normal = get_option('mdocs-navbar-text-color-normal');
	$navbar_text_color_hover = get_option('mdocs-navbar-text-color-hover');
	$highlight_color_new_normal = get_option('mdocs-file-highlight-color-new-normal');
	$highlight_color_new_hover = get_option('mdocs-file-highlight-color-new-hover');
	$highlight_color_updated_normal = get_option('mdocs-file-highlight-color-updated-normal');
	$highlight_color_updated_hover = get_option('mdocs-file-highlight-color-updated-hover');
	if(get_option('mdocs-override-post-title-font-size') == true) $mdocs_post_title_font_size = 'font-size: '.get_option('mdocs-post-title-font-size').'px;';
	else $mdocs_post_title_font_size = '';
	if(get_option('mdocs-post-show-title') == false) $mdocs_post_show_title = 'display: none;';
	else $mdocs_post_show_title = '';
	if(get_option('mdocs-hide-entry-div') == true) $mdocs_hide_entry_div = 'display: none !important;';
	else $mdocs_hide_entry_div = '';
	$document_list_style = '';
	foreach(get_option('mdocs-displayed-file-info') as $index => $file_info) {
		$document_list_style .= "#mdocs-list-table .mdocs-".$file_info['slug']." { width: ".$file_info['width']."%; }\n\t\t";
	}
	$set_inline_style = "
		/*body { background: inherit; } CAN'T REMEMBER WHY I PUT THIS IN?*/
		dd, li { margin: 0; }
		$document_list_style.mdocs-download-btn-config:hover { background: $download_button_hover_bg; color: $download_button_hover_color; }
		.mdocs-download-btn-config { color: $download_button_color; background: $download_button_bg ; }
		.mdocs-download-btn, .mdocs-download-btn:active { border: solid 1px $download_button_color !important; color: $download_button_color !important; background: $download_button_bg !important;  }
		.mdocs-download-btn:hover { background: $download_button_hover_bg !important; color: $download_button_hover_color !important;}
		.mdocs-container table, .mdocs-show-container, .mdocs-versions-body, .mdocs-container table #desc p { font-size: ".$mdocs_font_size."px !important; }
		.mdocs-navbar-default { background-color: $navbar_bg_color; border: solid $navbar_border_color 1px; }
		.mdocs-navbar-default .navbar-nav > li > a, .mdocs-navbar-default .navbar-brand { color: $navbar_text_color_normal; }
		.mdocs-navbar-default .navbar-nav > li > a:hover,
		.mdocs-navbar-default .navbar-brand:hover,
		.mdocs-navbar-default .navbar-nav > li > a:focus { color: $navbar_text_color_hover; }
		.mdocs-tooltip { list-style: none; }
		#mdocs-post-title { $mdocs_post_title_font_size $mdocs_post_show_title}
		.entry-summary { $mdocs_hide_entry_div }
		.table > thead > tr > td.mdocs-success,
		.table > tbody > tr > td.mdocs-success,
		.table > tfoot > tr > td.mdocs-success,
		.table > thead > tr > th.mdocs-success,
		.table > tbody > tr > th.mdocs-success,
		.table > tfoot > tr > th.mdocs-success,
		.table > thead > tr.mdocs-success > td,
		.table > tbody > tr.mdocs-success > td,
		.table > tfoot > tr.mdocs-success > td,
		.table > thead > tr.mdocs-success > th,
		.table > tbody > tr.mdocs-success > th,
		.table > tfoot > tr.mdocs-success > th {
		  background-color: $highlight_color_new_normal;
		}
		.table-hover > tbody > tr > td.mdocs-success:hover,
		.table-hover > tbody > tr > th.mdocs-success:hover,
		.table-hover > tbody > tr.mdocs-success:hover > td,
		.table-hover > tbody > tr:hover > .mdocs-success,
		.table-hover > tbody > tr.mdocs-success:hover > th {
		  background-color: $highlight_color_new_hover;
		}
		.table > thead > tr > td.mdocs-info,
		.table > tbody > tr > td.mdocs-info,
		.table > tfoot > tr > td.mdocs-info,
		.table > thead > tr > th.mdocs-info,
		.table > tbody > tr > th.mdocs-info,
		.table > tfoot > tr > th.mdocs-info,
		.table > thead > tr.mdocs-info > td,
		.table > tbody > tr.mdocs-info > td,
		.table > tfoot > tr.mdocs-info > td,
		.table > thead > tr.mdocs-info > th,
		.table > tbody > tr.mdocs-info > th,
		.table > tfoot > tr.mdocs-info > th {
		  background-color: $highlight_color_updated_normal;
		}
		.table-hover > tbody > tr > td.mdocs-info:hover,
		.table-hover > tbody > tr > th.mdocs-info:hover,
		.table-hover > tbody > tr.mdocs-info:hover > td,
		.table-hover > tbody > tr:hover > .mdocs-info,
		.table-hover > tbody > tr.mdocs-info:hover > th {
		  background-color: $highlight_color_updated_hover;
		}
		.mdocs table td,.mdocs table th { border: none; }
		.mdocs a { text-decoration: none !important; }
		.form-group-lg select.form-control { line-height: inherit !important; }
	";
	//	TWENTY SIXTEEN FIX
	if(wp_get_theme() == "Twenty Sixteen") $set_inline_style .= "html { font-size: inherit !important; }";
	return $set_inline_style;
}
?>