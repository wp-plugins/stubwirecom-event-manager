<!-- STARTING STUBWIRE TEMPLATE HEADER -->
		<div align="center">
		<table width="615" border="0">
<!-- ENDING STUBWIRE TEMPLATE HEADER -->
<?php
$meta = stubwire_events_meta();
while($event = stubwire_get_event()): ?>
<?php

			$newDesciption = "<p> </p><table align=\"center\" width=\"95%\">\n";
			$newDesciption .= "	<tr>\n";
			$newDesciption .= "		<td colspan=\"2\" class=\"EventDetails\">";
			if (!empty($event['shortDescription']))	{
				$posFind = "youtube.com/embed/";
				$posYouTube = strpos($event['shortDescription'], $posFind);
				
				//<object width="640" height="390"><param name="movie" value="http://www.youtube.com/v/-DUeFGDf-As&hl=en_US&feature=player_embedded&version=3"></param><param name="allowFullScreen" value="true"></param><param name="allowScriptAccess" value="always"></param><embed src="http://www.youtube.com/v/-DUeFGDf-As&hl=en_US&feature=player_embedded&version=3" type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="640" height="390"></embed></object>
				
				//<iframe width="640" height="390" src="http://www.youtube.com/embed/ezE-wfmJsvE" frameborder="0" allowfullscreen></iframe>
				
				//<script type="text/javascript" src="http://player.ooyala.com/player.js?embedCode=1ocmRsMjrOcRQyYaXx4aFNr4o_3J6HGV&width=640&height=360"></script>
				
				//http://www.youtube.com/v/-DUeFGDf-As&hl=en_US&feature=player_embedded&version=3
				
				if ($posYouTube === false) {
					// YouTube was not found

					$posFind = "youtube.com/v/";
					$posYouTube = strpos($event['shortDescription'], $posFind);
					
					if ($posYouTube === false) {
						// YouTube was not found

						/*$posFind = "player.ooyala.com/player.js";
						$posOoyalaPlayer = strpos($event['shortDescription'], $posFind);
						
						if ($posOoyalaPlayer === false) {
							// Cant find the ooyala.com code
							
						}	else	{
							$posFind = "&width=";
							$posOoyalaPlayerWidth = strpos($event['shortDescription'], $posFind, $posOoyalaPlayer);
							
							if ($posOoyalaPlayerWidth === false) {
								// Cant find the width
								
							}	else	{
								$OoyalaWidth = substr($event['shortDescription'], ($posOoyalaPlayerWidth+STRLEN($posFind);
								echo "<h1>WIDTH=" . $OoyalaWidth . "</h1>";
							}
						}*/
										//<script type="text/javascript" src="http://player.ooyala.com/player.js?embedCode=1ocmRsMjrOcRQyYaXx4aFNr4o_3J6HGV&width=640&height=360"></script>
					} else {
						$startFileName = substr($event['shortDescription'], ($posYouTube+strlen($posFind)));
						$posYouTubeEnd = strpos($startFileName, "&");
						if ($posYouTube === false) {
							
						}	else	{
							$youTubeFileID = substr($startFileName, 0, $posYouTubeEnd);
							
							//$event['shortDescription'] = "TEMP REMOVED VIDEO";
							$event['shortDescription'] = "<!-- START STUBWIRE CONVERT --><object width=\"425\" height=\"350\" data=\"http://www.youtube.com/v/" . $youTubeFileID . "\" type=\"application/x-shockwave-flash\"><param name=\"src\" value=\"http://www.youtube.com/v/" . $youTubeFileID . "\" /></object><!-- END STUBWIRE CONVERT -->\n";
						}
					}
				} else {
					$startFileName = substr($event['shortDescription'], ($posYouTube+strlen($posFind)));
					$posYouTubeEnd = strpos($startFileName, "\"");
					if ($posYouTube === false) {
						
					}	else	{
						$youTubeFileID = substr($startFileName, 0, $posYouTubeEnd);
						
						//$event['shortDescription'] = "TEMP REMOVED VIDEO";
						$event['shortDescription'] = "<!-- START STUBWIRE CONVERT --><object width=\"425\" height=\"350\" data=\"http://www.youtube.com/v/" . $youTubeFileID . "\" type=\"application/x-shockwave-flash\"><param name=\"src\" value=\"http://www.youtube.com/v/" . $youTubeFileID . "\" /></object><!-- END STUBWIRE CONVERT -->\n";
					}
				}

				$newDesciption .= "<span class=\"shortDesc2\">";
				$newDesciption .= $event['shortDescription'];
				$newDesciption .= "</span>";
			}
			$newDesciption .= "<span class=\"fullDesc2\">";
			$newDesciption .= $event['fullDescription'];
			$newDesciption .= "</span>";
			$newDesciption .= "</td>\n";
			$newDesciption .= "	</tr>\n";
			$newDesciption .= "</table>\n";
			
?>
<!-- STARTING STUBWIRE TEMPLATE BODY -->
<!-- Start Event --> 
			<tr class="stubwire_eventbox">
				<td class="stubwire_eventbox_left">
					<?php if ($event['eventImageURLMedium']): ?>
					<a href="<?php echo $event['url']; ?>"><img src="<?php echo $event['eventImageURLMedium']?>" border="0" width="120" vspace="10" hspace="10"/></a>
					<?php else: ?>
					 
					<?php endif ?>

					<div class="stubwire_eventbox_venuename"><a href="#" title="<?php echo $event['venue.name'] ?>" rel="bookmark"><?php echo $event['venue.name'] ?></a></div>
					<div class="stubwire_eventbox_venuestate"><?php echo $event['venue.city'] ?>,<?php echo $event['venue.state'] ?></div>
					<div class="stubwire_eventbox_eventdate2"><?php echo date("D M j, Y", strtotime($event['dateTime'])); ?></div>
					<div class="stubwire_eventbox_doors">DOORS: <?php echo $event['doorsOpenAt']; ?></div>
					<div class="stubwire_eventbox_ageinfo"><?php echo $event['ageDescription']; ?></div>
					<div class="stubwire_eventbox_ticketprice"><?php echo $event['ticketPriceFriendly']; ?></div>
					<div> </div>
					<div class="stubwire_eventbox_button_buy2" align="center"><a href="<?php echo $event['buyNowLink']; ?>"><img src="<?php bloginfo('template_directory'); ?>/images/buynow.jpg" alt="buy" /></a></div>
					<div> </div>
<?
if (!empty($event['facebookEventID']))	{
?>
					<div class="stubwire_eventbox_facebook_event" align="center"><a href="http://www.facebook.com/events/<?php echo $event['facebookEventID']; ?>/"><img src="<?php bloginfo('template_directory'); ?>/images/facebook_event.gif" alt="Facebook Event" /></a></div>
					<div> </div>
<?
}
?>
	<?php
	#print_r($event); exit();
	if (count($event['acts']) > 0)	{
	?>
	<div id="stubwire_eventbox_acts">
		<ul class="act">
		<?php
			foreach ($event['acts'] as $act)	{
				$actListing = "<li>";				
	#			print_r($act);
				
				if (!empty($act['act.url']))
					$actListing .= "<a href=\"" . $act['act.url'] . "\" target=\"_blank\">";
				$actListing .= $act['act.name'];
				if (!empty($act['act.url']))
					$actListing .= "</a>";
				$actListing .= "</li>";
				
				echo $actListing;
				
			}
		?>
		</ul>
	</div>
	<?php
	}
	?>
				</td>
				<td class="stubwire_eventbox_middle">
					<div class="stubwire_eventbox_eventname"><a href="<?php echo $event['url']; ?>"><?php echo $event['name']?></a></div>
					<?php echo $newDesciption; ?>
				</td>
			</tr>
<!-- End Event -->
<!-- ENDING STUBWIRE TEMPLATE BODY -->
<?php stubwire_next_event(); endwhile; ?>
<!-- STARTING STUBWIRE TEMPLATE FOOTER -->
		</table>
		</div>
		<?php
		if (isset($meta['vars']['page_num']))	{
			if ($meta['total_pages'] > 1)	{
				echo "<div class=\"swe_pager\">Page: ";
				$qs = $_GET;
				for($i=1;$i<=$meta['total_pages'];$i++) {
					if ($i==$meta['page_num']) echo " <b>$i</b> ";
					else {
						$qs[$meta['vars']['page_num']] = $i;
						echo " <a href=\"?".http_build_query($qs)."\">$i</a> ";
					}
				}
				echo "</div>";
			}
		}
		?>
<!-- ENDING STUBWIRE TEMPLATE FOOTER -->