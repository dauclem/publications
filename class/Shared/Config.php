<?php

namespace Shared;

use Shared;

class Config extends Shared implements \Interfaces\Shared\Config {
	/** @var string */
	protected $site_url;
	/** @var string */
	protected $vcs_type;
	/** @var string */
	protected $vcs_url;
	/** @var string */
	protected $vcs_user;
	/** @var string */
	protected $vcs_password;
	/** @var string */
	protected $vcs_web_url;
	/** @var string */
	protected $changelog_path;
	/** @var string */
	protected $bug_tracker_type;
	/** @var string */
	protected $bug_tracker_url;
	/** @var string */
	protected $bug_tracker_user;
	/** @var string */
	protected $bug_tracker_password;
	/** @var string */
	protected $bug_tracker_query;
	/** @var string */
	protected $mail_content;
	/** @var string */
	protected $mail_subject;

	/**
	 * {@inheritDoc}
	 */
	public function getDependenciesList() {
		return array_merge(parent::getDependenciesList(), array(
															   'database',
															   'form_utils',
														  ));
	}

	/**
	 * {@inheritDoc}
	 */
	public function install() {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->getConnection();
		$connection->exec('DROP TABLE IF EXISTS config');
		$connection->exec('CREATE TABLE config(
							vcs_type TEXT,
							vcs_url TEXT,
							vcs_user TEXT,
							vcs_password TEXT,
							vcs_web_url TEXT,
							changelog_path TEXT,
							bug_tracker_type TEXT,
							bug_tracker_url TEXT,
							bug_tracker_user TEXT,
							bug_tracker_password TEXT,
							bug_tracker_query TEXT,
							mail_content TEXT,
							mail_subject TEXT)');
		$connection->exec('INSERT INTO config(vcs_type) VALUES ("subversion")');

		$connection->exec('CREATE TABLE IF NOT EXISTS config_recipients(
							email TEXT)');
		$connection->exec('CREATE UNIQUE INDEX IF NOT EXISTS config_recipient ON config_recipients (email)');
	}

	/**
	 * {@inheritDoc}
	 */
	public function initialize() {
		$this->site_url = 'http';
		if (@$_SERVER['HTTPS'] == 'on') {
			$this->site_url .= 's';
		}
		$this->site_url .= '://'.@$_SERVER['SERVER_NAME'];
		if (@$_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] != '80') {
			$this->site_url .= ':'.$_SERVER['SERVER_PORT'];
		}
		$this->site_url .= '/';

		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->getConnection();
		$result     = $connection->querySingle('SELECT name FROM sqlite_master WHERE type="table" AND name="config"');
		if (!$result) {
			if (!isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] != '/configuration/') {
				header('Status: 302 Found', true, 302);
				header('Location: /configuration/', true, 302);
				exit;
			}
		} else {
			$data = $connection->querySingle('SELECT vcs_type, vcs_url, vcs_user, vcs_password, vcs_web_url, changelog_path,
													bug_tracker_type, bug_tracker_url, bug_tracker_user,
													bug_tracker_password, bug_tracker_query,
													mail_content, mail_subject
												FROM config', true);
			list($this->vcs_type, $this->vcs_url, $this->vcs_user, $this->vcs_password, $this->vcs_web_url, $this->changelog_path,
				$this->bug_tracker_type, $this->bug_tracker_url, $this->bug_tracker_user,
				$this->bug_tracker_password, $this->bug_tracker_query,
				$this->mail_content, $this->mail_subject) = array_values($data);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSiteUrl() {
		return $this->site_url;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getVcsType() {
		return $this->vcs_type;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getVcsUrl() {
		return $this->vcs_url;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getVcsUser() {
		return $this->vcs_user;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getVcsPassword() {
		return $this->vcs_password;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getVcsWebUrl() {
		return $this->vcs_web_url;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getChangelogPath() {
		return $this->changelog_path;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBugTrackerType() {
		return $this->bug_tracker_type;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBugTrackerUrl() {
		return $this->bug_tracker_url;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBugTrackerUser() {
		return $this->bug_tracker_user;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBugTrackerPassword() {
		return $this->bug_tracker_password;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBugTrackerQuery() {
		return $this->bug_tracker_query;
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
	 * Save changes into db
	 *
	 * @param string $property property name
	 */
	protected function save($property) {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		if ($property) {
			$connection = $database->getConnection();
			$connection->exec('UPDATE config SET '.$property.' = \''.$connection->escapeString($this->$property).'\'');
			$this->initialize();
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function setVcsType($vcs_type) {
		$this->vcs_type = $vcs_type;
		$this->save('vcs_type');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setVcsUrl($vcs_url) {
		$this->vcs_url = $vcs_url;
		$this->save('vcs_url');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setVcsUser($vcs_user) {
		$this->vcs_user = $vcs_user;
		$this->save('vcs_user');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setVcsPassword($vcs_password) {
		$this->vcs_password = $vcs_password;
		$this->save('vcs_password');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setVcsWebUrl($vcs_web_url) {
		$this->vcs_web_url = $vcs_web_url;
		$this->save('vcs_web_url');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setChangelogPath($changelog_path) {
		$this->changelog_path = $changelog_path;
		$this->save('changelog_path');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setBugTrackerType($bug_tracker_type) {
		$this->bug_tracker_type = $bug_tracker_type;
		$this->save('bug_tracker_type');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setBugTrackerUrl($bug_tracker_url) {
		$this->bug_tracker_url = $bug_tracker_url;
		$this->save('bug_tracker_url');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setBugTrackerUser($bug_tracker_user) {
		$this->bug_tracker_user = $bug_tracker_user;
		$this->save('bug_tracker_user');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setBugTrackerPassword($bug_tracker_password) {
		$this->bug_tracker_password = $bug_tracker_password;
		$this->save('bug_tracker_password');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setBugTrackerQuery($bug_tracker_query) {
		$this->bug_tracker_query = $bug_tracker_query;
		$this->save('bug_tracker_query');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setMailContent($mail_content) {
		$this->mail_content = $mail_content;
		$this->save('mail_content');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setMailSubject($mail_subject) {
		$this->mail_subject = $mail_subject;
		$this->save('mail_subject');
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
		$connection->exec('REPLACE INTO config_recipients(email)
							VALUES (\''.$connection->escapeString($email).'\')');
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeRecipient($email) {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->getConnection();
		$connection->exec('DELETE FROM config_recipients
							WHERE email = \''.$connection->escapeString($email).'\'');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRecipients() {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->getConnection();
		$result     = $connection->querySingle('SELECT name FROM sqlite_master WHERE type="table" AND name="config"');
		$emails     = array();
		if ($result) {
			$result = $connection->query('SELECT email FROM config_recipients');
			while (list($email) = $result->fetchArray()) {
				$emails[] = $email;
			}
		}
		return $emails;
	}
}
