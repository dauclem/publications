<?php

namespace Interfaces\Object;

use Interfaces\Object;

/**
 * Class represents a Bug Issue instance
 */
interface Issue extends Object {
	/**
	 * Get Issue object id
	 *
	 * @return int|string
	 */
	public function getId();

	/**
	 * Get Issue title
	 *
	 * @return string
	 */
	public function getTitle();

	/**
	 * Get Issue type (Bug, technical task, feature, etc...)
	 *
	 * @return string
	 */
	public function getType();

	/**
	 * Get value of field used for restrict notification
	 *
	 * @return string
	 */
	public function getRestrictNotifValue();

		/**
	 * Get Issue web url
	 *
	 * @return string
	 */
	public function getUrl();
}