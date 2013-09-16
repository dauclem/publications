<?php

namespace Interfaces\Object;

use Interfaces\Object;
use Interfaces\Object\Tracker;
use Interfaces\Object\Publication;

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
	 * Get related publication object if this row is a publication
	 *
	 * @return Publication|null
	 */
	public function get_publication();

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
	 * @param Publication $publication
	 */
	public function set_publication(Publication $publication);
}