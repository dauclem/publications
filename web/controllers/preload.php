<?php

require __DIR__.'/../../include/setup.php';

/** @var \Interfaces\Shared\Project $project_shared */
$project_shared = $dic->getObject('project');

$base_url = $config_shared->getSiteUrl().'all.php?project_id=';

foreach ($project_shared->getProjects() as $project) {
	if ($project->isVisible()) {
		file($project->getUrlSeeAll());
	}
}