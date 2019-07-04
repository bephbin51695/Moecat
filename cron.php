<?php
require_once("include/bittorrent.php");
dbconn();
$useCronTriggerCleanUp=true;
if ($useCronTriggerCleanUp) {
	$return = autoclean();
	if ($return) {
		echo $return."\n";
	} else {
		echo "Clean-up not triggered.\n";
		imdbdoubanautoupdate();
	}
} else {
	echo "Forbidden. Clean-up is set to be browser-triggered.\n";
}


