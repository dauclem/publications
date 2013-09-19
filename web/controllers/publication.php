<?php

require __DIR__.'/../../include/setup.php';

$publication_id = isset($_GET['publication_id']) ? $_GET['publication_id'] : 0;
/** @var \Interfaces\Object\Publication $publication */
$publication = $dic->getObject('publication_object', $publication_id);

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'remove') {
	if ($publication) {
		$publication->remove();
		header('Status: 302 Found', true, 302);
		header('Location: '.$publication->getProject()->getUrl(), true, 302);
		exit;
	}
} elseif ($action == 'notemp') {
	if ($publication) {
		$publication->setDate(time());
		$publication->setTemp(false);
		header('Status: 302 Found', true, 302);
		header('Location: '.$publication->getProject()->getUrl(), true, 302);
		exit;
	}
} elseif (count($_POST)) {
	if ($action == 'edit') {
		if ($publication) {
			$is_temp = isset($_POST['is_temp']) && $_POST['is_temp'] == 'on';
			$publication->setTemp($is_temp);

			if (!$is_temp) {
				$date = isset($_POST['date']) ? strtotime(trim($_POST['date'])) : '';
				if ($date) {
					$publication->setDate($date);
				}
			}

			$comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';
			if ($comments) {
				$publication->setComments($comments);
			}
		}
	} else {
		$is_temp    = isset($_POST['is_temp']) && $_POST['is_temp'] == 'on';
		$date       = isset($_POST['date']) ? strtotime(trim($_POST['date'])) : '';
		$project_id = isset($_POST['project_id']) ? trim($_POST['project_id']) : '';
		/** @var \Interfaces\Object\Project $project */
		$project = $dic->getObject('project_object', $project_id);
		if ($project && ($is_temp || $date)) {
			$comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';
			/** @var \Interfaces\Shared\Publication $publication_shared */
			$publication_shared = $dic->getObject('publication');
			$publication        = $publication_shared->create($project, $is_temp, $date, $comments);
		}
	}

	if ($publication) {
		header('Status: 302 Found', true, 302);
		header('Location: '.$publication->getProject()->getUrl(), true, 302);
		exit;
	}
}

require $dic->getParam('path_templates').'/publication.php';