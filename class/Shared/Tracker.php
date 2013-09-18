<?php

namespace Shared;

use Shared;

abstract class Tracker extends Shared implements \Interfaces\Shared\Tracker {
	/**
	 * {@inheritDoc}
	 */
	public function getTrackersFromMessage($message) {
		$trackers = array();
		if (preg_match_all($this->getTrackerIdPattern(), $message, $matches)) {
			if (isset($matches[1])) {
				foreach ($matches[1] as $tracker_id) {
					$tracker = $this->dic->getObject('tracker_object', $tracker_id);
					if ($tracker) {
						$trackers[] = $tracker;
					}
				}
			}
		}
		return $trackers;
	}
}