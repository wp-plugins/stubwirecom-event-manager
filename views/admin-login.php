<?php
// Update the Settings
$login_emailaddress		= "";
$login_password				= "";

if (isset($_POST['login_emailaddress']))
	$login_emailaddress		= $_POST['login_emailaddress'];
if (isset($_POST['login_password']))
	$login_password				= $_POST['login_password'];

$boolLoginStatus = false;
if (isset($_POST['FormAction']) && $_POST['FormAction']=='login') {
  $clients = $this->get_ClientsToAccessFromStubWire($login_emailaddress, $login_password);
  
  if (!isset($clients) || empty($clients))	{
  	$pageHTML_Body .= "<div class=\"error\"><strong>ERROR</strong><br>The login information you entered is not valid. Please try again.</div>";
  	$login_password = "";
  }	else	{
  	$boolLoginStatus = true;
  	$pageHTML_Body .= "<div id=\"message\" class=\"updated fade\"><p><strong>Login Information Saved</strong></p>You can now edit the settings by <a href=\"" . $currentPageURL . "&subpage=admin-settings\">CLICKING HERE</a>.</div>";

	  update_option('stubwire_login_emailaddress', $login_emailaddress);
	  update_option('stubwire_login_password', $login_password);
  }
}	else	{
	if (empty($login_emailaddress))	{
		// First time they are loading the page so lets give them the last data
	  $login_emailaddress		= get_option('stubwire_login_emailaddress');
	}
}

if (!$boolLoginStatus)	{
	$pageHTML_Body .= "<p class=\"install-help\">Insert your login information for StubWire.com to be able to access your client records.(" . get_option('stubwire_Promoted') . ")</p>\n";
	$pageHTML_Body .= "<form method=\"POST\" action=\"" . $currentPageURL . "&subpage=admin-login\">\n";
	$pageHTML_Body .= "<input type=\"hidden\" name=\"FormAction\" id=\"FormAction\" value=\"login\">\n";
	$pageHTML_Body .= "<h3>Login Information</h3>\n";
	$pageHTML_Body .= "<ul>\n";
	$pageHTML_Body .= "	<li><label for=\"login_emailaddress\">Email Address<span> *</span>: </label>\n";
	$pageHTML_Body .= "	<input maxlength=\"45\" size=\"20\" name=\"login_emailaddress\" id=\"login_emailaddress\" value=\"" . $login_emailaddress . "\" /></li>\n";
	$pageHTML_Body .= "	<li><label for=\"login_password\">Password<span> *</span>: </label>\n";
	$pageHTML_Body .= "	<input maxlength=\"45\" size=\"20\" name=\"login_password\" id=\"login_password\" value=\"" . $login_password . "\" type=\"password\" /></li>\n";
	$pageHTML_Body .= "</ul>\n";
	$pageHTML_Body .= "<input class=\"button-primary\" type=\"submit\" name=\"Save\" value=\"Login To StubWire Account\" id=\"submitbutton\" />\n";
	$pageHTML_Body .= "</form>\n";
}

echo $pageHTML_Header . $pageHTML_Body . $pageHTML_Footer;
?>