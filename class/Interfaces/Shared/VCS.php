<?php

namespace Interfaces\Shared;

use Interfaces\Shared;
use Interfaces\Object\Row;

/**
 * Class to manage Version Control System communications
 */
interface VCS extends Shared {
	/**
	 * Get preg part to match a revision in a string
	 *
	 * @return string
	 */
	public function getPregRevision();

	/**
	 * Get list of logs to display for a project
	 *
	 * @param \Interfaces\Object\Project          $project
	 * @param int[]            $revision_begins   Associative array with array(project_id => revision_begin, ...). Get all revision previous of this. No specified for all
	 * @param \Interfaces\Object\Publication|null $publication_limit Last publication to display. Other must be displayed with "see_more". null for no limit
	 *
	 * @return Row[]
	 */
	public function getAllRows(\Interfaces\Object\Project $project, $revision_begins, $publication_limit);

	/**
	 * Get revisions format like merge-info bug not real good merged.
	 * This method merge correctly all revisions as optimized merge-info syntax
	 *
	 * @param string $revisions
	 * @return string
	 */
	public function optimizeRevisions($revisions);

	/**
	 * Get web url to show revision and diff between previous revision
	 *
	 * @param \Interfaces\Object\Project    $project
	 * @param string|int $revision
	 * @return string
	 */
	public function getRevisionUrl(\Interfaces\Object\Project $project, $revision);
}