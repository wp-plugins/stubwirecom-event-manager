<?php
// Lets see if this function already exists, if so we dont need to specify the functions again
if( !function_exists('stubwire_update_available') ) {
	function stubwire_update_available() {
		global $stubWire;

		$UpgradeAvailable = false;
		if ($stubWire->CheckForUpgradeAvailable())	{
			$UpgradeAvailable = true;
		}
		
		return $UpgradeAvailable;
	}
	function stubwire_cron_handler() {
		global $stubWire;
		
		$stubWire->get_EventsFromStubWire(false, "");
	}

	/**
	 * Fetchs a single event's data
	 *
	 */
	function stubwire_select_event($eventid, $pageTemplate='')	{
		$pageDataToPull = array();
		$pageDataToPull['page_len']			= "1";
		if (empty($pageTemplate))	{
			$pageDataToPull['template']		= "{selectedevent}";
		}	else	{
			$pageDataToPull['template']		= $pageTemplate;
		}
		$pageDataToPull['page_skip']		= "0";
		$pageDataToPull['page_num']			= "1";
		$pageDataToPull['where']				= "event.id='" . $eventid . "'";
		
		//echo "<h1>template-tags.php - about to call locate_stubwire_template</H1>";
		$template = locate_stubwire_template($pageDataToPull['template']);
		//echo "<h1>template-tags.php - calling locate_stubwire_template</H1>";

		// fetch the event
		stubwire_select_events($pageDataToPull);
				
		ob_start();
		if (isset($template['filepath']) && !empty($template['filepath'])) {
			//echo "<h1>template-tags.php - stubwire_select_event - filepath NOT EMPTY</H1>";
			include($template['filepath']);
		}	elseif (isset($template['template']) && !empty($template['template'])) {
			//echo "<h1>template-tags.php - stubwire_select_event - content NOT EMPTY</H1>";
			eval($template['template']);
		} else {
			//echo "<h1>template-tags.php - stubwire_select_event - loading default</H1>";
			$this->_default_template();
		}
		return ob_get_clean();
	}
	
	/**
	 * Fetch a new list of events from the database
	 *
	 * Possible parameters:
	 *   * page_len  [10] Number of items to retrieve
	 *   * page_num [1]  Number of pages (of page_len) to skip + 1
	 *
	 * @todo: Addl filter criteria:
	 *  * event status
	 *  * event date (relation to today)
	 *  * promoter picks
	 *  * state
	 *
	 * @param array parameters
	 *
	 */
	function stubwire_select_events($params=null) {
		//echo "<h1>stubwire_select_events STARTING</h1>\n\n";
		global $stubWire;
		global $stubwire_event_list; // we'll store our results here
		global $stubwire_event_list_meta; // related statistics
		global $wpdb; // wordpress db obj
		global $table_prefix; // wordpress table prefix
		$param_array				= array();	// temporary
		$provided_vars			= array(); // list of vars which are assignable in the query string
		$defaults						= array('page_len' => 20,'page_num' => 1,);

		// if $params is a string, then parse it as URL-encoded
		$cfg = wp_parse_args($params,$defaults);

		// If a param is in the form key={{name}} or key={{name|default}} then look up
		// the name in $_REQUEST and substitute. Make a note of it in $provided_vars
		foreach($cfg as $key=>$val) {
			if (preg_match("/{{(.*)(?:\\|(.*))}}/",$val,$val_matches)) {
				if (isset($_REQUEST[$val_matches[1]])) $cfg[$key]=$_REQUEST[$val_matches[1]];
				else if (isset($val_matches[2]))$cfg[$key]=$val_matches[2];
				else $cfg[$key]=null;
				$provided_vars[$key] = $val_matches[1];
			}
		}

		// zero-index the page_number
		if ($cfg['page_num']<1) $cfg['page_num']=1;
		$page_skip = $cfg['page_num']-1;

		// our custom table prefix
		$prefix = $table_prefix . STUBWIRE_TABLE_PREFIX;
		$WHERE = "";
		if (isset($cfg['where']) && $cfg['where']) {
			$WHERE = "WHERE {$cfg['where']} ";
		}
		if (isset($cfg['where']) && $cfg['where']) {
			$WHERE = "WHERE {$cfg['where']} ";
		}
		if (empty($WHERE))	{
			$WHERE .= "WHERE (eventStatus='Active' OR eventStatus='Canceled') ";
		}	else	{
			$WHERE .= "AND (eventStatus='Active' OR eventStatus='Canceled') ";
		}
		if (isset($cfg['order']) && $cfg['order']) {
			$ORDERBY = "ORDER BY {$cfg['order']} ";
		}	else	{
			$ORDERBY = "ORDER BY event.dateTime ASC ";
		}

$t_time = current_time('mysql');
$t_time = strtotime($t_time) - (3600 * 6);
$t_time = date("Y/m/d g:i:s A", $t_time);

$WHERE = str_replace("{NOW()}", "'" . $t_time . "'", $WHERE);
		
$query = $wpdb->prepare(
				"SELECT SQL_CALC_FOUND_ROWS event.*, "
				.  "venue.id as `venue.id`, venue.name as `venue.name`, venue.address as `venue.address`, "
				.  "venue.address2 as `venue.address2`, venue.city as `venue.city`, venue.state as `venue.state`, "
				.  "venue.zip as `venue.zip`, venue.url as `venue.url`, venue.image as `venue.image` "
				. "FROM {$prefix}events as `event` "
				. "LEFT JOIN {$prefix}venues as `venue` on `venue`.`id`=`event`.`venueid` "
				. $WHERE
				. $ORDERBY
				. "LIMIT %d OFFSET %d",
				$cfg['page_len'], // limit
				$cfg['page_len'] * $page_skip // offset
				);

		
		// includes the act and venue columns if available using their respective prefixes
		$stubwire_event_list = $wpdb->get_results($query, ARRAY_A);
		
		// found_rows used for paging
		$found_rows = $wpdb->get_var('SELECT FOUND_ROWS()');
		// statistics useful for paging (and possibly other things)
		$stubwire_event_list_meta = array(
			'total_rows'		=> $found_rows,
			'total_pages'		=> ceil($found_rows / $cfg['page_len']),
			'skipped_rows'	=> $cfg['page_len'] * $page_skip,
			'page_num'			=> $cfg['page_num'],
			'page_len'			=> $cfg['page_len'],
			'vars'					=> $provided_vars,
		);
		
		/*echo "<pre>";
		print_r($stubwire_event_list_meta);
		echo "</pre>";*/

		/*echo "<h1>stubwire_select_events ADD ACTS FOR EACH stubwire_event_list</h1>\n\n";
		echo "<pre>";
		print_r($stubwire_event_list);
		echo "</pre>\n\n";*/
		
		$selectedEvents = array();
		foreach($stubwire_event_list as $event) {
			$event['facebookWallPosts']		= maybe_unserialize($event['facebookWallPosts']);
			$event['facebookAttending']		= maybe_unserialize($event['facebookAttending']);
			$event['facebookURL']					= "http://www.facebook.com/event.php?eid=" . $event['facebookEventID'];

			$query = $wpdb->prepare(
					"SELECT act.id as `act.id`, act.name as `act.name`, act.url as `act.url`, act.type as `act.type` "
					. "FROM {$prefix}acts as `act` "
					. "WHERE `act`.`eventid`='%d' AND act.status='Active'",$event['id']);
					
			$stubwire_event_acts = $wpdb->get_results($query,ARRAY_A);
			$event['acts'] = $stubwire_event_acts;

			$queryWebsite = strtolower($stubWire->get_CurrentDomain());
			if (substr($queryWebsite, 0, 3)=='wp_')	{
				$queryWebsite = substr($queryWebsite, 3);
			}
			if (substr($queryWebsite, 0, 4)=='www.')	{
				$queryWebsite = substr($queryWebsite, 4);
			}
			/*$Referer = stubwire_getReferer();

			if (empty($Referer))	{
				$Referer = $queryWebsite;
			}	else	{
				$Referer = $queryWebsite . "_|_" . $Referer;
			}*/

			$referelLink = urlencode($queryWebsite);
			
			$event['url']					= str_replace("{REFERERINFOHERE}", $referelLink, $event['url']);
			$event['buyNowLink']	= str_replace("{REFERERINFOHERE}", $referelLink, $event['buyNowLink']);

			if (!$stubWire->check_ContainsHTML($event['shortDescription']))	{
				$event['shortDescription'] = nl2br($event['shortDescription']);
			}
			if (!$stubWire->check_ContainsHTML($event['fullDescription']))	{
				$event['fullDescription'] = nl2br($event['fullDescription']);
			}
		
			if ($event['ticketPriceDoorLabel']=='Advance / Door')
				$event['ticketPriceDoorLabel'] = "DOOR";
			elseif ($event['ticketPriceDoorLabel']=='Advance / Day of Show')
				$event['ticketPriceDoorLabel'] = "DAY OF";
			elseif ($event['ticketPriceDoorLabel']=='Advance Only')
				$event['ticketPriceDoorLabel'] = "";
			else
				$event['ticketPriceDoorLabel'] = "DOOR";


			if ($event['ticketPriceAdvance']=='0.00' || $event['ticketPriceAdvance']=='$0.00')
				$event['ticketPriceAdvance'] = "FREE";
			if ($event['ticketPriceDoor']=='0.00' || $event['ticketPriceDoor']=='$0.00')
				$event['ticketPriceDoor'] = "FREE";
				
			if ($event['ticketPriceDoorLabel']=='' || $event['ticketPriceAdvance']==$event['ticketPriceDoor'])	{
				// Ticket does not hace a door or day of price
				$event['ticketPriceFriendly'] = $event['ticketPriceAdvance'];
				if (!empty($event['orderEntryBoxOfficeCashServiceFee']))	{
					$event['ticketPriceFriendly'] .= " ADV";
				}
			}	else	{
				$event['ticketPriceFriendly'] = $event['ticketPriceAdvance'] . " ADV<br>" . $event['ticketPriceDoor'] . " " . $event['ticketPriceDoorLabel'];
			}
		
			$selectedEvents[]=$event;
		}
		
		$stubwire_event_list = $selectedEvents;

		/*echo "<h1>stubwire_select_events RETURNING stubwire_event_list</h1>\n\n";
		echo "<pre>";
		print_r($stubwire_event_list);
		echo "</pre>\n\n";*/
				
		return $stubwire_event_list;
	}

	/**
	 * Get the meta data relating to the last events query.
	 * @global <type> $stubwire_event_list_meta
	 * @param string $key optional key to retrieve (if not the whole array)
	 * @return mixed either the provided keyed value or the array itself
	 */
	function stubwire_events_meta($key=null) {
		global $stubwire_event_list_meta; // event statistics
		if (isset($key)) return $stubwire_event_list_meta[$key];
		return $stubwire_event_list_meta;
	}

	/**
	 * Returns current event if there is one, null otherwise.
	 * @return array
	 */
	function stubwire_get_event() {
		global $stubwire_event_list; // we'll store our results here
		if (count($stubwire_event_list)>0) return $stubwire_event_list[0];
		return null;
	}

	/**
	 * Returns the full list of stubwire events
	 * @return array
	 */
	function stubwire_get_event_list() {
		global $stubwire_event_list; // we'll store our results here
		return $stubwire_event_list;
	}

	/**
	 * Skips to the next event.
	 */
	function stubwire_next_event() {
		global $stubwire_event_list; // we'll store our results here
		if (count($stubwire_event_list)>0) array_shift($stubwire_event_list);
		return count($stubwire_event_list)>0;
	}

	/**
	 * Returns the number of remaining events.
	 * @global array $stubwire_event_list
	 * @return integer
	 */
	function stubwire_count_events() {
		global $stubwire_event_list;
		return count($stubwire_event_list);
	}

	/**
	 * Gets a specific column for the current event.
	 * @global array $stubwire_event_list
	 * @param string $key column to retrieve
	 * @return string
	 */
	function stubwire_event_data($key) {
		global $stubwire_event_list;
		if (count($stubwire_event_list)>0) return $stubwire_event_list[0][$key];
		return null;
	}

	/**
	 * Get the image associated with the current event
	 * @return string url
	 */
	function stubwire_event_image() {
		return stubwire_event_data("eventImageURLSmall");
	}
} // end if !function_exists('stubwire_update_available')