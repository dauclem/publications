<?php

namespace Shared\Tracker;

use Shared\Tracker;

class Jira extends Tracker implements \Interfaces\Shared\Tracker\Jira {
	/**
	 * {@inheritDoc}
	 */
	public function get_object_definition() {
		return 'jira_object';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_tracker_id_pattern() {
		return '#([A-Z]{2,}-[0-9]+)#';
	}
}