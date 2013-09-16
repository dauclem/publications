<?php

namespace Interfaces\Shared;

use Interfaces\Shared;

/**
 * Class to manage projects
 */
interface Project extends Shared {
	/**
	 * Install (and reset) db for projects
	 */
	public function install();

	/**
	 * Create new project
	 *
	 * @param string $name     Unique name
	 * @param string $vcs_base Base of repository path (to detect merge from other branches of same repository)
	 * @param string $vcs_path Path of branch after $vcs_base
	 * @param bool   $visible  If true, allow to see this project
	 * @param bool   $has_prod If true, allow to create Publication action related to this project
	 * @return \Interfaces\Object\Project
	 */
	public function create($name, $vcs_base, $vcs_path, $visible, $has_prod);

	/**
	 * Get All projects
	 *
	 * @return \Interfaces\Object\Project[]
	 */
	public function get_projects();

	/**
	 * Get a Project object from its name
	 *
	 * @param string $name
	 * @return \Interfaces\Object\Project
	 */
	public function get_from_name($name);

	/**
	 * Get a Project object from its vcs path
	 *
	 * @param string $vcs_base
	 * @param string $vcs_path
	 * @return \Interfaces\Object\Project
	 */
	public function get_from_vcs_path($vcs_base, $vcs_path);

	/**
	 * Get current project from url
	 *
	 * @return \Interfaces\Object\Project
	 */
	public function get_current_project();
}
