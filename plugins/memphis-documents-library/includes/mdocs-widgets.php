<?php
function mdocs_widgets() {
	register_widget( 'mdocs_top_downloads' );
	register_widget( 'mdocs_top_rated' );
	register_widget( 'mdocs_last_updated' );
}
class mdocs_last_updated extends WP_Widget {
	function __construct() {
		// Instantiate the parent object
		parent::__construct( 'mdocs_last_updated', 'Memphis Last Updated' );
	}
	function widget( $args, $instance ) {
		if(!isset($instance['lu-count'])) $instance['lu-count'] = 5;
		$mdocs = get_option('mdocs-list');
		$the_list  = mdocs_array_sort($mdocs,'modified', SORT_DESC, true);
		extract($args, EXTR_SKIP);
		echo $before_widget;
		echo $before_title;
		//Display title as stored in this instance of the widget
		if(get_option('mdocs-hide-widget-titles') == false) _e('Last Updated', 'memphis-documents-library');
		echo $after_title;
		?>
		<table class="table table-condensed mdocs-widget-table">
			<tr>
				<?php if(get_option('mdocs-hide-widget-column-titles') == false) { ?>
				<?php if(get_option('mdocs-hide-widget-numbers') == false) { ?><th></th><?php } ?>
				<th>File</th>
				<th>Date</th>
				<?php } ?>
			</tr>
		<?php
		$item_index = 1;
		for($i=0; $i< $instance['lu-count']; $i++) {
			if(!isset($the_list[$i])) break;
			if($the_list[$i]['file_status'] == 'public' || isset($instance['lu-show-all']) && $instance['lu-show-all'] == 'on') {
				$permalink = mdocs_get_permalink($the_list[$i]['parent']);
				echo '<tr>';
				if(get_option('mdocs-hide-widget-numbers') == false) echo '<td >'.($item_index).'.</td>';
				echo '<td><a href="'.$permalink.'null" >'.$the_list[$i]['name'].'</a></td>';
				echo '<td class="mdocs-widget-date" ><small>'.date(get_option('mdocs-date-format'), $the_list[$i]['modified']).'</small></td>';
				echo '</tr>';
				$item_index++;
			} else $instance['lu-count']++;
		}
		?>
		</table>
		<?php
		echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['lu-count'] = strip_tags( $new_instance['lu-count'] );
		$instance['lu-show-all'] = strip_tags( $new_instance['lu-show-all'] );
		return $instance;
	}
	function form( $instance ) {
		if(!isset($instance['lu-count'])) $instance['lu-count'] = 5;
		if(!isset($instance['lu-show-all'])) $instance['lu-show-all'] = '';
		?>
		<p>
			<label>Number of Items in List:</label>
			<input style="width: 100%;" type="text" name="<?php echo $this->get_field_name('lu-count'); ?>" value="<?php echo esc_attr($instance['lu-count']); ?>" />
			<input type="checkbox" name="<?php echo $this->get_field_name('lu-show-all'); ?>" <?php echo checked($instance['lu-show-all'], 'on'); ?>/> <?php _e('Show All File', 'memphis-documents-library'); ?>
		</p>
		<?php
	}
}
class mdocs_top_rated extends WP_Widget {
	function __construct() {
		// Instantiate the parent object
		parent::__construct( 'mdocs_top_rated', 'Memphis Top Rated' );
	}
	function widget( $args, $instance ) {
		if(!isset($instance['tr-count'])) $instance['tr-count'] = 5;
		$mdocs = get_option('mdocs-list');
		$the_list  = mdocs_array_sort($mdocs,'rating', SORT_DESC, true);
		extract($args, EXTR_SKIP);
		echo $before_widget;
		echo $before_title;
		if(get_option('mdocs-hide-widget-titles') == false)  _e('Top Rated', 'memphis-documents-library'); 
		echo $after_title;
		?>
		<table class="table table-condensed mdocs-widget-table">
			<tr>
				<?php if(get_option('mdocs-hide-widget-column-titles') == false) { ?>
				<?php if(get_option('mdocs-hide-widget-numbers') == false) { ?><th></th><?php } ?>
				<th><?php _e('File', 'memphis-documents-library'); ?></th>
				<th><?php _e('Rating', 'memphis-documents-library'); ?></th>
				<?php } ?>
			</tr>
		<?php
		$item_index = 1;
		for($i=0; $i< $instance['tr-count']; $i++) {
			if(!isset($the_list[$i])) break;
			if($the_list[$i]['file_status'] == 'public' || isset($instance['tr-show-all']) && $instance['tr-show-all'] == 'on') {
				$permalink = mdocs_get_permalink($the_list[$i]['parent']);
				echo '<tr>';
				if(get_option('mdocs-hide-widget-numbers') == false) echo '<td >'.($item_index).'.</td>';
				echo '<td><a href="'.$permalink.'null" >'.$the_list[$i]['name'].'</a></td>';
				echo '<td class="mdocs-widget-rating"><small>';
				for($j=1;$j<=5;$j++) {
					if($the_list[$i]['rating'] >= $j) echo '<i class="fas fa-star fa-xs mdocs-gold" id="'.$j.'" aria-hidden="true"></i>';
					elseif(ceil($the_list[$i]['rating']) == $j ) echo '<i class="fas fa-star-half-alt fa-xs mdocs-gold" id="'.$j.'" aria-hidden="true"></i>';
					else echo '<i class="far fa-star fa-xs" id="'.$j.'" aria-hidden="true"></i>';
				}
				echo '</small></td>';
				echo '</tr>';
				$item_index++;
			} else $instance['tr-count']++;
		}
		?>
		</table>
		<?php
		echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['tr-count'] = strip_tags( $new_instance['tr-count'] );
		$instance['tr-show-all'] = strip_tags( $new_instance['tr-show-all'] );
		return $instance;
	}
	function form( $instance ) {
		if(!isset($instance['tr-count'])) $instance['tr-count'] = 5;
		if(!isset($instance['tr-show-all'])) $instance['tr-show-all'] = '';
		?>
		<p>
			<label>Number of Items in List:</label>
			<input style="width: 100%;" type="text" name="<?php echo $this->get_field_name('tr-count'); ?>" value="<?php echo esc_attr($instance['tr-count']); ?>" />
			<input type="checkbox" name="<?php echo $this->get_field_name('tr-show-all'); ?>" <?php echo checked($instance['tr-show-all'], 'on'); ?>/> <?php _e('Show All File', 'memphis-documents-library'); ?>
		</p>
		<?php
	}
}
class mdocs_top_downloads extends WP_Widget {
	function __construct() {
		// Instantiate the parent object
		parent::__construct( 'mdocs_top_downloads', 'Memphis Top Downloads' );
	}
	function widget( $args, $instance ) {
		if(!isset($instance['td-count'])) $instance['td-count'] = 5;
		$mdocs = get_option('mdocs-list');
		$the_list  = mdocs_array_sort($mdocs,'downloads', SORT_DESC, true);
		extract($args, EXTR_SKIP);
		echo $before_widget;
		echo $before_title;
		if(get_option('mdocs-hide-widget-titles') == false) _e('Top Downloads', 'memphis-documents-library');
		echo $after_title;
		?>
		<table class="table table-condensed mdocs-widget-table">
			<tr>
				<?php if(get_option('mdocs-hide-widget-column-titles') == false) { ?>
				<?php if(get_option('mdocs-hide-widget-numbers') == false) { ?><th></th><?php } ?>
				<th>File</th>
				<th>DLs</th>
				<?php } ?>
			</tr>
		<?php
		$item_index = 1;
		for($i=0; $i< $instance['td-count']; $i++) {
			if(!isset($the_list[$i])) break;
			if($the_list[$i]['file_status'] == 'public' || isset($instance['td-show-all']) && $instance['td-show-all'] == 'on') {
				$permalink = mdocs_get_permalink( $the_list[$i]['parent']);
				echo '<tr>';
				if(get_option('mdocs-hide-widget-numbers') == false) echo '<td >'.($item_index).'.</td>';
				echo '<td><a href="'.$permalink.'null" >'.$the_list[$i]['name'].'</a></td>';
				echo '<td>'.$the_list[$i]['downloads'].'</td>';
				echo '</tr>';
				$item_index++;
			} else $instance['td-count']++;
		}
		?>
		</table>
		<?php
		echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['td-count'] = strip_tags( $new_instance['td-count'] );
		$instance['td-show-all'] = strip_tags( $new_instance['td-show-all'] );
		return $instance;
	}
	function form( $instance ) {
		if(!isset($instance['td-count'])) $instance['td-count'] = 5;
		if(!isset($instance['td-show-all'])) $instance['td-show-all'] = '';
		?>
		<p>
			<label>Number of Items in List:</label>
			<input style="width: 100%;" type="text" name="<?php echo $this->get_field_name('td-count'); ?>" value="<?php echo esc_attr($instance['td-count']); ?>" />
			<input type="checkbox" name="<?php echo $this->get_field_name('td-show-all'); ?>" <?php echo checked($instance['td-show-all'], 'on'); ?>/> <?php _e('Show All File', 'memphis-documents-library'); ?>
		</p>
		<?php
	}
}
?>