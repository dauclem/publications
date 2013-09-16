<?php
require __DIR__.'/common/header.php';
require __DIR__.'/common/top_nav.php';
?>

<div class="row">
	<div class="col-lg-8 col-lg-offset-2">
		<fieldset>
			<legend>Configuration</legend>
			<a href="/configuration/">Modifier la configuration générale</a>
		</fieldset>
		<br />
	</div>
</div>

<div class="row">
	<div class="col-lg-8 col-lg-offset-2">
		<fieldset>
			<legend>Projets</legend>
		</fieldset>

		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<td>&nbsp;</td>
					<td>Nom</td>
					<td>Repository</td>
					<td>Visible</td>
					<td>Prodable</td>
				</tr>
			</thead>
			<tbody>
				<?php
				/** @var \Interfaces\Shared\Project $project_shared */
				$project_shared = $dic->get_object('project');
				foreach ($project_shared->get_projects() as $project) {
					echo '<tr>
						<td>
							<a href="/configuration/projet/'.urlencode($project->get_name()).'/">
								<i class="glyphicon glyphicon-wrench"></i>
							</a>
						</td>
						<td>'.htmlentities($project->get_name()).'</td>
						<td>'.htmlentities($project->get_vcs_repository()).'</td>
						<td>'.($project->is_visible() ? '<i class="glyphicon glyphicon-ok"></i>' : '').'</td>
						<td>'.($project->has_prod() ? '<i class="glyphicon glyphicon-ok"></i>' : '').'</td>
					</tr>';
				}
				?>
			</tbody>
		</table>

		<a href="/configuration/projet/">Ajouter un projet</a>
	</div>
</div>

<?php
require __DIR__.'/common/footer.php';