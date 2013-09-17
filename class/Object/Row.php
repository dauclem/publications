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
		$trackers = array();
		foreach ($this->comments as $project_id => $comments) {
			foreach ($comments as $comment) {
				$trackers = array_merge($trackers, $tracker_shared->get_trackers_from_message($comment));
			}
		}
		$trackers = array_unique($trackers, SORT_REGULAR);
		usort($trackers, function(\Interfaces\Object\Tracker $a, \Interfaces\Object\Tracker $b) {
			if ($a->get_type() == $b->get_type()) {
				return 0;
			}
			return $a->get_type() < $b->get_type() ? 1 : -1;
		});
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