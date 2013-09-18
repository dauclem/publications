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
	public function getId();

	/**
	 * @return Project
	 */
	public function getProject();

	/**
	 * Return timestamp of publication date
	 *
	 * @return int
	 */
	public function getDate();

	/**
	 * @return string
	 */
	public function getComments();

	/**
	 * @param int $date timestamp
	 */
	public function setDate($date);

	/**
	 * @param string $comments
	 */
	public function setComments($comments);

	/**
	 * Get previous publication
	 *
	 * @return Publication
	 */
	public function getPrevious();

	/**
	 * Get next publication (after this in date order)
	 *
	 * @return \Interfaces\Object\Publication
	 */
	public function getNext();

	/**
	 * Remove this publication object from database
	 */
	public function remove();

	/**
	 * Get publication edit page
	 *
	 * @return string
	 */
	public function getUrl();

	/**
	 * Create Row object with Publication infos
	 *
	 * @param Row[] $rows
	 * @return Row
	 */
	public function createRow($rows);
}
