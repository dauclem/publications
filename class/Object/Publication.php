<?php

namespace Object;

use Object;

class Publication extends Object implements \Interfaces\Object\Publication {
	/** @var int */
	protected $id;
	/** @var int */
	protected $project_id;
	/** @var string */
	protected $date;
	/** @var string */
	protected $comments;

	/**
	 * {@inheritDoc}
	 */
	public function get_dependencies_list() {
		return array_merge(parent::get_dependencies_list(), array(
																 'database',
																 'publication',
																 'project_object',
																 'config',
															));
	}

	/**
	 * {@inheritDoc}
	 */
	public function initialize_id($object_id) {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$data     = $database->get_connection()->querySingle('SELECT id, project_id, date, comments
															FROM publication
															WHERE id = '.(int)$object_id, true);
		@list($this->id, $this->project_id, $this->date, $this->comments) = array_values($data);
		$this->id         = (int)$this->id;
		$this->project_id = (int)$this->project_id;
		$this->date       = (int)$this->date;
	}

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
	public function get_project() {
		static $project = false;
		if ($project === false) {
			$project = $this->dic->get_object('project_object', $this->project_id);
		}
		return $project;
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
	public function get_comments() {
		return $this->comments;
	}

	/**
	 * Save changes into db
	 *
	 * @param string $property property name
	 */
	protected function save($property) {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		if ($property) {
			$connection = $database->get_connection();
			$connection->exec('UPDATE publication
								SET '.$property.' = \''.$connection->escapeString($this->$property).'\'
								WHERE id = '.$this->id);
			$this->initialize_id($this->id);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_date($date) {
		$date = (int)$date;
		/** @var \Interfaces\Shared\Publication $publication_shared */
		$publication_shared = $this->dependence_objects['publication'];
		if ($date <= 0 || $publication_shared->get_publication_from_date($this->get_project(), $date)) {
			return null;
		}

		$this->date = $date;
		$this->save('date');
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_comments($comments) {
		$this->comments = $comments;
		$this->save('comments');
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_previous() {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->get_connection();
		$id         = $connection->querySingle('SELECT id
												FROM publication
												WHERE project_id = '.$this->project_id.'
													AND date < '.$this->date.'
												ORDER BY date DESC
												LIMIT 1');
		return $this->dic->get_object('publication_object', $id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_next() {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->get_connection();
		$id         = $connection->querySingle('SELECT id
												FROM publication
												WHERE project_id = '.$this->project_id.'
													AND date > '.$this->date.'
												ORDER BY date ASC
												LIMIT 1');
		return $this->dic->get_object('publication_object', $id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function remove() {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$database->get_connection()->exec('DELETE FROM publication WHERE id = '.$this->id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_url() {
		return $this->get_project()->get_url().'publication/'.$this->id.'/';
	}

	/**
	 * {@inheritDoc}
	 */
	public function create_row($rows) {
		/** @var Row $row */
		$row = $this->dic->get_object('row_object');
		$row->set_publication($this);
		$row->set_date($this->date);

		$revisions = $changelog = $messages = array();
		/** @var Publication $previous_publication */
		$previous_publication = $this->get_previous();
		if ($previous_publication) {
			$previous_date = $previous_publication->get_date();
			foreach ($rows as $this_row) {
				if ($this_row->get_date() > $previous_date && $this_row->get_date() < $this->date) {
					foreach ($this_row->get_revisions() as $project_id => $revision) {
						$revisions[$project_id] = $revision.(isset($revisions[$project_id]) ? ','.$revisions[$project_id] : '');
					}

					$changelog = array_merge($changelog, $this_row->get_changelog());

					foreach ($this_row->get_comments() as $project_id => $message) {
						$messages[$project_id] = array_merge($message, isset($messages[$project_id]) ? $messages[$project_id] : array());
					}
				}
			}
		} else {
			$this->comments = 'PREMIERE PUBLICATION'."\n\n".$this->comments;
		}

		/** @var \Interfaces\Shared\Config $config_shared */
		$config_shared = $this->dependence_objects['config'];
		/** @var \Interfaces\Shared\VCS $vcs */
		$vcs = $this->dic->get_object('vcs');
		array_map(array($vcs, 'optimize_revisions'), $revisions);

		$row->set_revisions($revisions);
		$row->set_changelog($changelog);
		$row->set_comments($messages);

		return $row;
	}
}
