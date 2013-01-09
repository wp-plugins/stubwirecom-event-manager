<?
			echo "<ul>";
			while($event = stubwire_get_event()) {
				echo "<li><a href=\"" . get_permalink($event['wp_postid']) . "\"><b>" . $event['name'] . "</b></a><br><i>" . date("m/d/Y", strtotime($event['dateTime'])) . "</i></li>";
				stubwire_next_event();
			}
			echo "</ul>";
?>