<?php

namespace Object;

use Object;

/**
 * Class represents a Bug Tracker instance
 */
abstract class Tracker extends Object implements \Interfaces\Object\Tracker {
	protected $id;
	protected $title;
	protected $type;

	/**
	 * {@inheritDoc}
	 */
	public function is_valid() {
		return (bool)$this->id;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_type() {
		return $this->type;
	}
}