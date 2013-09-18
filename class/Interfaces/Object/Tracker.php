<?php

namespace Interfaces\Object;

use Interfaces\Object;

/**
 * Class represents a Bug Tracker instance
 */
interface Tracker extends Object {
	/**
	 * Get Tracker object id
	 *
	 * @return int|string
	 */
	public function getId();

	/**
	 * Get Tracker title
	 *
	 * @return string
	 */
	public function getTitle();

	/**
	 * Get Tracker type (Bug, technical task, feature, etc...)
	 *
	 * @return string
	 */
	public function getType();

	/**
	 * Get tracker web url
	 *
	 * @return string
	 */
	public function getUrl();
}