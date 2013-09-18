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
	public function isValid() {
		return (bool)$this->id;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * {@inheritDoc}
	 */
	public function __toString() {
		return get_called_class().'|'.$this->getId();
	}
}