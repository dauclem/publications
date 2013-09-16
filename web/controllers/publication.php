<?php

require __DIR__.'/../../include/setup.php';

$publication_id = isset($_GET['publication_id']) ? $_GET['publication_id'] : 0;
/** @var \Interfaces\Object\Publication $publication */
$publication = $dic->get_object('publication_object', $publication_id);

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'remove') {
	if ($publication) {
		$publication->remove();
		header('Status: 302 Found', true, 302);
		header('Location: '.$publication->get_project()->get_url(), true, 302);
		exit;
	}
} elseif (count($_POST)) {
	if ($action == 'remove') {
		if ($publication) {
			$publication->remove();
		}
	} elseif ($action == 'edit') {
		if ($publication) {
			$date = isset($_POST['date']) ? strtotime(trim($_POST['date'])) : '';
			if ($date) {
				$publication->set_date($date);
			}

			$comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';
			if ($comments) {
				$publication->set_comments($comments);
			}
		}
	} else {
		$date       = isset($_POST['date']) ? strtotime(trim($_POST['date'])) : '';
		$project_id = isset($_POST['project_id']) ? trim($_POST['project_id']) : '';
		/** @var \Interfaces\Object\Project $project */
		$project = $dic->get_object('project_object', $project_id);
		if ($project && $date) {
			$comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';
			/** @var \Interfaces\Shared\Publication $publication_shared */
			$publication_shared = $dic->get_object('publication');
			$publication        = $publication_shared->create($project, $date, $comments);
		}
	}

	if ($publication) {
		header('Status: 302 Found', true, 302);
		header('Location: '.$publication->get_project()->get_url(), true, 302);
		exit;
	}
}

require $dic->get_param('path_templates').'/publication.php';