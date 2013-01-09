<h3>Import Events</h3>
<?
	// Set the page's timeout to 60 mins
	set_time_limit(3600);
	
if (isset($_GET['process']) && $_GET['process']='1')	{
	$additionalSearch = "";
	if (isset($_GET['allevents']) && $_GET['allevents']='1')	{
		$additionalSearch = "&alldates=1";
	}
	$pageHTML_Body = $this->get_EventsFromStubWire(true, $additionalSearch);
}	else	{
	$pageHTML_Body = "<script>\n";	
	$pageHTML_Body .= "function updateEvents()	{\n";
	$pageHTML_Body .= "	window.location = '?page=stubwire.class.php&subpage=admin-import-events&process=1'\n";
	$pageHTML_Body .= "}\n";
	$pageHTML_Body .= "function updateAllEvents()	{\n";
	$pageHTML_Body .= "	window.location = '?page=stubwire.class.php&subpage=admin-import-events&process=1&allevents=1'\n";
	$pageHTML_Body .= "}\n";
	$pageHTML_Body .= "</script>\n";	
	$pageHTML_Body .= "<p>This will go out to StubWire and update all your current events.</p>\n";
	$pageHTML_Body .= "<div><input class=\"button-primary\" type=\"button\" name=\"btnUpdateEvents\" id=\"btnUpdateEvents\" value=\"Update Events\" onclick=\"updateEvents();\" /></div>\n";

	$pageHTML_Body .= "<h1>Additional Import Options</h1>\n";
	$pageHTML_Body .= "<p>If you want to import all your old events to make sure everything is listed, please select the button below.<br><b>PLEASE NOTE:</b> You should only have to do this once, unless you uninstall the WordPress Plugin<br><font color=red>This process might take a long time depending on how many events you have listed on StubWire.com</font></p>\n";
	$pageHTML_Body .= "<div><input class=\"button-primary\" type=\"button\" name=\"btnUpdateEvents\" id=\"btnUpdateEvents\" value=\"Update ALL Events\" onclick=\"updateAllEvents();\" /></div>\n";
}
echo $pageHTML_Header . $pageHTML_Body . $pageHTML_Footer;
?>