<?php

namespace Shared;

use Shared;

class Project extends Shared implements \Interfaces\Shared\Project {
	/** @var \Interfaces\Object\Project */
	protected $current_project;

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

		$connection->exec('CREATE TABLE IF NOT EXISTS project(
							id INTEGER PRIMARY KEY AUTOINCREMENT,
							name TEXT,
							description TEXT,
							vcs_base TEXT,
							vcs_path TEXT,
							visible INTEGER(1),
							has_prod INTEGER(1))');
		$connection->exec('CREATE UNIQUE INDEX IF NOT EXISTS project_name ON project (name)');
		$connection->exec('CREATE UNIQUE INDEX IF NOT EXISTS project_vcs_path ON project (vcs_base, vcs_path)');

		$connection->exec('CREATE TABLE IF NOT EXISTS project_externals(
							id_project INTEGER,
							id_external INTEGER)');
		$connection->exec('CREATE UNIQUE INDEX IF NOT EXISTS project_name ON project_externals (id_project, id_external)');

		$connection->exec('CREATE TABLE IF NOT EXISTS project_recipients(
							id_project INTEGER,
							email TEXT)');
		$connection->exec('CREATE UNIQUE INDEX IF NOT EXISTS project_recipient ON project_recipients (id_project, email)');
	}

	/**
	 * {@inheritDoc}
	 */
	public function create($name, $vcs_base, $vcs_path, $visible, $has_prod) {
		$name = trim($name);
		if (!$name || $this->get_from_name($name) || $this->get_from_vcs_path($vcs_base, $vcs_path)) {
			return null;
		}

		$vcs_base = preg_replace('#/$#', '', trim($vcs_base));
		$vcs_path = preg_replace('#/$#', '', trim($vcs_path));
		$visible  = $visible ? 1 : 0;
		$has_prod = $has_prod ? 1 : 0;

		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->get_connection();
		$connection->exec('INSERT INTO project(name, vcs_base, vcs_path, visible, has_prod)
							VALUES (\''.$connection->escapeString($name).'\',
									\''.$connection->escapeString($vcs_base).'\',
									\''.$connection->escapeString($vcs_path).'\',
									'.$visible.',
									'.$has_prod.')');

		return $this->dic->get_object('project_object', $connection->lastInsertRowid());
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_projects() {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$result   = $database->get_connection()->query('SELECT id FROM project ORDER BY name');
		$objects  = array();
		while (list($id) = $result->fetchArray()) {
			/** @var \Interfaces\Object\Project $object */
			$object = $this->dic->get_object('project_object', $id);
			if ($object) {
				$objects[] = $object;
			}
		}
		return $objects;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_from_name($name) {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->get_connection();
		$id         = $connection->querySingle('SELECT id FROM project WHERE name = \''.$connection->escapeString($name).'\'');
		return $this->dic->get_object('project_object', $id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_from_vcs_path($vcs_base, $vcs_path) {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->get_connection();
		$id         = $connection->querySingle('SELECT id
												FROM project
												WHERE vcs_base = \''.$connection->escapeString($vcs_base).'\'
													AND vcs_path = \''.$connection->escapeString($vcs_path).'\'');
		return $this->dic->get_object('project_object', $id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_current_project() {
		if (!$this->current_project) {
			$name = isset($_GET['project_name']) ? trim($_GET['project_name']) : '';
			if (!$name) {
				$this->current_project = null;
			} else {
				/** @var \Interfaces\Shared\Database $database */
				$database              = $this->dependence_objects['database'];
				$connection            = $database->get_connection();
				$id                    = $connection->querySingle('SELECT id FROM project WHERE name = \''.$connection->escapeString($name).'\'');
				$this->current_project = $this->dic->get_object('project_object', $id);
			}
		}
		return $this->current_project;
	}
}
