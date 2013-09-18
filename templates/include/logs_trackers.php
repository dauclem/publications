<?php

/** @var \Interfaces\Object\Tracker[] $trackers_list */
if (!isset($trackers_list) || !is_array($trackers_list)) {
	$trackers_list = array();
}

$current_type = '';
foreach ($trackers_list as $this_tracker) {
	if ($current_type != $this_tracker->getType()) {
		if ($current_type) {
			echo '<br />';
		}

		$current_type = $this_tracker->getType();
		echo '<strong>_____'.htmlentities($current_type).'_____</strong><br />';
	}

	echo '<a target="_blank" title="'.htmlentities($this_tracker->getTitle()).'" href="'.$this_tracker->getUrl().'">'
		 .htmlentities($this_tracker->getId()).' : '.htmlentities($this_tracker->getTitle())
		 .'</a><br />';
}