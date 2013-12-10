<?php

namespace Object;

use Object;

class Publication extends Object implements \Interfaces\Object\Publication {
	/** @var int */
	protected $id;
	/** @var int */
	protected $project_id;
	/** @var bool */
	protected $is_temp;
	/** @var string */
	protected $date;
	/** @var string */
	protected $comments;

	/**
	 * {@inheritDoc}
	 */
	public function getDependenciesList() {
		return array_merge(parent::getDependenciesList(), array(
															   'database',
															   'publication',
															   'project_object',
															   'config',
														  ));
	}

	/**
	 * {@inheritDoc}
	 */
	public function initializeId($object_id) {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$data     = $database->getConnection()->querySingle('SELECT id, project_id, is_temp, date, comments
															FROM publication
															WHERE id = '.(int)$object_id, true);
		@list($this->id, $this->project_id, $this->is_temp, $this->date, $this->comments) = array_values($data);
		$this->id         = (int)$this->id;
		$this->project_id = (int)$this->project_id;
		$this->is_temp    = (bool)$this->is_temp;
		$this->date       = (int)$this->date;
	}

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
	public function getProject() {
		static $project = false;
		if ($project === false) {
			$project = $this->dic->getObject('project_object', $this->project_id);
		}
		return $project;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isTemp() {
		return $this->is_temp;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDate() {
		return $this->is_temp ? time() : $this->date;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getComments() {
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
			$connection = $database->getConnection();
			$connection->exec('UPDATE publication
								SET '.$property.' = \''.$connection->escapeString($this->$property).'\'
								WHERE id = '.$this->id);
			$this->initializeId($this->id);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function setTemp($is_temp) {
		$is_temp = (bool)$is_temp;
		/** @var \Interfaces\Shared\Publication $publication_shared */
		$publication_shared = $this->dependence_objects['publication'];
		if ($this->isTemp() == $is_temp
			|| ($is_temp && $publication_shared->getPublicationTemp($this->getProject()))
		) {
			return null;
		}

		if (!$is_temp && !$this->date) {
			$this->date = time();
			$this->save('date');
		}

		$this->is_temp = (int)$is_temp;
		$this->save('is_temp');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setDate($date) {
		$date = (int)$date;
		/** @var \Interfaces\Shared\Publication $publication_shared */
		$publication_shared = $this->dependence_objects['publication'];
		if ($date <= 0 || $publication_shared->getPublicationFromDate($this->getProject(), $date)) {
			return null;
		}

		$this->date = $date;
		$this->save('date');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setComments($comments) {
		$this->comments = $comments;
		$this->save('comments');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPrevious() {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->getConnection();
		$id         = $connection->querySingle('SELECT id
												FROM publication
												WHERE project_id = '.$this->project_id.'
													AND is_temp = 0
													'.($this->is_temp ? '' : 'AND date < '.$this->date).'
												ORDER BY date DESC
												LIMIT 1');
		return $this->dic->getObject('publication_object', $id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getNext() {
		if ($this->is_temp) {
			return null;
		}

		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->getConnection();
		$id         = $connection->querySingle('SELECT id
												FROM publication
												WHERE project_id = '.$this->project_id.'
													AND (date > '.$this->date.'
														OR is_temp = 1)
												ORDER BY is_temp ASC, date ASC
												LIMIT 1');
		return $this->dic->getObject('publication_object', $id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function remove() {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$database->getConnection()->exec('DELETE FROM publication WHERE id = '.$this->id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUrl() {
		return $this->getProject()->getUrl().'publication/'.$this->id.'/';
	}

	/**
	 * {@inheritDoc}
	 */
	public function createRow($rows) {
		/** @var Row $row */
		$row = $this->dic->getObject('row_object');
		$row->setRelatedObject($this);
		$row->setDate($this->getDate());

		$revisions = $changelog = $messages = array();
		/** @var Publication $previous_publication */
		$previous_publication = $this->getPrevious();
		if ($previous_publication) {
			$previous_date = $previous_publication->getDate();
			foreach ($rows as $this_row) {
				if ($this_row->getDate() > $previous_date && $this_row->getDate() < $this->getDate()) {
					foreach ($this_row->getRevisions() as $project_id => $revision) {
						$revisions[$project_id] = $revision.(isset($revisions[$project_id]) ? ','.$revisions[$project_id] : '');
					}

					$changelog = array_merge($changelog, $this_row->getChangelog());

					foreach ($this_row->getComments() as $project_id => $message) {
						$messages[$project_id] = array_merge($message, isset($messages[$project_id]) ? $messages[$project_id] : array());
					}
				}
			}
		} else {
			$this->comments = 'PREMIERE PUBLICATION'."\n\n".$this->comments;
		}

		/** @var \Interfaces\Shared\VCS $vcs */
		$vcs = $this->dic->getObject('vcs');
		array_map(array($vcs, 'optimizeRevisions'), $revisions);

		$row->setRevisions($revisions);
		$row->setChangelog($changelog);
		$row->setComments($messages);

		return $row;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_email_infos($issues) {
		/** @var \Interfaces\Shared\Config $config_shared */
		$config_shared = $this->dependence_objects['config'];
		$project       = $this->getProject();

		$recipients = implode(';', $project->getRecipients());
		$cc         = implode(';', $config_shared->getRecipients());
		$subject    = 'Publication de '.$project->getName();

		$nl           = urlencode("\n");
		$current_type = $issues_str = '';
		foreach ($issues as $issue) {
			if ($current_type != $issue->getType()) {
				if ($current_type) {
					$issues_str .= $nl;
				}
				$current_type = $issue->getType();
				$issues_str .= $current_type.' :'.$nl;
			}
			$issues_str .= $issue->getId().' : '.$issue->getTitle().$nl;
		}

		$replace = array(
			"\n"        => $nl,
			'{PROJECT}' => $project->getName(),
			'{ISSUES}'  => $issues_str,
		);
		$body    = str_replace(array_keys($replace), array_values($replace), $project->getDisplayMailContent());

		return array(
			'recipients' => $recipients,
			'cc'         => $cc,
			'subject'    => $subject,
			'body'       => $body,
		);
	}
}
