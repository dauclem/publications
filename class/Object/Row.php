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
	/** @var \Interfaces\Object */
	protected $related_object = null;

	/**
	 * {@inheritDoc}
	 */
	public function get_dependencies_list() {
		return array_merge(parent::get_dependencies_list(), array(
																 'tracker',
															));
	}

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
		/** @var \Interfaces\Shared\Tracker $tracker_shared */
		$tracker_shared = $this->dependence_objects['tracker'];
		$trackers = array();
		foreach ($this->comments as $comments) {
			foreach ($comments as $comment) {
				$trackers = array_merge($trackers, $tracker_shared->get_trackers_from_message($comment));
			}
		}
		$trackers = array_unique($trackers, SORT_REGULAR);
		usort($trackers, function(Tracker $a, Tracker $b) {
			if ($a->get_type() == $b->get_type()) {
				return 0;
			}
			return $a->get_type() < $b->get_type() ? 1 : -1;
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_related_object() {
		return $this->related_object;
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
	public function set_related_object(\Interfaces\Object $related_object) {
		if (!($related_object instanceof \Interfaces\Object)) {
			$related_object = null;
		}
		$this->related_object = $related_object;
	}
}