<?php

require __DIR__.'/../../include/setup.php';

header('Content: text/json');

/** @var \Interfaces\Shared\Project $project_shared */
$project_shared  = $dic->get_object('project');
$current_project = $project_shared->get_current_project();

$revision_begins = isset($_GET['begins']) && is_array($_GET['begins']) ? $_GET['begins'] : array();

$last_publication_id = $_GET['last_publication_id'];
/** @var \Interfaces\Object\Publication $publication */
$publication = $dic->get_object('publication_object', $last_publication_id);
$publication = $publication->get_previous();

require $dic->get_param('path_templates').'/logs.php';
