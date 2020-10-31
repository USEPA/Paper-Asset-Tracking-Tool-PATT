<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class vskb_widget extends WP_Widget {
	// constructor 
	public function __construct() {
		$widget_ops = array( 'classname' => 'vskb-widget', 'description' => esc_attr__('Display your categories and posts in a widget.', 'very-simple-knowledge-base') );
		parent::__construct( 'vskb_widget', esc_attr__('Very Simple Knowledge Base', 'very-simple-knowledge-base'), $widget_ops );
	}

	// set widget in dashboard
	function form( $instance ) {
		$instance = wp_parse_args( $instance, array(
			'vskb_title' => '',
			'vskb_text' => '',
			'vskb_shortcode' => '',
			'vskb_attributes' => ''
		));
		$vskb_title = !empty( $instance['vskb_title'] ) ? $instance['vskb_title'] : __('Very Simple Knowledge Base', 'very-simple-knowledge-base');
		$vskb_text = $instance['vskb_text'];
		$vskb_shortcode = $instance['vskb_shortcode'];
		$vskb_attributes = $instance['vskb_attributes'];

		// widget input fields
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'vskb_title' ); ?>"><?php esc_attr_e('Title', 'very-simple-knowledge-base'); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'vskb_title' ); ?>" name="<?php echo $this->get_field_name( 'vskb_title' ); ?>" type="text" value="<?php echo esc_attr( $vskb_title ); ?>">
 		</p>
		<p>
		<label for="<?php echo $this->get_field_id('vskb_text'); ?>"><?php esc_attr_e('Text above Knowledge Base', 'very-simple-knowledge-base'); ?>:</label>
		<textarea class="widefat monospace" rows="6" cols="20" id="<?php echo $this->get_field_id('vskb_text'); ?>" name="<?php echo $this->get_field_name('vskb_text'); ?>"><?php echo wp_kses_post( $vskb_text ); ?></textarea>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'vskb_shortcode' ); ?>"><?php esc_attr_e( 'List', 'very-simple-knowledge-base' ); ?>:</label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'vskb_shortcode' ); ?>" name="<?php echo $this->get_field_name( 'vskb_shortcode' ); ?>">
			<option value='one'<?php echo ($vskb_shortcode == 'one')?'selected':''; ?>><?php esc_attr_e( 'One column', 'very-simple-knowledge-base' ); ?></option>
			<option value='two'<?php echo ($vskb_shortcode == 'two')?'selected':''; ?>><?php esc_attr_e( 'Two columns', 'very-simple-knowledge-base' ); ?></option>
			<option value='three'<?php echo ($vskb_shortcode == 'three')?'selected':''; ?>><?php esc_attr_e( 'Three columns', 'very-simple-knowledge-base' ); ?></option>
			<option value='four'<?php echo ($vskb_shortcode == 'four')?'selected':''; ?>><?php esc_attr_e( 'Four columns', 'very-simple-knowledge-base' ); ?></option>
		</select>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'vskb_attributes' ); ?>"><?php esc_attr_e('Attributes', 'very-simple-knowledge-base'); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'vskb_attributes' ); ?>" name="<?php echo $this->get_field_name( 'vskb_attributes' ); ?>" type="text" placeholder="<?php esc_attr_e( 'Example: posts_per_page=&quot;2&quot;', 'very-simple-knowledge-base' ); ?>" value="<?php echo esc_attr( $vskb_attributes ); ?>">
 		</p>
		<?php $link_label = __( 'click here', 'very-simple-knowledge-base' ); ?>
		<?php $link_wp = '<a href="https://wordpress.org/plugins/very-simple-knowledge-base" target="_blank">'.$link_label.'</a>'; ?>
		<p><?php printf( esc_attr__( 'For info, available attributes and support %s.', 'very-simple-knowledge-base' ), $link_wp ); ?></p>
		<?php
	}

	// update widget
	function update( $new_instance, $old_instance ) {
		$instance = array();

		// sanitize input
		$instance['vskb_title'] = sanitize_text_field( $new_instance['vskb_title'] );
		$instance['vskb_text'] = wp_kses_post( $new_instance['vskb_text'] );
		$instance['vskb_shortcode'] = sanitize_text_field( $new_instance['vskb_shortcode'] );
		$instance['vskb_attributes'] = sanitize_text_field( $new_instance['vskb_attributes'] );

		return $instance;
	}

	// display widget with knowledge base in frontend
	function widget( $args, $instance ) {
		echo $args['before_widget'];

		if ( !empty( $instance['vskb_title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', esc_attr($instance['vskb_title']) ). $args['after_title'];
		}

		if ( !empty( $instance['vskb_text'] ) ) {
			echo '<div class="vskb-widget-text">'.wpautop( wp_kses_post($instance['vskb_text']).'</div>');
		}

		if ( $instance['vskb_shortcode'] == 'four' ) {
			$content = '[knowledgebase ';
		} else if ( $instance['vskb_shortcode'] == 'three' ) {
			$content = '[knowledgebase-three ';
		} else if ( $instance['vskb_shortcode'] == 'two' ) {
			$content = '[knowledgebase-two ';
		} else {
			$content = '[knowledgebase-one ';
		}
		if ( !empty( $instance['vskb_attributes'] ) ) {
			$content .= wp_strip_all_tags($instance['vskb_attributes']);
		}
		$content .= ']';
		echo do_shortcode( $content );

		echo $args['after_widget'];
	}
}
