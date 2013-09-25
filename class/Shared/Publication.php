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
							id_temp INTEGER,
							date INTEGER,
							comments TEXT)');
		$connection->exec('CREATE UNIQUE INDEX IF NOT EXISTS project_date ON publication (project_id, is_temp, date)');
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(\Interfaces\Object\Project $project, $is_temp, $date, $comments) {
		$is_temp  = $is_temp ? 1 : 0;
		$date     = (int)$date;
		$comments = trim($comments);
		if (!$project
			|| (!$is_temp && $date <= 0)
			|| ($is_temp && $this->getPublicationTemp($project))
			|| $this->getPublicationFromDate($project, $date)
		) {
			return null;
		}

		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->getConnection();
		$connection->exec('INSERT INTO publication(project_id, is_temp, date, comments)
							VALUES ('.$project->getId().', '.$is_temp.', '.($is_temp ? 0 : $date).', \''.$connection->escapeString($comments).'\')');

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
														ORDER BY is_temp ASC, date ASC');
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
	public function getPublicationTemp(\Interfaces\Object\Project $project) {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$id       = $database->getConnection()->querySingle('SELECT id
															FROM publication
															WHERE project_id = '.$project->getId().'
																AND is_temp = 1');
		return $this->dic->getObject('publication_object', $id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPublicationFromId(\Interfaces\Object\Project $project, $id_publication) {
		/** @var \Interfaces\Object\Publication $object */
		$object = $this->dic->getObject('publication_object', $id_publication);
		return $object->getProject() == $project ? $object : null;
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
												ORDER BY is_temp ASC, date ASC
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
												ORDER BY is_temp DESC, date DESC
												LIMIT 1');
		return $this->dic->getObject('publication_object', $id);
	}
}
