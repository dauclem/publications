<?php
require __DIR__.'/common/header.php';
require __DIR__.'/common/top_nav.php';
?>

<h1 class="text-center">Configuration générale</h1>

<form action="" method="post" class="form-horizontal">
	<fieldset>
		<legend>VCS (Version Control System)</legend>
		<div class="form-group<?php echo isset($errors['VCS_type']) ? ' has-error' : ''; ?>">
			<label for="VCS_type" class="col-lg-2 control-label">Type de VCS</label>

			<div class="col-lg-10">
				<select class="form-control" id="VCS_type" name="VCS_type">
					<option
						value="subversion"<?php echo isset($VCS_type) && $VCS_type == 'subversion' ? ' selected=selected' : ''; ?>>
						SVN (Subversion)
					</option>
					<option value="git"
							class="disabled"<?php echo isset($VCS_type) && $VCS_type == 'git' ? ' selected=selected' : ''; ?>>
						Git
					</option>
				</select>
				<?php if (isset($errors['VCS_type'])) { ?>
					<span class="help-block"><?php echo $errors['VCS_type']; ?></span>
				<?php } ?>
			</div>
		</div>
		<div class="form-group<?php echo isset($errors['VCS_url']) ? ' has-error' : ''; ?>">
			<label for="VCS_url" class="col-lg-2 control-label">Url de base du repository</label>

			<div class="col-lg-10">
				<input type="url" class="form-control"
					   id="VCS_url" name="VCS_url" placeholder="http://"
					   value="<?php echo isset($VCS_url) ? $VCS_url : ''; ?>">
				<?php if (isset($errors['VCS_url'])) { ?>
					<span class="help-block"><?php echo $errors['VCS_url']; ?></span>
				<?php } ?>
			</div>
		</div>
		<div class="form-group<?php echo isset($errors['VCS_user']) ? ' has-error' : ''; ?>">
			<label for="VCS_user" class="col-lg-2 control-label">Nom d'utilisateur</label>

			<div class="col-lg-10">
				<input type="text" class="form-control"
					   id="VCS_user" name="VCS_user" value="<?php echo isset($VCS_user) ? $VCS_user : ''; ?>">
				<?php if (isset($errors['VCS_user'])) { ?>
					<span class="help-block"><?php echo $errors['VCS_user']; ?></span>
				<?php } ?>
			</div>
		</div>
		<div class="form-group<?php echo isset($errors['VCS_password']) ? ' has-error' : ''; ?>">
			<label for="VCS_password" class="col-lg-2 control-label">Mot de passe</label>

			<div class="col-lg-10">
				<input type="password" class="form-control"
					   id="VCS_password" name="VCS_password"
					   <?php echo $config_shared->getVcsPassword() ? 'placeholder="inchangé"' : ''; ?>
					   value="<?php echo isset($VCS_password) ? $VCS_password : ''; ?>">
				<?php if (isset($errors['VCS_password'])) { ?>
					<span class="help-block"><?php echo $errors['VCS_password']; ?></span>
				<?php } ?>
			</div>
		</div>
		<div class="form-group<?php echo isset($errors['VCS_web_url']) ? ' has-error' : ''; ?>">
			<label for="VCS_web_url" class="col-lg-2 control-label">Url de base du site web du repository
				(facultatif)</label>

			<div class="col-lg-10">
				<input type="url" class="form-control"
					   id="VCS_web_url" name="VCS_web_url" placeholder="http://"
					   value="<?php echo isset($VCS_web_url) ? $VCS_web_url : ''; ?>">
				<?php if (isset($errors['VCS_web_url'])) { ?>
					<span class="help-block"><?php echo $errors['VCS_web_url']; ?></span>
				<?php } ?>
			</div>
		</div>
		<div class="form-group<?php echo isset($errors['changelog_path']) ? ' has-error' : ''; ?>">
			<label for="changelog_path" class="col-lg-2 control-label">Chemin relatif du fichier changelog</label>

			<div class="col-lg-10">
				<input type="text" class="form-control"
					   id="changelog_path" name="changelog_path"
					   value="<?php echo isset($changelog_path) ? $changelog_path : ''; ?>">
				<?php if (isset($errors['changelog_path'])) { ?>
					<span class="help-block"><?php echo $errors['changelog_path']; ?></span>
				<?php } ?>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Bug tracker</legend>
		<div class="form-group<?php echo isset($errors['bug_tracker_type']) ? ' has-error' : ''; ?>">
			<label for="bug_tracker_type" class="col-lg-2 control-label">Type de bug tracker</label>

			<div class="col-lg-10">
				<select class="form-control" id="bug_tracker_type" name="bug_tracker_type">
					<option
						value="jira"<?php echo isset($bug_tracker_type) && $bug_tracker_type == 'jira' ? ' selected=selected' : ''; ?>>
						JIRA
					</option>
				</select>
				<?php if (isset($errors['bug_tracker_type'])) { ?>
					<span class="help-block"><?php echo $errors['bug_tracker_type']; ?></span>
				<?php } ?>
			</div>
		</div>
		<div class="form-group<?php echo isset($errors['bug_tracker_url']) ? ' has-error' : ''; ?>">
			<label for="bug_tracker_url" class="col-lg-2 control-label">Url de base de bug tracker</label>

			<div class="col-lg-10">
				<input type="url" class="form-control"
					   id="bug_tracker_url" name="bug_tracker_url" placeholder="http://"
					   value="<?php echo isset($bug_tracker_url) ? htmlentities($bug_tracker_url) : ''; ?>">
				<?php if (isset($errors['bug_tracker_url'])) { ?>
					<span class="help-block"><?php echo $errors['bug_tracker_url']; ?></span>
				<?php } ?>
			</div>
		</div>
		<div class="form-group<?php echo isset($errors['bug_tracker_user']) ? ' has-error' : ''; ?>">
			<label for="bug_tracker_user" class="col-lg-2 control-label">Nom d'utilisateur</label>

			<div class="col-lg-10">
				<input type="text" class="form-control"
					   id="bug_tracker_user" name="bug_tracker_user"
					   value="<?php echo isset($bug_tracker_user) ? htmlentities($bug_tracker_user) : ''; ?>">
				<?php if (isset($errors['bug_tracker_user'])) { ?>
					<span class="help-block"><?php echo $errors['bug_tracker_user']; ?></span>
				<?php } ?>
			</div>
		</div>
		<div class="form-group<?php echo isset($errors['bug_tracker_password']) ? ' has-error' : ''; ?>">
			<label for="bug_tracker_password" class="col-lg-2 control-label">Mot de passe</label>

			<div class="col-lg-10">
				<input type="password" class="form-control"
					   id="bug_tracker_password" name="bug_tracker_password"
					   <?php echo $config_shared->getBugTrackerPassword() ? 'placeholder="inchangé"' : ''; ?>
					   value="<?php echo isset($bug_tracker_password) ? htmlentities($bug_tracker_password) : ''; ?>">
				<?php if (isset($errors['bug_tracker_password'])) { ?>
					<span class="help-block"><?php echo $errors['bug_tracker_password']; ?></span>
				<?php } ?>
			</div>
		</div>
		<div class="form-group<?php echo isset($errors['bug_tracker_query']) ? ' has-error' : ''; ?>">
			<label for="bug_tracker_query" class="col-lg-2 control-label">Début de la requête <?php echo $config_shared->getBugTrackerType(); ?></label>

			<div class="col-lg-10">
				<input type="text" class="form-control"
					   id="bug_tracker_query" name="bug_tracker_query"
					   value="<?php echo isset($bug_tracker_query) ? htmlentities($bug_tracker_query) : ''; ?>">
				<span class="help-block">
					Utilisée pour filter les tâches à envoyer par mail pour publication.<br />
					{PROJECT_ID} sera remplacé par l'identifiant <?php echo $config_shared->getBugTrackerType(); ?> spécifié dans la configuration du projet.
					<?php if (isset($errors['bug_tracker_query'])) { ?>
						<?php echo '<br />'.$errors['bug_tracker_query']; ?>
					<?php } ?>
				</span>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend>Publications</legend>

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

		<div class="form-group<?php echo isset($errors['recipients']) ? ' has-error' : ''; ?>">
			<label class="col-lg-2 control-label">Destinataires</label>

			<div class="col-lg-10">
				<?php
					$this_recipients = !empty($recipients) ? $recipients : $config_shared->getRecipients();
					foreach ($this_recipients as $recipient) {
						echo '<input type="email" class="form-control" name="recipients[]" value="'.htmlentities($recipient).'" /><br />';
					}
				?>
				<input type="email" class="form-control" name="recipients[]" id="new_recipient" value="" /><br />

				<a href="#" id="addRecipient" class="btn btn-primary btn-sm">Ajouter</a><br />
				<span class="help-block">
					Liste des personnes en copie de tous les mails de publication.
					<?php if (isset($errors['recipients'])) { ?>
						<?php echo '<br />'.$errors['recipients']; ?>
					<?php } ?>
				</span>
			</div>
		</div>
	</fieldset>

	<div class="text-center">
		<input type="submit" class="btn btn-primary" value="Enregistrer"/>
	</div>
</form>

<script type="text/javascript">
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
