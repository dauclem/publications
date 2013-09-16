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
	 * @param int     $date
	 * @param string  $comments
	 * @param \Interfaces\Object\Project $project
	 * @return \Interfaces\Object\Publication
	 */
	public function create(\Interfaces\Object\Project $project, $date, $comments);

	/**
	 * Get All publications from a specific project
	 *
	 * @param \Interfaces\Object\Project $project
	 * @return \Interfaces\Object\Publication[]
	 */
	public function get_publications(\Interfaces\Object\Project $project);

	/**
	 * Get a publication object instance from its id
	 *
	 * @param \Interfaces\Object\Project $project
	 * @param int     $id_publication
	 * @return \Interfaces\Object\Publication
	 */
	public function get_publication_from_id(\Interfaces\Object\Project $project, $id_publication);

	/**
	 * Get a publication object instance from its date
	 *
	 * @param \Interfaces\Object\Project $project
	 * @param int     $date Timestamp
	 * @return \Interfaces\Object\Publication
	 */
	public function get_publication_from_date(\Interfaces\Object\Project $project, $date);

	/**
	 * Get first publication object for this project (oldest by date)
	 *
	 * @param \Interfaces\Object\Project $project
	 * @return \Interfaces\Object\Publication
	 */
	public function get_first_publication(\Interfaces\Object\Project $project);

	/**
	 * Get last publication object for this project (newest by date)
	 *
	 * @param \Interfaces\Object\Project $project
	 * @return \Interfaces\Object\Publication
	 */
	public function get_last_publication(\Interfaces\Object\Project $project);
}
