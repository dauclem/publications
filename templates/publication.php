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
				$project_shared = $dic->getObject('project');
				$current_project = $project_shared->getCurrentProject();
				foreach ($project_shared->getProjects() as $project) {
					if ($project->hasProd()) {
						echo '<option value="'.$project->getId().'"'.($current_project == $project ? ' selected="selected"' : '').'>'
							 .htmlentities($project->getName())
							 .'</option>';
					}
				}
				?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3" for="is_temp">En préparation :</label>

		<div class="col-lg-6">
			<input type="checkbox" class="form-control" name="is_temp" id="is_temp"
				   <?php echo $publication && $publication->isTemp() ? 'checked="checked"' : ''; ?>/>
		</div>
	</div>
	<div class="form-group" id="date_group">
		<label class="control-label col-lg-3" for="date">Date :</label>

		<div class="col-lg-6">
			<input type="text" class="form-control" name="date" id="date"
				   value="<?php echo date('Y-m-d H:i', $publication ? $publication->getDate() : time()); ?>"/>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-lg-3" for="comments">Commentaires :</label>

		<div class="col-lg-6">
			<textarea class="form-control" name="comments"
					  id="comments"><?php echo $publication ? htmlentities($publication->getComments()) : ''; ?></textarea>
		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-6">
			<input type="submit" class="form-control btn btn-primary"/>
		</div>
	</div>
</form>

<script type="text/javascript">
	$(function() {
		function check_is_temp() {
			if ($('#is_temp').attr('checked')) {
				$('#date_group').hide();
			} else {
				$('#date_group').show();
			}
		}

		check_is_temp();
		$('#is_temp').change(check_is_temp);
	});
</script>

<?php
require __DIR__.'/common/footer.php';
?>