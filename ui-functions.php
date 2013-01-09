<?php
/**
 * Display-related stubwire functions.
 */
function stubwire_substitute_event($content)	{
	//$key_1_value = get_post_meta(76, 'key_1', true);
	return $content;
}
function stubwire_substitute_events($content) {
	return preg_replace_callback('/\[\[stubwire-events\s*(.*)\]\]/i','_stubwire_event_substitution',$content);
}

/**
 * Internal function to perform substitution
 * @param string $match matched string to replace
 */
function _stubwire_event_substitution($matches) {
	//echo "<h1>_stubwire_event_substitution STARTING</h1>\n\n";
	$arg_str = $matches[1];
	$arg_sets = array();
	$cfg = array( // defaults
		'template' => '{selectedevent}',
		'page_len' => 10,
		'page_skip' => 0,
	);
	// big hairy regex parses out key=value, key="value", key="my \"value\"", etc.
	preg_match_all('/([^=,\s]*)\s*=\s*("(?:\\\\.|[^"\\\\]+)*"|[^,"\s*]*)/',$arg_str,$arg_sets,PREG_SET_ORDER);
	foreach($arg_sets as $arg_set) {
		// get rid of begin & end quotes and quote escapes
		$cfg[strtolower($arg_set[1])] = preg_replace(array('/^"/','/"$/','/\\\\"/'),array('','','"'),$arg_set[2]);
	}

	//echo "<h1>ui-functions.php - about to call locate_stubwire_template</H1>";
	$template = locate_stubwire_template($cfg['template']);
	/*echo "<h1>ui-functions.php - called locate_stubwire_template</H1>";
	echo "<pre>";
	print_r($template);
	echo "</pre>";*/
	
	// fetch the event
	stubwire_select_events($cfg);

	/*if (isset($template['filepath']) && !empty($template['filepath'])) {
		echo "<h1>ui-functions.php - filepath not empty</H1>";
	}	elseif (isset($template['template']) && !empty($template['template'])) {
		echo "<h1>ui-functions.php - content not empty</H1>";
	} else {
		echo "<h1>ui-functions.php - loading default</H1>";
	}*/
	
	ob_start();
	if (isset($template['filepath']) && !empty($template['filepath'])) {
		include($template['filepath']);
	}	elseif (isset($template['template']) && !empty($template['template'])) {
		//echo "\n\n\n\n\n";
		$echoContent = print_r($template['template'], true);
		//echo "							<textarea rows=\"20\" cols=\"100\" name=\"TemplateContentHeader\" id=\"content\" tabindex=\"2\">" . $echoContent . "</textarea></p>\n";
		//echo "\n\n\n\n\n";
		//echo "\n\n\n\n\n";
		//echo "\n\n\n\n\n";
		//echo "\n\n\n\n\n";
		//echo $template['template'];
		//die;
		
		eval($template['template']);
	} else {
		_stubwire_event_template();
	}
	return ob_get_clean();
}

function locate_stubwire_template($filename)	{
	global $stubWire;
	
	// echo "<h1>locate_stubwire_template STARTING</h1>\n\n";
	
	//echo "<h1>ui-functions.php - locate_stubwire_template called</H1>";
	
	$template = array();
	$template['filepath'] = "";
	$template['code']			= "";

		//echo "<h1>TEMPLATETOLOAD</h1>";
		//echo "<h1>(" . $filename . ")(" . $idTemplate . ")</h1>";
		
	if ($filename=='{selectedevent}')	{
		$filename = get_option('stubwire_EventTemplate');
	}
	
	if (substr($filename, 0, 3)=='db:')	{
		$idTemplate = substr($filename, 3, strlen($filename));
		$dbTemplate = $stubWire->get_StubWireTemplate($idTemplate, "db");
		//content
		if (isset($dbTemplate[0]['template']) && !empty($dbTemplate[0]['template']))	{
			//echo "<h1>ui-functions.php - locate_stubwire_template - CONTENT NOT EMPTY</H1>";
			$template['template']			= $dbTemplate[0]['template'];
		}
		//echo "<h1>TEMPLATETOLOAD</h1>";
		//echo "<h1>(" . $filename . ")(" . $idTemplate . ")</h1>";
		/*echo "<pre>";
		print_r($dbTemplate);
		echo "</pre>";
		echo "<hr>";
		echo "<pre>";
		print_r($template['code']);
		echo "</pre>";
		die;*/
	}	else	{		
		$filename = str_replace("file:", "", $filename);
		
		// LETS PULL FROM THE FILE NAME
		$arrTemplate = $stubWire->get_StubWireTemplateInfo($filename);
		
		if (is_array($arrTemplate) && isset($arrTemplate['template']) && !empty($arrTemplate['template']))	{
			return $arrTemplate;
		}
		
		echo "Pulling the old way<br>";
		
		//$filename = substr($filename, 5, strlen($filename));
		$plugin_dir_path = dirname(__FILE__);
		
		// Lets build the file path
		$template['filepath'] = $plugin_dir_path . "/templates/" . $filename . ".php";
		
		// Lets see if this file is available
		if (!is_file($template['filepath']))	{
			// Lets use wordpress standard template to look in the theme directory for the filename
			$template['filepath'] = locate_template($filename, false);
		}
	}

	// Lets return the path for this file
	return $template;
}

function _stubwire_event_template() {
	//echo "<h1>_stubwire_event_template STARTING</h1>\n\n";

	$template = locate_stubwire_template(get_option('stubwire_EventTemplate'));

	if (isset($template['template']) && !empty($template['template'])) {
		//echo "<h1>class-sw-eventlist.php - widget - content NOT EMPTY</H1>";
		eval($template['template']);
	} else {
	?>
	<!-- stubwire built-in event template -->
	<div class="swe">
		<?php
		$meta = stubwire_events_meta();
		while($event = stubwire_get_event()): ?>
		<div class="swe_event">
			<div class="swe_image">
				<?php if ($event['eventImageURLSmall']): ?>
				<a href="<?php echo $event['url']; ?>"><img src="<?php echo $event['eventImageURLSmall']?>" border="0"/></a>
				<?php else: ?>
				<i>No image</i>
				<?php endif ?>
			</div>
			<div class="swe_details">
				<span class="swe_name"><a href="<?php echo $event['url']; ?>"><?php echo $event['name']?></a></span>
				<?php if ($event['act.name']): ?>
				-- <span class="swe_act"><?php echo $event['act.name']?></span>
				<?php endif ?>
			</div>
			<?php if ($event['venue.id']): ?>
			<div class="swe_venue">
				<span class="swe_venue_name"><?php echo $event['venue.name'] ?></span>
			</div>
			<?php endif ?>
		</div>
		<?php stubwire_next_event(); endwhile; ?>
		<?php if (isset($meta['vars']['page_num'])): ?>
		<div class="swe_pager">Page:
		<?php
		$qs = $_GET;
		for($i=1;$i<$meta['total_pages'];$i++) {
			if ($i==$meta['page_num']) echo " <b>$i</b> ";
			else {
				$qs[$meta['vars']['page_num']] = $i;
				echo " <a href=\"?".http_build_query($qs)."\">$i</a> ";
			}
		}?>
			
		</div>
		<?php endif ?>
	</div>
	<!-- end stubwire template -->
	<?php
	}
}