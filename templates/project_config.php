<?php
require __DIR__.'/common/header.php';
require __DIR__.'/common/top_nav.php';
?>

<h1 class="text-center">
	<?php echo $current_project ? 'Configuration du projet : '.htmlentities($current_project->getName()) : 'Creation d\'un projet'; ?>
</h1>

<form action="" method="post" class="form-horizontal">
	<fieldset>
		<legend>Général</legend>

		<div class="form-group<?php echo isset($errors['name']) ? ' has-error' : ''; ?>">
			<label for="name" class="col-lg-2 control-label">Nom du projet</label>

			<div class="col-lg-10">
				<input type="text" class="form-control"
					   id="name" name="name"
					   value="<?php echo isset($name) ? htmlentities($name) : ''; ?>">
				<?php if (isset($errors['name'])) { ?>
					<span class="help-block"><?php echo $errors['name']; ?></span>
				<?php } ?>
			</div>
		</div>

		<div class="form-group<?php echo isset($errors['description']) ? ' has-error' : ''; ?>">
			<label for="description" class="col-lg-2 control-label">Description (texte libre - peut être du HTML)</label>

			<div class="col-lg-10">
				<textarea class="form-control"
					   id="description" name="description"
					   ><?php echo isset($description) ? htmlentities($description) : ''; ?></textarea>
				<?php if (isset($errors['description'])) { ?>
					<span class="help-block"><?php echo $errors['description']; ?></span>
				<?php } ?>
			</div>
		</div>

		<div class="form-group<?php echo isset($errors['bug_tracker_id']) ? ' has-error' : ''; ?>">
			<label for="bug_tracker_id" class="col-lg-2 control-label">Identifiant du projet sur <?php echo $config_shared->getBugTrackerType(); ?></label>

			<div class="col-lg-10">
				<input type="text" class="form-control"
					   id="bug_tracker_id" name="bug_tracker_id"
					   value="<?php echo isset($bug_tracker_id) ? htmlentities($bug_tracker_id) : ''; ?>">
				<?php if (isset($errors['bug_tracker_id'])) { ?>
					<span class="help-block"><?php echo $errors['bug_tracker_id']; ?></span>
				<?php } ?>
			</div>
		</div>

		<div class="form-group<?php echo isset($errors['visible']) ? ' has-error' : ''; ?>">
			<label for="visible" class="col-lg-2 control-label">Afficher le projet dans les onglets</label>

			<div class="col-lg-10">
				<input type="checkbox" class="form-control"
					   id="visible" name="visible"
					   <?php echo !empty($visible) ? 'checked="checked"' : ''; ?>">
				<?php if (isset($errors['visible'])) { ?>
					<span class="help-block"><?php echo $errors['visible']; ?></span>
				<?php } ?>
			</div>
		</div>

		<div class="form-group<?php echo isset($errors['has_prod']) ? ' has-error' : ''; ?>">
			<label for="has_prod" class="col-lg-2 control-label">Le projet peut avoir des publications</label>

			<div class="col-lg-10">
				<input type="checkbox" class="form-control"
					   id="has_prod" name="has_prod"
					   <?php echo !empty($has_prod) ? 'checked="checked"' : ''; ?>">
				<?php if (isset($errors['has_prod'])) { ?>
					<span class="help-block"><?php echo $errors['has_prod']; ?></span>
				<?php } ?>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Repository</legend>

		<div class="form-group<?php echo isset($errors['vcs_base']) ? ' has-error' : ''; ?>">
			<label for="vcs_base" class="col-lg-2 control-label">Base du repository</label>

			<div class="col-lg-10">
				<input type="text" class="form-control"
					   id="vcs_base" name="vcs_base"
					   value="<?php echo isset($vcs_base) ? htmlentities($vcs_base) : ''; ?>">
				<?php if (isset($errors['vcs_base'])) { ?>
					<span class="help-block">
						Ce qui se met après l'url de base des repository mais avant le path spécifique du projet. Il sera commun aux branches mergées avec ce projet.
						<?php echo '<br />'.$errors['vcs_base']; ?>
					</span>
				<?php } ?>
			</div>
		</div>

		<div class="form-group<?php echo isset($errors['vcs_path']) ? ' has-error' : ''; ?>">
			<label for="vcs_path" class="col-lg-2 control-label">Chemin du projet dans le repository</label>

			<div class="col-lg-10">
				<input type="text" class="form-control"
					   id="vcs_path" name="vcs_path"
					   value="<?php echo isset($vcs_path) ? htmlentities($vcs_path) : ''; ?>">
				<?php if (isset($errors['vcs_path'])) { ?>
					<span class="help-block">
						Sera rajouté après l'url de base du repository et le champ "Base du repository".
						<?php echo '<br />'.$errors['vcs_path']; ?>
					</span>
				<?php } ?>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Dépendances (external)</legend>

		<div class="form-group<?php echo isset($errors['externals']) ? ' has-error' : ''; ?>">
			<div class="col-lg-10 col-lg-offset-2">
				<?php
					if ($current_project) {
						foreach ($current_project->getExternals() as $external) {
							echo '<select name="externals[]" class="form-control">';
								echo '<option value="0">--</option>';
								foreach ($project_shared->getProjects() as $project) {
									echo '<option value="'.$project->getId().'"'.
										 ($project == $external ? 'selected="selected"' : '')
										 .'>'
										 .htmlentities($project->getName())
										.'</option>';
								}
							echo '</select><br />';
						}
					}

					echo '<select name="externals[]" id="new_external" class="form-control">';
						echo '<option value="0">--</option>';
						foreach ($project_shared->getProjects() as $project) {
							echo '<option value="'.$project->getId().'">'
								 .htmlentities($project->getName())
								 .'</option>';
						}
					echo '</select><br />';
				?>
				<a href="#" id="addExternal" class="btn btn-primary btn-sm">Ajouter</a><br />
				<?php if (isset($errors['externals'])) { ?>
					<span class="help-block">
						Liste des autres projets (branches) dont dépend ce projet.
						<?php echo '<br />'.$errors['externals']; ?>
					</span>
				<?php } ?>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Publications</legend>

		<div class="form-group<?php echo isset($errors['mail_subject']) ? ' has-error' : ''; ?>">
			<label for="mail_subject" class="col-lg-2 control-label">Sujet du mail de publication</label>

			<div class="col-lg-10">
				<input type="text" class="form-control" id="mail_subject" name="mail_subject"
					   value="<?php echo isset($mail_subject) ? htmlentities($mail_subject) : ''; ?>" />
				<span class="help-block">
					{PROJECT} sera remplacé par le nom du projet.
					<?php if (isset($errors['mail_subject'])) { ?>
						<?php echo '<br />'.$errors['mail_subject']; ?>
					<?php } ?>
				</span>
			</div>
		</div>

		<div class="form-group<?php echo isset($errors['mail_content']) ? ' has-error' : ''; ?>">
			<label for="mail_content" class="col-lg-2 control-label">Mail de publication</label>

			<div class="col-lg-10">
				<textarea class="form-control" id="mail_content" name="mail_content" rows="6"
					><?php echo isset($mail_content) ? htmlentities($mail_content) : ''; ?></textarea>
				<span class="help-block">
					Utilisée pour filter les tâches à envoyer par mail pour publication.<br />
					{PROJECT} sera remplacé par le nom du projet.<br />
					{ISSUES} sera remplacé par la liste des tâches.
					<?php if (isset($errors['mail_content'])) { ?>
						<?php echo '<br />'.$errors['mail_content']; ?>
					<?php } ?>
				</span>
			</div>
		</div>

		<div class="form-group<?php echo isset($errors['mail_post_publi_subject']) ? ' has-error' : ''; ?>">
			<label for="mail_post_publi_subject" class="col-lg-2 control-label">Sujet du mail post-publication</label>

			<div class="col-lg-10">
				<input type="text" class="form-control" id="mail_post_publi_subject" name="mail_post_publi_subject"
					   value="<?php echo isset($mail_post_publi_subject) ? htmlentities($mail_post_publi_subject) : ''; ?>" />
				<span class="help-block">
					{PROJECT} sera remplacé par le nom du projet.
					<?php if (isset($errors['mail_post_publi_subject'])) { ?>
						<?php echo '<br />'.$errors['mail_post_publi_subject']; ?>
					<?php } ?>
				</span>
			</div>
		</div>

		<div class="form-group<?php echo isset($errors['mail_post_publi_content']) ? ' has-error' : ''; ?>">
			<label for="mail_post_publi_content" class="col-lg-2 control-label">Mail post-publication</label>

			<div class="col-lg-10">
				<textarea class="form-control" id="mail_post_publi_content" name="mail_post_publi_content" rows="6"
					><?php echo isset($mail_post_publi_content) ? htmlentities($mail_post_publi_content) : ''; ?></textarea>
				<span class="help-block">
					{PROJECT} sera remplacé par le nom du projet.
					<?php if (isset($errors['mail_post_publi_content'])) { ?>
						<?php echo '<br />'.$errors['mail_post_publi_content']; ?>
					<?php } ?>
				</span>
			</div>
		</div>

		<div class="form-group<?php echo isset($errors['recipients']) ? ' has-error' : ''; ?>">
			<label class="col-lg-2 control-label">Destinataires</label>

			<div class="col-lg-10">
				<?php
					$this_recipients = !empty($recipients) ? $recipients : ($current_project ? $current_project->getRecipients() : array());
					foreach ($this_recipients as $recipient) {
						echo '<input type="email" class="form-control" name="recipients[]" value="'.htmlentities($recipient).'" /><br />';
					}
				?>
				<input type="email" class="form-control" name="recipients[]" id="new_recipient" value="" /><br />

				<a href="#" id="addRecipient" class="btn btn-primary btn-sm">Ajouter</a><br />
				<?php if (isset($errors['recipients'])) { ?>
					<span class="help-block">
						Liste des personnes destinées à recevoir le mail de publication.
						<?php echo '<br />'.$errors['recipients']; ?>
					</span>
				<?php } ?>
			</div>
		</div>
	</fieldset>

	<div class="text-center">
		<input type="submit" class="btn btn-primary" value="Enregistrer"/>
	</div>
</form>

<script type="text/javascript">
	$('#addExternal').click(function() {
		$('#new_external')
			.clone()
				.attr('id', '')
				.insertBefore($('#addExternal'))
				.after('<br />');
		return false;
	});

	$('#addRecipient').click(function() {
		$('#new_recipient')
			.clone()
				.val('')
				.attr('id', '')
				.insertBefore($('#addRecipient'))
				.after('<br />');
		return false;
	});
</script>

<?php
require __DIR__.'/common/footer.php';
?>