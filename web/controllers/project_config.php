<?php

require __DIR__.'/../../include/setup.php';

/** @var \Interfaces\Shared\Project $project_shared */
$project_shared  = $dic->getObject('project');
$current_project = $project_shared->getCurrentProject();

if ($_POST) {
	$name           = isset($_POST['name']) ? trim($_POST['name']) : '';
	$description    = isset($_POST['description']) ? trim($_POST['description']) : '';
	$visible        = !empty($_POST['visible']);
	$has_prod       = !empty($_POST['has_prod']);
	$vcs_base       = isset($_POST['vcs_base']) ? trim($_POST['vcs_base']) : '';
	$vcs_path       = isset($_POST['vcs_path']) ? trim($_POST['vcs_path']) : '';
	$bug_tracker_id = isset($_POST['bug_tracker_id']) ? trim($_POST['bug_tracker_id']) : '';
	$externals      = isset($_POST['externals']) ? (array)$_POST['externals'] : array();
	$recipients     = isset($_POST['recipients']) ? (array)$_POST['recipients'] : array();

	$errors = array();
	if (!$name) {
		$errors['name'] = 'Vous devez indiquer un nom de projet';
	}

	// Remove beginning or ended /
	$vcs_base = preg_replace('#^/?(.*)/?$#', '\\1', $vcs_base);
	if (!$vcs_base) {
		$errors['vcs_base'] = 'Vous devez indiquer le chemin de base du repository';
	}

	// Begin with / and remove ended /
	$vcs_path = '/'.preg_replace('#^/?(.*)/?$#', '\\1', $vcs_path);
	if (!$vcs_path) {
		$errors['vcs_path'] = 'Vous devez indiquer le chemin du projet dans le repository';
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
		if ($current_project) {
			$current_project->setName($name);
			$current_project->setVisible($visible);
			$current_project->setHasProd($has_prod);
			$current_project->setVcsBase($vcs_base);
			$current_project->setVcsPath($vcs_path);
		} else {
			$current_project = $project_shared->create($name, $vcs_base, $vcs_path, $visible, $has_prod);
		}

		if ($current_project) {
			$current_project->setBugTrackerId($bug_tracker_id);
			$current_project->setDescription($description);

			foreach ($current_project->getExternals() as $external_project) {
				$current_project->removeExternal($external_project);
			}
			foreach ($externals as $external_project_id) {
				/** @var \Interfaces\Object\Project $external_project */
				$external_project = $dic->getObject('project_object', $external_project_id);
				if ($external_project) {
					$current_project->addExternal($external_project);
				}
			}

			foreach ($current_project->getRecipients() as $recipient) {
				$current_project->removeRecipient($recipient);
			}
			foreach ($recipients as $recipient) {
				$current_project->addRecipient($recipient);
			}
		}
	}
} elseif ($current_project) {
	$name           = $current_project->getName();
	$description    = $current_project->getDescription();
	$visible        = $current_project->isVisible();
	$has_prod       = $current_project->hasProd();
	$vcs_base       = $current_project->getVcsBase();
	$vcs_path       = $current_project->getVcsPath();
	$bug_tracker_id = $current_project->getBugTrackerId();
}

require $dic->getParam('path_templates').'/project_config.php';
