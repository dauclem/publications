<?php

require __DIR__.'/../../include/setup.php';

header('Content: text/json');

/** @var \Interfaces\Shared\Project $project_shared */
$project_shared  = $dic->getObject('project');
$current_project = $project_shared->getCurrentProject();

$revision_begins = isset($_GET['begins']) && is_array($_GET['begins']) ? $_GET['begins'] : array();

$last_publication_id = $_GET['last_publication_id'];
/** @var \Interfaces\Object\Publication $publication */
$publication = $dic->getObject('publication_object', $last_publication_id);
$publication = $publication->getPrevious();

require $dic->getParam('path_templates').'/logs.php';
