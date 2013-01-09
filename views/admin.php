<?php
// Lets get and store the login information
$stubwire_login_emailaddress		= get_option('stubwire_login_emailaddress');
$stubwire_login_password				= get_option('stubwire_login_password');

$currentQuery = $_GET;
if (isset($_GET['subpage']))
	$currentPage	= trim($_GET['subpage']);
else
	$currentPage	= "admin-settings";
if (isset($_GET['subsubpage']))
	$currentSubPage	= trim($_GET['subsubpage']);
else
	$currentSubPage	= "";

// Lets build the current page URL
$newQuery = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1) . "?";
foreach ($currentQuery as $field => $value)	{
	if ($field=='subpage' || $field=='subsubpage')	{
		// We dont want to add the admin page
	}	else	{
		$newQuery .= $field . '=' . $value . '&';
	}
}
$currentPageURL = rtrim($newQuery, '&');

if (empty($currentPage))	{
	$currentPage = "admin-settings";
}

$pageHTML_Header			= "";
$pageHTML_SubHeader		= "";
$pageHTML_Body				= "";
$pageHTML_Footer			= "";

$pageHTML_Header .= "<div class=\"wrap\">\n";
$pageHTML_Header .= "	<div id=\"icon-tools\" class=\"icon32\"><br /></div>\n";
$pageHTML_Header .= "	<h2 class=\"nav-tab-wrapper\">\n";

if (!empty($stubwire_login_emailaddress) && !empty($stubwire_login_password))	{
	$pageHTML_Header .= "		<a href=\"?page=stubwire.class.php&subpage=admin-settings\" class=\"nav-tab";
	if ($currentPage=='admin-settings')
		$pageHTML_Header .= " nav-tab-active";
	$pageHTML_Header .= "\">Settings</a>\n";
	$pageHTML_Header .= "		<a href=\"?page=stubwire.class.php&subpage=admin-templates\" class=\"nav-tab";
	if ($currentPage=='admin-templates')
		$pageHTML_Header .= " nav-tab-active";
	$pageHTML_Header .= "\">Templates</a>\n";
	$pageHTML_Header .= "		<a href=\"?page=stubwire.class.php&subpage=admin-events\" class=\"nav-tab";
	if ($currentPage=='admin-events')
		$pageHTML_Header .= " nav-tab-active";
	$pageHTML_Header .= "\">Events</a>\n";
	$pageHTML_Header .= "		<a href=\"?page=stubwire.class.php&subpage=admin-stats\" class=\"nav-tab";
	if ($currentPage=='admin-stats')
		$pageHTML_Header .= " nav-tab-active";
	$pageHTML_Header .= "\">Stats</a>\n";
	$pageHTML_Header .= "		<a href=\"?page=stubwire.class.php&subpage=admin-import-events\" class=\"nav-tab";
	if ($currentPage=='import-events')
		$pageHTML_Header .= " nav-tab-active";
	$pageHTML_Header .= "\">Import Events</a>\n";
}	else	{
	$pageHTML_Header .= "		<a href=\"?page=stubwire.class.php&subpage=admin-login\" class=\"nav-tab";
	if ($currentPage=='admin-login')
		$pageHTML_Header .= " nav-tab-active";
	$pageHTML_Header .= "\">Login</a>\n";
}
$pageHTML_Header .= "	</h2>\n";

include($currentPage . ".php");
?>
</div>