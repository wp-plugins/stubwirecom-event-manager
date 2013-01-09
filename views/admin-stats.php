<?php
if (empty($stubwire_login_emailaddress) || empty($stubwire_login_password))	{
	$pageHTML_Body .= "<div class=\"error\"><strong>ERROR</strong><br>The login information you entered is not valid. Please login again by <a href=\"" . $currentPageURL . "&subpage=admin-login\">CLICKING HERE</a></div>";
}	else	{
	$pluginStats = $this->get_PluginStats();

	$pageHTML_Body .= "<form method=\"POST\" action=\"" . $currentPageURL . "&subpage=admin-stats\" name=\"FormSettings\" id=\"FormSettings\">\n";
	$pageHTML_Body .= "<input type=\"hidden\" name=\"FormAction\" id=\"FormAction\" value=\"settings\">\n";
			
  $pageHTML_Body .= "  <h3>Current Stats</h3>\n";

	$pageHTML_Body .= "<table class=\"wp-list-table\" cellspacing=\"10\" border=\"1\">\n";
	$pageHTML_Body .= "	<thead>\n";
	$pageHTML_Body .= "		<tr>\n";
	$pageHTML_Body .= "			<th>Name</th>\n";
	$pageHTML_Body .= "			<th>Value</th>\n";
	$pageHTML_Body .= "		</tr>\n";
	$pageHTML_Body .= "	</thead>\n";
	$pageHTML_Body .= "	<tbody id=\"the-list\">\n";
	foreach ($pluginStats as $type => $value)	{
		$pageHTML_Body .= "		<tr valign=\"top\">\n";
		$pageHTML_Body .= "			<td><b>" . $type . ":</b></td>\n";
		$pageHTML_Body .= "			<td>" . $value . "</td>\n";
		$pageHTML_Body .= "		</tr>\n";
	}
	$pageHTML_Body .= "	</tbody>\n";
	$pageHTML_Body .= "</table>\n";
	$pageHTML_Body .= "<p>&nbsp;</p>\n";

	//$pageHTML_Body .= "<input class=\"button-primary\" type=\"submit\" name=\"Save\" value=\"SAVE\" id=\"submitbutton\" />\n";
	$pageHTML_Body .= "</form>\n";

	/*global $wpdb;
	
	$tablePrefix = $wpdb->prefix . "stubwire_";
	
	$query = "SELECT SQL_CALC_FOUND_ROWS * FROM wp_stubwire_events as `event` LEFT JOIN wp_stubwire_venues as `venue` on `venue`.`id`=`event`.`venueid` ORDER BY event.dateTime ASC LIMIT 20 OFFSET 0";	
	$query = $wpdb->prepare($query,"");
	$dbData = $wpdb->get_results($query,ARRAY_A);
	$tmpData = print_r($dbData, true);
	$pageHTML_Body .= "<!-- \n" . $tmpData . " \n -->\n";*/
}

echo $pageHTML_Header . $pageHTML_Body . $pageHTML_Footer;
?>