<?php

require __DIR__.'/../../include/setup.php';

/** @var \Interfaces\Shared\Project $project_shared */
$project_shared = $dic->getObject('project');
$current_project = $project_shared->getCurrentProject();

/** @var \Interfaces\Shared\Publication $publication_shared */
$publication_shared = $dic->getObject('publication');
$publication_shared->create($current_project, false, time(), '');