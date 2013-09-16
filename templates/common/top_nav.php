<ul class="nav nav-tabs">
	<?php
	/** @var \Interfaces\Shared\Project $project_shared */
	$project_shared = $dic->get_object('project');
	$current_project = $project_shared->get_current_project();

	echo '<li'.(!$current_project ? ' class="active"' : '').'>
		<a href="/">Admin</a>
	</li>';

	foreach ($project_shared->get_projects() as $project) {
		if ($project->is_visible()) {
			echo '<li'.($current_project == $project ? ' class="active"' : '').'>
				<a href="'.$project->get_url().'">'.htmlentities($project->get_name()).'</a>
			</li>';
		}
	}
	?>
</ul>