<?php
global $wpdb;

$this->Upgrade(STUBWIRE_PLUGIN_VERSION);

if (empty($stubwire_login_emailaddress) || empty($stubwire_login_password))	{
	$pageHTML_Body .= "<div class=\"error\"><strong>ERROR</strong><br>The login information you entered is not valid. Please login again by <a href=\"?page=stubwire.class.php&subpage=admin-login\">CLICKING HERE</a></div>";
}	else	{
	if (isset($_POST['FormAction']) && $_POST['FormAction']=='settings') {
		$formPostCategory				= $_POST['PostCategory'];
		$formPostAuthor					= $_POST['PostAuthor'];
		$formEventTemplate			= $_POST['EventTemplate'];
		
		$formVenues							= $_POST['Venues'];
		$formPromoted						= $_POST['Promoted'];
		$formClient							= $_POST['Client'];
		
	  update_option('stubwire_PostCategory', $formPostCategory);
	  update_option('stubwire_PostAuthor', $formPostAuthor);
	  update_option('stubwire_EventTemplate', $formEventTemplate);
	  
	  update_option('stubwire_Venues', $formVenues);
	  update_option('stubwire_Promoted', $formPromoted);
	  update_option('stubwire_Client', $formClient);

		$pageHTML_Body .= "<div id=\"message\" class=\"updated fade\"><p><strong>Settings Saved.</strong></p></div>";
	}	else	{
		// Lets pull the previous settings
		$formPostCategory		= get_option('stubwire_PostCategory');
		$formPostAuthor			= get_option('stubwire_PostAuthor');
		$formEventTemplate	= get_option('stubwire_EventTemplate');
		
		$formVenues					= get_option('stubwire_Venues');
		$formPromoted				= get_option('stubwire_Promoted');
		$formClient					= get_option('stubwire_Client');
	}
	
	// Lets get the array of the clients they can access
	$clients = $this->get_ClientsToAccessFromStubWire($stubwire_login_emailaddress, $stubwire_login_password);
	
	$pageHTML_Body .= "<script>\n";
	$pageHTML_Body .= "function addVenue(id)	{\n";
	$pageHTML_Body .= "	var strData = new String();\n";
	$pageHTML_Body .= "	var arrData = new Array();\n";
	$pageHTML_Body .= "	strData = document.getElementById(\"Venues\").value;\n";
	$pageHTML_Body .= "	arrData = strData.split(',');\n";
	$pageHTML_Body .= "	bolInArray = false;\n";
	$pageHTML_Body .= "	for(i = 0; i < arrData.length; i++){\n";
	$pageHTML_Body .= "		if (arrData[i]==id)	{\n";
	$pageHTML_Body .= "			bolInArray = true;\n";
	$pageHTML_Body .= "		}\n";
	$pageHTML_Body .= "	}\n";
	$pageHTML_Body .= "	if (!bolInArray)	{\n";
	$pageHTML_Body .= "		if (strData!='')	{\n";
	$pageHTML_Body .= "			strData = strData + \",\";\n";
	$pageHTML_Body .= "		}\n";
	$pageHTML_Body .= "		strData = strData + id;\n";
	$pageHTML_Body .= "	}\n";
	$pageHTML_Body .= "	document.getElementById(\"Venues\").value = strData;\n";
	$pageHTML_Body .= "	document.getElementById(\"Venue\" + id + \"\").value=\"SELECTED\"\n";
	$pageHTML_Body .= "}\n";

	$pageHTML_Body .= "function addPromoted(id)	{\n";
	$pageHTML_Body .= "	var strData = new String();\n";
	$pageHTML_Body .= "	var arrData = new Array();\n";
	$pageHTML_Body .= "	strData = document.getElementById(\"Promoted\").value;\n";
	$pageHTML_Body .= "	arrData = strData.split(',');\n";
	$pageHTML_Body .= "	bolInArray = false;\n";
	$pageHTML_Body .= "	for(i = 0; i < arrData.length; i++){\n";
	$pageHTML_Body .= "		if (arrData[i]==id)	{\n";
	$pageHTML_Body .= "			bolInArray = true;\n";
	$pageHTML_Body .= "		}\n";
	$pageHTML_Body .= "	}\n";
	$pageHTML_Body .= "	if (!bolInArray)	{\n";
	$pageHTML_Body .= "		if (strData!='')	{\n";
	$pageHTML_Body .= "			strData = strData + \",\";\n";
	$pageHTML_Body .= "		}\n";
	$pageHTML_Body .= "		strData = strData + id;\n";
	$pageHTML_Body .= "	}\n";
	$pageHTML_Body .= "	document.getElementById(\"Promoted\").value = strData;\n";
	$pageHTML_Body .= "	document.getElementById(\"Promoted\" + id + \"\").value=\"SELECTED\"\n";
	$pageHTML_Body .= "}\n";
	
	$pageHTML_Body .= "function addClient(id)	{\n";
	$pageHTML_Body .= "	var strData = new String();\n";
	$pageHTML_Body .= "	var arrData = new Array();\n";
	$pageHTML_Body .= "	strData = document.getElementById(\"Client\").value;\n";
	$pageHTML_Body .= "	arrData = strData.split(',');\n";
	$pageHTML_Body .= "	bolInArray = false;\n";
	$pageHTML_Body .= "	for(i = 0; i < arrData.length; i++){\n";
	$pageHTML_Body .= "		if (arrData[i]==id)	{\n";
	$pageHTML_Body .= "			bolInArray = true;\n";
	$pageHTML_Body .= "		}\n";
	$pageHTML_Body .= "	}\n";
	$pageHTML_Body .= "	if (!bolInArray)	{\n";
	$pageHTML_Body .= "		if (strData!='')	{\n";
	$pageHTML_Body .= "			strData = strData + \",\";\n";
	$pageHTML_Body .= "		}\n";
	$pageHTML_Body .= "		strData = strData + id;\n";
	$pageHTML_Body .= "	}\n";
	$pageHTML_Body .= "	document.getElementById(\"Client\").value = strData;\n";
	$pageHTML_Body .= "	document.getElementById(\"Client\" + id + \"\").value=\"SELECTED\"\n";
	$pageHTML_Body .= "}\n";
	$pageHTML_Body .= "</script>\n";

	$pageHTML_Body .= "<form method=\"POST\" action=\"?page=stubwire.class.php&subpage=admin-settings\" name=\"FormSettings\" id=\"FormSettings\">\n";
	$pageHTML_Body .= "<input type=\"hidden\" name=\"FormAction\" id=\"FormAction\" value=\"settings\">\n";

	$pageHTML_Body .= "<h3>Posting Options</h3>\n";
	$pageHTML_Body .= "<table class=\"form-table\">\n";
	/*$pageHTML_Body .= "	<tr>\n";
	$pageHTML_Body .= "		<th><label for=\"user_login\">Username</label></th>\n";
	$pageHTML_Body .= "		<td><input type=\"text\" name=\"user_login\" id=\"user_login\" value=\"admin\" disabled=\"disabled\" class=\"regular-text\" /> <span class=\"description\">Usernames cannot be changed.</span></td>\n";
	$pageHTML_Body .= "	</tr>\n";*/
	$pageHTML_Body .= "	<tr>\n";
	$pageHTML_Body .= "		<th><label for=\"PostCategory\">Category To Post Events:</label></th>\n";
	$pageHTML_Body .= "		<td>\n";
	$pageHTML_Body .= "			<select name=\"PostCategory\" id=\"PostCategory\">\n";

	$queryCategory = "SELECT ";
	$queryCategory .= $wpdb->prefix . "terms.term_id, ";
	$queryCategory .= $wpdb->prefix . "terms.name, ";
	$queryCategory .= $wpdb->prefix . "term_taxonomy.taxonomy ";
	$queryCategory .= "FROM " . $wpdb->prefix . "terms ";
	$queryCategory .= "INNER JOIN " . $wpdb->prefix . "term_taxonomy ";
	$queryCategory .= "ON " . $wpdb->prefix . "terms.term_id = " . $wpdb->prefix . "term_taxonomy.term_id ";
	$queryCategory .= "WHERE " . $wpdb->prefix . "term_taxonomy.taxonomy = 'category'";
	
	$query = $wpdb->prepare($queryCategory,"");
	$allCategorys = $wpdb->get_results($query,ARRAY_A);
	
	$pageHTML_Body .= "				<option id=\"PostCategory\" value=\"\"> - </option>\n";
	foreach ($allCategorys as $category)	{
		$pageHTML_Body .= "				<option id=\"PostCategory\" value=\"" . $category['term_id'] . "\"";
		if ($category['term_id']==$formPostCategory)
			$pageHTML_Body .= " selected=\"selected\"";
		$pageHTML_Body .= ">" . $category['name'] . "</option>\n";
	}	
	
	$pageHTML_Body .= "			</select>\n";
	$pageHTML_Body .= "		</td>\n";
	$pageHTML_Body .= "	</tr>\n";
	$pageHTML_Body .= "	<tr>\n";
	$pageHTML_Body .= "		<th><label for=\"PostAuthor\">Post Author To Use:</label></th>\n";
	$pageHTML_Body .= "		<td>\n";
	$pageHTML_Body .= "			<select name=\"PostAuthor\" id=\"PostAuthor\">\n";

	$queryUsers = "SELECT ";
	$queryUsers .= "u.ID, ";
	$queryUsers .= "u.user_login ";
	$queryUsers .= "FROM " . $wpdb->prefix . "users u ";
	//$queryUsers .= "JOIN " . $wpdb->prefix . "usermeta um ";
	//$queryUsers .= "um.user_id = u.ID ";
	//$queryUsers .= "WHERE um.meta_key = '" . $wpdb->prefix . "capabilities' ";
	//$queryUsers .= "AND um.meta_value LIKE '%administrator%'";
	$query = $wpdb->prepare($queryUsers,"");
	$allUsers = $wpdb->get_results($query,ARRAY_A);
	
	$pageHTML_Body .= "				<option id=\"PostAuthor\" value=\"\"> - </option>\n";
	foreach ($allUsers as $user)	{
		$pageHTML_Body .= "				<option id=\"PostAuthor\" value=\"" . $user['ID'] . "\"";
		if ($user['ID']==$formPostAuthor)
			$pageHTML_Body .= " selected=\"selected\"";
		$pageHTML_Body .= ">" . $user['user_login'] . "</option>\n";
	}	
	
	$pageHTML_Body .= "			</select>\n";
	$pageHTML_Body .= "		</td>\n";
	$pageHTML_Body .= "	</tr>\n";
	$pageHTML_Body .= "	<tr>\n";
	$pageHTML_Body .= "		<th><label for=\"EventTemplate\">Template for selected event:</label></th>\n";
	$pageHTML_Body .= "		<td>\n";
	$pageHTML_Body .= "			<select name=\"EventTemplate\" id=\"EventTemplate\">\n";
	$pageHTML_Body .= "				<option id=\"EventTemplate\" value=\"\"> - </option>\n";
	foreach ($this->get_StubWireAllTemplates() as $template)	{	
		$pageHTML_Body .= "				<option id=\"EventTemplate\" value=\"" . $template['filename'] . "\"";
		if ($template['filename']==$formEventTemplate)
			$pageHTML_Body .= " selected=\"selected\"";
		$pageHTML_Body .= ">" . $template['filename'] . "</option>\n";
	}
	
	$pageHTML_Body .= "			</select>\n";
	$pageHTML_Body .= "		</td>\n";
	$pageHTML_Body .= "	</tr>\n";
	$pageHTML_Body .= "</table>\n";

	$pageHTML_Body .= "<h3>Select Client Events to Pull</h3>\n";
	$pageHTML_Body .= "<p class=\"install-help\">Select the clients you wish to access, along with the type of info that should be pulled. Using as a Venue will pull all events at the venue, where the client will only pull the events you have access to.</p>\n";

	$pageHTML_Body .= "Venues:<input type=\"text\" name=\"Venues\" id=\"Venues\" value=\"" . $formVenues . "\">&nbsp;&nbsp;\n";
	$pageHTML_Body .= "Promoted: <input type=\"text\" name=\"Promoted\" id=\"Promoted\" value=\"" . $formPromoted . "\">&nbsp;&nbsp;\n";
	$pageHTML_Body .= "Client: <input type=\"text\" name=\"Client\" id=\"Client\" value=\"" . $formClient . "\">&nbsp;&nbsp;<br>\n";

	$pageHTML_Body .= "<table class=\"wp-list-table widefat\" cellspacing=\"0\" border=\"1\">\n";
	$pageHTML_Body .= "	<thead>\n";
	$pageHTML_Body .= "		<tr>\n";
	$pageHTML_Body .= "			<th>Name</th>\n";
	$pageHTML_Body .= "			<th>Venue</th>\n";
	$pageHTML_Body .= "			<th>Promoted</th>\n";
	$pageHTML_Body .= "			<th>Client</th>\n";
	$pageHTML_Body .= "		</tr>\n";
	$pageHTML_Body .= "	</thead>\n";
	$pageHTML_Body .= "	<tbody id=\"the-list\">\n";

	foreach ($clients as $client)	{
		$pageHTML_Body .= "		<tr valign=\"top\">\n";
		$pageHTML_Body .= "			<td>" . $client['name'] . " (" . $client['id'] . ")</td>\n";
		$pageHTML_Body .= "			<td>";
		if ($client['isVenue']=='true')	{
			$pageHTML_Body .= "<input class=\"button-primary\" type=\"button\" name=\"Venue" . $client['id'] . "\" id=\"Venue" . $client['id'] . "\" value=\"Use As Venue\" onclick=\"addVenue('" . $client['id'] . "');\" />";
		}	else	{
			$pageHTML_Body .= "&nbsp;";
		}
		$pageHTML_Body .= "</td>\n";
		$pageHTML_Body .= "			<td>";
		if ($client['isPromoter']=='true')	{
			$pageHTML_Body .= "<input class=\"button-primary\" type=\"button\" name=\"Promoted" . $client['id'] . "\" id=\"Promoted" . $client['id'] . "\" value=\"Use As Promoted\" onclick=\"addPromoted('" . $client['id'] . "');\" />";
		}	else	{
			$pageHTML_Body .= "&nbsp;";
		}
		$pageHTML_Body .= "</td>\n";
		$pageHTML_Body .= "			<td>";
		if ($client['isPromoter']=='true')	{
			$pageHTML_Body .= "<input class=\"button-primary\" type=\"button\" name=\"Client" . $client['id'] . "\" id=\"Client" . $client['id'] . "\" value=\"Use As Client\" onclick=\"addClient('" . $client['id'] . "');\" />";
		}	else	{
			$pageHTML_Body .= "&nbsp;";
		}
		$pageHTML_Body .= "</td>\n";
		$pageHTML_Body .= "		</tr>\n";
	}

	$pageHTML_Body .= "	</tbody>\n";
	$pageHTML_Body .= "</table>\n";

	$pageHTML_Body .= "<input class=\"button-primary\" type=\"submit\" name=\"Save\" value=\"SAVE\" id=\"submitbutton\" />\n";
	$pageHTML_Body .= "</form>\n";
}

echo $pageHTML_Header . $pageHTML_Body . $pageHTML_Footer;
?>