<?php

namespace Interfaces\Shared;

use Interfaces\Shared;

/**
 * Class to manage Bug Tracker
 */
interface Issue extends Shared {
	/**
	 * Get Issue id pattern for preg to find Issue ids into a string
	 *
	 * @return string
	 */
	public function getIssueIdPattern();

	/**
	 * Find all Issues id into $message and return respective Issue objects
	 *
	 * @param string $message
	 * @return \Interfaces\Object\Issue[]
	 */
	public function getIssuesFromMessage($message);

	/**
	 * Filter Issues list with query set to config to filter only Issues to send by email
	 *
	 * @param \Interfaces\Object\Issue[] $issues
	 * @param \Interfaces\Object\Project   $project
	 * @return \Interfaces\Object\Issue[]
	 */
	public function filterIssues($issues, \Interfaces\Object\Project $project = null);
}