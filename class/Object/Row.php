<?php

namespace Object;

use Object;
use Interfaces\Object\Tracker;

class Row extends Object implements \Interfaces\Object\Row {
	/** @var int timestamp */
	protected $date = 0;
	/** @var array */
	protected $revisions = array();
	/** @var array */
	protected $changelog = array();
	/** @var array */
	protected $comments = array();
	/** @var \Interfaces\Object\Publication */
	protected $publication = null;

	/**
	 * {@inheritDoc}
	 */
	public function initialize_id($object_id) {
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_valid() {
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_date() {
		return $this->date;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_revisions() {
		return $this->revisions;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_changelog() {
		return $this->changelog;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_comments() {
		return $this->comments;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_trackers() {
		// TODO : implements
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_publication() {
		return $this->publication;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_date($date) {
		$this->date = abs((int)$date);
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_revisions($revisions) {
		$this->revisions = (array)$revisions;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_changelog($changelog) {
		$this->changelog = (array)$changelog;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_comments($comments) {
		$this->comments = (array)$comments;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_publication(\Interfaces\Object\Publication $publication) {
		if (!($publication instanceof \Interfaces\Object\Publication)) {
			$publication = null;
		}
		$this->publication = $publication;
	}
}