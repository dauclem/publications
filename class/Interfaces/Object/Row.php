<?php

namespace Interfaces\Object;

use Interfaces\Object;
use Interfaces\Object\Issue;

interface Row extends Object {
	/**
	 * @return int
	 */
	public function getDate();

	/**
	 * Get array with project id as key which contains an array of revisions
	 *
	 * @return array
	 */
	public function getRevisions();

	/**
	 * Get array with project id as key which contains an array of changelog text
	 *
	 * @return array
	 */
	public function getChangelog();

	/**
	 * Get array with project id as key which contains an array of comments
	 *
	 * @return array
	 */
	public function getComments();

	/**
	 * Calculate all bug tracked into comments
	 *
	 * @return Issue[]
	 */
	public function getIssues();

	/**
	 * Get related object if this row has one
	 *
	 * @return Object|null
	 */
	public function getRelatedObject();

	/**
	 * @param int $date
	 */
	public function setDate($date);

	/**
	 * Set array with project id as key which contains an array of revisions
	 *
	 * @param array $revisions
	 */
	public function setRevisions($revisions);

	/**
	 * Set array with project id as key which contains an array of changelog text
	 *
	 * @param array $changelog
	 */
	public function setChangelog($changelog);

	/**
	 * Set array with project id as key which contains an array of comments
	 *
	 * @param array $comments
	 */
	public function setComments($comments);

	/**
	 * @param Object $related_object
	 */
	public function setRelatedObject(Object $related_object);
}