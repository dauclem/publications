<?php

require __DIR__.'/../../include/setup.php';

/** @var \Interfaces\Shared\Project $project_shared */
$project_shared  = $dic->getObject('project');
$current_project = $project_shared->getCurrentProject();
if (!$current_project) {
	exit('Invalid project');
}

/** @var \Interfaces\Shared\Publication $publication_shared */
$publication_shared = $dic->getObject('publication');
$publication_temp   = $publication_shared->getPublicationTemp($current_project);
if (!$publication_temp) {
	$publication_temp = $publication_shared->create($current_project, true, time(), '');
}
if (!$publication_temp) {
	exit('Error to get or create "publication_temp"');
}

/** @var \Interfaces\Shared\VCS $vcs */
$vcs = $dic->getObject('vcs');

$publication  = $publication_temp->getPrevious();
$all_rows     = $vcs->getAllRows($current_project, $revision_begins, $publication);
$publications = array($publication->getNext()->createRow($all_rows));
$all_rows     = array_merge($all_rows, $publications);
usort($all_rows, function (\Interfaces\Object\Row $a, \Interfaces\Object\Row $b) {
	if ($a->getDate() == $b->getDate()) {
		return 0;
	}
	return $a->getDate() < $b->getDate() ? 1 : -1;
});

/** @var \Interfaces\Object\Row $row */
foreach ($all_rows as $k => $row) {
	$related_object   = $row->getRelatedObject();
	$this_publication = $related_object instanceof \Interfaces\Object\Publication ? $related_object : null;
	if ($this_publication && $this_publication->isTemp()) {
		break;
	}
}

/** @var \Interfaces\Shared\Issue $issue_shared */
$issue_shared = $dic->getObject('issue');
$issues       = $row->getIssues();
$issues       = $issue_shared->filterIssues($issues, $current_project);

$email_infos = $this_publication->get_email_infos($issues);
$dest        = $email_infos['recipients'].($email_infos['recipients'] && $email_infos['cc'] ? ',' : '').$email_infos['cc'];

// temporary to debug
$dest = $email_infos['cc'];

mail($dest, $email_infos['subject'], $email_infos['body']);