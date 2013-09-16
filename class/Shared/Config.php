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

	/**
	 * {@inheritDoc}
	 */
	public function get_dependencies_list() {
		return array_merge(parent::get_dependencies_list(), array(
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
		$connection = $database->get_connection();
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
							bug_tracker_password TEXT)');
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
		if (@$_SERVER['SERVER_PORT'] != '80') {
			$this->site_url .= ':'.$_SERVER['SERVER_PORT'];
		}
		$this->site_url .= '/';

		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->get_connection();
		$result     = $connection->querySingle('SELECT name FROM sqlite_master WHERE type="table" AND name="config"');
		if (!$result) {
			$this->install();
		}

		$data = $connection->querySingle('SELECT vcs_type, vcs_url, vcs_user, vcs_password, vcs_web_url, changelog_path,
												bug_tracker_type, bug_tracker_url, bug_tracker_user, bug_tracker_password
											FROM config', true);
		list($this->vcs_type, $this->vcs_url, $this->vcs_user, $this->vcs_password, $this->vcs_web_url, $this->changelog_path,
			$this->bug_tracker_type, $this->bug_tracker_url, $this->bug_tracker_user, $this->bug_tracker_password) = array_values($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_site_url() {
		return $this->site_url;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_vcs_type() {
		return $this->vcs_type;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_vcs_url() {
		return $this->vcs_url;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_vcs_user() {
		return $this->vcs_user;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_vcs_password() {
		return $this->vcs_password;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_vcs_web_url() {
		return $this->vcs_web_url;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_changelog_path() {
		return $this->changelog_path;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_bug_tracker_type() {
		return $this->bug_tracker_type;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_bug_tracker_url() {
		return $this->bug_tracker_url;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_bug_tracker_user() {
		return $this->bug_tracker_user;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_bug_tracker_password() {
		return $this->bug_tracker_password;
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
			$connection->exec('UPDATE config SET '.$property.' = \''.$connection->escapeString($this->$property).'\'');
			$this->initialize();
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_vcs_type($vcs_type) {
		$this->vcs_type = $vcs_type;
		$this->save('vcs_type');
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_vcs_url($vcs_url) {
		$this->vcs_url = $vcs_url;
		$this->save('vcs_url');
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_vcs_user($vcs_user) {
		$this->vcs_user = $vcs_user;
		$this->save('vcs_user');
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_vcs_password($vcs_password) {
		$this->vcs_password = $vcs_password;
		$this->save('vcs_password');
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_vcs_web_url($vcs_web_url) {
		$this->vcs_web_url = $vcs_web_url;
		$this->save('vcs_web_url');
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_changelog_path($changelog_path) {
		$this->changelog_path = $changelog_path;
		$this->save('changelog_path');
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_bug_tracker_type($bug_tracker_type) {
		$this->bug_tracker_type = $bug_tracker_type;
		$this->save('bug_tracker_type');
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_bug_tracker_url($bug_tracker_url) {
		$this->bug_tracker_url = $bug_tracker_url;
		$this->save('bug_tracker_url');
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_bug_tracker_user($bug_tracker_user) {
		$this->bug_tracker_user = $bug_tracker_user;
		$this->save('bug_tracker_user');
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_bug_tracker_password($bug_tracker_password) {
		$this->bug_tracker_password = $bug_tracker_password;
		$this->save('bug_tracker_password');
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
		$connection->exec('REPLACE INTO config_recipients(email)
							VALUES (\''.$connection->escapeString($email).'\')');
	}

	/**
	 * {@inheritDoc}
	 */
	public function remove_recipient($email) {
		/** @var \Interfaces\Shared\Database $database */
		$database   = $this->dependence_objects['database'];
		$connection = $database->get_connection();
		$connection->exec('DELETE FROM config_recipients
							WHERE email = \''.$connection->escapeString($email).'\'');
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_recipients() {
		/** @var \Interfaces\Shared\Database $database */
		$database = $this->dependence_objects['database'];
		$result   = $database->get_connection()->query('SELECT email FROM config_recipients');
		$emails   = array();
		while (list($email) = $result->fetchArray()) {
			$emails[] = $email;
		}
		return $emails;
	}
}
