<?php

namespace Interfaces\Object;

use Interfaces\Object;
use Interfaces\Object\Project;

/**
 * Class to manage a publication
 */
interface Publication extends Object {
	/**
	 * @return int
	 */
	public function get_id();

	/**
	 * @return Project
	 */
	public function get_project();

	/**
	 * Return timestamp of publication date
	 *
	 * @return int
	 */
	public function get_date();

	/**
	 * @return string
	 */
	public function get_comments();

	/**
	 * @param int $date timestamp
	 */
	public function set_date($date);

	/**
	 * @param string $comments
	 */
	public function set_comments($comments);

	/**
	 * Get previous publication
	 *
	 * @return Publication
	 */
	public function get_previous();

	/**
	 * Get next publication (after this in date order)
	 *
	 * @return \Interfaces\Object\Publication
	 */
	public function get_next();

	/**
	 * Remove this publication object from database
	 */
	public function remove();

	/**
	 * Get publication edit page
	 *
	 * @return string
	 */
	public function get_url();

	/**
	 * Create Row object with Publication infos
	 *
	 * @param Row[] $rows
	 * @return Row
	 */
	public function create_row($rows);
}
