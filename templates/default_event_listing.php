		<div align="center">
		<table class="EventList" align="center" width="95%">
		<?php
		$meta = stubwire_events_meta();
		while($event = stubwire_get_event()): ?>
<!-- Start Event --> 
			<tr class="EventListRow">
				<td class="EventImage"><a href="<? echo $event['eventImageURLOriginal']; ?>" title=" " class="shutterset_set_1"><img src="<? echo $event['eventImageURLMedium']; ?>" border="0"></a></td>
				<td class="EventInfo">
<?
$eventDate = "";
if ($event['isParentEvent']=='Yes')	{
	$tmpParentEarliestChild = strtotime($event['parentEarliestChildDate']) - (5 * 3600);
	$tmpParentLatestChild = strtotime($event['parentLatestChildDate']) - (5 * 3600);
	
	$eventDate = date("D M d", $tmpParentEarliestChild) . " to " . date("D M d", $tmpParentLatestChild);
}	else	{
	$eventDate = date("l F jS Y", strtotime($event['dateTime']));
}
?>
					<div id="eventName"><a href="<? echo $event['url']; ?>"><? echo $event['name']; ?></a></div>
					<div id="eventDate"><? echo $eventDate; ?></div>
<?
if ($event['eventStatus']=='Canceled')	{
?>
					<div id="eventStatus">CANCELED</div>
<?
}	else	{
?>
					<div id="eventAges"><? echo $event['ageDescription']; ?></div>
					<div id="eventTicketPrices"><? echo $event['ticketPriceFriendly']; ?></div>
<?
		echo "<div id=\"action_buttons\">\n";
		echo "	<input type=\"button\" value=\"More Info\" name=\"btnMoreInfo\" id=\"btnMoreInfo\" class=\"buttonMoreInfo\" onClick=\"location.href='" . get_permalink($event['wp_postid']) . "';\">\n";
		if (strtotime($event['dateTime']) > strtotime(date("Y-n-d H:i:s"))-21600)	{
			if ($event['eventAdminAccess']=='Yes' && $event['ticketsCountAvailable']=='0')	{
			}	else	{
				echo "	<input type=\"button\" value=\"Buy Now\" name=\"btnBuyNow\" id=\"btnBuyNow\" class=\"buttonBuyNow\" onClick=\"location.href='" . $event['buyNowLink'] . "';\">\n";
			}
		}
		echo "</div>\n";
		if (!empty($event['facebookEventID']) || !empty($event['facebookEventURL']))	{
			$eventURL = $event['facebookEventURL'];
			if (empty($eventURL))	{
				$eventURL = "http://www.facebook.com/events/" . $event['facebookEventID'] . "/";
				//.jpg
			}
			echo "<div align=center>\n";
			echo "	<a href=\"" . $eventURL . "\"><img src=\"";
			bloginfo('stylesheet_directory');
			echo "/images/btn_FacebookRSVP.png\" border=\"0\"></a>\n";
			echo "</div>\n";
		}
}
?>
				</td>
<?
echo "          </tr>\n";
							

	?>
<!-- End Event -->

		<?php stubwire_next_event(); endwhile; ?>
		</table>
		</div>
		<?php
		if (isset($meta['vars']['page_num']))	{
			if ($meta['total_pages'] > 1)	{
				echo "<div class=\"wp-pagenavi\">Page (" . $meta['total_rows'] . ")(" . $meta['total_pages'] . "): ";
				$qs = $_GET;
				for($i=1;$i<=$meta['total_pages'];$i++) {
					if ($i==$meta['page_num'])	{
						echo " <span class='current'>$i</span> ";
					}	else {
						$qs[$meta['vars']['page_num']] = $i;
						echo " <a href=\"?".http_build_query($qs)."\" class=\"page larger\">$i</a> ";
					}
				}
				echo "</div>";
			}
		}
		?>