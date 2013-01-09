<?php
/*
Plugin Name: StubWire.com Event Manager
Plugin URI: http://plugins.svn.wordpress.org/stubwirecom-event-manager/
Description: Allows you to list out all your events from StubWire.com by placing a small shortcode on the page to display the content. You will then be able to edit the templates to display as much, or as little information about each event you have scheduled. A Buy Now button will be placed on your site with a tracking code so you know exactly where your visitors came from inside your StubWire.com Reporting Features.
Version: 10
Author: StubWire.com
Author URI: http://www.stubwire.com/
Text Domain: events
Last Updated: 09/07/12
*/

define('STUBWIRE_PLUGIN_VERSION','10');
define('STUBWIRE_API_VERSION','7');
define('STUBWIRE_API_URL','website.api.stubwire.com/');
define('STUBWIRE_TABLE_PREFIX','stubwire_'); //Slider TABLE NAME
define('STUBWIRE_SCHEDULED_EVENT_ACTION', 'stubwire_cron_handler'); # For WordPress Scheduled Event handler

// VERSION 8 ADDED 11/28/2012
// - Fixed error when domain was not on the approved domain
// - Added setting so it can send the detailed logs

// BRAD - The function register_activation_hook (introduced in WordPress 2.0) registers a plugin function to be run when the plugin is activated.
register_activation_hook(__FILE__, array('StubWire', 'install'));
register_deactivation_hook(__FILE__, array('StubWire', 'uninstall'));

// BRAD - See Plugin API/Action Reference for a list of hooks for action. Actions are (usually) triggered when the Wordpress core calls do_action(). 
// BRAD - Runs in the HTML <head> section of the admin panel to check if its running the correct version of php
add_action( 'admin_head', 'stubwire_version_check' );
add_action( 'admin_init', 'stubwire_settings_register' );
add_action(STUBWIRE_SCHEDULED_EVENT_ACTION, 'stubwire_cron_handler'); # Used by the WordPress Event Scheduler

// THE FOLLOWING WILL FORCE THE CRON TO RUN RIGHT AWAY
//wp_schedule_single_event(time(), 'stubwire_cron_handler');

function stubwire_load_widgets()	{
	//register_widget('StubWireWidget');
	register_widget('SW_EventList');

}

function stubwire_version_check() {
	// BRAD - Called by add_action for the admin_head to display an error in the head if its not running the correct php version
	if ( version_compare( PHP_VERSION, "5.1", "<") ) {
		echo "<div class='error'>The StubWire Plugin requires PHP 5.1 or greater.  Please de-activate StubWire Plugin.</div>";
	}
}
function stubwire_settings_register()	{
	global $stubWire;

	//$stubWire->Upgrade_From_9();
}
function stubwire_install() {
		global $stubWire;
		// BRAD - They would like to install the plugin
		
		// BRAD - Lets make sure they are using the correct version of php
    if ( version_compare( PHP_VERSION, "5.1", "<") ) {
        trigger_error('', E_USER_ERROR);
    } else {
	    require_once(dirname(__FILE__) . "/stubwire.class.php");
	    require_once(dirname(__FILE__) . "/template-tags.php");

			$stubWire->Install();
    }
}
function stubwire_uninstall() {
		global $stubWire;
		// BRAD - They would like to uninstall the plugin
		//echo "Lets call the StubWire Class so we can do stubwire_uninstall<br>";
    require_once(dirname(__FILE__) . "/stubwire.class.php");
    require_once(dirname(__FILE__) . "/template-tags.php");
    /*echo "Required the stubwire class so lets move on in xxxxx<br>";
    if (is_object($stubWire))	{
    	
    }	else	{
    	
    }*/
    
    $stubWire->UnInstall();
}

function stubwire_plugin_actionlinks($links, $file){
	static $stubWire;
	if( !$stubWire ) $stubWire = plugin_basename(__FILE__);
	if( $file == $stubWire )	{
		$settings_link = '<a href="plugins.php?page=stubwire.class.php">' . __('Settings') . '</a>';
		$links = array_merge( array($settings_link), $links); // before other links
	}
	return $links;
}

if (version_compare(phpversion(), "5.1", ">=")) {
    require_once(dirname(__FILE__) . "/stubwire.class.php");
    require_once(dirname(__FILE__) . "/stubwire_widget.class.php");
    require_once(dirname(__FILE__) . "/template-tags.php");
    
	require_once(dirname(__FILE__) . '/ui-functions.php');
	require_once(dirname(__FILE__) . '/class-sw-eventlist.php');
}

// BRAD - Lets setup the widgets we would like to use
add_action( 'widgets_init', 'stubwire_load_widgets' );

add_filter('the_content','stubwire_substitute_events');
add_filter('the_content','stubwire_substitute_event');

add_filter( 'plugin_action_links', 'stubwire_plugin_actionlinks',10,2);