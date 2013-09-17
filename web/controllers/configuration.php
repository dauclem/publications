<?php

require __DIR__.'/../../include/setup.php';

if ($_POST) {
	/** @var \Interfaces\Shared\FormUtils $form_utils */
	$form_utils = $dic->get_object('form_utils');

	$VCS_type             = isset($_POST['VCS_type']) ? trim($_POST['VCS_type']) : '';
	$VCS_url              = isset($_POST['VCS_url']) ? trim($_POST['VCS_url']) : '';
	$VCS_user             = isset($_POST['VCS_user']) ? trim($_POST['VCS_user']) : '';
	$VCS_password         = isset($_POST['VCS_password']) ? trim($_POST['VCS_password']) : $config_shared->get_vcs_password();
	$VCS_web_url          = isset($_POST['VCS_web_url']) ? trim($_POST['VCS_web_url']) : '';
	$changelog_path       = isset($_POST['changelog_path']) ? trim($_POST['changelog_path']) : '';
	$bug_tracker_type     = isset($_POST['bug_tracker_type']) ? trim($_POST['bug_tracker_type']) : '';
	$bug_tracker_url      = isset($_POST['bug_tracker_url']) ? trim($_POST['bug_tracker_url']) : '';
	$bug_tracker_user     = isset($_POST['bug_tracker_user']) ? trim($_POST['bug_tracker_user']) : '';
	$bug_tracker_password = isset($_POST['bug_tracker_password']) ? trim($_POST['bug_tracker_password']) : $config_shared->get_bug_tracker_password();
	$recipients  = isset($_POST['recipients']) ? (array)$_POST['recipients'] : array();

	$errors         = array();
	$VCS_type_class = $dic->get_object($VCS_type);
	if (!($VCS_type_class instanceof \Interfaces\Shared\VCS)) {
		$errors['VCS_type'] = 'Le type de VCS est incorrect. Veuillez en choisir un dans la liste';
	}
	// Get only domain name and remove ended /
	$VCS_url = preg_replace('#^(https?://[^/]+)/.*$#', '\\1', $VCS_url);
	if (!$form_utils->check_url($VCS_url)) {
		$errors['VCS_url'] = 'L\'url est incorrecte';
	}
	if (!$VCS_user) {
		$errors['VCS_user'] = 'Vous devez indiquer un nom d\'utilisateur';
	}
	if (!$config_shared->get_vcs_password() && !$VCS_password) {
		$errors['VCS_password'] = 'Vous devez indiquer un mot de passe';
	}
	// Get only domain name and remove ended /
	$VCS_web_url = preg_replace('#^(https?://[^/]+)/.*$#', '\\1', $VCS_web_url);
	if ($VCS_web_url && !$form_utils->check_url($VCS_web_url)) {
		$errors['VCS_web_url'] = 'L\'url est incorrecte';
	}

	$bug_tracker_class = $dic->get_object($bug_tracker_type);
	if (!($bug_tracker_class instanceof \Interfaces\Shared\Tracker)) {
		$errors['bug_tracker_type'] = 'Le type de bug tracker est incorrect. Veuillez en choisir un dans la liste';
	}
	// Get only domain name and remove ended /
	$bug_tracker_url = preg_replace('#^(https?://[^/]+)/.*$#', '\\1', $bug_tracker_url);
	if (!$form_utils->check_url($bug_tracker_url)) {
		$errors['bug_tracker_url'] = 'L\'url est incorrecte';
	}
	if (!$bug_tracker_user) {
		$errors['bug_tracker_user'] = 'Vous devez indiquer un nom d\'utilisateur';
	}
	if (!$config_shared->get_bug_tracker_password() && !$bug_tracker_password) {
		$errors['bug_tracker_password'] = 'Vous devez indiquer un mot de passe';
	}

	/** @var \Interfaces\Shared\FormUtils $form_utils */
	$form_utils = $dic->get_object('form_utils');
	foreach ($recipients as $k => $recipient) {
		if (!$recipient) {
			unset($recipients[$k]);
		} elseif (!$form_utils->check_email($recipient)) {
			$errors['recipients'] = 'Vous ne pouvez saisir que des adresses email comme destinataires';
		}
	}

	if (!$errors) {
		// Store config
		$config_shared->set_vcs_type($VCS_type);
		$config_shared->set_vcs_url($VCS_url);
		$config_shared->set_vcs_user($VCS_user);
		if ($VCS_password) {
			$config_shared->set_vcs_password($VCS_password);
		}
		$config_shared->set_vcs_web_url($VCS_web_url);
		$config_shared->set_changelog_path($changelog_path);
		$config_shared->set_bug_tracker_type($bug_tracker_type);
		$config_shared->set_bug_tracker_url($bug_tracker_url);
		$config_shared->set_bug_tracker_user($bug_tracker_user);
		if ($bug_tracker_password) {
			$config_shared->set_bug_tracker_password($bug_tracker_password);
		}

		foreach ($config_shared->get_recipients() as $recipient) {
			$config_shared->remove_recipient($recipient);
		}
		foreach ($recipients as $recipient) {
			$config_shared->add_recipient($recipient);
		}

		/** @var \Interfaces\Shared\Project $project_shared */
		$project_shared = $dic->get_object('project');
		$project_shared->install();

		/** @var \Interfaces\Shared\Publication $publication_shared */
		$publication_shared = $dic->get_object('publication');
		$publication_shared->install();
	}
} else {
	$VCS_type         = $config_shared->get_vcs_type();
	$VCS_url          = $config_shared->get_vcs_url();
	$VCS_user         = $config_shared->get_vcs_user();
	$VCS_web_url      = $config_shared->get_vcs_web_url();
	$changelog_path   = $config_shared->get_changelog_path();
	$bug_tracker_type = $config_shared->get_bug_tracker_type();
	$bug_tracker_url  = $config_shared->get_bug_tracker_url();
	$bug_tracker_user = $config_shared->get_bug_tracker_user();
}

require $dic->get_param('path_templates').'/configuration.php';
