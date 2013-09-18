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
	public function getDependenciesList() {
		return array_merge(parent::getDependenciesList(), array(
																 'tracker',
															));
	}

	/**
	 * {@inheritDoc}
	 */
	public function initializeId($object_id) {
	}

	/**
	 * {@inheritDoc}
	 */
	public function isValid() {
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRevisions() {
		return $this->revisions;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getChangelog() {
		return $this->changelog;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getComments() {
		return $this->comments;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTrackers() {
		/** @var \Interfaces\Shared\Tracker $tracker_shared */
		$tracker_shared = $this->dependence_objects['tracker'];
		$trackers = array();
		foreach ($this->comments as $comments) {
			foreach ($comments as $comment) {
				$trackers = array_merge($trackers, $tracker_shared->getTrackersFromMessage($comment));
			}
		}
		$trackers = array_unique($trackers, SORT_REGULAR);
		usort($trackers, function(Tracker $a, Tracker $b) {
			if ($a->getType() == $b->getType()) {
				return 0;
			}
			return $a->getType() < $b->getType() ? 1 : -1;
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRelatedObject() {
		return $this->related_object;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setDate($date) {
		$this->date = abs((int)$date);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setRevisions($revisions) {
		$this->revisions = (array)$revisions;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setChangelog($changelog) {
		$this->changelog = (array)$changelog;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setComments($comments) {
		$this->comments = (array)$comments;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setRelatedObject(\Interfaces\Object $related_object) {
		if (!($related_object instanceof \Interfaces\Object)) {
			$related_object = null;
		}
		$this->related_object = $related_object;
	}
}