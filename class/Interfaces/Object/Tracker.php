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
	public function get_id();

	/**
	 * Get Tracker title
	 *
	 * @return string
	 */
	public function get_title();

	/**
	 * Get Tracker type (Bug, technical task, feature, etc...)
	 *
	 * @return string
	 */
	public function get_type();

	/**
	 * Get tracker web url
	 *
	 * @return string
	 */
	public function get_url();
}