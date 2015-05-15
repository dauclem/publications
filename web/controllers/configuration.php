<?php

require __DIR__.'/../../include/setup.php';

if ($_POST) {
	/** @var \Interfaces\Shared\FormUtils $form_utils */
	$form_utils = $dic->getObject('form_utils');

	$VCS_type             = isset($_POST['VCS_type']) ? trim($_POST['VCS_type']) : '';
	$VCS_url              = isset($_POST['VCS_url']) ? trim($_POST['VCS_url']) : '';
	$VCS_user             = isset($_POST['VCS_user']) ? trim($_POST['VCS_user']) : '';
	$VCS_password         = isset($_POST['VCS_password']) ? trim($_POST['VCS_password']) : $config_shared->getVcsPassword();
	$VCS_web_url          = isset($_POST['VCS_web_url']) ? trim($_POST['VCS_web_url']) : '';
	$changelog_path       = isset($_POST['changelog_path']) ? trim($_POST['changelog_path']) : '';
	$bug_tracker_type     = isset($_POST['bug_tracker_type']) ? trim($_POST['bug_tracker_type']) : '';
	$bug_tracker_url      = isset($_POST['bug_tracker_url']) ? trim($_POST['bug_tracker_url']) : '';
	$bug_tracker_user     = isset($_POST['bug_tracker_user']) ? trim($_POST['bug_tracker_user']) : '';
	$bug_tracker_password = isset($_POST['bug_tracker_password']) ? trim($_POST['bug_tracker_password']) : $config_shared->getBugTrackerPassword();
	$bug_tracker_query    = isset($_POST['bug_tracker_query']) ? trim($_POST['bug_tracker_query']) : '';
	$mail_subject         = isset($_POST['mail_subject']) ? trim($_POST['mail_subject']) : '';
	$mail_content         = isset($_POST['mail_content']) ? trim($_POST['mail_content']) : '';
	$mail_sender          = isset($_POST['mail_sender']) ? trim($_POST['mail_sender']) : '';
	$recipients           = isset($_POST['recipients']) ? (array)$_POST['recipients'] : array();

	$errors = array();
	$dic->setObjectDefinition('vcs', '\\Shared\\VCS\\'.ucfirst($VCS_type), true);
	try {
		$VCS_type_class = $dic->getObject('vcs');
	} catch (Exception $e) {
		$errors['VCS_type'] = 'Le type de VCS est incorrect. Veuillez en choisir un dans la liste';
	}

	// Get only domain name and remove ended /
	$VCS_url = preg_replace('#^(https?://[^/]+)/.*$#', '\\1', $VCS_url);
	if (!$form_utils->checkUrl($VCS_url)) {
		$errors['VCS_url'] = 'L\'url est incorrecte';
	}
	if (!$VCS_user) {
		$errors['VCS_user'] = 'Vous devez indiquer un nom d\'utilisateur';
	}
	if (!$config_shared->getVcsPassword() && !$VCS_password) {
		$errors['VCS_password'] = 'Vous devez indiquer un mot de passe';
	}
	// Get only domain name and remove ended /
	$VCS_web_url = preg_replace('#^(https?://[^/]+)/.*$#', '\\1', $VCS_web_url);
	if ($VCS_web_url && !$form_utils->checkUrl($VCS_web_url)) {
		$errors['VCS_web_url'] = 'L\'url est incorrecte';
	}

	$dic->setObjectDefinition('tracker', '\\Shared\\Tracker\\'.ucfirst($bug_tracker_type), true);
	try {
		$VCS_type_class = $dic->getObject('vcs');
	} catch (Exception $e) {
		$errors['bug_tracker_type'] = 'Le type de bug tracker est incorrect. Veuillez en choisir un dans la liste';
	}
	// Get only domain name and remove ended /
	$bug_tracker_url = preg_replace('#^(https?://[^/]+)/.*$#', '\\1', $bug_tracker_url);
	if (!$form_utils->checkUrl($bug_tracker_url)) {
		$errors['bug_tracker_url'] = 'L\'url est incorrecte';
	}
	if (!$bug_tracker_user) {
		$errors['bug_tracker_user'] = 'Vous devez indiquer un nom d\'utilisateur';
	}
	if (!$config_shared->getBugTrackerPassword() && !$bug_tracker_password) {
		$errors['bug_tracker_password'] = 'Vous devez indiquer un mot de passe';
	}

	/** @var \Interfaces\Shared\FormUtils $form_utils */
	$form_utils = $dic->getObject('form_utils');
	foreach ($recipients as $k => $recipient) {
		if (!$recipient) {
			unset($recipients[$k]);
		} elseif (!$form_utils->checkEmail($recipient)) {
			$errors['recipients'] = 'Vous ne pouvez saisir que des adresses email comme destinataires';
		}
	}

	if (!$errors) {
		$config_shared->install();

		// Store config
		$config_shared->setVcsType($VCS_type);
		$config_shared->setVcsUrl($VCS_url);
		$config_shared->setVcsUser($VCS_user);
		if ($VCS_password) {
			$config_shared->setVcsPassword($VCS_password);
		}
		$config_shared->setVcsWebUrl($VCS_web_url);
		$config_shared->setChangelogPath($changelog_path);
		$config_shared->setBugTrackerType($bug_tracker_type);
		$config_shared->setBugTrackerUrl($bug_tracker_url);
		$config_shared->setBugTrackerUser($bug_tracker_user);
		if ($bug_tracker_password) {
			$config_shared->setBugTrackerPassword($bug_tracker_password);
		}
		$config_shared->setBugTrackerQuery($bug_tracker_query);
		$config_shared->setMailSubject($mail_subject);
		$config_shared->setMailContent($mail_content);
		$config_shared->setMailSender($mail_sender);

		foreach ($config_shared->getRecipients() as $recipient) {
			$config_shared->removeRecipient($recipient);
		}
		foreach ($recipients as $recipient) {
			$config_shared->addRecipient($recipient);
		}

		/** @var \Interfaces\Shared\Project $project_shared */
		$project_shared = $dic->getObject('project');
		$project_shared->install();

		/** @var \Interfaces\Shared\Publication $publication_shared */
		$publication_shared = $dic->getObject('publication');
		$publication_shared->install();
	}
} else {
	$VCS_type          = $config_shared->getVcsType();
	$VCS_url           = $config_shared->getVcsUrl();
	$VCS_user          = $config_shared->getVcsUser();
	$VCS_web_url       = $config_shared->getVcsWebUrl();
	$changelog_path    = $config_shared->getChangelogPath();
	$bug_tracker_type  = $config_shared->getBugTrackerType();
	$bug_tracker_url   = $config_shared->getBugTrackerUrl();
	$bug_tracker_user  = $config_shared->getBugTrackerUser();
	$bug_tracker_query = $config_shared->getBugTrackerQuery();
	$mail_subject      = $config_shared->getMailSubject();
	$mail_content      = $config_shared->getMailContent();
	$mail_sender       = $config_shared->getMailSender();
}

require $dic->getParam('path_templates').'/configuration.php';
