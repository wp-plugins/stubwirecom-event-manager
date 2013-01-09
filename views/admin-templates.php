<?php
if (empty($stubwire_login_emailaddress) || empty($stubwire_login_password))	{
	$pageHTML_Body .= "<div class=\"error\"><strong>ERROR</strong><br>The login information you entered is not valid. Please login again by <a href=\"?page=stubwire.class.php&subpage=admin-login\">CLICKING HERE</a></div>";
}	else	{
	if (isset($_GET['action']) && $_GET['action']=='delete' && $_GET['template']!='')	{
		$templateInfo = $this->get_StubWireTemplateInfo($_GET['template']);
		
		if (!is_array($templateInfo) || !isset($templateInfo['id']) || empty($templateInfo['id']))	{
			$pageHTML_Body .= "<div class=\"error\"><strong>ERROR</strong><br>You are unable to delete this template as it could not be located</div>";
		}	elseif (!isset($templateInfo['id']) || empty($templateInfo['id']) || strtolower($templateInfo['default'])=='yes')	{
			$pageHTML_Body .= "<div class=\"error\"><strong>ERROR</strong><br>You are unable to delete this template as its the default template when we are unable to find the selected template.</div>";
		}	else	{
			$this->delete_Template($templateInfo);
			$pageHTML_Body .= "<div id=\"message\" class=\"updated fade\"><p><strong>The template has been deleted</strong></p></div>";
		}
	}
	
	$pageHTML_Body .= "<script>\n";	
	$pageHTML_Body .= "function editTemplate(id)	{\n";
	$pageHTML_Body .= "	window.location = '?page=stubwire.class.php&subpage=admin-template&action=edit&template=' + id\n";
	$pageHTML_Body .= "}\n";
	$pageHTML_Body .= "function codeTemplate(id)	{\n";
	$pageHTML_Body .= "	window.location = '?page=stubwire.class.php&subpage=admin-template&action=code&template=' + id\n";
	$pageHTML_Body .= "}\n";
	$pageHTML_Body .= "</script>\n";
	
	$pageHTML_Body .= "<h3>Event Templates</h3>\n";
	$pageHTML_Body .= "<p class=\"install-help\">Listed below are the templates that are available for you to use in listing out the events.</p>\n";
	$pageHTML_Body .= "<div id=\"publishing-action\"><input type=\"button\" name=\"AddNew\" id=\"AddNew\" class=\"button-primary\" value=\"Add New Template\" tabindex=\"5\" onclick=\"window.location='?page=stubwire.class.php&subpage=admin-template&action=add&template=';\"  /></div>\n";
	$pageHTML_Body .= "<table class=\"wp-list-table widefat\" cellspacing=\"0\" border=\"1\">\n";
	$pageHTML_Body .= "	<thead>\n";
	$pageHTML_Body .= "		<tr>\n";
	$pageHTML_Body .= "			<th>Name</th>\n";
	$pageHTML_Body .= "			<th>File Name</th>\n";
	$pageHTML_Body .= "			<th>Last Updated</th>\n";
	$pageHTML_Body .= "			<th>&nbsp;</th>\n";
	$pageHTML_Body .= "			<th>&nbsp;</th>\n";
	$pageHTML_Body .= "		</tr>\n";
	$pageHTML_Body .= "	</thead>\n";
	$pageHTML_Body .= "	<tbody id=\"the-list\">\n";
	
	foreach ($this->get_StubWireAllTemplates() as $template)	{		
		$pageHTML_Body .= "		<tr valign=\"top\">\n";
		$pageHTML_Body .= "			<td>" . $template['name'];
		if (strtolower($template['default'])=='yes' || strtolower($template['isdefault'])=='yes')	{
			$pageHTML_Body .= " <font color='red'><i>(default)</i></font>";
		}
		$pageHTML_Body .= "</td>\n";
		$pageHTML_Body .= "			<td>" . $template['filename'] . "</td>\n";
		$pageHTML_Body .= "			<td>" . $template['LastUpdatedAt'] . "</td>\n";
		$pageHTML_Body .= "			<td><input class=\"button-primary\" type=\"button\" name=\"EditTemplate" . $template['id'] . "\" id=\"EditTemplate" . $template['id'] . "\" value=\"Edit Template\" onclick=\"editTemplate('" . $template['filename'] . "');\" /></td>\n";
		$pageHTML_Body .= "			<td><input class=\"button-primary\" type=\"button\" name=\"CodeTemplate" . $template['id'] . "\" id=\"CodeTemplate" . $template['id'] . "\" value=\"Template Code\" onclick=\"codeTemplate('" . $template['filename'] . "');\" /></td>\n";
		$pageHTML_Body .= "		</tr>\n";
	}

	$pageHTML_Body .= "	</tbody>\n";
	$pageHTML_Body .= "</table>\n";
}

echo $pageHTML_Header . $pageHTML_Body . $pageHTML_Footer;
?>