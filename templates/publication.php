<?php
require __DIR__.'/common/header.php';
require __DIR__.'/common/top_nav.php';
?>

<form action="" method="post" class="form-horizontal">
	<div class="form-group">
		<label class="control-label col-lg-2" for="project_id">Projet :</label>

		<div class="col-lg-3">
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
		<label class="control-label col-lg-2" for="date">Date :</label>

		<div class="col-lg-3">
			<input type="text" class="form-control" name="date" id="date"
				   value="<?php echo date('Y-m-d H:i', $publication ? $publication->get_date() : time()); ?>"/>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-lg-2" for="comments">Commentaires :</label>

		<div class="col-lg-3">
			<textarea class="form-control" name="comments"
					  id="comments"><?php echo $publication ? htmlentities($publication->get_comments()) : ''; ?></textarea>
		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-3">
			<input type="submit" class="form-control" class="btn"/>
		</div>

</form>

<?php
require __DIR__.'/common/footer.php';
?>