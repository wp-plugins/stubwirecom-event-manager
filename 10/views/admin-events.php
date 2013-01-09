<?php
global $wpdb;

if (empty($stubwire_login_emailaddress) || empty($stubwire_login_password))	{
	$pageHTML_Body .= "<div class=\"error\"><strong>ERROR</strong><br>The login information you entered is not valid. Please login again by <a href=\"" . $currentPageURL . "&subpage=admin-login\">CLICKING HERE</a></div>";
}	else	{
	if (isset($_POST['FormAction']) && $_POST['FormAction']=='cleardatabase') {		
		$this->deleteAllEvents();
		
		$pageHTML_Body .= "<div id=\"message\" class=\"updated fade\"><p><strong>All Events have been cleared</strong></p></div>";
	}
	if (isset($_POST['FormAction']) && $_POST['FormAction']=='deleteEvent') {		
		$this->deleteEvent($_POST['EventID'], '');
		
		$pageHTML_Body .= "<div id=\"message\" class=\"updated fade\"><p><strong>Event has been deleted</strong></p></div>";
	}
	
	$pageHTML_Body .= "<script>\n";	
	$pageHTML_Body .= "function clearDatabase()	{\n";
	$pageHTML_Body .= "	var answer = confirm(\"This will delete all old and current event information. Are you sure?\")\n";
	$pageHTML_Body .= "	if (answer){\n";
	$pageHTML_Body .= "		alert(\"Bye bye!\")\n";
	$pageHTML_Body .= "		document.getElementById(\"FormAction\").value = 'cleardatabase'\n";
	$pageHTML_Body .= "		document.FormEvents.submit();\n";
	$pageHTML_Body .= "	}\n";
	$pageHTML_Body .= "}\n";
	$pageHTML_Body .= "function deleteEvent(event)	{\n";
	$pageHTML_Body .= "	var answer = confirm(\"You are about to delete this event. Are you sure?\")\n";
	$pageHTML_Body .= "	if (answer){\n";
	$pageHTML_Body .= "		alert(\"Bye bye!\")\n";
	$pageHTML_Body .= "		document.getElementById(\"FormAction\").value = 'deleteEvent'\n";
	$pageHTML_Body .= "		document.getElementById(\"EventID\").value = event\n";
	$pageHTML_Body .= "		document.FormEvents.submit();\n";
	$pageHTML_Body .= "	}\n";
	$pageHTML_Body .= "}\n";
	$pageHTML_Body .= "</script>\n";

	$pageHTML_Body .= "<form method=\"POST\" action=\"" . $currentPageURL . "&subpage=admin-events\" name=\"FormEvents\" id=\"FormEvents\">\n";
	$pageHTML_Body .= "<input type=\"hidden\" name=\"FormAction\" id=\"FormAction\" value=\"\">\n";
	$pageHTML_Body .= "<input type=\"hidden\" name=\"EventID\" id=\"EventID\" value=\"\">\n";
	$pageHTML_Body .= "</form>\n";
	
	$allEvents = $this->get_AllEvents();
	
	$pageHTML_Body .= "<h3>Events</h3>\n";
	$pageHTML_Body .= "<p class=\"install-help\">Listed below are the events that we have added into your WordPress. You can click the DELETE button to delete that event from your internal database. <b>WARNING:</b> If the event is still in StubWire, it will be imported back into your WordPress.</p>\n";
	if (count($allEvents) > 0)	{
		$pageHTML_Body .= "<div id=\"publishing-action\"><input class=\"button-primary\" type=\"button\" name=\"ClearEventDatabase\" id=\"ClearEventDatabase\" value=\"Clear All " . count($allEvents) . " Events!\" onclick=\"clearDatabase();\" /></div>\n";
	}
	$pageHTML_Body .= "<table class=\"wp-list-table widefat\" cellspacing=\"0\" border=\"1\">\n";
	$pageHTML_Body .= "	<thead>\n";
	$pageHTML_Body .= "		<tr>\n";
	$pageHTML_Body .= "			<th>ID</th>\n";
	$pageHTML_Body .= "			<th>Name</th>\n";
	$pageHTML_Body .= "			<th>Date</th>\n";
	$pageHTML_Body .= "			<th>Status</th>\n";
	$pageHTML_Body .= "			<th>WPPost ID</th>\n";
	$pageHTML_Body .= "			<th>&nbsp;</th>\n";
	$pageHTML_Body .= "		</tr>\n";
	$pageHTML_Body .= "	</thead>\n";
	$pageHTML_Body .= "	<tbody id=\"the-list\">\n";
	if (count($allEvents) < 1)	{
		$pageHTML_Body .= "		<tr valign=\"top\">\n";
		$pageHTML_Body .= "			<td colspan=\"6\">No Event data can be found. Please import your event data</td>\n";
		$pageHTML_Body .= "		</tr>\n";
	}	else	{
		foreach ($allEvents as $event)	{
			$pageHTML_Body .= "		<tr class=\"no-items\" valign=\"top\">\n";
			$pageHTML_Body .= "			<td>" . $event['id'] . "</td>\n";
			$pageHTML_Body .= "			<td>" . $event['name'] . "</td>\n";
			$pageHTML_Body .= "			<td>" . $event['dateTime'] . "</td>\n";
			$pageHTML_Body .= "			<td>" . $event['eventStatus'] . "</td>\n";
			$pageHTML_Body .= "			<td>" . $event['wp_postid'] . "</td>\n";
			$pageHTML_Body .= "			<td><input class=\"button-primary\" type=\"button\" name=\"eventAction\" id=\"eventAction\" value=\"Delete\" onclick=\"deleteEvent('" . $event['id'] . "');\" /></td>\n";
			$pageHTML_Body .= "		</tr>\n";
			
			//wp_delete_post($event['wp_postid'], true);
		}
	}
			
	/*foreach ($this->get_StubWireAllTemplates() as $template)	{
		$pageHTML_Body .= "		<tr valign=\"top\">\n";
		$pageHTML_Body .= "			<td>" . $template['name'] . "</td>\n";
		$pageHTML_Body .= "			<td>" . $template['filename'] . "</td>\n";
		$pageHTML_Body .= "			<td>" . $template['lastupdated'] . "</td>\n";
		$pageHTML_Body .= "			<td><input class=\"button-primary\" type=\"button\" name=\"CodeTemplate" . $template['filename'] . "\" id=\"CodeTemplate" . $template['filename'] . "\" value=\"Template Code\" onclick=\"codeTemplate('" . $template['filename'] . "');\" /></td>\n";
		$pageHTML_Body .= "		</tr>\n";
	}*/

	$pageHTML_Body .= "	</tbody>\n";
	$pageHTML_Body .= "</table>\n";
}

echo $pageHTML_Header . $pageHTML_Body . $pageHTML_Footer;
?>