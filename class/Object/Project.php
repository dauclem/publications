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
	/** @var string */
	protected $bug_tracker_id;
	/** @var bool */
	protected $visible;
	/** @var bool */
	protected $has_prod;
	/** @var string */
	protected $mail_content;
	/** @var string */
	protected $mail_subject;
	/** @var string */
	protected $mail_post_publi_content;
	/** @var string */
	protected $mail_post_publi_subject;

	/**
	 * {@inheritDoc}
	 */
	public function getDependenciesList() {
		return array_merge(parent::getDependenciesList(), array(
			'database',
			'project',
			'config',
			'form_utils',
		));
	}

	/**
	 * {@inheritDoc}
	 */
	public function initializeId($object_id) {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$data     = $database->getConnection()->querySingle('SELECT id, name, description, vcs_base, vcs_path,
																tracker_id, visible, has_prod,
																mail_content, mail_subject,
																mail_post_publi_content, mail_post_publi_subject
															FROM project
															WHERE id = '.(int)$object_id, true);
		@list($this->id, $this->name, $this->description, $this->vcs_base, $this->vcs_path,
			$this->bug_tracker_id, $this->visible, $this->has_prod,
			$this->mail_content, $this->mail_subject,
			$this->mail_post_publi_content, $this->mail_post_publi_subject) = array_values($data);
		$this->id       = (int)$this->id;
		$this->visible  = (bool)$this->visible;
		$this->has_prod = (bool)$this->has_prod;
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
	public function getName() {
		return $this->name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getVcsBase() {
		return $this->vcs_base;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getVcsPath() {
		return $this->vcs_path;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBugTrackerId() {
		return $this->bug_tracker_id;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getVcsRepository() {
		/** @var \Interfaces\Shared\Config $config_shared */
		$config_shared = $this->dependence_objects['config'];
		return $config_shared->getVcsUrl().'/'.$this->vcs_base.$this->vcs_path;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isVisible() {
		return $this->visible;
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasProd() {
		return $this->has_prod;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMailContent() {
		return $this->mail_content;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMailSubject() {
		return $this->mail_subject;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMailPostPubliContent() {
		return $this->mail_post_publi_content;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMailPostPubliSubject() {
		return $this->mail_post_publi_subject;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDisplayMailContent() {
		if ($this->mail_content) {
			return $this->mail_content;
		}

		/** @var \Interfaces\Shared\Config $config */
		$config = $this->dependence_objects['config'];
		return $config->getMailContent();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDisplayMailSubject() {
		if ($this->mail_subject) {
			return $this->mail_subject;
		}

		/** @var \Interfaces\Shared\Config $config */
		$config = $this->dependence_objects['config'];
		return $config->getMailSubject();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDisplayMailPostPubliContent() {
		if ($this->mail_post_publi_content) {
			return $this->mail_post_publi_content;
		}

		/** @var \Interfaces\Shared\Config $config */
		$config = $this->dependence_objects['config'];
		return $config->getMailPostPubliContent();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDisplayMailPostPubliSubject() {
		if ($this->mail_post_publi_subject) {
			return $this->mail_post_publi_subject;
		}

		/** @var \Interfaces\Shared\Config $config */
		$config = $this->dependence_objects['config'];
		return $config->getMailPostPubliSubject();
	}

	/**
	 * Save changes into db
	 *
	 * @param string     $property property name
	 * @param mixed|null $value    force value. If null, use value of object property
	 */
	protected function save($property, $value = null) {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		if ($property) {
			$connection = $database->getConnection();
			$connection->exec('UPDATE project
								SET '.$property.' = \''.$connection->escapeString($value !== null ? $value : $this->$property).'\'
								WHERE id = '.$this->id);
			$this->initializeId($this->id);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function setName($name) {
		$name = str_replace('/', '-', trim($name));
		/** @var \Interfaces\Shared\Project $project_shared */
		$project_shared = $this->dependence_objects['project'];
		if (!$name || $project_shared->getFromName($name)) {
			return null;
		}

		$this->name = $name;
		$this->save('name');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setDescription($description) {
		$this->description = $description;
		$this->save('description');
	}


	/**
	 * {@inheritDoc}
	 */
	public function setVcsBase($vcs_base) {
		$this->vcs_base = $vcs_base;
		$this->save('vcs_base');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setVcsPath($vcs_path) {
		$this->vcs_path = $vcs_path;
		$this->save('vcs_path');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setBugTrackerId($bug_tracker_id) {
		$this->bug_tracker_id = $bug_tracker_id;
		$this->save('tracker_id', $bug_tracker_id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setVisible($visible) {
		$this->visible = $visible;
		$this->save('visible');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setHasProd($has_prod) {
		$this->has_prod = $has_prod;
		$this->save('has_prod');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setMailContent($mail_content) {
		/** @var \Interfaces\Shared\Config $config */
		$config             = $this->dependence_objects['config'];
		$this->mail_content = $mail_content != $config->getMailContent() ? $mail_content : '';
		$this->save('mail_content');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setMailSubject($mail_subject) {
		/** @var \Interfaces\Shared\Config $config */
		$config             = $this->dependence_objects['config'];
		$this->mail_subject = $mail_subject != $config->getMailSubject() ? $mail_subject : '';
		$this->save('mail_subject');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setMailPostPubliContent($mail_post_publi_content) {
		/** @var \Interfaces\Shared\Config $config */
		$config                        = $this->dependence_objects['config'];
		$this->mail_post_publi_content = $mail_post_publi_content != $config->getMailContent() ? $mail_post_publi_content : '';
		$this->save('mail_post_publi_content');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setMailPostPubliSubject($mail_post_publi_subject) {
		/** @var \Interfaces\Shared\Config $config */
		$config                        = $this->dependence_objects['config'];
		$this->mail_post_publi_subject = $mail_post_publi_subject != $config->getMailSubject() ? $mail_post_publi_subject : '';
		$this->save('mail_post_publi_subject');
	}

	/**
	 * {@inheritDoc}
	 */
	public function addExternal(\Interfaces\Object\Project $project) {
		if ($project == $this) {
			return;
		}

		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$database->getConnection()->exec('REPLACE INTO project_externals(id_project, id_external)
											VALUES ('.$this->id.', '.$project->getId().')');
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeExternal(\Interfaces\Object\Project $project) {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$database->getConnection()->exec('DELETE FROM project_externals
											WHERE id_project = '.$this->id.'
												AND id_external = '.$project->getId());
	}

	/**
	 * {@inheritDoc}
	 */
	public function getExternals() {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$result   = $database->getConnection()->query('SELECT id_external
														FROM project_externals
														WHERE id_project = '.$this->id);
		$objects  = array();
		while (list($id) = $result->fetchArray()) {
			/** @var \Interfaces\Object\Project $database */
			$object = $this->dic->getObject('project_object', $id);
			if ($object) {
				$objects[] = $object;
			}
		}
		return $objects;
	}

	/**
	 * {@inheritDoc}
	 */
	public function addRecipient($email) {
		/** @var \Interfaces\Shared\FormUtils $form_utils */
		$form_utils = $this->dependence_objects['form_utils'];
		$email      = trim($email);
		if (!$email || !$form_utils->checkEmail($email)) {
			return;
		}

		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->getConnection();
		$connection->exec('REPLACE INTO project_recipients(id_project, email)
							VALUES ('.$this->id.', \''.$connection->escapeString($email).'\')');
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeRecipient($email) {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->getConnection();
		$connection->exec('DELETE FROM project_recipients
							WHERE id_project = '.$this->id.'
								AND email = \''.$connection->escapeString($email).'\'');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRecipients() {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$result   = $database->getConnection()->query('SELECT email
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
	public function getUrl() {
		/** @var \Interfaces\Shared\Config $config */
		$config = $this->dependence_objects['config'];
		return $config->getSiteUrl().'projet/'.urlencode($this->name).'/';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUrlSeeAll() {
		return $this->getUrl().'all/';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUrlSeeMore() {
		return $this->getUrl().'see_more/';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUrlAddPublication() {
		return $this->getUrl().'publication/';
	}
}
