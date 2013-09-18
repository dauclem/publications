<?php

namespace Object;

use Object;
use Interfaces\Object\Issue;

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
																 'issue',
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
	public function getIssues() {
		/** @var \Interfaces\Shared\Issue $issue_shared */
		$issue_shared = $this->dependence_objects['issue'];
		$issues = array();
		foreach ($this->comments as $comments) {
			foreach ($comments as $comment) {
				$issues = array_merge($issues, $issue_shared->getIssuesFromMessage($comment));
			}
		}
		$issues = array_unique($issues, SORT_REGULAR);
		usort($issues, function(Issue $a, Issue $b) {
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