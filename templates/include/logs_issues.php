<?php

/** @var \Interfaces\Object\Issue[] $issues_list */
if (!isset($issues_list) || !is_array($issues_list)) {
	$issues_list = array();
}

$current_type = '';
foreach ($issues_list as $this_issue) {
	if ($current_type != $this_issue->getType()) {
		if ($current_type) {
			echo '<br />';
		}

		$current_type = $this_issue->getType();
		echo '<strong>_____'.htmlentities($current_type).'_____</strong><br />';
	}

	echo '<a target="_blank" title="'.htmlentities($this_issue->getTitle()).'" href="'.$this_issue->getUrl().'">'
		 .htmlentities($this_issue->getId()).' : '.htmlentities($this_issue->getTitle())
		 .'</a><br />';
}