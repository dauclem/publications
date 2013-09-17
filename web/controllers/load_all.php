<?php

require __DIR__.'/../../include/setup.php';

/** @var \Interfaces\Shared\Project $project_shared */
$project_shared = $dic->get_object('project');

$base_url = $config_shared->get_site_url().'all.php?project_id=';

foreach ($project_shared->get_projects() as $project) {
	if ($project->is_visible()) {
		file($base_url.$project->get_id());
	}
}