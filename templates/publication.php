<?php
require __DIR__.'/common/header.php';
require __DIR__.'/common/top_nav.php';
?>

<h1 class=text-center>
	<?php echo $publication ? 'Editer une publication' : 'Ajouter une publication'; ?>
</h1>

<form action="" method="post" class="form-horizontal row">
	<div class="form-group">
		<label class="control-label col-lg-3" for="project_id">Projet :</label>

		<div class="col-lg-6">
			<select class="form-control" name="project_id" id="project_id">
				<?php
				/** @var \Interfaces\Shared\Project $project_shared */
				$project_shared = $dic->get_object('project');
				$current_project = $project_shared->get_current_project();
				foreach ($project_shared->get_projects() as $project) {
					if ($project->has_prod()) {
						echo '<option value="'.$project->get_id().'"'.($current_project == $project ? ' selected="selected"' : '').'>'
							 .htmlentities($project->get_name())
							 .'</option>';
					}
				}
				?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3" for="date">Date :</label>

		<div class="col-lg-6">
			<input type="text" class="form-control" name="date" id="date"
				   value="<?php echo date('Y-m-d H:i', $publication ? $publication->get_date() : time()); ?>"/>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-lg-3" for="comments">Commentaires :</label>

		<div class="col-lg-6">
			<textarea class="form-control" name="comments"
					  id="comments"><?php echo $publication ? htmlentities($publication->get_comments()) : ''; ?></textarea>
		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-6">
			<input type="submit" class="form-control btn btn-primary"/>
		</div>
	</div>
</form>

<?php
require __DIR__.'/common/footer.php';
?>