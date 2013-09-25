<?php
require __DIR__.'/common/header.php';
require __DIR__.'/common/top_nav.php';
?>

<h1 class="text-center">Administration</h1>

<fieldset>
	<legend>Configuration</legend>
	<a href="/configuration/">Modifier la configuration générale</a>
</fieldset>
<br />

<fieldset>
	<legend>Projets</legend>
</fieldset>

<table class="table table-bordered table-hover" id="project_list">
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th>Nom</th>
			<th>Repository</th>
			<th>Visible</th>
			<th>Prodable</th>
			<th>Dernière publication</th>
		</tr>
	</thead>
	<tbody>
		<?php
		/** @var \Interfaces\Shared\Project $project_shared */
		$project_shared = $dic->getObject('project');
		foreach ($project_shared->getProjects() as $project) {
			echo '<tr>
				<td>
					<a href="/configuration/projet/'.urlencode($project->getName()).'/">
						<i class="glyphicon glyphicon-wrench"></i>
					</a>
				</td>
				<td>'.htmlentities($project->getName()).'</td>
				<td>'.htmlentities($project->getVcsRepository()).'</td>
				<td class="text-center">'.($project->isVisible() ? '<i class="glyphicon glyphicon-ok"></i>' : '').'</td>
				<td class="text-center">'.($project->hasProd() ? '<i class="glyphicon glyphicon-ok"></i>' : '').'</td>
				<td class="text-center">';

			$this_last_publication = $publication_shared->getLastPublication($project, true);
			if ($this_last_publication) {
				echo date('d/m/Y H:i', $this_last_publication->getDate());
			}

			echo '</td>
			</tr>';
		}
		?>
	</tbody>
</table>

<a href="/configuration/projet/" class="btn btn-primary">Ajouter un projet</a>

<?php
require __DIR__.'/common/footer.php';