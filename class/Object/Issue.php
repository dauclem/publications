<?php

namespace Object;

use Object;

/**
 * Class represents a Bug Tracker instance
 */
abstract class Issue extends Object implements \Interfaces\Object\Issue {
	protected $id;
	protected $title;
	protected $type;
	protected $retrict_notif_value;

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
	public function getRestrictNotifValue() {
		return $this->retrict_notif_value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function __toString() {
		return get_called_class().'|'.$this->getId();
	}
}