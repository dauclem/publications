<?php

namespace Interfaces\Shared;

use Interfaces\Shared;

/**
 * Class to manage all Config
 */
interface Config extends Shared {
	/**
	 * Call this method for reset config install
	 */
	public function install();

	/**
	 * Get site base url
	 *
	 * @return string
	 */
	public function get_site_url();

	/**
	 * Get VCS ident to get correct class name
	 *
	 * @return string
	 */
	public function get_vcs_type();

	/**
	 * Get VCS repository base url
	 *
	 * @return string
	 */
	public function get_vcs_url();

	/**
	 * Get VCS user name
	 *
	 * @return string
	 */
	public function get_vcs_user();

	/**
	 * Get VCS user password
	 *
	 * @return string
	 */
	public function get_vcs_password();

	/**
	 * Get VCS base url for web diff displayed
	 *
	 * @return string
	 */
	public function get_vcs_web_url();

	/**
	 * Get changelog file path from project root (for example : core/changelog)
	 *
	 * @return string
	 */
	public function get_changelog_path();

	/**
	 * Get bug tracker ident to get correct class name
	 *
	 * @return string
	 */
	public function get_bug_tracker_type();

	/**
	 * Get bug tracker base url
	 *
	 * @return string
	 */
	public function get_bug_tracker_url();

	/**
	 * Get bug tracker user name
	 *
	 * @return string
	 */
	public function get_bug_tracker_user();

	/**
	 * Get bug tracker user password
	 *
	 * @return string
	 */
	public function get_bug_tracker_password();

	/**
	 * Get bug tracker query begin to get valid tracker object for publications
	 *
	 * @return string
	 */
	public function get_bug_tracker_query();

	/**
	 * Set VCS ident to set correct class name
	 *
	 * @param string $vcs_type
	 */
	public function set_vcs_type($vcs_type);

	/**
	 * Set VCS repository base url
	 *
	 * @param string $vcs_url
	 */
	public function set_vcs_url($vcs_url);

	/**
	 * Set VCS user name
	 *
	 * @param string $vcs_user
	 */
	public function set_vcs_user($vcs_user);

	/**
	 * Set VCS user password
	 *
	 * @param string $vcs_password
	 */
	public function set_vcs_password($vcs_password);

	/**
	 * Set VCS base url for web diff displayed
	 *
	 * @param string $vcs_web_url
	 */
	public function set_vcs_web_url($vcs_web_url);

	/**
	 * Set changelog file path
	 *
	 * @param string $changelog_path
	 */
	public function set_changelog_path($changelog_path);

	/**
	 * Set bug tracker ident to set correct class name
	 *
	 * @param string $bug_tracker_type
	 */
	public function set_bug_tracker_type($bug_tracker_type);

	/**
	 * Set bug tracker base url
	 *
	 * @param string $bug_tracker_url
	 */
	public function set_bug_tracker_url($bug_tracker_url);

	/**
	 * Set bug tracker user name
	 *
	 * @param string $bug_tracker_user
	 */
	public function set_bug_tracker_user($bug_tracker_user);

	/**
	 * Set bug tracker user password
	 *
	 * @param string $bug_tracker_password
	 */
	public function set_bug_tracker_password($bug_tracker_password);

	/**
	 * Set bug tracker query begin to get valid tracker object for publications
	 *
	 * @param string $bug_tracker_query
	 */
	public function set_bug_tracker_query($bug_tracker_query);

	/**
	 * Add a recipient for this all projects
	 *
	 * @param string $email must be only email address
	 */
	public function add_recipient($email);

	/**
	 * Remove a recipient for all projects
	 *
	 * @param string $email must be only email address
	 */
	public function remove_recipient($email);

	/**
	 * Get list of Email recipients for publication notification for all projects
	 *
	 * @return string[]
	 */
	public function get_recipients();
}
