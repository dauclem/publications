<?php

namespace Interfaces\Object;

use Interfaces\Object;
use Interfaces\Object\Tracker;

interface Row extends Object {
	/**
	 * @return int
	 */
	public function get_date();

	/**
	 * Get array with project id as key which contains an array of revisions
	 *
	 * @return array
	 */
	public function get_revisions();

	/**
	 * Get array with project id as key which contains an array of changelog text
	 *
	 * @return array
	 */
	public function get_changelog();

	/**
	 * Get array with project id as key which contains an array of comments
	 *
	 * @return array
	 */
	public function get_comments();

	/**
	 * Calculate all bug tracked into comments
	 *
	 * @return Tracker[]
	 */
	public function get_trackers();

	/**
	 * Get related object if this row has one
	 *
	 * @return Object|null
	 */
	public function get_related_object();

	/**
	 * @param int $date
	 */
	public function set_date($date);

	/**
	 * Set array with project id as key which contains an array of revisions
	 *
	 * @param array $revisions
	 */
	public function set_revisions($revisions);

	/**
	 * Set array with project id as key which contains an array of changelog text
	 *
	 * @param array $changelog
	 */
	public function set_changelog($changelog);

	/**
	 * Set array with project id as key which contains an array of comments
	 *
	 * @param array $comments
	 */
	public function set_comments($comments);

	/**
	 * @param Object $related_object
	 */
	public function set_related_object(Object $related_object);
}