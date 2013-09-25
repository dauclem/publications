<?php

namespace Interfaces\Shared;

use Interfaces\Shared;

/**
 * Class to manage publications
 */
interface Publication extends Shared {
	/**
	 * Install (and reset) db for publications
	 */
	public function install();

	/**
	 * Create new publication object in database and return it
	 *
	 * @param \Interfaces\Object\Project $project
	 * @param bool    $is_temp
	 * @param int     $date
	 * @param string  $comments
	 * @param \Interfaces\Object\Project $project
	 * @return \Interfaces\Object\Publication
	 */
	public function create(\Interfaces\Object\Project $project, $is_temp, $date, $comments);

	/**
	 * Get All publications from a specific project
	 *
	 * @param \Interfaces\Object\Project $project
	 * @return \Interfaces\Object\Publication[]
	 */
	public function getPublications(\Interfaces\Object\Project $project);

	/**
	 * Get unique temporary publication for this project
	 *
	 * @param \Interfaces\Object\Project $project
	 * @return \Interfaces\Object\Publication
	 */
	public function getPublicationTemp(\Interfaces\Object\Project $project);

	/**
	 * Get a publication object instance from its id
	 *
	 * @param \Interfaces\Object\Project $project
	 * @param int     $id_publication
	 * @return \Interfaces\Object\Publication
	 */
	public function getPublicationFromId(\Interfaces\Object\Project $project, $id_publication);

	/**
	 * Get a publication object instance from its date
	 *
	 * @param \Interfaces\Object\Project $project
	 * @param int     $date Timestamp
	 * @return \Interfaces\Object\Publication
	 */
	public function getPublicationFromDate(\Interfaces\Object\Project $project, $date);

	/**
	 * Get first publication object for this project (oldest by date)
	 *
	 * @param \Interfaces\Object\Project $project
	 * @return \Interfaces\Object\Publication
	 */
	public function getFirstPublication(\Interfaces\Object\Project $project);

	/**
	 * Get last publication object for this project (newest by date)
	 *
	 * @param \Interfaces\Object\Project $project
	 * @param bool $no_temp if true, do not get temporary publication
	 * @return \Interfaces\Object\Publication
	 */
	public function getLastPublication(\Interfaces\Object\Project $project, $no_temp = false);
}
