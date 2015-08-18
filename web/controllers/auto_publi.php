<?php

require __DIR__.'/../../include/setup.php';

/** @var \Interfaces\Shared\Project $project_shared */
$project_shared  = $dic->getObject('project');
$current_project = $project_shared->getCurrentProject();

/** @var \Interfaces\Shared\Publication $publication_shared */
$publication_shared = $dic->getObject('publication');
$publication_temp   = $publication_shared->getPublicationTemp($current_project);
if ($publication_temp) {
	$publication_temp->setTemp(false);
	$this_publication = $publication_temp;
} else {
	$this_publication = $publication_shared->create($current_project, false, time(), '');
}

if (!empty($_GET['email'])) {
	$email_infos = $this_publication->get_email_infos(array(), true);
	$dest        = $email_infos['recipients'].($email_infos['recipients'] && $email_infos['cc'] ? ',' : '').$email_infos['cc'];
	$sender      = $email_infos['sender'];
	$additional_parameters = $sender ? "From: $sender\nReply-to: $sender\nReturn-Path: $sender" : '';
	mail($dest, $email_infos['subject'], $email_infos['body'], $additional_parameters);
}
