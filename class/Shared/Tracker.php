<?php

namespace Shared;

use Shared;

abstract class Tracker extends Shared implements \Interfaces\Shared\Tracker {
	/**
	 * {@inheritDoc}
	 */
	public function get_trackers_from_message($message) {
		$trackers = array();
		if (preg_match_all($this->get_tracker_id_pattern(), $message, $matches)) {
			if (isset($matches[1])) {
				foreach ($matches[1] as $tracker_id) {
					$tracker = $this->dic->get_object($this->get_object_definition(), $tracker_id);
					if ($tracker) {
						$trackers[] = $tracker;
					}
				}
			}
		}
		return $trackers;
	}
}