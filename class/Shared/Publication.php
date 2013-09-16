<?php

namespace Shared;

use Shared;

class Publication extends Shared implements \Interfaces\Shared\Publication {
	/**
	 * {@inheritDoc}
	 */
	public function get_dependencies_list() {
		return array_merge(parent::get_dependencies_list(), array(
																 'database',
															));
	}

	/**
	 * {@inheritDoc}
	 */
	public function install() {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->get_connection();

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
		if (!$project || $date <= 0 || $this->get_publication_from_date($project, $date)) {
			return null;
		}

		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->get_connection();
		$connection->exec('INSERT INTO publication(project_id, date, comments)
							VALUES ('.$project->get_id().', '.$date.', \''.$connection->escapeString($comments).'\')');

		return $this->dic->get_object('publication_object', $connection->lastInsertRowid());
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_publications(\Interfaces\Object\Project $project) {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$result   = $database->get_connection()->query('SELECT id
														FROM publication
														WHERE project_id = '.$project->get_id().'
														ORDER BY date ASC');
		$objects  = array();
		while (list($id) = $result->fetchArray()) {
			/** @var \Interfaces\Object\Publication $object */
			$object = $this->dic->get_object('publication_object', $id);
			if ($object) {
				$objects[] = $object;
			}
		}
		return $objects;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_publication_from_id(\Interfaces\Object\Project $project, $id_publication) {
		/** @var \Interfaces\Object\Publication $object */
		$object = $this->dic->get_object('publication_object', $id_publication);
		return $object->get_project() == $project ? $object: null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_publication_from_date(\Interfaces\Object\Project $project, $date) {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->get_connection();
		$id         = $connection->querySingle('SELECT id
												FROM publication
												WHERE project_id = '.$project->get_id().'
													AND date = '.(int)$date);
		return $this->dic->get_object('publication_object', $id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_first_publication(\Interfaces\Object\Project $project) {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->get_connection();
		$id         = $connection->querySingle('SELECT id
												FROM publication
												WHERE project_id = '.$project->get_id().'
												ORDER BY date ASC
												LIMIT 1');
		return $this->dic->get_object('publication_object', $id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_last_publication(\Interfaces\Object\Project $project) {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->get_connection();
		$id         = $connection->querySingle('SELECT id
												FROM publication
												WHERE project_id = '.$project->get_id().'
												ORDER BY date DESC
												LIMIT 1');
		return $this->dic->get_object('publication_object', $id);
	}
}
