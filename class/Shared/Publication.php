<?php

namespace Shared;

use Shared;

class Publication extends Shared implements \Interfaces\Shared\Publication {
	/**
	 * {@inheritDoc}
	 */
	public function getDependenciesList() {
		return array_merge(parent::getDependenciesList(), array(
																 'database',
															));
	}

	/**
	 * {@inheritDoc}
	 */
	public function install() {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->getConnection();

		$connection->exec('CREATE TABLE IF NOT EXISTS publication(
							id INTEGER PRIMARY KEY AUTOINCREMENT,
							project_id INTEGER,
							date INTEGER,
							comments TEXT)');
		$connection->exec('CREATE UNIQUE INDEX IF NOT EXISTS project_date ON publication (project_id, date)');
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(\Interfaces\Object\Project $project, $date, $comments) {
		$date     = (int)$date;
		$comments = trim($comments);
		if (!$project || $date <= 0 || $this->getPublicationFromDate($project, $date)) {
			return null;
		}

		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->getConnection();
		$connection->exec('INSERT INTO publication(project_id, date, comments)
							VALUES ('.$project->getId().', '.$date.', \''.$connection->escapeString($comments).'\')');

		return $this->dic->getObject('publication_object', $connection->lastInsertRowid());
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPublications(\Interfaces\Object\Project $project) {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$result   = $database->getConnection()->query('SELECT id
														FROM publication
														WHERE project_id = '.$project->getId().'
														ORDER BY date ASC');
		$objects  = array();
		while (list($id) = $result->fetchArray()) {
			/** @var \Interfaces\Object\Publication $object */
			$object = $this->dic->getObject('publication_object', $id);
			if ($object) {
				$objects[] = $object;
			}
		}
		return $objects;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPublicationFromId(\Interfaces\Object\Project $project, $id_publication) {
		/** @var \Interfaces\Object\Publication $object */
		$object = $this->dic->getObject('publication_object', $id_publication);
		return $object->getProject() == $project ? $object: null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPublicationFromDate(\Interfaces\Object\Project $project, $date) {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->getConnection();
		$id         = $connection->querySingle('SELECT id
												FROM publication
												WHERE project_id = '.$project->getId().'
													AND date = '.(int)$date);
		return $this->dic->getObject('publication_object', $id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFirstPublication(\Interfaces\Object\Project $project) {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->getConnection();
		$id         = $connection->querySingle('SELECT id
												FROM publication
												WHERE project_id = '.$project->getId().'
												ORDER BY date ASC
												LIMIT 1');
		return $this->dic->getObject('publication_object', $id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLastPublication(\Interfaces\Object\Project $project) {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->getConnection();
		$id         = $connection->querySingle('SELECT id
												FROM publication
												WHERE project_id = '.$project->getId().'
												ORDER BY date DESC
												LIMIT 1');
		return $this->dic->getObject('publication_object', $id);
	}
}
