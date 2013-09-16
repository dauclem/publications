<?php

namespace Object;

use Object;

class Project extends Object implements \Interfaces\Object\Project {
	/** @var int */
	protected $id;
	/** @var string */
	protected $name;
	/** @var string */
	protected $description;
	/** @var string */
	protected $vcs_base;
	/** @var string */
	protected $vcs_path;
	/** @var bool */
	protected $visible;
	/** @var bool */
	protected $has_prod;

	/**
	 * {@inheritDoc}
	 */
	public function get_dependencies_list() {
		return array_merge(parent::get_dependencies_list(), array(
																 'database',
																 'project',
																 'config',
																 'form_utils',
															));
	}

	/**
	 * {@inheritDoc}
	 */
	public function initialize_id($object_id) {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$data     = $database->get_connection()->querySingle('SELECT id, name, description, vcs_base, vcs_path, visible, has_prod
															FROM project
															WHERE id = '.(int)$object_id, true);
		@list($this->id, $this->name, $this->description, $this->vcs_base, $this->vcs_path, $this->visible, $this->has_prod) = array_values($data);
		$this->id       = (int)$this->id;
		$this->visible  = (bool)$this->visible;
		$this->has_prod = (bool)$this->has_prod;
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
	public function get_name() {
		return $this->name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_vcs_base() {
		return $this->vcs_base;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_vcs_path() {
		return $this->vcs_path;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_vcs_repository() {
		/** @var \Interfaces\Shared\Config $config_shared */
		$config_shared = $this->dependence_objects['config'];
		return $config_shared->get_vcs_url().'/'.$this->vcs_base.$this->vcs_path;
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_visible() {
		return $this->visible;
	}

	/**
	 * {@inheritDoc}
	 */
	public function has_prod() {
		return $this->has_prod;
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
			$connection->exec('UPDATE project
								SET '.$property.' = \''.$connection->escapeString($this->$property).'\'
								WHERE id = '.$this->id);
			$this->initialize_id($this->id);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_name($name) {
		$name = trim($name);
		/** @var \Interfaces\Shared\Project $project_shared */
		$project_shared = $this->dependence_objects['project'];
		if (!$name || $project_shared->get_from_name($name)) {
			return null;
		}

		$this->name = $name;
		$this->save('name');
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_description($description) {
		$this->description = $description;
		$this->save('description');
	}


	/**
	 * {@inheritDoc}
	 */
	public function set_vcs_base($vcs_base) {
		$this->vcs_base = $vcs_base;
		$this->save('vcs_base');
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_vcs_path($vcs_path) {
		$this->vcs_path = $vcs_path;
		$this->save('vcs_path');
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_visible($visible) {
		$this->visible = $visible;
		$this->save('visible');
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_has_prod($has_prod) {
		$this->has_prod = $has_prod;
		$this->save('has_prod');
	}

	/**
	 * {@inheritDoc}
	 */
	public function add_external(\Interfaces\Object\Project $project) {
		if ($project == $this) {
			return;
		}

		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$database->get_connection()->exec('REPLACE INTO project_externals(id_project, id_external)
											VALUES ('.$this->id.', '.$project->get_id().')');
	}

	/**
	 * {@inheritDoc}
	 */
	public function remove_external(\Interfaces\Object\Project $project) {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$database->get_connection()->exec('DELETE FROM project_externals
											WHERE id_project = '.$this->id.'
												AND id_external = '.$project->get_id());
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_externals() {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$result   = $database->get_connection()->query('SELECT id_external
														FROM project_externals
														WHERE id_project = '.$this->id);
		$objects  = array();
		while (list($id) = $result->fetchArray()) {
			/** @var \Interfaces\Object\Project $database */
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
	public function add_recipient($email) {
		/** @var \Interfaces\Shared\FormUtils $form_utils */
		$form_utils = $this->dependence_objects['form_utils'];
		$email      = trim($email);
		if (!$email || !$form_utils->check_email($email)) {
			return;
		}

		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->get_connection();
		$connection->exec('REPLACE INTO project_recipients(id_project, email)
							VALUES ('.$this->id.', \''.$connection->escapeString($email).'\')');
	}

	/**
	 * {@inheritDoc}
	 */
	public function remove_recipient($email) {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->get_connection();
		$connection->exec('DELETE FROM project_recipients
							WHERE id_project = '.$this->id.'
								AND email = \''.$connection->escapeString($email).'\'');
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_recipients() {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$result   = $database->get_connection()->query('SELECT email
														FROM project_recipients
														WHERE id_project = '.$this->id);
		$emails   = array();
		while (list($email) = $result->fetchArray()) {
			$emails[] = $email;
		}
		return $emails;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_url() {
		/** @var \Interfaces\Shared\Config $config */
		$config = $this->dependence_objects['config'];
		return $config->get_site_url().'projet/'.urlencode($this->name).'/';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_url_see_all() {
		return $this->get_url().'all/';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_url_see_more() {
		return $this->get_url().'see_more/';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_url_add_publication() {
		return $this->get_url().'publication/';
	}
}
