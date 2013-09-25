<ul class="nav nav-tabs">
	<?php
	/** @var \Interfaces\Shared\Project $project_shared */
	$project_shared = $dic->getObject('project');
	$current_project = $project_shared->getCurrentProject();

	/** @var \Interfaces\Shared\Publication $publication_shared */
	$publication_shared = $dic->getObject('publication');

	echo '<li'.(!$current_project ? ' class="active"' : '').'>
		<a href="/">Admin</a>
	</li>';

	foreach ($project_shared->getProjects() as $project) {
		if ($project->isVisible()) {
			echo '<li'.($current_project == $project ? ' class="active"' : '').'>';
			echo '<a href="'.$project->getUrl().'">'.htmlentities($project->getName()).'</a>';

			$this_last_publication = $publication_shared->getLastPublication($project, true);
			if ($this_last_publication) {
				echo '<span class="nav_project_date">'.date('d/m/Y', $this_last_publication->getDate()).'</i>';
			}

			echo  '</li>';
		}
	}
	?>
</ul>