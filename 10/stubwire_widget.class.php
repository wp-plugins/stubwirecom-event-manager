<?php
class StubWireWidget extends WP_Widget {
     function StubWireWidget() {
				/* Widget settings. */
				$widget_ops = array( 'classname' => 'stubwire', 'description' => 'Displays a list of your upcoming events' );
		
				/* Widget control settings. */
				$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'stubwire-widget' );
		
				/* Create the widget. */
				$this->WP_Widget( 'stubwire-widget', 'StubWire Events', $widget_ops, $control_ops );
     }

     function widget($args, $instance) {
				extract( $args );
				
				/* User-selected settings. */
				$title = apply_filters('widget_title', $instance['title'] );
				$name = $instance['name'];
				$sex = $instance['sex'];
				$show_sex = isset( $instance['show_sex'] ) ? $instance['show_sex'] : false;
				
				/* Before widget (defined by themes). */
				echo $before_widget;
				
				/* Title of widget (before and after defined by themes). */
				if ( $title )
					echo $before_title . $title . $after_title;
				
				/* Display name from widget settings. */
				if ( $name )
					echo '<p>Hello.  My name is' . $name . '.</p>';
				
				/* Show sex. */
				if ( $show_sex )
					echo '<p>I am a ' . $sex . '.</p>';
				
				/* After widget (defined by themes). */
				echo $after_widget;
     }

     function update($new_instance, $old_instance) {
				$instance = $old_instance;
		
				/* Strip tags (if needed) and update the widget settings. */
				$instance['title'] = strip_tags( $new_instance['title'] );
				$instance['name'] = strip_tags( $new_instance['name'] );
				$instance['sex'] = $new_instance['sex'];
				$instance['show_sex'] = $new_instance['show_sex'];
		
				return $instance;
     }

     function form($instance) {
				/* Set up some default widget settings. */
				$defaults = array( 'title' => 'Example', 'name' => 'John Doe', 'sex' => 'male', 'show_sex' => true );
				$instance = wp_parse_args( (array) $instance, $defaults );
				?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'name' ); ?>">Your Name:</label>
			<input id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" value="<?php echo $instance['name']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'sex' ); ?>">Sex:</label>
			<select id="<?php echo $this->get_field_id( 'sex' ); ?>" name="<?php echo $this->get_field_name( 'sex' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'male' == $instance['format'] ) echo 'selected="selected"'; ?>>male</option>
				<option <?php if ( 'female' == $instance['format'] ) echo 'selected="selected"'; ?>>female</option>
			</select>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_sex'], true ); ?> id="<?php echo $this->get_field_id( 'show_sex' ); ?>" name="<?php echo $this->get_field_name( 'show_sex' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_sex' ); ?>">Display sex publicly?</label>
		</p>
		<?
     }
}