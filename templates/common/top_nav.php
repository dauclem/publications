<ul class="nav nav-tabs">
	<?php
	/** @var \Interfaces\Shared\Project $project_shared */
	$project_shared = $dic->getObject('project');
	$current_project = $project_shared->getCurrentProject();

	echo '<li'.(!$current_project ? ' class="active"' : '').'>
		<a href="/">Admin</a>
	</li>';

	foreach ($project_shared->getProjects() as $project) {
		if ($project->isVisible()) {
			echo '<li'.($current_project == $project ? ' class="active"' : '').'>
				<a href="'.$project->getUrl().'">'.htmlentities($project->getName()).'</a>
			</li>';
		}
	}
	?>
</ul>