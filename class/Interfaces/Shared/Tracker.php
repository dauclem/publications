<?php

namespace Interfaces\Shared;

use Interfaces\Shared;

/**
 * Class to manage Bug Tracker
 */
interface Tracker extends Shared {
	/**
	 * Get object definition to construct object
	 *
	 * @return string
	 */
	public function get_object_definition();

	/**
	 * Get tracker id pattern for preg to find tracker ids into a string
	 *
	 * @return string
	 */
	public function get_tracker_id_pattern();

	/**
	 * Find all trackers id into $message and return respective tracker objects
	 *
	 * @param string $message
	 * @return \Interfaces\Object\Tracker[]
	 */
	public function get_trackers_from_message($message);

	/**
	 * Filter trackers list with query set to config to filter only trackers to send by email
	 *
	 * @param \Interfaces\Object\Tracker[] $trackers
	 * @return \Interfaces\Object\Tracker[]
	 */
	public function filter_trackers($trackers);
}