<?php

namespace Interfaces\Object;

use Interfaces\Object;

/**
 * Class to manage a project
 */
interface Project extends Object {
	/**
	 * Get project object unique id
	 *
	 * @return int
	 */
	public function get_id();

	/**
	 * Get project name
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * Get project description
	 *
	 * @return string
	 */
	public function get_description();

	/**
	 * Get base of repository path (to detect merge from other branches of same repository)
	 *
	 * @return string
	 */
	public function get_vcs_base();

	/**
	 * Get path of branch after $vcs_base
	 *
	 * @return string
	 */
	public function get_vcs_path();

	/**
	 * Get full VCS repository url
	 *
	 * @return string
	 */
	public function get_vcs_repository();

	/**
	 * Get Tracker id to filter into query
	 *
	 * @return string
	 */
	public function get_tracker_id();

	/**
	 * Return true if this project is displayable as complete project
	 *
	 * @return bool
	 */
	public function is_visible();

	/**
	 * Return true if this project can have publications
	 *
	 * @return bool
	 */
	public function has_prod();

	/**
	 * Set project name
	 *
	 * @param string $name
	 */
	public function set_name($name);

	/**
	 * Set project description
	 *
	 * @param string $description
	 */
	public function set_description($description);

	/**
	 * Set base of repository path (to detect merge from other branches of same repository)
	 *
	 * @param string $vcs_base
	 */
	public function set_vcs_base($vcs_base);

	/**
	 * Set path of branch after $vcs_base
	 *
	 * @param string $vcs_path
	 */
	public function set_vcs_path($vcs_path);

	/**
	 * Set Tracker id to filter into query
	 *
	 * @param string $tracker_id
	 */
	public function set_tracker_id($tracker_id);

	/**
	 * Set if project is accessible
	 *
	 * @param bool $visible
	 */
	public function set_visible($visible);

	/**
	 * Set if this project can create Publication objects
	 *
	 * @param bool $has_prod
	 */
	public function set_has_prod($has_prod);

	/**
	 * Add project defined as external for this project
	 *
	 * @param Project $project
	 */
	public function add_external(Project $project);

	/**
	 * Remove project defined as external for this project
	 *
	 * @param Project $project
	 */
	public function remove_external(Project $project);

	/**
	 * Get all external projects
	 *
	 * @return Project[]
	 */
	public function get_externals();

	/**
	 * Add a recipient for this project only
	 *
	 * @param string $email Email an be formated like : "John Doe <john.doe@email.com>"
	 */
	public function add_recipient($email);

	/**
	 * Remove a recipient for this project only
	 *
	 * @param string $email email must be only email address or exactly same parameter done into add_recipient
	 */
	public function remove_recipient($email);

	/**
	 * Get list of Email recipients for publication notification for this project
	 *
	 * @return string[]
	 */
	public function get_recipients();

	/**
	 * Get project url
	 *
	 * @return string
	 */
	public function get_url();

	/**
	 * Get project url without pagination
	 *
	 * @return string
	 */
	public function get_url_see_all();

	/**
	 * Get project url for one page without header
	 *
	 * @return string
	 */
	public function get_url_see_more();

	/**
	 * Get project url to add publication
	 *
	 * @return string
	 */
	public function get_url_add_publication();
}
