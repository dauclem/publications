<?php

require __DIR__.'/../../include/setup.php';

/** @var \Interfaces\Shared\Project $project_shared */
$project_shared  = $dic->getObject('project');
$current_project = $project_shared->getCurrentProject();

/** @var \Interfaces\Shared\Publication $publication_shared */
$publication_shared = $dic->getObject('publication');
$publication_temp   = $publication_shared->getPublicationTemp($current_project);
if ($publication_temp) {
	$publication_temp->setTemp(false);
	$this_publication = $publication_temp;
} else {
	$this_publication = $publication_shared->create($current_project, false, time(), '');
}

if (!empty($_GET['email'])) {
	$this_publication->prepare_mail(array(), true)->send();
	$email_infos = $this_publication->get_email_infos(array(), true);
	$mail = new PHPMailer;
	$mail->Subject = $email_infos['subject'];
	$mail->Body = $email_infos['body'];
	$mail->setFrom($email_infos['sender'], 'MesDiscussions');
	$mail->addReplyTo($email_infos['sender'], 'MesDiscussions');

	foreach ($email_infos['recipients'] as $recipient) {
		$mail->addAddress($recipient);
	}
	foreach ($email_infos['cc'] as $recipient) {
		$mail->addCC($recipient);
	}

	$mail->send();
}
