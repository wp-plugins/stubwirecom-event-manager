<?php
class SW_EventList extends WP_Widget {
    function __construct() {
		$widget_opts = array("description"=>"A short list of upcoming events");
		parent::__construct(false,"Stubwire Events",$widget_opts);
	}

	function widget($args,$instance) {
		$title = apply_filters('widget_title', $instance['title']);

		echo $args['before_widget'];
		if ($title) {
			echo $args['before_title'];
			echo $title;
			echo $args['after_title'];
		}

		//echo "<pre>".print_r($instance,true)."</pre>";return;
		$events = stubwire_select_events($instance);

		//echo "<h1>class-sw-eventlist.php - about to call locate_stubwire_template</H1>";
		$template = locate_stubwire_template($instance['template']);
		//echo "<h1>class-sw-eventlist.php - calling locate_stubwire_template</H1>";
		
		if (isset($template['filepath']) && !empty($template['filepath'])) {
			//echo "<h1>class-sw-eventlist.php - widget - filepath NOT EMPTY</H1>";
			include($template['filepath']);
		}	elseif (isset($template['template']) && !empty($template['template'])) {
			//echo "<h1>class-sw-eventlist.php - widget - content NOT EMPTY</H1>";
			eval($template['template']);
		} else {
			//echo "<h1>class-sw-eventlist.php - widget - LOADING DEFAULT</H1>";
			$this->_default_template();
		}

		echo $args['after_widget'];
	}

	function _default_template() {
		$template = locate_stubwire_template('file:default_widget_listing');

		if (isset($template['template']) && !empty($template['template'])) {
			//echo "<h1>class-sw-eventlist.php - widget - content NOT EMPTY</H1>";
			eval($template['template']);
		} else {
			echo "<ul>";
			while($event = stubwire_get_event()) {
				echo "<li>{$event['name']}</li>";
				stubwire_next_event();
			}
			echo "</ul>";
		}
	}

	function form($instance) {
		global $stubWire;
		
		/*$formTitle = "Upcoming Events";
		$formTemplate = "default_widget_listing";
		$formPageLen = "25";
		$formWhere = "dateTime > NOW()";
		$formOrder = "dateTime ASC";
		
		echo "<p><label for=\"" . $this->get_field_id('title') . "\">Title: </label><br/>";
		echo "<input id=\"" . $this->get_field_id('title') . "\" name=\"" . $this->get_field_name('title') . "\" class=\"widefat\" value=\"" . $formTitle . "\" /></p>";

		echo "<p><label for=\"" . $this->get_field_id('template') . "\">Template: </label><br/>";
		foreach ($stubWire->get_StubWireAllTemplates() as $template)	{
			echo "File:" . $template['filename'] . "<br>";
		}
		echo "<input id=\"" . $this->get_field_id('template') . "\" name=\"" . $this->get_field_name('template') . "\" class=\"widefat\" value=\"" . $formTemplate . "\" /></p>";

		echo "<p><label for=\"" . $this->get_field_id('page_len') . "\">Number of Records: </label><br/>";
		echo "<input id=\"" . $this->get_field_id('page_len') . "\" name=\"" . $this->get_field_name('page_len') . "\" class=\"widefat\" value=\"" . $formPageLen . "\" /></p>";

		echo "<p><label for=\"" . $this->get_field_id('where') . "\">Where Statement: </label><br/>";
		echo "<input id=\"" . $this->get_field_id('where') . "\" name=\"" . $this->get_field_name('where') . "\" class=\"widefat\" value=\"" . $formWhere . "\" /></p>";

		echo "<p><label for=\"" . $this->get_field_id('order') . "\">Order By: </label><br/>";
		echo "<input id=\"" . $this->get_field_id('order') . "\" name=\"" . $this->get_field_name('order') . "\" class=\"widefat\" value=\"" . $formOrder . "\" /></p>";*/
		
		$defaults = array(
			'title' => 'Events',
			'page_len' => 75,
			'where' => 'dateTime > NOW() AND eventStatus<>\'Canceled - Hidden\'',
			'order' => 'dateTime ASC',
			'template' => 'basic_widget_listing',
		);
		$instance = wp_parse_args((array)$instance,$defaults);
		$fields = array(
			"title"=>"Title",
			"template"=>"Template file (optional)",
			"page_len"=>"Number of items",
			"where"=>"Events To Pull",
			"order"=>"How the events are ordered",
		);
		
		/*echo "<h1>Fields</h1>";
		echo "<pre>";
		print_r($fields);
		echo "</pre>";
		
		echo "<h1>instance</h1>";
		echo "<pre>";
		print_r($instance);
		echo "</pre>";*/
		
		foreach ($fields as $k=>$v) {
			/*echo "<hr>";
			echo "<pre>";
			print_r($k);
			echo "</pre>";
			echo "<pre>";
			print_r($v);
			echo "</pre>";
			echo "<hr>";*/
			$id = $this->get_field_id($k);
			$name = $this->get_field_name($k);
			$label = $v;
			echo "<p><label for=\"{$id}\">{$label}: </label><br/>";
			echo "<input id=\"{$id}\" name=\"{$name}\" class=\"widefat\" value=\"{$instance[$k]}\" /></p>";
		}
	 }

	 function update($new,$old) {
		 return $new;
	 }
}