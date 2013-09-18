<?php

namespace Interfaces\Shared;

use Interfaces\Shared;

/**
 * Class to manage Bug Tracker
 */
interface Tracker extends Shared {
	/**
	 * Get tracker id pattern for preg to find tracker ids into a string
	 *
	 * @return string
	 */
	public function getTrackerIdPattern();

	/**
	 * Find all trackers id into $message and return respective tracker objects
	 *
	 * @param string $message
	 * @return \Interfaces\Object\Tracker[]
	 */
	public function getTrackersFromMessage($message);

	/**
	 * Filter trackers list with query set to config to filter only trackers to send by email
	 *
	 * @param \Interfaces\Object\Tracker[] $trackers
	 * @param \Interfaces\Object\Project   $project
	 * @return \Interfaces\Object\Tracker[]
	 */
	public function filterTrackers($trackers, \Interfaces\Object\Project $project = null);
}