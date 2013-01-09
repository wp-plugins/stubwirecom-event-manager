<?php
 // Test for StubWire plugin. Make smarter later, with message to user about installing StubWire first.
 if ( !class_exists( 'StubWire' ) ) {
	class StubWire {		
		/**************************************************************
		 * StubWire Configuration
		 **************************************************************/		
		public $pluginDir; // DYNAMICALLY SET
		public $stubwireAPIBaseURL			= ''; // DYNAMICALLY SET
		public $stubwireAPITransport		= ''; // DYNAMICALLY SET
		
		public $stubwireDebugImportEvents	= false;
		public $stubwireDebugShowQuerys		= false;
		public $stubwireDebugImportEvent	= "2990"; // If debug is on, it will only list out this event id. If no event id it will display data from all events

		function __construct() {
			$this->set_Variables();
			$this->set_Hooks();
		}
		
		/**************************************************************
		 * StubWire PUBLIC FUNCTIONS
		 **************************************************************/
		function CheckForUpgradeAvailable()	{
			// First we would call the API URL and pass the current version and return true or false
			// TODO: Add check, returning true by default for now ( $CurrentVersionNumber )
			return true;
		}
		function Upgrade($currentVersion)	{
			global $wpdb;
			
			//update_option('stubwire_plugin_version', "4");
			
			$oldVersion = get_option('stubwire_plugin_version');
			
			if ( $oldVersion != $currentVersion ) {
				if ( version_compare( PHP_VERSION, "5.1", "<") || !current_user_can( 'activate_plugins' )) {
					trigger_error('', E_USER_ERROR);
				}	else	{					
					if ($oldVersion=='1' || $oldVersion=='2' || $oldVersion=='3')	{
						$this->Upgrade_From_3();
					}	elseif ($oldVersion=='4')	{
						$this->Upgrade_From_4();
					}	elseif ($oldVersion=='5' || $oldVersion=='6' || $oldVersion=='7')	{
						// NOTHING NEEDS TO BE CHANGED BETWEEN VERSIONS 5, 6 &7
					}	elseif ($oldVersion=='8')	{
						$this->Upgrade_From_8();
					}	elseif ($oldVersion=='9')	{
						$this->Upgrade_From_9();
					}	else	{
						echo "Unknown Version to upgrade from. This version=(" . $currentVersion . ") old installed version=(" . $oldVersion . ")<br>";
						die;
					}				
					
					update_option('stubwire_plugin_version', STUBWIRE_PLUGIN_VERSION);
				}
			}
		}
		function Upgrade_From_3()	{
			global $wpdb;

			// BRAD - Install DB tables
			$tablePrefix = $wpdb->prefix . STUBWIRE_TABLE_PREFIX;
				
			$sql = "ALTER TABLE `" . $tablePrefix . "events` ADD `orderEntryBoxOfficeCashServiceFee` varchar(5) NULL";
			if ( $wpdb->query($sql) === false )	{
				echo "Error during the alter table<br>";
				
				echo $sql;
				die;
			}
			$this->Upgrade_From_4();
		}
		function Upgrade_From_4()	{
			global $wpdb;

			// BRAD - Install DB tables
			$tablePrefix = $wpdb->prefix . STUBWIRE_TABLE_PREFIX;
				
			$sql = "ALTER TABLE `" . $tablePrefix . "images` ADD `type` varchar(25) NULL";
			if ( $wpdb->query($sql) === false )	{
				echo "Error during the alter table<br>";
				
				echo $sql;
				die;
			}
		}
		function Upgrade_From_8()	{
			global $wpdb;

			// BRAD - Install DB tables
			$tablePrefix = $wpdb->prefix . STUBWIRE_TABLE_PREFIX;
				
			$sql = "ALTER TABLE `" . $tablePrefix . "acts` ADD `status` varchar(25) NULL";
			if ( $wpdb->query($sql) === false )	{
				echo "Error during the alter table<br>";
				
				echo $sql;
				die;
			}
			$this->Upgrade_From_9();
		}
		function Upgrade_From_9()	{
			global $wpdb;

			// BRAD - Install DB tables
			$tablePrefix = $wpdb->prefix . STUBWIRE_TABLE_PREFIX;

				$sql = "DROP TABLE IF EXISTS " . $tablePrefix . "templates";
				$wpdb->query($sql);
				
				$sql = "CREATE TABLE `" . $tablePrefix . "templates` (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`name` varchar(128),
							`filename` varchar(128),
							`template` text,
							`isdefault` enum('No','Yes') NOT NULL DEFAULT 'No',
							`LastUpdatedAt` datetime,
							PRIMARY KEY  (`id`),
							UNIQUE KEY `" . STUBWIRE_TABLE_PREFIX . "TemplateID` (`id`),
							UNIQUE index `filename` (`name`)
               );";
			if ( $wpdb->query($sql) === false )	{
				echo "Error during the alter table<br>";
				
				echo $sql;
				die;
			}

				$sql = "insert into `" . $tablePrefix . "templates` (`id`, `name`, `filename`, `template`, `isdefault`, `LastUpdatedAt`) values('1','Default - Event Listing','default_event_listing','','Yes','2013-01-01 00:00:00');";
			if ( $wpdb->query($sql) === false )	{
				echo "Error during the alter table<br>";
				
				echo $sql;
				die;
			}
				
				$sql = "insert into `" . $tablePrefix . "templates` (`id`, `name`, `filename`, `template`, `isdefault`, `LastUpdatedAt`) values('2','Default - Event','default_event','','Yes','2013-01-01 00:00:00');";
			if ( $wpdb->query($sql) === false )	{
				echo "Error during the alter table<br>";
				
				echo $sql;
				die;
			}
				
				$sql = "insert into `" . $tablePrefix . "templates` (`id`, `name`, `filename`, `template`, `isdefault`, `LastUpdatedAt`) values('3','Default - Widget Listing','default_widget_listing','','Yes','2013-01-01 00:00:00');";
			if ( $wpdb->query($sql) === false )	{
				echo "Error during the alter table<br>";
				
				echo $sql;
				die;
			}
		}
		
		function Install()	{
			global $wpdb;
			
			if ( version_compare( PHP_VERSION, "5.1", "<") || !current_user_can( 'activate_plugins' )) {
				trigger_error('', E_USER_ERROR);
			}	else	{
				// BRAD - Install DB tables
				$tablePrefix = $wpdb->prefix . STUBWIRE_TABLE_PREFIX;
				
				$sql = "DROP TABLE IF EXISTS " . $tablePrefix . "events";
				$wpdb->query($sql);
               
				$sql = "CREATE TABLE `" . $tablePrefix . "events` (
							`id` int(11) NOT NULL,
							`wp_postid` int(11),
							`venueid` int(11),
							`venueroomname` varchar(100),
							`name` varchar(100),
							`isParentEvent` enum('No','Yes'),
							`parentEventID` int(11),
							`parentEarliestChildDate` dateTime,
							`parentLatestChildDate` dateTime,
							`isParentEventPurchasesEnabled` enum('No','Yes'),
							`url` varchar(250),
							`shortDescription` text,
							`fullDescription` text,
							`dateTime` dateTime,
							`doorsOpenAt` varchar(32),
							`isSpotlightEvent` enum('No','Yes'),
							`isFeaturedEvent` enum('No','Yes'),
							`isPromoterPickEvent` enum('No','Yes'),
							`ticketsAvailable` enum('No','Yes'),
							`ticketsCountAvailable` int(11),
							`ticketsCountPurchased` int(11),
							`timeOnSale` dateTime,
							`timeOffSale` dateTime,
							`ageDescription` varchar(32),
							`ticketPriceAdvance` varchar(50),
							`ticketPriceDoor` varchar(50),
							`ticketPriceDoorLabel` varchar(50),
							`orderEntryBoxOfficeCashServiceFee` varchar(5),
							`buyNowLink` varchar(250),
							`eventImage` varchar(150),
							`eventImageURLSmall` varchar(150),
							`eventImageURLMedium` varchar(150),
							`eventImageURLOriginal` varchar(150),
							`sellableTroughInternet` enum('No','Yes'),
							`sellableTroughInternetReason` varchar(250),
							`eventAdminAccess` enum('No','Yes'),
							`eventStatus` varchar(50),
							`facebookEventID` varchar(75),
							`facebookEventURL` varchar(75),
							`facebookWallPosts` text,
							`facebookAttending` text,
							`LastUpdatedAt` datetime,
							PRIMARY KEY  (`id`),
							UNIQUE KEY `" . STUBWIRE_TABLE_PREFIX . "EventID` (`id`)                                                     
               );";
				$rs = $wpdb->query($sql);

				$sql = "DROP TABLE IF EXISTS " . $tablePrefix . "venues";
				$wpdb->query( $sql );
			
				$sql = "CREATE TABLE " . $tablePrefix . "venues (
							id int(11) NOT NULL,
							name varchar(128),
							address varchar(128),
							address2 varchar(128),
							city varchar(64),
							state varchar(10),
							zip varchar(12),
							url varchar(150),
							image varchar(150),
							`LastUpdatedAt` datetime,
							PRIMARY KEY  (`id`),
							UNIQUE KEY `" . STUBWIRE_TABLE_PREFIX . "VenueID` (`id`)                                                     
               );";
				$rs = $wpdb->query($sql);

				$sql = "DROP TABLE IF EXISTS " . $tablePrefix . "acts";
				$wpdb->query( $sql );
			
				$sql = "CREATE TABLE `" . $tablePrefix . "acts` (
							`id` int(11) NOT NULL,
							`eventid` int(11),
							`name` varchar(100),
							`url` varchar(100),
							`type` varchar(128),
							`status` varchar(25),
							`displayorder` tinyint(2),
							`LastUpdatedAt` datetime,
							PRIMARY KEY  (`id`),
							UNIQUE KEY `" . STUBWIRE_TABLE_PREFIX . "ActID` (`id`)                                                     
               );";
				$rs = $wpdb->query($sql);

				$sql = "DROP TABLE IF EXISTS `" . $tablePrefix . "images`";
				$wpdb->query( $sql );
			
				$sql = "CREATE TABLE `" . $tablePrefix . "images` (
							`id` int(11) NOT NULL,
							`eventid` int(11),
							`ismainimage` tinyint(1),
							`isposterimage` tinyint(1),
							`name` varchar(128),
							`caption` text,
							`filename` varchar(255),
							`displayorder` tinyint(2),
							`status` varchar(50),
							`LastUpdatedAt` datetime,
							PRIMARY KEY  (`id`),
							UNIQUE KEY `" . STUBWIRE_TABLE_PREFIX . "ImageID` (`id`)                                                     
               );";
				$rs = $wpdb->query($sql);

				/*$sql = "DROP TABLE IF EXISTS `" . $tablePrefix . "templates`";
				$wpdb->query( $sql );*/
				
				$sql = "CREATE TABLE `" . $tablePrefix . "templates` (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`name` varchar(128),
							`filename` varchar(128),
							`template` text,
							`isdefault` enum('No','Yes') NOT NULL DEFAULT 'No',
							`LastUpdatedAt` datetime,
							PRIMARY KEY  (`id`),
							UNIQUE KEY `" . STUBWIRE_TABLE_PREFIX . "TemplateID` (`id`),
							UNIQUE index `filename` (`name`)
               );";
				$rs = $wpdb->query($sql);

$sql = "insert into `" . $tablePrefix . "templates` (`id`, `name`, `filename`, `template`, `isdefault`, `LastUpdatedAt`) values('1','Default - Event Listing','default_event_listing','','Yes','2013-01-01 00:00:00');";
$rs = $wpdb->query($sql);

$sql = "insert into `" . $tablePrefix . "templates` (`id`, `name`, `filename`, `template`, `isdefault`, `LastUpdatedAt`) values('2','Default - Event','default_event','','Yes','2013-01-01 00:00:00');";
$rs = $wpdb->query($sql);

$sql = "insert into `" . $tablePrefix . "templates` (`id`, `name`, `filename`, `template`, `isdefault`, `LastUpdatedAt`) values('3','Default - Widget Listing','default_widget_listing','','Yes','2013-01-01 00:00:00');";
$rs = $wpdb->query($sql);
				

				// BRAD - Add cron job so
				wp_schedule_event(time(), 'hourly', 'stubwire_cron_handler');
				
				update_option('stubwire_plugin_version', STUBWIRE_PLUGIN_VERSION);
			}
		}
		function UnInstall()	{
			
			try	{
				if (is_object($this))	{
					$this->deleteAllEvents();
				}
			} catch (Exception $e) {
				// TODO: Catch this ERROR
			} catch( TEC_Post_Exception $e ) {
				// TODO: Catch this ERROR
			}

			// Lets make sure the CRON is removed
			wp_clear_scheduled_hook('stubwire_cron_handler'); # Remove cron
		}
		
		function set_Variables()	{
			$this->pluginDir    = basename(dirname(__FILE__));
			
			// use SSL if we support it
			$version = curl_version();
			$this->stubwireAPITransport = ($version['features'] & CURL_VERSION_SSL ? 'https' : 'http'); 
			$this->stubwireAPIURL = $this->stubwireAPITransport . '://';
			
			$this->stubwireAPIBaseURL		= $this->add_QueryString(STUBWIRE_API_URL,"website=" . $this->get_CurrentDomain());
			$this->stubwireAPIBaseURL		= $this->add_QueryString($this->stubwireAPIBaseURL,"version=" . STUBWIRE_API_VERSION);
			$this->stubwireAPIBaseURL		= $this->add_QueryString($this->stubwireAPIBaseURL,"plugin_version=" . STUBWIRE_PLUGIN_VERSION);
			$this->stubwireAPIBaseURL		= $this->add_QueryString($this->stubwireAPIBaseURL,"application=wordpress");
			$this->stubwireAPIBaseURL		= $this->add_QueryString($this->stubwireAPIBaseURL,"application_version=" . $GLOBALS['wp_version']);
		}
		function set_Hooks()	{
			add_action( 'admin_menu', 		array( $this, 'addAction_PluginsMenu' ) );
		}
		function add_QueryString($url,$query)	{
			$pos = strpos($url, "?");
			if ($pos === false) {
				$url .= "?" . $query;
			}	else	{
				$url .= "&" . $query;
			}
			
			return $url;
		}
		function get_CurrentDomain()	{
			$domain = "";
			if (isset($_SERVER['HTTP_HOST']))
				$domain = trim(strtolower($_SERVER['HTTP_HOST']));
			if (empty($domain) && isset($_SERVER['SERVER_NAME']))
				$domain = trim(strtolower($_SERVER['SERVER_NAME']));
			if (empty($domain))
				$domain = "unknown";
			
			$domain = trim(strtolower($domain));

			if (strlen($domain)>3 && substr($domain, 0, 4)=='www.')	{
				$domain = substr($domain, 4);
			}

			$domain = "WP_" . $domain;
			
			return $domain;
		}
		
		function get_PluginStats()	{
			global $wpdb;
			
			$stats = array();
			$stats['Events Last Imported']  = get_option('stubwire_EventsLastUpdated');
			
			$tablePrefix = $wpdb->prefix . STUBWIRE_TABLE_PREFIX;
			
			$query = "SELECT ";
			$query .= "COUNT(`id`) AS TotalEvents ";
			$query .= "FROM " . $tablePrefix . "events ";
			$query = $wpdb->prepare($query,"");
			$dbResults = $wpdb->get_results($query,ARRAY_A);
			
			$stats['Total Events']  = $dbResults[0]['TotalEvents'];
			
			return $stats;
		}

		function deleteAllEvents()	{
			global $wpdb;
			
			@set_time_limit(0);

			/*echo "<H1>deleteAllEvents - WHY ARE WE DELETING</H1>";
			die;*/
			
			$tablePrefix = $wpdb->prefix . STUBWIRE_TABLE_PREFIX;
			
			$queryEvents = "SELECT ";
			$queryEvents .= "`id`, ";
			$queryEvents .= "`wp_postid` ";
			$queryEvents .= " FROM " . $tablePrefix . "events";
			$queryEvents .= " ORDER BY `wp_postid` ASC";
			$query = $wpdb->prepare($queryEvents,"");
			$allEvents = $wpdb->get_results($query,ARRAY_A);
			
			foreach ($allEvents as $event)	{
				$this->deleteEvent($event['id'], $event['wp_postid']);
			}

			// Now lets clean up and delete all the venues
			$sql = "DELETE FROM " . $tablePrefix . "venues";
			$wpdb->query( $sql );
			
			// Now that all the posts for these events deleted, lets delete the events
			/*$sql = "DELETE FROM " . $tablePrefix . "events";
			$wpdb->query( $sql );

			$sql = "DELETE FROM " . $tablePrefix . "venues";
			$wpdb->query( $sql );

			$sql = "DELETE FROM " . $tablePrefix . "acts";
			$wpdb->query( $sql );

			$sql = "DELETE FROM " . $tablePrefix . "images";
			$wpdb->query( $sql );*/
		}
		
		function deleteEvent($eventID, $wpPostID)	{
			// 07/26/12 - BRAD - This function is new and hasnt been built in yet
			global $wpdb;
			
			@set_time_limit(0);
			
			if (!is_numeric($eventID))	{
				// EVENT ID DOES NOT APPEAR TO BE VALID
				echo "[" . __FILE__ . "][line:" . __LINE__ . "] Unable to delete event as the EventID does not appear to be valid";
				die;
			}
			
			$tablePrefix = $wpdb->prefix . STUBWIRE_TABLE_PREFIX;
			
			if (empty($wpPostID) || !is_numeric($wpPostID))	{
				// WE NEED TO FIND THE WP POST ID FOR THIS EVENT

				$sqlQuery = "SELECT `wp_postid` FROM " . $tablePrefix . "events as events WHERE `events`.`id`='%d'";
				$query = $wpdb->prepare($sqlQuery, $eventID);
				$stubwire_event_check = $wpdb->get_results($query,ARRAY_A);

				if (isset($stubwire_event_check[0]))	{
					$wpPostID = $stubwire_event_check[0]['wp_postid'];
				}
			}
			
			$sql = "DELETE FROM " . $tablePrefix . "events where id= '" . $wpdb->escape($eventID) . "'";
			$wpdb->query( $sql );
			
			$sql = "DELETE FROM " . $tablePrefix . "acts where eventid= '" . $wpdb->escape($eventID) . "'";
			$wpdb->query( $sql );
			
			$sql = "DELETE FROM " . $tablePrefix . "images where eventid= '" . $wpdb->escape($eventID) . "'";
			$wpdb->query( $sql );
			
			if (empty($wpPostID) || !is_numeric($wpPostID))	{
				// WE ARE NOT ABLE TO DELETE THIS POST AS THE WP ID IS EMPTY
			}	else	{
				wp_delete_post($wpPostID, true);
			}
		}
		function check_ContainsHTML($string)	{
			if (preg_match("/([\<])([^\>]{1,})*([\>])/i", $string)) {
			    return true;
			} 
			return false;
		}
		function get_StubWireTemplateInfo($fileName)	{
			global $wpdb;
			
			$tablePrefix = $wpdb->prefix . STUBWIRE_TABLE_PREFIX;
			
			// PULL TEMPLATE FROM THE DB
			$queryCheck = "SELECT
												  `id`,
												  `name`,
												  `filename`,
												  `template`,
												  `isdefault`,
												  `LastUpdatedAt`
												FROM
													`" . $tablePrefix . "templates`
												WHERE
													`filename`='%s'";
			$query = $wpdb->prepare($queryCheck, $fileName);
			$getID = $wpdb->get_results($query,ARRAY_A);

			if (!empty($getID[0]['id']))	{
				$template										= array();
				$template['id']							= $getID[0]['id'];
				$template['name']						= $getID[0]['name'];
				$template['filename']				= $getID[0]['filename'];
				$template['template']				= $getID[0]['template'];
				$template['default']				= $getID[0]['isdefault'];
				$template['lastupdated']		= $getID[0]['LastUpdatedAt'];

				$plugin_dir_path = dirname(__FILE__);
				$template_directory = $plugin_dir_path . "/templates/";
				$template['filefullpath']				= $plugin_dir_path . "/templates/" . $template['filename'] . ".php";
				$template['filepath']						= $template['filefullpath'];

				// LETS MAKE SURE THIS FILE IS STORED ON THE SERVER, IF NOT LETS STORE IT
				if (file_exists($template['filefullpath']) && empty($template['template'])) {
					$template['template'] = file_get_contents($template['filefullpath']);

					if (!empty($template['template']))	{
						// LETS SAVE THIS TEMPLATE WE JUST READ TO THE DB
						$queryUpdate = "UPDATE
																" . $tablePrefix . "templates
														SET
																`template`			= %s
														WHERE
																`id`						= %s";
						$query = $wpdb->prepare($queryUpdate,$template['template'],$template['id']);
						$wpdb->get_results($query,ARRAY_A);
					}
				}
				
				// LETS MAKE SURE THIS FILE IS STORED ON THE SERVER, IF NOT LETS STORE IT
				if (!file_exists($template['filefullpath']) && !empty($template['template'])) {
					file_put_contents($template['filefullpath'], $template['template']);
				}
			
				return $template;
			}	else	{
				// CANT FIND IT IN THE DATABASE SO LETS CONTINUE AND LOOK AT THE FILES ON THE SERVER
				//echo "Cant find (" . $fileName . ")<br>";
				$plugin_dir_path = dirname(__FILE__);
				$template_directory = $plugin_dir_path . "/templates/";
				$fileFullPath = $plugin_dir_path . "/templates/" . $fileName . ".php";

				if (!file_exists($fileFullPath))	{
					echo "not found (" . $fileFullPath . ")<br>";
					return "";
				}
				
				$fileContents = file_get_contents($fileFullPath);

				$updateDetails = array();
				$updateDetails['id']				= "";
				$updateDetails['name']			= $fileName;
				$updateDetails['filename']	= $fileName;
				$updateDetails['template']	= $fileContents;

				/*echo "<hr><pre>";
				print_r($updateDetails);
				echo "</pre><hr>";*/
				$results = $this->update_Template($updateDetails);
				
				return $results;
				/*echo "<hr><pre>";
				print_r($results);
				echo "</pre><hr>";*/
			}
			
			$plugin_dir_path = dirname(__FILE__);
			$template_directory = $plugin_dir_path . "/templates/";
			$fileFullPath = $plugin_dir_path . "/templates/" . $fileName . ".php";
			
			if (!file_exists($fileFullPath))	{
				return "";
			}
			$fileContents = file_get_contents($fileFullPath);
			
			$tmpFind = "* @name:";
			$posName = strpos($fileContents, $tmpFind);
			if ($posName === false) {
				$templateName = "Unknown";
			}	else	{
				$templateName = substr($fileContents, ($posName+strlen($tmpFind)));

				$tmpFind = "\n";
				$posEnter = strpos($templateName, $tmpFind);
				$templateName = substr($templateName, 0, $posEnter);
				$templateName = trim($templateName);
			}

			$tmpFind = "* @defaulttemplate:";
			$posName = strpos($fileContents, $tmpFind);
			if ($posName === false) {
				$templateDefault = "false";
			}	else	{
				$templateDefault = substr($fileContents, ($posName+strlen($tmpFind)));

				$tmpFind = "\n";
				$posEnter = strpos($templateDefault, $tmpFind);
				$templateDefault = substr($templateDefault, 0, $posEnter);
				$templateDefault = trim($templateDefault);
			}

			$tmpFind = "* @lastupdated:";
			$posName = strpos($fileContents, $tmpFind);
			if ($posName === false) {
				$templateLastUpdated = "Unknown";
			}	else	{
				$templateLastUpdated = substr($fileContents, ($posName+strlen($tmpFind)));

				$tmpFind = "\n";
				$posEnter = strpos($templateLastUpdated, $tmpFind);
				$templateLastUpdated = substr($templateLastUpdated, 0, $posEnter);
				$templateLastUpdated = trim($templateLastUpdated);
			}
			
			$fileName = str_replace(".php", "", $fileName);
			
			$template = array();
			$template['name'] = $templateName;
			$template['filename'] = $fileName;
			$template['fullpath'] = $fileFullPath;
			$template['default'] = $templateDefault;
			$template['lastupdated'] = $templateLastUpdated;
			$template['template'] = $fileContents;
			
			return $template;
		}
		
		function get_AllEvents()	{
			global $wpdb;
			
			$tablePrefix = $wpdb->prefix . STUBWIRE_TABLE_PREFIX;
			
			$queryEvents = "SELECT ";
			$queryEvents .= "`id`, ";
			$queryEvents .= "`name`, ";
			$queryEvents .= "`dateTime`, ";
			$queryEvents .= "`eventStatus`, ";
			$queryEvents .= "`wp_postid` ";
			$queryEvents .= "FROM " . $tablePrefix . "events";
			$queryEvents .= " ORDER BY `dateTime` DESC";
			$query = $wpdb->prepare($queryEvents,"");
			
			$allEvents = $wpdb->get_results($query,ARRAY_A);
			
			return $allEvents;
		}
		
		function get_StubWireAllTemplates()	{
			global $wpdb;
			
			$tablePrefix = $wpdb->prefix . STUBWIRE_TABLE_PREFIX;
			
			$queryTemplates = "SELECT ";
			$queryTemplates .= "`id`, ";
			$queryTemplates .= "`name`, ";
			$queryTemplates .= "`filename`, ";
			$queryTemplates .= "`template`, ";
			$queryTemplates .= "`isdefault`, ";
			$queryTemplates .= "`LastUpdatedAt` ";
			$queryTemplates .= "FROM " . $tablePrefix . "templates";
			$queryTemplates .= " ORDER BY `isdefault` ASC, `LastUpdatedAt` DESC";
			$query = $wpdb->prepare($queryTemplates,"");
			
			$allTemplates = $wpdb->get_results($query,ARRAY_A);
			
			return $allTemplates;
			
			$plugin_dir_path = dirname(__FILE__);
			$template_directory = $plugin_dir_path . "/templates/";
			 
			//get all image files with a .jpg extension.
			$files = glob($template_directory . "*.php");
			 
			//print each file name
			$templates = array();
			foreach($files as $file)	{
				$templateFile = str_replace($template_directory, "", $file);
				$templateFile = str_replace(".php", "", $templateFile);
				
				$template = $this->get_StubWireTemplateInfo($templateFile);
				
				array_push($templates, $template);
			}
			
			return $templates;
		}

		function get_StubWireTemplate($TemplateID, $type='db')	{
			global $wpdb;
			
			if ($type=='file')	{
				echo "get template from file<br>";
				die;
			} elseif ($type=='db')	{
				$tablePrefix = $wpdb->prefix . STUBWIRE_TABLE_PREFIX;
				
				$queryTemplates = "SELECT ";
				$queryTemplates .= "`id`, ";
				$queryTemplates .= "`name`, ";
				$queryTemplates .= "`content`, ";
				$queryTemplates .= "`LastUpdatedAt` ";
				$queryTemplates .= "FROM " . $tablePrefix . "templates ";
				if ($TemplateID=='default_event')	{
					$queryTemplates .= "WHERE `id`='2'";
				}	elseif ($TemplateID=='default_list')	{
					$queryTemplates .= "WHERE `id`='1'";
				}	else	{
					$queryTemplates .= "WHERE `id`='%d'";
				}
				$query = $wpdb->prepare($queryTemplates,$TemplateID);
				$allTemplate = $wpdb->get_results($query,ARRAY_A);
			}
			
			return $allTemplate;
		}
		
		function add_Template($Details)	{
			global $wpdb;
			
			$tablePrefix = $wpdb->prefix . STUBWIRE_TABLE_PREFIX;

			$queryInsert = "INSERT INTO
													" . $tablePrefix . "templates
											SET
													`name`					= '%s',
													`filename`			= '%s',
													`isdefault`			= 'No',
													`LastUpdatedAt`	= NOW()";
			$query = $wpdb->prepare($queryInsert,$Details['name'],$Details['filename']);
			$insertTemplate = $wpdb->get_results($query,ARRAY_A);
			
			$queryAdd = "SELECT LAST_INSERT_ID() AS LID";
			$query = $wpdb->prepare($queryAdd,"");
			$getID = $wpdb->get_results($query,ARRAY_A);
			
			$Details['id'] = $getID[0]['LID'];

			return $Details;
		}
		
		function get_NewTemplateName($Name)	{
			global $wpdb;
			
			$tablePrefix = $wpdb->prefix . STUBWIRE_TABLE_PREFIX;
			
			$boolFoundName = false;
			
			// This is a new template so lets figure out what the name should be
			$templateName = strtolower($Name);
			$templateName = str_replace(" ", "_", $templateName);

			$tmpTemplateNumber = "";
			$tmpTemplateSuffix = "";
			while (!$boolFoundName)	{
				$nameToCheck = $templateName . $tmpTemplateSuffix . $tmpTemplateNumber;
				
				// LETS MAKE SURE THIS TEMPLATE NAME IS NOT ALREADY IN USE
				$queryCheck = "SELECT `id` FROM `" . $tablePrefix . "templates` WHERE `filename`='%s'";
				$query = $wpdb->prepare($queryCheck, $nameToCheck);
				$getID = $wpdb->get_results($query,ARRAY_A);
				
				$TemplateID = $getID[0]['id'];
				
				if (empty($TemplateID))	{
					$boolFoundName	= true;
					$templateName		= $nameToCheck;
				}	else	{
					// NAME IS IN USE SO LETS CHECK ANOTHER NAME
					if (empty($tmpTemplateNumber))	{
						$tmpTemplateNumber = 1;
						$tmpTemplateSuffix = "_";
					}	else	{
						$tmpTemplateNumber++;
					}
				}
			} 
			
			return $templateName;
		}
		function update_Template($Details)	{
			global $wpdb;
			
			$tablePrefix = $wpdb->prefix . STUBWIRE_TABLE_PREFIX;
			
			if (!isset($Details['filename']) || empty($Details['filename']))	{
				$Details['filename'] = $this->get_NewTemplateName($Details['name']);
			}
			if (!isset($Details['id']) || empty($Details['id']))	{
				// WE NOW NEED TO ADD IN A NEW RECORD SO WE CAN UPDATE IT
				$Details = $this->add_Template($Details);
			}	else	{
				// THEY SET THE FILE NAME SO WE ARE UPDATING
			}
			
			// FIRST LETS SAVE IT TO THE DB
			$queryUpdate = "UPDATE
													" . $tablePrefix . "templates
											SET
													`name`					= %s,
													`filename`			= %s,
													`template`			= %s,
													`LastUpdatedAt`	= NOW()
											WHERE
													`id`						= %s";
			$query = $wpdb->prepare($queryUpdate,$Details['name'],$Details['filename'],$Details['template'],$Details['id']);
			$updateTemplate = $wpdb->get_results($query,ARRAY_A);
			
			// NOW LETS SAVE IT TO THE SERVER
			$plugin_dir_path = dirname(__FILE__);
			$fileFullPath = $plugin_dir_path . "/templates/" . $Details['filename'] . ".php";
			
			file_put_contents($fileFullPath, $Details['template']);

			$plugin_dir_path = dirname(__FILE__);
			$template_directory = $plugin_dir_path . "/templates/";
			$Details['filefullpath']				= $plugin_dir_path . "/templates/" . $Details['filename'] . ".php";
			$Details['filepath']						= $Details['filefullpath'];
				
			return $Details;
		}

		function delete_Template($arrTemplate)	{
			global $wpdb;

			$tablePrefix = $wpdb->prefix . STUBWIRE_TABLE_PREFIX;

			$queryUpdate = "DELETE FROM
													" . $tablePrefix . "templates
											WHERE
													`id`						= %s";
			$query = $wpdb->prepare($queryUpdate,$arrTemplate['id']);
			$updateTemplate = $wpdb->get_results($query,ARRAY_A);
			
			unlink($arrTemplate['filefullpath']);
			
			return true;
		}

		function sendLogEntry($logEntry, $logType)	{			
			$logEntry = "[WPCron][" . $this->get_CurrentDomain() . "]" . $logEntry;
			
			$request = $this->stubwireAPIBaseURL;

			$request = $this->add_QueryString($request,"action=submit_log");
				
			$fields = array(
			            'logType'=>urlencode($logType),
			            'logEntry'=>urlencode($logEntry)
			        );
			$fields_string = "";
			foreach($fields as $key=>$value) {
				$fields_string .= $key.'='.$value.'&';
			}
			rtrim($fields_string,'&');
			
			$ch = curl_init( );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			if( !ini_get('safe_mode') ) {
		  	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		  }
			curl_setopt( $ch, CURLOPT_TIMEOUT, 60 );
			curl_setopt( $ch, CURLOPT_URL, $request );
			curl_setopt( $ch, CURLOPT_POST, count($fields) );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields_string );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER,false );
			$curl_output = curl_exec( $ch );	
		}

		function get_FacebookEventData($eventID)	{
			$data = array();
			try {
				$request = $this->stubwireAPIBaseURL;

				$request = $this->add_QueryString($request,"action=facebook_event_info&event=" . $eventID);

				$fields = array(
				            'Event_ID'=>urlencode($eventID)
				        );
				$fields = array();

				//url-ify the data for the POST
				$fields_string = "";
				foreach($fields as $key=>$value) {
					$fields_string .= $key.'='.$value.'&';
				}
				rtrim($fields_string,'&');

				$ch = curl_init( );
				curl_setopt( $ch, CURLOPT_HEADER, 0 );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				if( !ini_get('safe_mode') ) {
		    	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		    }
				curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
				curl_setopt( $ch, CURLOPT_URL, $request );
				curl_setopt( $ch, CURLOPT_HEADER, 0 );
				curl_setopt( $ch, CURLOPT_POST, count($fields) );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields_string );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER,false );
				$curl_output = curl_exec( $ch );			
				
				$return_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
				if ( curl_error( $ch ) ) {
					// TODO: There was an error so we should do something here
				}	else	{
					curl_close( $ch );
					if ( $return_code == 0 || $return_code == 500 || $return_code == 400 ) {
						// TODO: Web page through an error so lets return the error $return_code
					} else {
						/*echo "<h1>FACEBOOK INFO</h1>";
						echo "<pre>";
						print_r($curl_output);
						echo "</pre>";*/
						// TODO: We got data so lets just write it to the browser for now
						ini_set( 'display_errors', 'Off' );
						$dom = new DOMDocument( '1.0', 'iso-8859-1' );
						if( $dom->loadXML( $curl_output, LIBXML_DTDVALID ) ) {
							foreach($dom->getElementsByTagName("client") as $cli) {							
								$client = array();
								$client['id']						= $cli->getElementsByTagName('id')->item(0)->nodeValue;
								$client['name']					= $cli->getElementsByTagName('name')->item(0)->nodeValue;;
								$client['isVenue']			= $cli->getElementsByTagName('isVenue')->item(0)->nodeValue;;
								$client['isPromoter']		= $cli->getElementsByTagName('isPromoter')->item(0)->nodeValue;;
								$client['isOutlet']			= $cli->getElementsByTagName('isOutlet')->item(0)->nodeValue;;
								
								array_push($clients, $client);
							}
						} else {
							// TODO: XML is Invalid
						}
					}
				}
			} catch( TEC_Post_Exception $e ) {
				// TODO: Catch this ERROR
			}
			
			return $clients;
		}
		function get_ClientsToAccessFromStubWire($EmailAddress, $Password)	{
			$clients = array();
			
			try {
				$request = $this->stubwireAPIBaseURL;

				$request = $this->add_QueryString($request,"action=login");

				$fields = array(
				            'Login_UserEmail'=>urlencode($EmailAddress),
				            'Login_UserPassword'=>urlencode($Password)
				        );

				//url-ify the data for the POST
				$fields_string = "";
				foreach($fields as $key=>$value) {
					$fields_string .= $key.'='.$value.'&';
				}
				rtrim($fields_string,'&');

				//echo "Calling URL:" . $request . "<br>";
				
				$ch = curl_init( );
				curl_setopt( $ch, CURLOPT_HEADER, 0 );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				if( !ini_get('safe_mode') ) {
		    	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		    }
				curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
				curl_setopt( $ch, CURLOPT_URL, $request );
				curl_setopt( $ch, CURLOPT_HEADER, 0 );
				curl_setopt( $ch, CURLOPT_POST, count($fields) );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields_string );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER,false );
				$curl_output = curl_exec( $ch );			
				
				$return_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
				if ( curl_error( $ch ) ) {
					// TODO: There was an error so we should do something here
				}	else	{
					curl_close( $ch );
					if ( $return_code == 0 || $return_code == 500 || $return_code == 400 ) {
						// TODO: Web page through an error so lets return the error $return_code
					} else {
						/*echo "<pre>";
						print_r($curl_output);
						echo "</pre>";*/
						// TODO: We got data so lets just write it to the browser for now
						ini_set( 'display_errors', 'Off' );
						$dom = new DOMDocument( '1.0', 'iso-8859-1' );
						if( $dom->loadXML( $curl_output, LIBXML_DTDVALID ) ) {
							foreach($dom->getElementsByTagName("client") as $cli) {							
								$client = array();
								$client['id']						= $cli->getElementsByTagName('id')->item(0)->nodeValue;
								$client['name']					= $cli->getElementsByTagName('name')->item(0)->nodeValue;;
								$client['isVenue']			= $cli->getElementsByTagName('isVenue')->item(0)->nodeValue;;
								$client['isPromoter']		= $cli->getElementsByTagName('isPromoter')->item(0)->nodeValue;;
								$client['isOutlet']			= $cli->getElementsByTagName('isOutlet')->item(0)->nodeValue;;
								
								array_push($clients, $client);
							}
						} else {
							// TODO: XML is Invalid
						}
					}
				}
			} catch( TEC_Post_Exception $e ) {
				// TODO: Catch this ERROR
			}
			
			return $clients;
		}
		
		function get_EventsFromStubWire($rtnOutput, $additionalQueryItems) {
			@set_time_limit(0);
			//set_time_limit ( 0 );

			$intEventsToImport = 0;
			$intEventsImported = 0;
			
			$this->sendLogEntry("Starting to import all events", "debug");
			//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') - Import Started', 'get_EventsFromStubWire started');

			$strOutput				= "";
			$debugData				= "";
			$debugCurlOutput	= "";
			try {
				$varLoginEmailAddress				= get_option('stubwire_login_emailaddress');
				$varPostCategory						= get_option('stubwire_PostCategory');
				$varPostAuthor							= get_option('stubwire_PostAuthor');
				$varSubmitDetailedLogs			= get_option('stubwire_SubmitDetailedLogs');
				$varVenues									= get_option('stubwire_Venues');
				$varPromoted								= get_option('stubwire_Promoted');
				$varClient									= get_option('stubwire_Client');

				$debugData .= " - Just pulled all the options\r\n";
				$debugData .= "\r\n";
				
				if( !isset($varLoginEmailAddress) || empty($varLoginEmailAddress) ) {
					$debugData .= " - Login email is empty so not processing\r\n";
					$debugData .= "\r\n";
				
					if ($this->stubwireDebugImportEvents)	{
						echo "Not importing as login email is missing<br>";
					}
					$strOutput .= "<font color=red><b>ERROR:</b> Settings are not set, so we can not import your events.</font><br>\n";
					$strOutput .= "<font color=red><i>Login Email Address is not set</i></font><br>\n";
					
					$this->sendLogEntry("Import Error within varLoginEmailAddress", "err");
					//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') - Import Error', $debugData);
					
					return $strOutput;
				} elseif( !isset($varPostCategory) || empty($varPostCategory) ) {
					$debugData .= " - Post Category is empty so not processing\r\n";
					$debugData .= "\r\n";
				
					if ($this->stubwireDebugImportEvents)	{
						echo "Not post category is missing<br>";
					}
					$strOutput .= "<font color=red><b>ERROR:</b> Settings are not set, so we can not import your events.</font><br>\n";
					$strOutput .= "<font color=red><i>You have not set the Post Category</i></font><br>\n";
					
					$this->sendLogEntry("Import Error within varPostCategory", "err");
					//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') - Import Error with varPostCategory', $debugData);
					
					return $strOutput;
				} elseif( !isset($varPostAuthor) || empty($varPostAuthor) ) {
					$debugData .= " - Post Author is empty so not processing\r\n";
					$debugData .= "\r\n";
				
					if ($this->stubwireDebugImportEvents)	{
						echo "Not importing as post author<br>";
					}
					$strOutput .= "<font color=red><b>ERROR:</b> Settings are not set, so we can not import your events.</font><br>\n";
					$strOutput .= "<font color=red><i>You have not set the Arthor to post the events as</i></font><br>\n";
					
					$this->sendLogEntry("Import Error within varPostAuthor", "err");
					//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') - Import Error', $debugData);
					
					return $strOutput;
				} elseif(empty($varVenues) && empty($varPromoted) && empty($varClient) ) {
					$debugData .= " - Venues, Promoted or Client are all empty so not processing\r\n";
					$debugData .= "\r\n";
					
					if ($this->stubwireDebugImportEvents)	{
						echo "Not importing as we cant find client record to import<br>";
					}
					$strOutput .= "<font color=red><b>ERROR:</b> Settings are not set, so we can not import your events.</font><br>\n";
					$strOutput .= "<font color=red><i>You have not selected the client to import events for</i></font><br>\n";
					
					$this->sendLogEntry("Import Error within varVenues, varPromoted or varClient", "err");
					//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') - Import Error', $debugData);
					
					return $strOutput;
				}
				
				$request = $this->stubwireAPIBaseURL;
				if (!empty($varVenues))
					$request .= "&venue=" . $varVenues;
				if (!empty($varClient))
					$request .= "&client=" . $varClient;
				if (!empty($varPromoted))
					$request .= "&promoted=" . $varPromoted;
				if (!empty($additionalQueryItems))
					$request .= $additionalQueryItems;

				$debugData .= " - Requesting Data from URL(" . $request . ")\r\n";
				$debugData .= "\r\n";
					
				if ($this->stubwireDebugImportEvents)	{
					echo "Importing from URL:" . $request . "<br>";
				}
				
				$fields = array(
				            'Login_UserEmail'=>urlencode(get_option('stubwire_login_emailaddress')),
				            'Login_UserPassword'=>urlencode(get_option('stubwire_login_password'))
				        );
				$fields_string = "";
				foreach($fields as $key=>$value) {
					$fields_string .= $key.'='.$value.'&';
				}
				rtrim($fields_string,'&');
				
				//$this->sendLogEntry("Calling CURL to get events", "err");
				//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') - Step - Calling CURL', $debugData);
				
				$ch = curl_init( );
				curl_setopt( $ch, CURLOPT_HEADER, 0 );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				if( !ini_get('safe_mode') ) {
		    	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		    }
				curl_setopt( $ch, CURLOPT_TIMEOUT, 60 );
				curl_setopt( $ch, CURLOPT_URL, $request );
				curl_setopt( $ch, CURLOPT_POST, count($fields) );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields_string );
				curl_setopt( $ch, CURLOPT_HEADER, 0 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER,false );
				$curl_output = curl_exec( $ch );			
				
				//$this->sendLogEntry("CURL just finished", "err");
				//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') - Step - Received CURL', $debugData);
				
				$debugCurlOutput = $curl_output;
				
				//$this->sendLogEntry("CURL output=" . $debugCurlOutput, "info");
				
				$return_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
				if ( curl_error( $ch ) ) {
					$debugData .= " - CURL reported an error. Error=(" . curl_error($ch) . ")\r\n";
					$debugData .= "\r\n";

					// TODO: There was an error so we should do something here
					if ($this->stubwireDebugImportEvents)	{
						echo "error in curl_error<br>";
					}
					$strOutput .= "<font color=red><b>ERROR:</b> There was an error while trying to grab events (" . curl_error($ch) . ")</font><br>\n";
					
					$this->sendLogEntry("CURL returned an error. Error=(" . curl_error($ch) . ")", "err");
					//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') - Step - CURL has an error', $debugData);
				}
				$debugData .= " - closing CURL\r\n";
				$debugData .= "\r\n";
				curl_close( $ch );
				if ( $return_code == 0 || $return_code == 500 || $return_code == 400 ) {
					$debugData .= " - CURL returned return_code=(" . $return_code . ") so not processing\r\n";
					$debugData .= "\r\n";
					
					// TODO: Web page through an error so lets return the error $return_code
					if ($this->stubwireDebugImportEvents)	{
						echo "return code is not correct. code=(" . $return_code . ")<br>";
					}
					$strOutput .= "<font color=red><b>ERROR:</b> There was an error while trying to grab events as it returned an error code (" . $return_code . ")</font><br>\n";
					
					$this->sendLogEntry("CURL status code is not correct. Status Code:(" . $return_code . ")", "err");
					//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') - Step - CURL status code', $debugData);
				} else {
					$debugData .= " - We got the data we will be using to import\r\n";
					$debugData .= "\r\n";
					
					//$this->sendLogEntry("Starting to process events", "info");
					//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') - Step - Start Importing', $debugData);
									
					if ($this->stubwireDebugImportEvents)	{
						echo "Lets pull the data that we will be using to import<br>";
					}
					/*echo "<b>Lets pull the data and process it</b><br>";
					echo "<pre>";
					print_r(htmlentities($curl_output));
					echo "</pre>";
					echo "<b>Done</b>";
					die;*/
					// TODO: We got data so lets just write it to the browser for now
					//ini_set( 'display_errors', 'Off' );
					$dom = new DOMDocument( '1.0', 'iso-8859-1' );
					if( $dom->loadXML( $curl_output, LIBXML_DTDVALID ) ) {
						$debugData .= " - We loaded the XML so lets loop through the data\r\n";
						$debugData .= "\r\n";
					
						//$this->sendLogEntry("we have valid xml data", "info");
						//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') - Step - Valid XML Data', $debugData);
						
						//echo "time to loop through each event<br>";
						foreach($dom->getElementsByTagName("event") as $event) {
							$intEventsToImport++;
						}
						/*echo "intEventsToImport=" . $intEventsToImport . "<br>";
						die;*/
						
						foreach($dom->getElementsByTagName("event") as $event) {
							$intEventsImported++;
			
							$debugData .= " - Lets process the next event\r\n";
							$debugData .= "\r\n";

							$arrEvent = array();
							$arrEvent['id']															= trim($event->getElementsByTagName('id')->item(0)->nodeValue);
							//echo "Processing Event ID:" . $arrEvent['id']	 . "<br>";
							$arrEvent['name']														= trim($event->getElementsByTagName('name')->item(0)->nodeValue);
							$arrEvent['shortDescription']								= trim($event->getElementsByTagName('shortDescription')->item(0)->nodeValue);
							$arrEvent['fullDescription']								= trim($event->getElementsByTagName('fullDescription')->item(0)->nodeValue);
							$arrEvent['dateTime']												= trim($event->getElementsByTagName('dateTime')->item(0)->nodeValue);
							$arrEvent['doorsOpenAt']										= trim($event->getElementsByTagName('doorsOpenAt')->item(0)->nodeValue);
							$arrEvent['ageDescription']									= trim($event->getElementsByTagName('ageDescription')->item(0)->nodeValue);						
							$arrEvent['eventStatus']										= trim($event->getElementsByTagName('eventStatus')->item(0)->nodeValue);
							$arrEvent['LastUpdatedAt']									= trim($event->getElementsByTagName('eventLastUpdatedAt')->item(0)->nodeValue);

							$debugData .= " - Processing EventID:(" . $arrEvent['id'] . ") EventName:(" . $arrEvent['name'] . ")\r\n";
							$debugData .= "\r\n";

							/*if( $odd = $intEventsImported%10 )	{
							    // $odd == 1; the remainder of 25/2
							    //echo 'ODD Number!';
							}	else	{
							    // $odd == 0; nothing remains if e.g. $number is 48 instead,
								// as in 48 / 2
								$this->sendLogEntry("Processing Event ID " . $arrEvent['id'] . " Total Events:" . $intEventsToImport . " Events Imported:" . $intEventsImported, "info");
							}*/

							
							//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') - Step - Processing Event ID ' . $arrEvent['id'], $debugData);
							
							foreach($event->getElementsByTagName('options') as $option) {
								$arrEvent['isSpotlightEvent']								= trim($option->getElementsByTagName('isSpotlightEvent')->item(0)->nodeValue);
								$arrEvent['isFeaturedEvent']								= trim($option->getElementsByTagName('isFeaturedEvent')->item(0)->nodeValue);
								$arrEvent['isPromoterPickEvent']						= trim($option->getElementsByTagName('isPromoterPickEvent')->item(0)->nodeValue);
								$arrEvent['eventAdminAccess']								= trim($option->getElementsByTagName('eventAdminAccess')->item(0)->nodeValue);
							}
							
							foreach($event->getElementsByTagName('parentEvent') as $parent) {
								$arrEvent['parentEventID']									= trim($parent->getElementsByTagName('eventID')->item(0)->nodeValue);
								$arrEvent['isParentEvent']									= trim($parent->getElementsByTagName('isParentEvent')->item(0)->nodeValue);

								if (empty($arrEvent['isParentEvent']) || $arrEvent['isParentEvent']=='No')	{
									$arrEvent['parentEarliestChildDate']				= "";
									$arrEvent['parentLatestChildDate']					= "";
									$arrEvent['isParentEventPurchasesEnabled']	= "";
								}	else	{
									$arrEvent['parentEarliestChildDate']				= trim($parent->getElementsByTagName('earlyDate')->item(0)->nodeValue);
									$arrEvent['parentLatestChildDate']					= trim($parent->getElementsByTagName('lateDate')->item(0)->nodeValue);
									$arrEvent['isParentEventPurchasesEnabled']	= trim($parent->getElementsByTagName('purchaseParent')->item(0)->nodeValue);
								}
							}
							foreach($event->getElementsByTagName('facebook') as $facebook) {
								$arrEvent['facebookEventID']								= trim($facebook->getElementsByTagName('facebookEventID')->item(0)->nodeValue);
								$arrEvent['facebookEventURL']								= trim($facebook->getElementsByTagName('facebookEventURL')->item(0)->nodeValue);
								$arrEvent['facebookWallPosts']							= trim($facebook->getElementsByTagName('facebookWallPosts')->item(0)->nodeValue);
								$arrEvent['facebookAttending']							= trim($facebook->getElementsByTagName('facebookAttending')->item(0)->nodeValue);
							}
							//echo "Facebook Event URL:" . $arrEvent['facebookEventURL'] . "<br>";
							foreach($event->getElementsByTagName('urls') as $url) {
								$arrEvent['url']														= trim($url->getElementsByTagName('eventLink')->item(0)->nodeValue);
								$arrEvent['buyNowLink']											= trim($url->getElementsByTagName('buyNowLink')->item(0)->nodeValue);
							}
							foreach($event->getElementsByTagName('ticketInfo') as $info) {
								$arrEvent['ticketsAvailable']								= trim($info->getElementsByTagName('ticketsAvailable')->item(0)->nodeValue);
								$arrEvent['ticketsCountAvailable']					= trim($info->getElementsByTagName('ticketsCountAvailable')->item(0)->nodeValue);
								$arrEvent['ticketsCountPurchased']					= trim($info->getElementsByTagName('ticketsCountPurchased')->item(0)->nodeValue);							
								$arrEvent['timeOnSale']											= trim($info->getElementsByTagName('timeOnSale')->item(0)->nodeValue);
								$arrEvent['timeOffSale']										= trim($info->getElementsByTagName('timeOffSale')->item(0)->nodeValue);
								$arrEvent['ticketPriceAdvance']							= trim($info->getElementsByTagName('ticketPriceAdvance')->item(0)->nodeValue);
								$arrEvent['ticketPriceDoor']								= trim($info->getElementsByTagName('ticketPriceDoor')->item(0)->nodeValue);
								$arrEvent['ticketPriceDoorLabel']						= trim($info->getElementsByTagName('ticketPriceDoorLabel')->item(0)->nodeValue);
								$arrEvent['sellableTroughInternet']					= trim($info->getElementsByTagName('sellableTroughInternet')->item(0)->nodeValue);
								$arrEvent['sellableTroughInternetReason']		= trim($info->getElementsByTagName('sellableTroughInternetReason')->item(0)->nodeValue);
								$arrEvent['orderEntryBoxOfficeCashServiceFee']		= trim($info->getElementsByTagName('orderEntryBoxOfficeCashServiceFee')->item(0)->nodeValue);
							}
					 		$arrVenues = array();
					 		foreach($event->getElementsByTagName('venue') as $venue) {
					 			$arrVenue = array();
					 			$arrEvent['venueid']											= trim($venue->getElementsByTagName('id')->item(0)->nodeValue);
					 			$arrVenue['id']														= trim($venue->getElementsByTagName('id')->item(0)->nodeValue);
					 			$arrVenue['name']													= trim($venue->getElementsByTagName('name')->item(0)->nodeValue);
					 			$arrVenue['address']											= trim($venue->getElementsByTagName('address')->item(0)->nodeValue);
					 			$arrVenue['address2']											= trim($venue->getElementsByTagName('address2')->item(0)->nodeValue);
					 			$arrVenue['city']													= trim($venue->getElementsByTagName('city')->item(0)->nodeValue);
					 			$arrVenue['state']												= trim($venue->getElementsByTagName('state')->item(0)->nodeValue);
					 			$arrVenue['zip']													= trim($venue->getElementsByTagName('zip')->item(0)->nodeValue);
					 			$arrEvent['venueroomname']								= trim($venue->getElementsByTagName('room')->item(0)->nodeValue);
					 			$arrVenue['LastUpdatedAt']								= trim($venue->getElementsByTagName('venueLastUpdatedAt')->item(0)->nodeValue);
					 			
					 			array_push($arrVenues,$arrVenue);
					 		}
					 		$arrEvent['venues']													= $arrVenues;

					 		$arrImages = array();
					 		//echo "<h1>LETS GET THE IMAGES</h1>";
					 		foreach($event->getElementsByTagName('images') as $images) {
					 			/*echo "<pre>";
					 			print_r($images);
					 			echo "</pre>";
					 			echo "<hr>";*/
								$arrEvent['eventImage']											= trim($images->getElementsByTagName('eventImage')->item(0)->nodeValue);
								$arrEvent['eventImageURLSmall']							= trim($images->getElementsByTagName('eventImageURLSmall')->item(0)->nodeValue);
								$arrEvent['eventImageURLMedium']						= trim($images->getElementsByTagName('eventImageURLMedium')->item(0)->nodeValue);
								$arrEvent['eventImageURLOriginal']					= trim($images->getElementsByTagName('eventImageURLOriginal')->item(0)->nodeValue);
					 			foreach($images->getElementsByTagName('allImages') as $image) {
						 			/*echo "<h2>GRABBED AN IMAGE FROM ALLIMAGES</h2>";
						 			echo "<pre>";
						 			print_r($image);
						 			echo "</pre>";
						 			echo "<hr>";*/
						 			if (isset($image->getElementsByTagName('id')->item(0)->nodeValue) && !empty($image->getElementsByTagName('id')->item(0)->nodeValue))	{
							 			/*echo "<h2>WE GOT A IMAGE</h2>";
							 			echo "<pre>";
							 			print_r($images);
							 			echo "</pre>";
							 			echo "<hr>";*/

							 			$arrImage = array();
							 			$arrImage['id']														= trim($image->getElementsByTagName('id')->item(0)->nodeValue);
							 			$arrImage['isMainImage']									= trim($image->getElementsByTagName('isMainImage')->item(0)->nodeValue);
							 			$arrImage['isPosterImage']								= trim($image->getElementsByTagName('isPosterImage')->item(0)->nodeValue);
							 			$arrImage['imageName']										= trim($image->getElementsByTagName('imageName')->item(0)->nodeValue);
							 			$arrImage['imageCaption']									= trim($image->getElementsByTagName('imageCaption')->item(0)->nodeValue);
							 			$arrImage['imageURLSmall']								= trim($image->getElementsByTagName('imageURLSmall')->item(0)->nodeValue);
							 			if (isset($image->getElementsByTagName('imageURLMedium')->item(0)->nodeValue) && !empty($image->getElementsByTagName('imageURLMedium')->item(0)->nodeValue))
							 				$arrImage['imageURLMedium']							= trim($image->getElementsByTagName('imageURLMedium')->item(0)->nodeValue);
							 			else
							 				$arrImage['imageURLMedium']							= "";
							 			if (isset($image->getElementsByTagName('imageURLOriginal')->item(0)->nodeValue) && !empty($image->getElementsByTagName('imageURLOriginal')->item(0)->nodeValue))
							 				$arrImage['imageURLOriginal']						= trim($image->getElementsByTagName('imageURLOriginal')->item(0)->nodeValue);
							 			else
							 				$arrImage['imageURLOriginal']						= "";
							 			if (isset($image->getElementsByTagName('imageType')->item(0)->nodeValue) && !empty($image->getElementsByTagName('imageType')->item(0)->nodeValue))
							 				$arrImage['imageType']									= trim($image->getElementsByTagName('imageType')->item(0)->nodeValue);
							 			else
							 				$arrImage['imageType']									= "";
							 			if (isset($image->getElementsByTagName('fileName')->item(0)->nodeValue) && !empty($image->getElementsByTagName('fileName')->item(0)->nodeValue))
							 				$arrImage['fileName']										= trim($image->getElementsByTagName('fileName')->item(0)->nodeValue);
							 			else
							 				$arrImage['fileName']										= "";
							 			$arrImage['imageDisplayOrder']						= trim($image->getElementsByTagName('imageDisplayOrder')->item(0)->nodeValue);
							 			$arrImage['imageType']										= trim($image->getElementsByTagName('imageType')->item(0)->nodeValue);
							 			$arrImage['imageStatus']									= trim($image->getElementsByTagName('imageStatus')->item(0)->nodeValue);
							 			$arrImage['LastUpdatedAt']								= trim($image->getElementsByTagName('imageLastUpdatedAt')->item(0)->nodeValue);
							 			
							 			array_push($arrImages,$arrImage);
						 			}
						 		}
					 		}
					 		$arrEvent['images']													= $arrImages;
					 		
							 			/*echo "<h2><font color=red>LIST OUT ALL THE IMAGES</font></h2>";
							 			echo "<pre>";
							 			print_r($arrEvent['images']	);
							 			echo "</pre>";
							 			echo "<hr>";*/

					 		$arrActs = array();
					 		foreach($event->getElementsByTagName('act') as $act) {
								$arrAct = array();
								$arrAct['id']															= trim($act->getElementsByTagName('actid')->item(0)->nodeValue);
								$arrAct['name']														= trim($act->getElementsByTagName('actname')->item(0)->nodeValue);
					 			$arrAct['websiteurl']											= trim($act->getElementsByTagName('actwebsiteurl')->item(0)->nodeValue);
					 			$arrAct['type']														= trim($act->getElementsByTagName('acttype')->item(0)->nodeValue);
					 			$arrAct['status']													= trim($act->getElementsByTagName('actstatus')->item(0)->nodeValue);
					 			$arrAct['displayorder']										= trim($act->getElementsByTagName('actdisplayorder')->item(0)->nodeValue);
					 			$arrAct['LastUpdatedAt']									= trim($act->getElementsByTagName('actLastUpdatedAt')->item(0)->nodeValue);
					 			array_push($arrActs,$arrAct);
					 		}
					 		$arrEvent['acts']													= $arrActs;

							// ***** LETS MAKE SURE THE DATES ARE IN A VALID FORMAT
							if (!empty($arrEvent['parentEarliestChildDate']))	{
								if ($this->stubwireDebugImportEvents && (empty($this->stubwireDebugImportEvent) || $this->stubwireDebugImportEvent==$arrEvent['id']))	{
									echo "<b>parentEarliestChildDate:</b> is not empty so lets format it (" . $arrEvent['parentEarliestChildDate'] . ")<br>";
								}
								$sqlDate = $arrEvent['parentEarliestChildDate'];
								if (is_numeric($sqlDate))	{
									$sqlDate = date('Y-m-d H:i:s', $sqlDate);
								}
								$arrEvent['parentEarliestChildDate'] = $sqlDate;
								if ($this->stubwireDebugImportEvents && (empty($this->stubwireDebugImportEvent) || $this->stubwireDebugImportEvent==$arrEvent['id']))	{
									echo "<b>parentEarliestChildDate:</b> has been formatted to (" . $arrEvent['parentEarliestChildDate'] . ")<br>";
								}
							}
							if (!empty($arrEvent['parentLatestChildDate']))	{
								$sqlDate = $arrEvent['parentLatestChildDate'];
								if (is_numeric($sqlDate))	{
									$sqlDate = date('Y-m-d H:i:s', $sqlDate);
								}
								$arrEvent['parentLatestChildDate'] = $sqlDate;
							}
							if (!empty($arrEvent['timeOnSale']))	{
								$sqlDate = $arrEvent['timeOnSale'];
								if (is_numeric($sqlDate))	{
									$sqlDate = date('Y-m-d H:i:s', $sqlDate);
								}
								$arrEvent['timeOnSale'] = $sqlDate;
							}
							if (!empty($arrEvent['timeOffSale']))	{
								$sqlDate = $arrEvent['timeOffSale'];
								if (is_numeric($sqlDate))	{
									$sqlDate = date('Y-m-d H:i:s', $sqlDate);
								}
								$arrEvent['timeOffSale'] = $sqlDate;
							}					
					
			
							if ($this->stubwireDebugImportEvents && (empty($this->stubwireDebugImportEvent) || $this->stubwireDebugImportEvent==$arrEvent['id']))	{
								echo "Starting to process Event(" . $arrEvent['name'] . ")<br>";
							}

							$debugData .= " - Calling Import Event function for EventID:(" . $arrEvent['id'] . ") EventName:(" . $arrEvent['name'] . ")\r\n";
							$debugData .= "\r\n";
							
							$dumpEvent = print_r($arrEvent, true);
							
							//$this->sendLogEntry("we are about to call this->import_Event\r\n\r\n" . $dumpEvent, "debug");
							//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') - Step - Starting to import Event ID ' . $arrEvent['id'], $debugData . "\r\n\r\n" . $dumpEvent);



							$strOutput .= $this->import_Event($arrEvent, $rtnOutput);

							//$this->sendLogEntry("we just finished calling this->import_Event", "info");
							//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') - Step - Imported Event ID ' . $arrEvent['id'], $strOutput);
							
							if ($this->stubwireDebugImportEvents && (empty($this->stubwireDebugImportEvent) || $this->stubwireDebugImportEvent==$arrEvent['id']))	{
								echo "We just finished importing an event, lets move to the next<br>";
							}

							$debugData .= " - We just finished importing event with EventID:(" . $arrEvent['id'] . ") EventName:(" . $arrEvent['name'] . ")\r\n";
							$debugData .= "\r\n";
						}

						$debugData .= " - All event data has been imported\r\n";
						$debugData .= "\r\n";
							
						if ($this->stubwireDebugImportEvents)	{
							echo "All events have been imported, lets update the last time imported<br>";
						}
													
						update_option('stubwire_EventsLastUpdated', current_time('mysql'));
					} else {
						$debugData .= " - The XML loaded is not valid so we didnt process the request\r\n";
						$debugData .= "\r\n";
					
						$tmpOutputBuilder = "";
						$tmpOutputBuilder .= "<font color=red><b>ERROR:</b> There was an error loading the XML.</font><br>\n";
						$tmpOutputBuilder .= "<font color=blue><pre>\n";
						$tmpDump = print_r(htmlentities($curl_output), true);
						$tmpOutputBuilder .= $tmpDump . "\n";
						$tmpOutputBuilder .= "</pre></font>\n";
						
						$strOutput .= $tmpOutputBuilder;
						
						$this->sendLogEntry("XML NOT VALID - " . $tmpOutputBuilder, "err");
					}

					if ($this->stubwireDebugImportEvents)	{
						echo "We completed the import<br>";
					}
				}
			} catch( TEC_Post_Exception $e ) {
				// TODO: Catch this ERROR
				$debugData .= "***** EXCEPTION START *****\r\n";
				$debugData .= "We received an exception while trying to process the data.\r\n";
				$debugData .= "\r\n";
					
				$this->sendLogEntry("we got an exception of TEC_Post_Exception", "err");
				//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') - Import Error', $debugData);
				//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') - Import Error (CURL Data)', $debugCurlOutput);
			
				$strOutput .= "<font color=red><b>ERROR:</b> We through an error in TEC_Post_Exception</font><br>\n";
			}
			
			$this->sendLogEntry("Just completed processing (" . $intEventsToImport . ") events.", "debug");
			//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') - Import Completed', $debugData);
			
			return $strOutput;
		}
		
		function import_Event($arrEvent, $rtnOutput)	{			
			global $wpdb;
				
			$debugData = "";
			
			$tablePrefix = $wpdb->prefix . STUBWIRE_TABLE_PREFIX;

			// START THE QUERY TIME
			$timerTotalStart	= microtime(true); // Gets microseconds
			$timerQueryStart	= microtime(true); // Gets microseconds
			
			$sqlQuery = "SELECT `id`, `wp_postid`, `name`, `LastUpdatedAt` FROM " . $tablePrefix . "events as events WHERE `events`.`id`='%d'";
			$query = $wpdb->prepare($sqlQuery, $arrEvent['id']);
			if ($this->stubwireDebugShowQuerys && (empty($this->stubwireDebugImportEvent) || $this->stubwireDebugImportEvent==$arrEvent['id']))	{
				echo "<b>Running Query:</b><br>";
				echo "<pre>";
				print_r($query);
				echo "</pre>";
			}
			$stubwire_event_check = $wpdb->get_results($query,ARRAY_A);
			
			// *****************************************
			// TESTING THE OUTPUT TIME
			// *****************************************
			// CALCULATE THE QUERY TIME
			$timerTotalTime		= (microtime(true) - $timerTotalStart) . " sec";
			$timerQueryTime		= (microtime(true) - $timerQueryStart) . " sec";
			// LOG THE QUERY TIME
			$debugData .= "[" . $timerTotalTime . "][" . $timerQueryTime . "] " . $sqlQuery . "\r\n\r\n";
			// *****************************************
			
			
			$oldEvent = array();
			if (isset($stubwire_event_check[0]))	{
				$oldEvent['id']						= $stubwire_event_check[0]['id'];
				$oldEvent['wpPostID']			= $stubwire_event_check[0]['wp_postid'];
				$oldEvent['name']					= $stubwire_event_check[0]['name'];
				$oldEvent['lastUpdated']	= $stubwire_event_check[0]['LastUpdatedAt'];
			}	else	{
				$oldEvent['id']						= "";
				$oldEvent['wpPostID']			= "";
				$oldEvent['name']					= "";
				$oldEvent['lastUpdated']	= "";

				$tmpStubWireEventCheckDump = print_r($stubwire_event_check, true);
				$debugData .= "[line:" . __LINE__ . "] stubwire_event_check is empty. why?\r\n" . $tmpStubWireEventCheckDump . "\r\n\r\n";
			}
			
			$stubwireEventUpdated = false;
			$stubwireEventPostUpdated = false;
			
			$strOutput .= "Processing Event:" . $arrEvent['name'] . "<br>\n";
			
			$tmpOldEventDump = print_r($oldEvent, true);
			$debugData .= "[line:" . __LINE__ . "] oldEvent Array data\r\n" . $tmpOldEventDump . "\r\n\r\n";
			
			if (empty($oldEvent['id']))	{
				$debugData .= "[line:" . __LINE__ . "] ******* EVENT NEEDS UPDATED (also updating blog) ****** empty($oldEvent[id])\r\n\r\n";
				$stubwireEventUpdated = true;
				$stubwireEventPostUpdated = true;
			}	elseif ($oldEvent['lastUpdated']!=$arrEvent['LastUpdatedAt'])	{
				$debugData .= "[line:" . __LINE__ . "] ******* EVENT NEEDS UPDATED ****** lastUpdated IS NOT THE SAME\r\n";
				$debugData .= "oldEvent[lastUpdated]=" . $oldEvent['lastUpdated'] . "\r\n";
				$debugData .= "arrEvent[LastUpdatedAt]=" . $arrEvent['LastUpdatedAt'] . "\r\n";
				if (strtotime($oldEvent['lastUpdated'])!=strtotime($arrEvent['LastUpdatedAt']))	{
					$debugData .= "strtotime is NOT the same\r\n";
				}	else	{
					$debugData .= "strtotime is the same\r\n";
				}
				$debugData .= "\r\n";
				$stubwireEventUpdated = true;
			}	else	{
				// THIS EVENT HAS NOT CHANGED SO NO REASON TO UPDATE IT
			}
			
			if (empty($oldEvent['name']))	{
				$stubwireEventPostUpdated = true;
			}	elseif ($oldEvent['name']!=$arrEvent['name'])	{
				$debugData .= "[line:" . __LINE__ . "] ******* EVENT NEEDS UPDATED ****** lastUpdated IS NOT THE SAME\r\n\r\n";
				$stubwireEventPostUpdated = true;
			}	else	{
				// THIS EVENT NAME HAS NOT CHANGED
			}
			
			if ($this->stubwireDebugShowQuerys && (empty($this->stubwireDebugImportEvent) || $this->stubwireDebugImportEvent==$arrEvent['id']))	{
				echo "<b>Event ID:</b>" . $arrEvent['id'] . "<br>\r\n";
				echo "<b>Event Name:</b>" . $arrEvent['name'] . "<br>\r\n";
				echo "<b>Event Status:</b>" . $arrEvent['eventStatus'] . "<br>\r\n";
			}
			
			if ($stubwireEventPostUpdated)	{
				$debugData .= "[line:" . __LINE__ . "] ******* EVENT NAME OR NEW EVENT SO POST IT ****** stubwireEventPostUpdated IS SET AT TRUE\r\n\r\n";
			}
			if ($arrEvent['eventStatus']!='Active' && $arrEvent['eventStatus']!='Canceled')	{
				if ($this->stubwireDebugShowQuerys && (empty($this->stubwireDebugImportEvent) || $this->stubwireDebugImportEvent==$arrEvent['id']))	{
					echo "Event is not active or canceled so lets process it<br>\r\n";
				}
			
				// EVENT IS NOT IN ACTIVE OR CANCELED SO WE DONT WANT TO PROCESS IT
				$this->deleteEvent($arrEvent['id'], $oldEvent['wpPostID']);	
			}	else	{

				// *****************************************
				// TESTING THE OUTPUT TIME
				// *****************************************
				$timerQueryStart	= microtime(true); // Gets microseconds
				// *****************************************
					
				$sql = "INSERT INTO
													" . $tablePrefix . "events
											SET
													`id`															= '" . $wpdb->escape(trim($arrEvent['id'])) . "',
													`venueid`													= '" . $wpdb->escape(trim($arrEvent['venueid'])) . "',
													`venueroomname`										= '" . $wpdb->escape(trim($arrEvent['venueroomname'])) . "',
													`name`														= '" . $wpdb->escape(trim($arrEvent['name'])) . "',
													`shortDescription`								= '" . $wpdb->escape(trim($arrEvent['shortDescription'])) . "',
													`fullDescription`									= '" . $wpdb->escape(trim($arrEvent['fullDescription'])) . "',
													`isParentEvent`										= '" . $wpdb->escape(trim($arrEvent['isParentEvent'])) . "',
													`parentEventID`										= '" . $wpdb->escape(trim($arrEvent['parentEventID'])) . "',";
				if (empty($arrEvent['parentEarliestChildDate']))
					$sql .= " `parentEarliestChildDate`					= null,";
				else
					$sql .= " `parentEarliestChildDate`					= '" . $wpdb->escape(trim($arrEvent['parentEarliestChildDate'])) . "',";
				if (empty($arrEvent['parentLatestChildDate']))
					$sql .= " `parentLatestChildDate`					= null,";
				else
					$sql .= " `parentLatestChildDate`						= '" . $wpdb->escape(trim($arrEvent['parentLatestChildDate'])) . "',";
				if (empty($arrEvent['isParentEventPurchasesEnabled']))
					$sql .= " `isParentEventPurchasesEnabled`					= null,";
				else
					$sql .= " `isParentEventPurchasesEnabled`					= '" . $wpdb->escape(trim($arrEvent['isParentEventPurchasesEnabled'])) . "',";
				$sql .= " `url`															= '" . $wpdb->escape(trim($arrEvent['url'])) . "',
													`dateTime`												= '" . $wpdb->escape(trim($arrEvent['dateTime'])) . "',
													`doorsOpenAt`											= '" . $wpdb->escape(trim($arrEvent['doorsOpenAt'])) . "',
													`isSpotlightEvent`								= '" . $wpdb->escape(trim($arrEvent['isSpotlightEvent'])) . "',
													`isFeaturedEvent`									= '" . $wpdb->escape(trim($arrEvent['isFeaturedEvent'])) . "',
													`isPromoterPickEvent`							= '" . $wpdb->escape(trim($arrEvent['isPromoterPickEvent'])) . "',
													`ticketsAvailable`								= '" . $wpdb->escape(trim($arrEvent['ticketsAvailable'])) . "',
													`ticketsCountAvailable`						= '" . $wpdb->escape(trim($arrEvent['ticketsCountAvailable'])) . "',
													`ticketsCountPurchased`						= '" . $wpdb->escape(trim($arrEvent['ticketsCountPurchased'])) . "',";							
				if (empty($arrEvent['timeOnSale']))
					$sql .= " `timeOnSale`					= null,";
				else
					$sql .= " `timeOnSale`						= '" . $wpdb->escape(trim($arrEvent['timeOnSale'])) . "',";
				if (empty($arrEvent['timeOffSale']))
					$sql .= " `timeOffSale`					= null,";
				else
					$sql .= " `timeOffSale`						= '" . $wpdb->escape(trim($arrEvent['timeOffSale'])) . "',";				
				$sql .= " `ageDescription`									= '" . $wpdb->escape(trim($arrEvent['ageDescription'])) . "',
													`ticketPriceAdvance`							= '" . $wpdb->escape(trim($arrEvent['ticketPriceAdvance'])) . "',
													`ticketPriceDoor`									= '" . $wpdb->escape(trim($arrEvent['ticketPriceDoor'])) . "',
													`ticketPriceDoorLabel`						= '" . $wpdb->escape(trim($arrEvent['ticketPriceDoorLabel'])) . "',
													`buyNowLink`											= '" . $wpdb->escape(trim($arrEvent['buyNowLink'])) . "',
													`eventImage`											= '" . $wpdb->escape(trim($arrEvent['eventImage'])) . "',
													`eventImageURLSmall`							= '" . $wpdb->escape(trim($arrEvent['eventImageURLSmall'])) . "',
													`eventImageURLMedium`							= '" . $wpdb->escape(trim($arrEvent['eventImageURLMedium'])) . "',
													`eventImageURLOriginal`						= '" . $wpdb->escape(trim($arrEvent['eventImageURLOriginal'])) . "',
													`sellableTroughInternet`					= '" . $wpdb->escape(trim($arrEvent['sellableTroughInternet'])) . "',
													`sellableTroughInternetReason`		= '" . $wpdb->escape(trim($arrEvent['sellableTroughInternetReason'])) . "',
													`orderEntryBoxOfficeCashServiceFee`		= '" . $wpdb->escape(trim($arrEvent['orderEntryBoxOfficeCashServiceFee'])) . "',
													`eventAdminAccess`								= '" . $wpdb->escape(trim($arrEvent['eventAdminAccess'])) . "',
													`eventStatus`											= '" . $wpdb->escape(trim($arrEvent['eventStatus'])) . "',
													`facebookEventID`									= '" . $wpdb->escape(trim($arrEvent['facebookEventID'])) . "',
													`facebookEventURL`								= '" . $wpdb->escape(trim($arrEvent['facebookEventURL'])) . "',
													`facebookWallPosts`								= '" . $wpdb->escape(trim($arrEvent['facebookWallPosts'])) . "',
													`facebookAttending`								= '" . $wpdb->escape(trim($arrEvent['facebookAttending'])) . "',
													`LastUpdatedAt`										= '" . $wpdb->escape(trim($arrEvent['LastUpdatedAt'])) . "'
											ON DUPLICATE KEY UPDATE
													`venueid`													= '" . $wpdb->escape(trim($arrEvent['venueid'])) . "',
													`venueroomname`										= '" . $wpdb->escape(trim($arrEvent['venueroomname'])) . "',
													`name`														= '" . $wpdb->escape(trim($arrEvent['name'])) . "',
													`shortDescription`								= '" . $wpdb->escape(trim($arrEvent['shortDescription'])) . "',
													`fullDescription`									= '" . $wpdb->escape(trim($arrEvent['fullDescription'])) . "',
													`isParentEvent`										= '" . $wpdb->escape(trim($arrEvent['isParentEvent'])) . "',
													`parentEventID`										= '" . $wpdb->escape(trim($arrEvent['parentEventID'])) . "',";
				if (empty($arrEvent['parentEarliestChildDate']))
					$sql .= " `parentEarliestChildDate`					= null,";
				else
					$sql .= " `parentEarliestChildDate`					= '" . $wpdb->escape(trim($arrEvent['parentEarliestChildDate'])) . "',";
				if (empty($arrEvent['parentLatestChildDate']))
					$sql .= " `parentLatestChildDate`					= null,";
				else
					$sql .= " `parentLatestChildDate`						= '" . $wpdb->escape(trim($arrEvent['parentLatestChildDate'])) . "',";
				if (empty($arrEvent['isParentEventPurchasesEnabled']))
					$sql .= " `isParentEventPurchasesEnabled`					= null,";
				else
					$sql .= " `isParentEventPurchasesEnabled`					= '" . $wpdb->escape(trim($arrEvent['isParentEventPurchasesEnabled'])) . "',";
				$sql .= " `url`															= '" . $wpdb->escape(trim($arrEvent['url'])) . "',
													`dateTime`												= '" . $wpdb->escape(trim($arrEvent['dateTime'])) . "',
													`doorsOpenAt`											= '" . $wpdb->escape(trim($arrEvent['doorsOpenAt'])) . "',
													`isSpotlightEvent`								= '" . $wpdb->escape(trim($arrEvent['isSpotlightEvent'])) . "',
													`isFeaturedEvent`									= '" . $wpdb->escape(trim($arrEvent['isFeaturedEvent'])) . "',
													`isPromoterPickEvent`							= '" . $wpdb->escape(trim($arrEvent['isPromoterPickEvent'])) . "',
													`ticketsAvailable`								= '" . $wpdb->escape(trim($arrEvent['ticketsAvailable'])) . "',
													`ticketsCountAvailable`						= '" . $wpdb->escape(trim($arrEvent['ticketsCountAvailable'])) . "',
													`ticketsCountPurchased`						= '" . $wpdb->escape(trim($arrEvent['ticketsCountPurchased'])) . "',";
				if (empty($arrEvent['timeOnSale']))
					$sql .= " `timeOnSale`					= null,";
				else
					$sql .= " `timeOnSale`						= '" . $wpdb->escape(trim($arrEvent['timeOnSale'])) . "',";
				if (empty($arrEvent['timeOffSale']))
					$sql .= " `timeOffSale`					= null,";
				else
					$sql .= " `timeOffSale`						= '" . $wpdb->escape(trim($arrEvent['timeOffSale'])) . "',";
				
				$sql .= " `ageDescription`									= '" . $wpdb->escape(trim($arrEvent['ageDescription'])) . "',
													`ticketPriceAdvance`							= '" . $wpdb->escape(trim($arrEvent['ticketPriceAdvance'])) . "',
													`ticketPriceDoor`									= '" . $wpdb->escape(trim($arrEvent['ticketPriceDoor'])) . "',
													`ticketPriceDoorLabel`						= '" . $wpdb->escape(trim($arrEvent['ticketPriceDoorLabel'])) . "',
													`buyNowLink`											= '" . $wpdb->escape(trim($arrEvent['buyNowLink'])) . "',
													`eventImage`											= '" . $wpdb->escape(trim($arrEvent['eventImage'])) . "',
													`eventImageURLSmall`							= '" . $wpdb->escape(trim($arrEvent['eventImageURLSmall'])) . "',
													`eventImageURLMedium`							= '" . $wpdb->escape(trim($arrEvent['eventImageURLMedium'])) . "',
													`eventImageURLOriginal`						= '" . $wpdb->escape(trim($arrEvent['eventImageURLOriginal'])) . "',
													`sellableTroughInternet`					= '" . $wpdb->escape(trim($arrEvent['sellableTroughInternet'])) . "',
													`sellableTroughInternetReason`		= '" . $wpdb->escape(trim($arrEvent['sellableTroughInternetReason'])) . "',
													`orderEntryBoxOfficeCashServiceFee`		= '" . $wpdb->escape(trim($arrEvent['orderEntryBoxOfficeCashServiceFee'])) . "',
													`eventAdminAccess`								= '" . $wpdb->escape(trim($arrEvent['eventAdminAccess'])) . "',
													`eventStatus`											= '" . $wpdb->escape(trim($arrEvent['eventStatus'])) . "',
													`facebookEventID`									= '" . $wpdb->escape(trim($arrEvent['facebookEventID'])) . "',
													`facebookEventURL`								= '" . $wpdb->escape(trim($arrEvent['facebookEventURL'])) . "',
													`facebookWallPosts`								= '" . $wpdb->escape(trim($arrEvent['facebookWallPosts'])) . "',
													`facebookAttending`								= '" . $wpdb->escape(trim($arrEvent['facebookAttending'])) . "',
													`LastUpdatedAt`										= '" . $wpdb->escape(trim($arrEvent['LastUpdatedAt'])) . "'";
				if ($this->stubwireDebugShowQuerys && (empty($this->stubwireDebugImportEvent) || $this->stubwireDebugImportEvent==$arrEvent['id']))	{
					echo "<b>Running Query:</b><br>";
					echo "\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n";
					echo "<pre>";
					print_r($sql);
					echo "</pre>";
					echo "\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n";
				}
				$result = $wpdb->query( $sql );
				if ($this->stubwireDebugShowQuerys && (empty($this->stubwireDebugImportEvent) || $this->stubwireDebugImportEvent==$arrEvent['id']))	{
					echo "<b>Result:</b><br>";
					echo "<pre>";
					print_r($result);
					echo "</pre>";
				}
				//die;

				// *****************************************
				// TESTING THE OUTPUT TIME
				// *****************************************
				// CALCULATE THE QUERY TIME
				$timerTotalTime		= (microtime(true) - $timerTotalStart) . " sec";
				$timerQueryTime		= (microtime(true) - $timerQueryStart) . " sec";
				// LOG THE QUERY TIME
				$debugData .= "[" . $timerTotalTime . "][" . $timerQueryTime . "] " . $sql . "\r\n\r\n";
				// *****************************************
					
				$wpPostInfo = array();
				$wpPostInfo['event_name'] = $arrEvent['name'];
	
				foreach($arrEvent['venues'] as $venue) {
					// *****************************************
					// TESTING THE OUTPUT TIME
					// *****************************************
					$timerQueryStart	= microtime(true); // Gets microseconds
					// *****************************************
					
					// Lets see if this event is already in the data
					$sql = "SELECT `LastUpdatedAt` FROM " . $tablePrefix . "venues as venues WHERE `venues`.`id`='%d'";
					$query = $wpdb->prepare($sql,$venue['id']);
					$stubwire_venue_check = $wpdb->get_results($query,ARRAY_A);

					// *****************************************
					// TESTING THE OUTPUT TIME
					// *****************************************
					// CALCULATE THE QUERY TIME
					$timerTotalTime		= (microtime(true) - $timerTotalStart) . " sec";
					$timerQueryTime		= (microtime(true) - $timerQueryStart) . " sec";
					// LOG THE QUERY TIME
					$debugData .= "[" . $timerTotalTime . "][" . $timerQueryTime . "] " . $sql . "\r\n\r\n";
					// *****************************************










					if (empty($stubwire_venue_check))	{
						$debugData .= "[line:" . __LINE__ . "] ******* EVENT NEEDS UPDATED ****** Venue check is not in an array\r\n\r\n";
						$stubwireEventUpdated = true;
						$strOutput .= " - <font color=blue>Venue Check is not in an array so lets update the venue</font><br>\n";
					}	elseif ($stubwire_venue_check[0]['LastUpdatedAt']!=$venue['LastUpdatedAt'])	{
						/*echo "<pre>";
						print_r($stubwire_venue_check);
						echo "</pre>";*/
						if (!$stubwireEventUpdated)	{
							$debugData .= "[line:" . __LINE__ . "] ******* EVENT NEEDS UPDATED ****** lastUpdated for venue is not the same\r\n\r\n";
							$strOutput .= " - <font color=blue>Venue last updated (" . $stubwire_venue_check[0]['LastUpdatedAt'] . ") is different than (" . $venue['LastUpdatedAt'] . ")</font><br>\n";
							$stubwireEventUpdated = true;
						}
					}	else	{
						//echo "<font color='blue'>VENUE last updated is the same (" . $stubwire_venue_check[0]['LastUpdatedAt'] . ")</font><BR>";
					}
	
					if (!isset($venue['url']))
						$venue['url'] = "";
					if (!isset($venue['image']))
						$venue['image'] = "";

					// *****************************************
					// TESTING THE OUTPUT TIME
					// *****************************************
					$timerQueryStart	= microtime(true); // Gets microseconds
					// *****************************************
											
					$sql = "INSERT INTO
														" . $tablePrefix . "venues
												SET
														`id`															= '" . $wpdb->escape($venue['id']) . "',
														`name`														= '" . $wpdb->escape($venue['name']) . "',
														`address`													= '" . $wpdb->escape($venue['address']) . "',
														`address2`												= '" . $wpdb->escape($venue['address2']) . "',
														`city`														= '" . $wpdb->escape($venue['city']) . "',
														`state`														= '" . $wpdb->escape($venue['state']) . "',
														`zip`															= '" . $wpdb->escape($venue['zip']) . "',
														`url`															= '" . $wpdb->escape($venue['url']) . "',
														`image`														= '" . $wpdb->escape($venue['image']) . "',
														`LastUpdatedAt`										= '" . $wpdb->escape($venue['LastUpdatedAt']) . "'
												ON DUPLICATE KEY UPDATE
														`name`														= '" . $wpdb->escape($venue['name']) . "',
														`address`													= '" . $wpdb->escape($venue['address']) . "',
														`address2`												= '" . $wpdb->escape($venue['address2']) . "',
														`city`														= '" . $wpdb->escape($venue['city']) . "',
														`state`														= '" . $wpdb->escape($venue['state']) . "',
														`zip`															= '" . $wpdb->escape($venue['zip']) . "',
														`url`															= '" . $wpdb->escape($venue['url']) . "',
														`image`														= '" . $wpdb->escape($venue['image']) . "',
														`LastUpdatedAt`										= '" . $wpdb->escape($venue['LastUpdatedAt']) . "'";
					if ($this->stubwireDebugShowQuerys && (empty($this->stubwireDebugImportEvent) || $this->stubwireDebugImportEvent==$arrEvent['id']))	{
						echo "<b>Running Query:</b><br>";
						echo "<pre>";
						print_r($sql);
						echo "</pre>";
					}
					$result = $wpdb->query( $sql );

					// *****************************************
					// TESTING THE OUTPUT TIME
					// *****************************************
					// CALCULATE THE QUERY TIME
					$timerTotalTime		= (microtime(true) - $timerTotalStart) . " sec";
					$timerQueryTime		= (microtime(true) - $timerQueryStart) . " sec";
					// LOG THE QUERY TIME
					$debugData .= "[" . $timerTotalTime . "][" . $timerQueryTime . "] " . $sql . "\r\n\r\n";
					// *****************************************
					
					$wpPostInfo['venue_name'] = $venue['name'];
					$wpPostInfo['venue_city'] = $venue['city'];
					$wpPostInfo['venue_state'] = $venue['state'];
				}
	
				foreach($arrEvent['acts'] as $act) {
					// *****************************************
					// TESTING THE OUTPUT TIME
					// *****************************************
					$timerQueryStart	= microtime(true); // Gets microseconds
					// *****************************************
					
					$sql = "SELECT `LastUpdatedAt` FROM " . $tablePrefix . "acts as acts WHERE `acts`.`id`='%d'";
					$query = $wpdb->prepare($sql,$act['id']);
					$stubwire_act_check = $wpdb->get_results($query,ARRAY_A);

					// *****************************************
					// TESTING THE OUTPUT TIME
					// *****************************************
					// CALCULATE THE QUERY TIME
					$timerTotalTime		= (microtime(true) - $timerTotalStart) . " sec";
					$timerQueryTime		= (microtime(true) - $timerQueryStart) . " sec";
					// LOG THE QUERY TIME
					$debugData .= "[" . $timerTotalTime . "][" . $timerQueryTime . "] " . $sql . "\r\n\r\n";
					// *****************************************
					
					if (empty($stubwire_act_check))	{
						if (!$stubwireEventUpdated)	{
							$debugData .= "[line:" . __LINE__ . "] ******* EVENT NEEDS UPDATED ****** xxxxxxxxxxxxxxx\r\n\r\n";
							$strOutput .= " - <font color=blue>Act is not in an array so lets update it</font><br>\n";
							$stubwireEventUpdated = true;
						}
					}	elseif ($stubwire_act_check[0]['LastUpdatedAt']!=$act['LastUpdatedAt'])	{				
						if (!$stubwireEventUpdated)	{
							$debugData .= "[line:" . __LINE__ . "] ******* EVENT NEEDS UPDATED ****** xxxxxxxxxxxxxxx\r\n\r\n";
							$strOutput .= " - <font color=blue>Act  LAST UPDATED (" . $stubwire_act_check[0]['LastUpdatedAt'] . ") is different than (" . $act['LastUpdatedAt'] . ")</font><br>\n";
							$stubwireEventUpdated = true;
						}
					}	else	{
						//echo "<font color='blue'>ACT last updated is the same (" . $stubwire_act_check[0]['LastUpdatedAt'] . ")</font><BR>";
					}
					
					if (empty($act['websiteurl']))
						$act['websiteurl'] = "";
					
					if (isset($act['actStatus']) && $act['actStatus']=='Deleting')	{
						// *****************************************
						// TESTING THE OUTPUT TIME
						// *****************************************
						$timerQueryStart	= microtime(true); // Gets microseconds
						// *****************************************

						$sql = "DELETE FROM " . $tablePrefix . "acts where id= '" . $wpdb->escape($act['id']) . "'";
						if ($this->stubwireDebugShowQuerys && (empty($this->stubwireDebugImportEvent) || $this->stubwireDebugImportEvent==$arrEvent['id']))	{
							echo "<b>Running Query:</b><br>";
							echo "<pre>";
							print_r($sql);
							echo "</pre>";
						}
						$wpdb->query( $sql );

						// *****************************************
						// TESTING THE OUTPUT TIME
						// *****************************************
						// CALCULATE THE QUERY TIME
						$timerTotalTime		= (microtime(true) - $timerTotalStart) . " sec";
						$timerQueryTime		= (microtime(true) - $timerQueryStart) . " sec";
						// LOG THE QUERY TIME
						$debugData .= "[" . $timerTotalTime . "][" . $timerQueryTime . "] " . $sql . "\r\n\r\n";
						// *****************************************
					
					}	else	{
						// *****************************************
						// TESTING THE OUTPUT TIME
						// *****************************************
						$timerQueryStart	= microtime(true); // Gets microseconds
						// *****************************************
					
						$sql = "INSERT INTO
															" . $tablePrefix . "acts
													SET
															`id`															= '" . $wpdb->escape($act['id']) . "',
															`eventid`													= '" . $wpdb->escape($arrEvent['id']) . "',
															`name`														= '" . $wpdb->escape($act['name']) . "',
															`url`															= '" . $wpdb->escape($act['websiteurl']) . "',
															`type`														= '" . $wpdb->escape($act['type']) . "',
															`displayorder`										= '" . $wpdb->escape($act['displayorder']) . "',
															`status`													= '" . $wpdb->escape($act['status']) . "',
															`LastUpdatedAt`										= '" . $wpdb->escape($act['LastUpdatedAt']) . "'
													ON DUPLICATE KEY UPDATE
															`eventid`													= '" . $wpdb->escape($arrEvent['id']) . "',
															`name`														= '" . $wpdb->escape($act['name']) . "',
															`url`															= '" . $wpdb->escape($act['websiteurl']) . "',
															`type`														= '" . $wpdb->escape($act['type']) . "',
															`displayorder`										= '" . $wpdb->escape($act['displayorder']) . "',
															`status`													= '" . $wpdb->escape($act['status']) . "',
															`LastUpdatedAt`										= '" . $wpdb->escape($act['LastUpdatedAt']) . "'";
						if ($this->stubwireDebugShowQuerys && (empty($this->stubwireDebugImportEvent) || $this->stubwireDebugImportEvent==$arrEvent['id']))	{
							echo "<b>Running Query:</b><br>";
							echo "<pre>";
							print_r($sql);
							echo "</pre>";
						}
						$result = $wpdb->query( $sql );

						// *****************************************
						// TESTING THE OUTPUT TIME
						// *****************************************
						// CALCULATE THE QUERY TIME
						$timerTotalTime		= (microtime(true) - $timerTotalStart) . " sec";
						$timerQueryTime		= (microtime(true) - $timerQueryStart) . " sec";
						// LOG THE QUERY TIME
						$debugData .= "[" . $timerTotalTime . "][" . $timerQueryTime . "] " . $sql . "\r\n\r\n";
						// *****************************************
											
						if (!isset($wpPostInfo['event_artists']))
							$wpPostInfo['event_artists'] = "";
						if (!empty($wpPostInfo['event_artists']))
							$wpPostInfo['event_artists'] .= "<br>\r\n";
						if (!empty($act['url']) && isset($act['url']))
							$wpPostInfo['event_artists'] .= "<a href='" . $act['url'] . "'>";
						$wpPostInfo['event_artists'] .= $act['name'];
						if (!empty($act['url']) && isset($act['url']))
							$wpPostInfo['event_artists'] .= "</a>";
					}
				}
	
				foreach($arrEvent['images'] as $image) {
					// *****************************************
					// TESTING THE OUTPUT TIME
					// *****************************************
					$timerQueryStart	= microtime(true); // Gets microseconds
					// *****************************************
					
					$sql = "SELECT `LastUpdatedAt` FROM " . $tablePrefix . "images as images WHERE `images`.`id`='%d'";
					$query = $wpdb->prepare($sql,$image['id']);
					$stubwire_image_check = $wpdb->get_results($query,ARRAY_A);

					// *****************************************
					// TESTING THE OUTPUT TIME
					// *****************************************
					// CALCULATE THE QUERY TIME
					$timerTotalTime		= (microtime(true) - $timerTotalStart) . " sec";
					$timerQueryTime		= (microtime(true) - $timerQueryStart) . " sec";
					// LOG THE QUERY TIME
					$debugData .= "[" . $timerTotalTime . "][" . $timerQueryTime . "] " . $sql . "\r\n\r\n";
					// *****************************************
									
					if (empty($stubwire_image_check))	{
						$debugData .= "[line:" . __LINE__ . "] ******* EVENT NEEDS UPDATED ****** xxxxxxxxxxxxxxx\r\n\r\n";
						$stubwireEventUpdated = true;
						$strOutput .= " - <font color=blue> - Image check is not in an array so lets update the image</font><br>\n";
					}	elseif ($stubwire_image_check[0]['LastUpdatedAt']!=$image['LastUpdatedAt'])	{
						/*echo "<pre>";
						print_r($stubwire_image_check);
						echo "</pre>";*/
						if ($stubwireEventUpdated)	{
							$debugData .= "[line:" . __LINE__ . "] ******* EVENT NEEDS UPDATED ****** xxxxxxxxxxxxxxx\r\n\r\n";
							$strOutput .= " - <font color=blue> - Image LAST UPDATED (" . $stubwire_image_check[0]['LastUpdatedAt'] . ") is different than (" . $image['LastUpdatedAt'] . ")</font><br>\n";
							$stubwireEventUpdated = true;
						}
					}	else	{
						//echo "<font color='blue'>image last updated is the same (" . $stubwire_image_check[0]['LastUpdatedAt'] . ")</font><BR>";
					}
					
					if (isset($image['imageStatus']) && $image['imageStatus']=='Deleting')	{
						// *****************************************
						// TESTING THE OUTPUT TIME
						// *****************************************
						$timerQueryStart	= microtime(true); // Gets microseconds
						// *****************************************
					
						$sql = "DELETE FROM " . $tablePrefix . "images where id= '" . $wpdb->escape($image['id']) . "'";
						if ($this->stubwireDebugShowQuerys && (empty($this->stubwireDebugImportEvent) || $this->stubwireDebugImportEvent==$arrEvent['id']))	{
							echo "<b>Running Query:</b><br>";
							echo "<pre>";
							print_r($sql);
							echo "</pre>";
						}
						$wpdb->query( $sql );

						// *****************************************
						// TESTING THE OUTPUT TIME
						// *****************************************
						// CALCULATE THE QUERY TIME
						$timerTotalTime		= (microtime(true) - $timerTotalStart) . " sec";
						$timerQueryTime		= (microtime(true) - $timerQueryStart) . " sec";
						// LOG THE QUERY TIME
						$debugData .= "[" . $timerTotalTime . "][" . $timerQueryTime . "] " . $sql . "\r\n\r\n";
						// *****************************************
					}	else	{
						// *****************************************
						// TESTING THE OUTPUT TIME
						// *****************************************
						$timerQueryStart	= microtime(true); // Gets microseconds
						// *****************************************
					
						$sql = "INSERT INTO
															" . $tablePrefix . "images
													SET
															`id`															= '" . $wpdb->escape($image['id']) . "',
															`eventid`													= '" . $wpdb->escape($arrEvent['id']) . "',
															`ismainimage`											= '" . $wpdb->escape($image['isMainImage']) . "',
															`isposterimage`										= '" . $wpdb->escape($image['isPosterImage']) . "',
															`name`														= '" . $wpdb->escape($image['imageName']) . "',
															`caption`													= '" . $wpdb->escape($image['imageCaption']) . "',
															`filename`												= '" . $wpdb->escape($image['fileName']) . "',
															`displayorder`										= '" . $wpdb->escape($image['imageDisplayOrder']) . "',
															`type`														= '" . $wpdb->escape($image['imageType']) . "',
															`status`													= '" . $wpdb->escape($image['imageStatus']) . "',
															`LastUpdatedAt`										= '" . $wpdb->escape($image['LastUpdatedAt']) . "'
													ON DUPLICATE KEY UPDATE
															`eventid`													= '" . $wpdb->escape($arrEvent['id']) . "',
															`ismainimage`											= '" . $wpdb->escape($image['isMainImage']) . "',
															`isposterimage`										= '" . $wpdb->escape($image['isPosterImage']) . "',
															`name`														= '" . $wpdb->escape($image['imageName']) . "',
															`caption`													= '" . $wpdb->escape($image['imageCaption']) . "',
															`filename`												= '" . $wpdb->escape($image['fileName']) . "',
															`displayorder`										= '" . $wpdb->escape($image['imageDisplayOrder']) . "',
															`type`														= '" . $wpdb->escape($image['imageType']) . "',
															`status`													= '" . $wpdb->escape($image['imageStatus']) . "',
															`LastUpdatedAt`										= '" . $wpdb->escape($image['LastUpdatedAt']) . "'";
						if ($this->stubwireDebugShowQuerys && (empty($this->stubwireDebugImportEvent) || $this->stubwireDebugImportEvent==$arrEvent['id']))	{
							echo "<b>Running Query:</b><br>";
							echo "<pre>";
							print_r($sql);
							echo "</pre>";
						}
						$result = $wpdb->query( $sql );

						// *****************************************
						// TESTING THE OUTPUT TIME
						// *****************************************
						// CALCULATE THE QUERY TIME
						$timerTotalTime		= (microtime(true) - $timerTotalStart) . " sec";
						$timerQueryTime		= (microtime(true) - $timerQueryStart) . " sec";
						// LOG THE QUERY TIME
						$debugData .= "[" . $timerTotalTime . "][" . $timerQueryTime . "] " . $sql . "\r\n\r\n";
						// *****************************************
					}
				}
				
				$wpPostTitle		= $wpPostInfo['event_name'];
				$wpPostContent	= "<h3>" . $wpPostInfo['venue_name'] . "</h3><h4>" . $wpPostInfo['venue_city'] . ", " . $wpPostInfo['venue_state'] . "</h4>";
				if (!empty($wpPostInfo['event_artists']))
					$wpPostContent .= "<b>Artist's</b><br>" . $wpPostInfo['event_artists'];
	
				$wpPostContent	= "[[stubwire-events page_len=\"1\" page_num=\"{{event_page|1}}\" template=\"{selectedevent}\" where=\"event.id='" . $arrEvent['id'] . "'\" ]]";
				
				if ($stubwireEventUpdated)	{
					$debugData .= "[line:" . __LINE__ . "] ******* UPDATING EVENT AND WORDPRESS BLOG ******\r\n\r\n";
					$strOutput .= " - <font color=green><b>Event is new or updated so were doing a new post</b></font><br>\n";
					// Lets create a post for wordpress with the details
					$my_post = array(
					   'post_title' => $wpPostTitle,
					   'post_content' => $wpPostContent,
					   'post_status' => 'publish',
					   'post_author' => get_option('stubwire_PostAuthor'),
					   'post_category' => array(get_option('stubwire_PostCategory'))
					);
					
					if (!empty($oldEvent['wpPostID']))	{
						$my_post['ID'] = $oldEvent['wpPostID'];
						//$my_post['post_id'] = $oldEvent['wpPostID'];
						
						/*echo "<pre>";
						print_r($my_post);
						echo "</pre>";*/
					}

					if ($this->stubwireDebugShowQuerys && (empty($this->stubwireDebugImportEvent) || $this->stubwireDebugImportEvent==$arrEvent['id']))	{
						echo "<b>WP Post:</b>" . $my_post . "<br>";
					}

					// *****************************************
					// TESTING THE OUTPUT TIME
					// *****************************************
					$timerQueryStart	= microtime(true); // Gets microseconds
					$sqlQuery = "INSERTING WP POST";
					// *****************************************
					
					// Insert the post into the database
					$post_ID = wp_insert_post( $my_post );

					// *****************************************
					// TESTING THE OUTPUT TIME
					// *****************************************
					// CALCULATE THE QUERY TIME
					$timerTotalTime		= (microtime(true) - $timerTotalStart) . " sec";
					$timerQueryTime		= (microtime(true) - $timerQueryStart) . " sec";
					// LOG THE QUERY TIME
					$debugData .= "[" . $timerTotalTime . "][" . $timerQueryTime . "] " . $sqlQuery . "\r\n\r\n";
					// *****************************************
						
					add_post_meta( $post_ID, '_StubWireEventID', $arrEvent['id'], true);

					// *****************************************
					// TESTING THE OUTPUT TIME
					// *****************************************
					$timerQueryStart	= microtime(true); // Gets microseconds
					// *****************************************
					
					$sql = "UPDATE
														" . $tablePrefix . "events
												SET
														`wp_postid`	= '" . $wpdb->escape($post_ID) . "'
												WHERE
														`id`='" . $wpdb->escape($arrEvent['id']) . "'";
					if ($this->stubwireDebugShowQuerys && (empty($this->stubwireDebugImportEvent) || $this->stubwireDebugImportEvent==$arrEvent['id']))	{
						echo "<b>Running Query:</b><br>";
						echo "<pre>";
						print_r($sql);
						echo "</pre>";
					}
					$result = $wpdb->query( $sql );

					// *****************************************
					// TESTING THE OUTPUT TIME
					// *****************************************
					// CALCULATE THE QUERY TIME
					$timerTotalTime		= (microtime(true) - $timerTotalStart) . " sec";
					$timerQueryTime		= (microtime(true) - $timerQueryStart) . " sec";
					// LOG THE QUERY TIME
					$debugData .= "[" . $timerTotalTime . "][" . $timerQueryTime . "] " . $sql . "\r\n\r\n";
					// *****************************************
				}	else	{
					//$strOutput .= " - <font color=green>Nothing has changed on the event</font><br>\n";
				}
			}
			
			if (get_option('stubwire_SubmitDetailedLogs')=='Yes')	{
				$this->sendLogEntry("[import_Event] Finished imorting EventID " . $arrEvent['id'] . "\r\n\r\n" . $debugData, "debug");
			}
			//wp_mail('brad@stubwire.com', site_url() . ' (' . date("Y-n-d H:i:s") . ') Finished imorting EventID ' . $arrEvent['id'], $debugData);
			
			return $strOutput;
		}
		
		function addAction_PluginsMenu()	{
			add_submenu_page('plugins.php', 'StubWire.com Settings', 'StubWire.com', 'edit_pages', basename(__FILE__), array($this,'addView_AdminPlugins'));
		}
		function addView_AdminPlugins()	{
			include( dirname( __FILE__ ) . '/views/admin.php' );
		}
	} // end StubWire class

	//echo "Lets create the object<br>";
	$stubWire = new StubWire();
	//echo "Object created<br>";
} // end if !class_exists StubWire