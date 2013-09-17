<?php

require __DIR__.'/../../include/setup.php';

/** @var \Interfaces\Shared\Project $project_shared */
$project_shared  = $dic->get_object('project');
$current_project = $project_shared->get_current_project();

if ($_POST) {
	$name        = isset($_POST['name']) ? trim($_POST['name']) : '';
	$description = isset($_POST['description']) ? trim($_POST['description']) : '';
	$visible     = !empty($_POST['visible']);
	$has_prod    = !empty($_POST['has_prod']);
	$vcs_base    = isset($_POST['vcs_base']) ? trim($_POST['vcs_base']) : '';
	$vcs_path    = isset($_POST['vcs_path']) ? trim($_POST['vcs_path']) : '';
	$tracker_id  = isset($_POST['tracker_id']) ? trim($_POST['tracker_id']) : '';
	$externals   = isset($_POST['externals']) ? (array)$_POST['externals'] : array();
	$recipients  = isset($_POST['recipients']) ? (array)$_POST['recipients'] : array();

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
	$form_utils = $dic->get_object('form_utils');
	foreach ($recipients as $k => $recipient) {
		if (!$recipient) {
			unset($recipients[$k]);
		} elseif (!$form_utils->check_email($recipient)) {
			$errors['recipients'] = 'Vous ne pouvez saisir que des adresses email comme destinataires';
		}
	}

	if (!$errors) {
		if ($current_project) {
			$current_project->set_name($name);
			$current_project->set_visible($visible);
			$current_project->set_has_prod($has_prod);
			$current_project->set_vcs_base($vcs_base);
			$current_project->set_vcs_path($vcs_path);
		} else {
			$current_project = $project_shared->create($name, $vcs_base, $vcs_path, $visible, $has_prod);
		}

		if ($current_project) {
			$current_project->set_tracker_id($tracker_id);
			$current_project->set_description($description);

			foreach ($current_project->get_externals() as $external_project) {
				$current_project->remove_external($external_project);
			}
			foreach ($externals as $external_project_id) {
				/** @var \Interfaces\Object\Project $external_project */
				$external_project = $dic->get_object('project_object', $external_project_id);
				if ($external_project) {
					$current_project->add_external($external_project);
				}
			}

			foreach ($current_project->get_recipients() as $recipient) {
				$current_project->remove_recipient($recipient);
			}
			foreach ($recipients as $recipient) {
				$current_project->add_recipient($recipient);
			}
		}
	}
} elseif ($current_project) {
	$name        = $current_project->get_name();
	$description = $current_project->get_description();
	$visible     = $current_project->is_visible();
	$has_prod    = $current_project->has_prod();
	$vcs_base    = $current_project->get_vcs_base();
	$vcs_path    = $current_project->get_vcs_path();
	$tracker_id  = $current_project->get_tracker_id();
}

require $dic->get_param('path_templates').'/project_config.php';
