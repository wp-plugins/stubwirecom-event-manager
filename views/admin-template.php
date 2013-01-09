<?php
if (empty($stubwire_login_emailaddress) || empty($stubwire_login_password))	{
	$pageHTML_Body .= "<div class=\"error\"><strong>ERROR</strong><br>The login information you entered is not valid. Please login again by <a href=\"?page=stubwire.class.php&subpage=admin-login\">CLICKING HERE</a></div>";
}	else	{
	$queryAction				= $_GET['action'];
	$queryTemplate			= $_GET['template'];
	$queryTemplateID		= "";
	
	if (empty($queryAction))	{
		$queryAction = "add";
	}
	
	if ($queryAction!='add')	{
		// LETS GET THE TEMPLATE ID FOR THIS FILE
		$template = $this->get_StubWireTemplateInfo($queryTemplate);
		$queryTemplateID = $template['id'];
	}

	if (isset($_POST['submitted']) && $_POST['submitted']=='1') {		
		$formTemplateName							= stripslashes_deep($_POST['TemplateName']);
		$formTemplateFileName					= stripslashes_deep($_POST['TemplateFileName']);
		$formTemplateContent					= stripslashes_deep($_POST['TemplateContent']);

		$updateDetails = array();
		$updateDetails['id']				= $queryTemplateID;
		$updateDetails['name']			= $formTemplateName;
		$updateDetails['filename']	= $formTemplateFileName;
		$updateDetails['template']	= $formTemplateHeader . $formTemplateContent;
		
		if ($queryAction=='add')	{
			$queryAction		= "edit";
		}

		$updateDetails = $this->update_Template($updateDetails);
		
		$pageHTML_Body .= "<div id=\"message\" class=\"updated fade\"><p><strong>The template has been saved</strong></p></div>";
		
		$template = $this->get_StubWireTemplateInfo($updateDetails['filename']);

		$formTemplateName							= $template['name'];
		$formTemplateFileName					= $template['filename'];
		$formTemplateContent					= $template['template'];
		$formTemplateDefault					= $template['default'];
		$formLastUpdatedAt						= $template['lastupdated'];
		
		$queryTemplate = $formTemplateFileName;
	}	else	{
		// They are loading the page so lets load the previous template if its loading
		if (!empty($queryTemplate))	{
			$template = $this->get_StubWireTemplateInfo($queryTemplate);

				/*$template										= array();
				$template['id']							= $getID[0]['id'];
				$template['name']						= $getID[0]['name'];
				$template['filename']				= $getID[0]['filename'];
				$template['template']				= $getID[0]['template'];
				$template['default']				= $getID[0]['isdefault'];
				$template['lastupdated']		= $getID[0]['LastUpdatedAt'];*/
				
			$formTemplateName							= $template['name'];
			$formTemplateFileName					= $template['filename'];
			$formTemplateContent					= $template['template'];
			$formTemplateDefault					= $template['default'];
			$formLastUpdatedAt						= $template['lastupdated'];
			
			if (empty($formTemplateDefault))	{
				$formTemplateDefault = "no";
			}
		}	else	{
			$formTemplateDefault = "no";
		}
	}

	$pageHTML_Body .= "<script>\n";	
	$pageHTML_Body .= "function viewCodeTemplate()	{\n";
	$pageHTML_Body .= "	window.location = '?page=stubwire.class.php&subpage=admin-template&action=code&template=" . $queryTemplate . "'\n";
	$pageHTML_Body .= "}\n";
	$pageHTML_Body .= "function editTemplate()	{\n";
	$pageHTML_Body .= "	window.location = '?page=stubwire.class.php&subpage=admin-template&action=edit&template=" . $queryTemplate . "'\n";
	$pageHTML_Body .= "}\n";
	$pageHTML_Body .= "function deleteTemplate()	{\n";
	$pageHTML_Body .= "	var answer = confirm(\"This will delete this template which will make any page that is using it, use the default template. Are you sure?\")\n";
	$pageHTML_Body .= "	if (answer){\n";
	$pageHTML_Body .= "		alert(\"Bye bye!\")\n";
	$pageHTML_Body .= "		window.location = '?page=stubwire.class.php&subpage=admin-templates&action=delete&template=" . $formTemplateFileName . "'\n";
	$pageHTML_Body .= "	}\n";
	$pageHTML_Body .= "}\n";
	$pageHTML_Body .= "</script>\n";
	$pageHTML_Body .= "<div id=\"wpbody-content\">\n";
	$pageHTML_Body .= "	<div class=\"wrap\">\n";
	$pageHTML_Body .= "		<div id=\"icon-edit\" class=\"icon32 icon32-posts-post\"><br /></div>\n";
	$pageHTML_Body .= "		<h2>" . $queryAction . " Template</h2>\n";
	$pageHTML_Body .= "		<form name=\"post\" action=\"?page=stubwire.class.php&subpage=admin-template&action=" . $queryAction . "&template=" . $queryTemplate . "\" method=\"post\" id=\"post\">\n";
	$pageHTML_Body .= "		<input type=\"hidden\" name=\"submitted\" id=\"submitted\" value=\"1\">\n";
	$pageHTML_Body .= "		<div id=\"poststuff\" class=\"metabox-holder has-right-sidebar\">\n";
	$pageHTML_Body .= "			<div id=\"side-info-column\" class=\"inner-sidebar\">\n";
	$pageHTML_Body .= "				<div id=\"side-sortables\" class=\"meta-box-sortables\">\n";
	$pageHTML_Body .= "					<div id=\"submitdiv\" class=\"postbox \" >\n";
	$pageHTML_Body .= "						<div class=\"handlediv\" title=\"Click to toggle\"><br /></div>\n";
	$pageHTML_Body .= "						<h3 class=\"hndle\"><span>Publish</span></h3>\n";
	$pageHTML_Body .= "						<div class=\"inside\">\n";
	$pageHTML_Body .= "							<div class=\"submitbox\" id=\"submitpost\">\n";
	$pageHTML_Body .= "								<div id=\"minor-publishing\">\n";
	$pageHTML_Body .= "									<div id=\"misc-publishing-actions\">\n";
	$pageHTML_Body .= "										<div class=\"misc-pub-section\"><label for=\"post_status\">Last Updated:</label> <span id=\"post-status-display\">";
	if (empty($formLastUpdatedAt))	{
		$pageHTML_Body .= "<font color=red>NEW</font>";
	}	else	{
		$pageHTML_Body .= $formLastUpdatedAt;
	}
	$pageHTML_Body .= "</span></div>\n";
	if ($queryAction=='edit')	{
		$pageHTML_Body .= "										<div id=\"publishing-action\"><input type=\"button\" name=\"publish\" id=\"publish\" class=\"button-primary\" value=\"View Template Code\" tabindex=\"5\" onclick=\"viewCodeTemplate();\"  /></div><br><br>\n";
	}
	$pageHTML_Body .= "									</div>\n";
	$pageHTML_Body .= "									<div class=\"clear\"></div>\n";
	$pageHTML_Body .= "								</div>\n";
	$pageHTML_Body .= "								<div id=\"major-publishing-actions\">\n";
	if ($queryAction=='code')	{
		$pageHTML_Body .= "									<div id=\"publishing-action\"><input type=\"button\" name=\"publish\" id=\"publish\" class=\"button-primary\" value=\"Edit Template\" tabindex=\"5\" onclick=\"editTemplate();\"  /></div>\n";		
	}	else	{
		if (strtolower($formTemplateDefault)=='no')	{
			$pageHTML_Body .= "									<div id=\"delete-action\"><a class=\"submitdelete deletion\" href=\"#\" onclick=\"deleteTemplate();\">Delete Template</a></div>\n";
			$pageHTML_Body .= "									<div id=\"publishing-action\"><input type=\"submit\" name=\"publish\" id=\"publish\" class=\"button-primary\" value=\"Save Template\" tabindex=\"6\"  /></div>\n";
		}
	}
	$pageHTML_Body .= "									<div class=\"clear\"></div>\n";
	$pageHTML_Body .= "								</div>\n";
	$pageHTML_Body .= "							</div>\n";
	$pageHTML_Body .= "						</div>\n";
	$pageHTML_Body .= "					</div><!-- END ID submitdiv -->\n";
	$pageHTML_Body .= "					\n";
	$pageHTML_Body .= "					<div id=\"formatdiv\" class=\"postbox \" >\n";
	$pageHTML_Body .= "						<div class=\"handlediv\" title=\"Click to toggle\"><br /></div>\n";
	if ($queryAction=='code')	{
		$pageHTML_Body .= "						<h3 class=\"hndle\"><span>WHERE Clause Examples</span></h3>\n";
		$pageHTML_Body .= "						<div class=\"inside\">\n";	
	
		$pageHTML_Body .= "							<p><b>WHERE - Events Not Yet Happened</b><br />\n";
		$pageHTML_Body .= "							<code>dateTime <font color='red'>></font> NOW()</code></p>\n";
		
		$pageHTML_Body .= "							<p><b>WHERE - Events Not Yet Happened in Little Rock, AR</b><br />\n";
		$pageHTML_Body .= "							<code>venue.state<font color='red'>=</font>'AR' and venue.city<font color='red'>=</font>'Little Rock' and dateTime <font color='red'>></font> NOW()</code></p>\n";
		
		$pageHTML_Body .= "							<p><b>WHERE - Events Not Yet Happened in AR</b><br />\n";
		$pageHTML_Body .= "							<code>venue.state<font color='red'>=</font>'AR' and dateTime <font color='red'>></font> NOW()</code></p>\n";
		
		$pageHTML_Body .= "							<p><b>WHERE - Events Not Yet Happened in Springfield, MO or Joplin, MO</b><br />\n";
		$pageHTML_Body .= "							<code>venue.state<font color='red'>=</font>'MO' and <font color='red'>(</font>venue.city<font color='red'>=</font>'Springfield' or venue.city<font color='red'>=</font>'Joplin'<font color='red'>)</font> and dateTime <font color='red'>></font> NOW()</code></p>\n";
		
		$pageHTML_Body .= "							<p><b>WHERE - Events That Are Over</b><br />\n";
		$pageHTML_Body .= "							<code>dateTime <font color='red'><</font> NOW()</code></p>\n";
		$pageHTML_Body .= "						</div>\n";
	}	else	{
		$pageHTML_Body .= "						<h3 class=\"hndle\"><span>LOOPING BODY TAGS</span></h3>\n";
		$pageHTML_Body .= "						<div class=\"inside\">\n";
		$pageHTML_Body .= "						<p>Listed below are the tags that can be used in the <b>Looping Body</b> section. This is the section that will keep repeating until the current recordset of events has been outputted.</p>\n";
		$pageHTML_Body .= '<p><b>EVENT</b></p>' . "\n";
		$pageHTML_Body .= '<pre>$event[\'id\']' . "\n";
		$pageHTML_Body .= '$event[\'name\']' . "\n";
		$pageHTML_Body .= '$event[\'isParentEvent\']' . "\n";
		$pageHTML_Body .= '$event[\'parentEventID\']' . "\n";
		$pageHTML_Body .= '$event[\'parentEarliestChildDate\']' . "\n";
		$pageHTML_Body .= '$event[\'parentLatestChildDate\']' . "\n";
		$pageHTML_Body .= '$event[\'isParentEventPurchasesEnabled\']' . "\n";
		$pageHTML_Body .= '$event[\'url\']' . "\n";
		$pageHTML_Body .= '$event[\'shortDescription\']' . "\n";
		$pageHTML_Body .= '$event[\'fullDescription\']' . "\n";
		$pageHTML_Body .= '$event[\'dateTime\']' . "\n";
		$pageHTML_Body .= '$event[\'doorsOpenAt\']' . "\n";
		$pageHTML_Body .= '$event[\'isSpotlightEvent\']' . "\n";
		$pageHTML_Body .= '$event[\'isFeaturedEvent\']' . "\n";
		$pageHTML_Body .= '$event[\'isPromoterPickEvent\']' . "\n";
		$pageHTML_Body .= '$event[\'ticketsAvailable\']' . "\n";
		$pageHTML_Body .= '$event[\'timeOnSale\']' . "\n";
		$pageHTML_Body .= '$event[\'timeOffSale\']' . "\n";
		$pageHTML_Body .= '$event[\'ageDescription\']' . "\n";
		$pageHTML_Body .= '$event[\'ticketPriceAdvance\']' . "\n";
		$pageHTML_Body .= '$event[\'ticketPriceDoor\']' . "\n";
		$pageHTML_Body .= '$event[\'ticketPriceDoorLabel\']' . "\n";
		$pageHTML_Body .= '$event[\'buyNowLink\']' . "\n";
		$pageHTML_Body .= '$event[\'eventImage\']' . "\n";
		$pageHTML_Body .= '$event[\'eventImageURLSmall\']' . "\n";
		$pageHTML_Body .= '$event[\'eventImageURLMedium\']' . "\n";
		$pageHTML_Body .= '$event[\'eventImageURLOriginal\']' . "\n";
		$pageHTML_Body .= '$event[\'sellableTroughInternet\']' . "\n";
		$pageHTML_Body .= '$event[\'sellableTroughInternetReason\']' . "\n";
		$pageHTML_Body .= '$event[\'eventStatus\']' . "\n";
		$pageHTML_Body .= '$event[\'LastUpdatedAt\']' . "\n";
		$pageHTML_Body .= '$event[\'wp_postid\']</pre>' . "\n";
		$pageHTML_Body .= "\n";
		$pageHTML_Body .= '<p><b>VENUE</b></p>' . "\n";
		$pageHTML_Body .= '<pre>$event[\'venue.id\']' . "\n";
		$pageHTML_Body .= '$event[\'venue.name\']' . "\n";
		$pageHTML_Body .= '$event[\'venue.address\']' . "\n";
		$pageHTML_Body .= '$event[\'venue.address2\']' . "\n";
		$pageHTML_Body .= '$event[\'venue.city\']' . "\n";
		$pageHTML_Body .= '$event[\'venue.state\']' . "\n";
		$pageHTML_Body .= '$event[\'venue.zip\']' . "\n";
		$pageHTML_Body .= '$event[\'venue.LastUpdatedAt\']</pre>' . "\n";
		$pageHTML_Body .= "\n";
		$pageHTML_Body .= '<p><b>IMAGES</b></p>' . "\n";
		$pageHTML_Body .= '<pre>$event[\'images\'][\'id\']' . "\n";
		$pageHTML_Body .= '$event[\'images\'][\'isMainImage\']' . "\n";
		$pageHTML_Body .= '$event[\'images\'][\'isPosterImage\']' . "\n";
		$pageHTML_Body .= '$event[\'images\'][\'imageName\']' . "\n";
		$pageHTML_Body .= '$event[\'images\'][\'imageCaption\']' . "\n";
		$pageHTML_Body .= '$event[\'images\'][\'imageURLSmall\']' . "\n";
		$pageHTML_Body .= '$event[\'images\'][\'imageURLMedium\']' . "\n";
		$pageHTML_Body .= '$event[\'images\'][\'imageURLOriginal\']' . "\n";
		$pageHTML_Body .= '$event[\'images\'][\'imageType\']' . "\n";
		$pageHTML_Body .= '$event[\'images\'][\'fileName\']' . "\n";
		$pageHTML_Body .= '$event[\'images\'][\'imageDisplayOrder\']' . "\n";
		$pageHTML_Body .= '$event[\'images\'][\'imageStatus\']' . "\n";
		$pageHTML_Body .= '$event[\'images\'][\'LastUpdatedAt\']</pre>' . "\n";
		$pageHTML_Body .= "\n";
		$pageHTML_Body .= '<p><b>ACTS</b></p>' . "\n";
		$pageHTML_Body .= '<pre>$event[\'acts\'][\'id\']' . "\n";
		$pageHTML_Body .= '$event[\'acts\'][\'name\']' . "\n";
		$pageHTML_Body .= '$event[\'acts\'][\'websiteurl\']' . "\n";
		$pageHTML_Body .= '$event[\'acts\'][\'type\']' . "\n";
		$pageHTML_Body .= '$event[\'acts\'][\'displayorder\']' . "\n";
		$pageHTML_Body .= '$event[\'acts\'][\'LastUpdatedAt\']</pre>' . "\n";
		$pageHTML_Body .= "						</div>\n";
	}
	$pageHTML_Body .= "					</div><!-- END ID formatdiv -->\n";
	$pageHTML_Body .= "				</div><!-- END ID side-sortables -->\n";
	$pageHTML_Body .= "			</div><!-- END ID side-info-column -->\n";
	$pageHTML_Body .= "		\n";
	$pageHTML_Body .= "			<div id=\"post-body\">\n";
	$pageHTML_Body .= "				<div id=\"post-body-content\">\n";
	
	
	if (strtolower($formTemplateDefault)=='yes')	{
		$pageHTML_Body .= "<div class=\"error\"><strong>WARNING</strong><br>This is the default template when we are unable to find the template requested. You are unable to change the name or delete this template.</div>";
	}
	
	$pageHTML_Body .= "					<div class=\"postbox\" >\n";
	$pageHTML_Body .= "						<h3>Template Name</h3>\n";
	$pageHTML_Body .= "						<div class=\"inside\">\n";
	$pageHTML_Body .= "							<div id=\"titlediv\"><div id=\"titlewrap\"><input type=\"text\" name=\"TemplateName\" size=\"30\" tabindex=\"1\" value=\"" . $formTemplateName . "\"";
	if ($queryAction=='code' || $queryTemplate=='1' || $queryTemplate=='2')	{
		 $pageHTML_Body .= " disabled=\"disabled\"";
	}
	$pageHTML_Body .= " id=\"title\" autocomplete=\"off\" /></div></div>\n";
	$pageHTML_Body .= "						</div>\n";
	$pageHTML_Body .= "					</div><!-- END CLASS postbox -->\n";
	$pageHTML_Body .= "	\n";

	if ($queryAction!='add')	{
		$pageHTML_Body .= "					<div class=\"postbox\" >\n";
		$pageHTML_Body .= "						<h3>File Name</h3>\n";
		$pageHTML_Body .= "						<div class=\"inside\">\n";
		$pageHTML_Body .= "							<div id=\"titlediv\"><div id=\"titlewrap\">" . $formTemplateFileName . "</div></div>\n";
		$pageHTML_Body .= "							<input type=\"hidden\" name=\"TemplateFileName\" id=\"TemplateFileName\" value=\"" . $formTemplateFileName . "\" />\n";
		$pageHTML_Body .= "						</div>\n";
		$pageHTML_Body .= "					</div><!-- END CLASS postbox -->\n";
		$pageHTML_Body .= "	\n";
	
		$pageHTML_Body .= "					<div class=\"postbox\" >\n";
		$pageHTML_Body .= "						<h3>Last Updated</h3>\n";
		$pageHTML_Body .= "						<div class=\"inside\">\n";
		$pageHTML_Body .= "							<div id=\"titlediv\"><div id=\"titlewrap\">" . $formLastUpdatedAt . "</div></div>\n";
		$pageHTML_Body .= "						</div>\n";
		$pageHTML_Body .= "					</div><!-- END CLASS postbox -->\n";
		$pageHTML_Body .= "	\n";
	}
	
	if ($queryAction!='code')	{
		$pageHTML_Body .= "					<div class=\"postbox\" >\n";
		$pageHTML_Body .= "						<h3>Template Content</h3>\n";
		$pageHTML_Body .= "						<div class=\"inside\">\n";
		$pageHTML_Body .= "							<textarea rows=\"15\" cols=\"100\" name=\"TemplateContent\" id=\"content\" tabindex=\"2\">" . $formTemplateContent . "</textarea></p>\n";
		$pageHTML_Body .= "						</div>\n";
		$pageHTML_Body .= "					</div><!-- END CLASS postbox -->\n";
		$pageHTML_Body .= "					\n";
	}
	
	if ($queryAction=='code')	{
		$pageHTML_Body .= "					<div class=\"postbox\" >\n";
		$pageHTML_Body .= "						<h3>Template Code</h3>\n";
		$pageHTML_Body .= "						<div class=\"inside\">\n";
		$pageHTML_Body .= "							<p>The following will help you generate the code to post in your Template Pages.<br><b>NOTE:</b><i>When editing your page, you must paste in this code as HTML and not inside the WYSIWYG editor.</i></p>\n";
		$pageHTML_Body .= "							<br />\n";
		$pageHTML_Body .= "<script>\n";
		$pageHTML_Body .= "function generateTemplateCode()	{\n";
		$pageHTML_Body .= "	strRecPerPage = document.getElementById(\"gtcRecordsPerPage\").value;\n";
		$pageHTML_Body .= "	strPageNumToStart = document.getElementById(\"gtcPageNumberToStart\").value;\n";
		$pageHTML_Body .= "	strWhereClause = document.getElementById(\"gtcWhereClause\").value;\n";
		$pageHTML_Body .= "	strOrderBy = document.getElementById(\"gtcOrderBy\").value;\n";
		$pageHTML_Body .= "	if (strRecPerPage=='')	{\n";
		$pageHTML_Body .= "		strRecPerPage = '20';\n";
		$pageHTML_Body .= "	}\n";
		$pageHTML_Body .= "	if (strPageNumToStart=='')	{\n";
		$pageHTML_Body .= "		strPageNumToStart = '1';\n";
		$pageHTML_Body .= "	}\n";
		$pageHTML_Body .= "	if (strWhereClause=='')	{\n";
		$pageHTML_Body .= "		strWhereClause = '1=1';\n";
		$pageHTML_Body .= "	}\n";
		$pageHTML_Body .= "	if (strWhereClause=='')	{\n";
		$pageHTML_Body .= "		strWhereClause = '1=1';\n";
		$pageHTML_Body .= "	}\n";
		$pageHTML_Body .= "	if (strOrderBy=='')	{\n";
		$pageHTML_Body .= "		strOrderBy = 'dateTime DESC';\n";
		$pageHTML_Body .= "	}\n";
		$pageHTML_Body .= "	strTempText = '[[stubwire-events page_len=\"' + strRecPerPage + '\" page_num=\"{{event_page|' + strPageNumToStart + '}}\" template=\"" . $queryTemplate . "\" where=\"' + strWhereClause + '\" order=\"' + strOrderBy + '\" ]]'\n";
		//$pageHTML_Body .= "	alert(strTempText)\n";
		//$pageHTML_Body .= "	document.getElementById(\"GeneratedTemplateCode\").value = strTempText;\n";
		$pageHTML_Body .= "	document.post.GeneratedTemplateCode.value = strTempText;\n";
		$pageHTML_Body .= "}\n";
		$pageHTML_Body .= "</script>\n";
		
		$pageHTML_Body .= "<table class=\"form-table\">\n";
		$pageHTML_Body .= "	<tr>\n";
		$pageHTML_Body .= "		<th><label for=\"gtcRecordsPerPage\">Records Per Page:</label></th>\n";
		$pageHTML_Body .= "		<td><input type=\"text\" name=\"gtcRecordsPerPage\" id=\"gtcRecordsPerPage\" value=\"20\" class=\"regular-text\" onchange=\"generateTemplateCode();\" onkeyup=\"generateTemplateCode();\" tabindex=\"1\" /> <span class=\"description\">Must be numeric</span></td>\n";
		$pageHTML_Body .= "	</tr>\n";
		$pageHTML_Body .= "	<tr>\n";
		$pageHTML_Body .= "		<th><label for=\"gtcPageNumberToStart\">Page Number To Start:</label></th>\n";
		$pageHTML_Body .= "		<td><input type=\"text\" name=\"gtcPageNumberToStart\" id=\"gtcPageNumberToStart\" value=\"1\" class=\"regular-text\" onchange=\"generateTemplateCode();\" onkeyup=\"generateTemplateCode();\" tabindex=\"2\" /> <span class=\"description\">Must be numeric</span></td>\n";
		$pageHTML_Body .= "	</tr>\n";
		$pageHTML_Body .= "	<tr>\n";
		$pageHTML_Body .= "		<th><label for=\"gtcWhereClause\">Where Clause:</label></th>\n";
		$pageHTML_Body .= "		<td><input type=\"text\" name=\"gtcWhereClause\" id=\"gtcWhereClause\" value=\"dateTime > NOW() AND eventStatus<>'Canceled - Hidden'\" class=\"regular-text\" onchange=\"generateTemplateCode();\" onkeyup=\"generateTemplateCode();\" tabindex=\"3\" /> <span class=\"description\">This is the SQL Statement that limits the results returned. Form examples please look below.</span></td>\n";
		$pageHTML_Body .= "	</tr>\n";
		$pageHTML_Body .= "	<tr>\n";
		$pageHTML_Body .= "		<th><label for=\"gtcOrderBy\">Order By:</label></th>\n";
		$pageHTML_Body .= "		<td><input type=\"text\" name=\"gtcOrderBy\" id=\"gtcOrderBy\" value=\"dateTime DESC\" class=\"regular-text\" onchange=\"generateTemplateCode();\" onkeyup=\"generateTemplateCode();\" tabindex=\"3\" /> <span class=\"description\">This is the SQL Statement that limits the results returned. Form examples please look below.</span></td>\n";
		$pageHTML_Body .= "	</tr>\n";
		$pageHTML_Body .= "</table>\n";
		$pageHTML_Body .= "							<h2>Template Code</h2>\n";
		$pageHTML_Body .= "							<textarea rows=\"4\" cols=\"40\" name=\"GeneratedTemplateCode\" id=\"content\" tabindex=\"4\"></textarea>\n";
		$pageHTML_Body .= "	\n";
		$pageHTML_Body .= "						</div>\n";
		$pageHTML_Body .= "					</div><!-- END CLASS postbox -->\n";
		$pageHTML_Body .= "	\n";
	}
	
	$pageHTML_Body .= "	\n";
	$pageHTML_Body .= "				</div>\n";
	$pageHTML_Body .= "			</div>\n";
	$pageHTML_Body .= "			<br class=\"clear\" />\n";
	$pageHTML_Body .= "		</div><!-- END ID poststuff -->\n";
	$pageHTML_Body .= "		</form>\n";
	$pageHTML_Body .= "	</div>\n";
	$pageHTML_Body .= "	\n";
	$pageHTML_Body .= "	<script type=\"text/javascript\">\n";
	if ($queryAction=='code')	{
		$pageHTML_Body .= "	try{document.post.gtcRecordsPerPage.focus();}catch(e){}\n";
		$pageHTML_Body .= "	try{generateTemplateCode();}catch(e){}\n";
	}	else	{
		$pageHTML_Body .= "	try{document.post.title.focus();}catch(e){}\n";
	}
	$pageHTML_Body .= "	</script>\n";
	$pageHTML_Body .= "	\n";
	$pageHTML_Body .= "	<div class=\"clear\"></div>\n";
	$pageHTML_Body .= "</div><!-- wpbody-content -->\n";
}

echo $pageHTML_Header . $pageHTML_Body . $pageHTML_Footer;
?>
