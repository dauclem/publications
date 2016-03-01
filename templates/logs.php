<?php

if (!isset($revision_begins) || !is_array($revision_begins)) {
	$revision_begins = array();
}
if (!isset($publication)) {
	$publication = null;
}
if (!isset($current_project)) {
	/** @var \Interfaces\Shared\Project $project_shared */
	$project_shared  = $dic->getObject('project');
	$current_project = $project_shared->getCurrentProject();
}

/** @var \Interfaces\Shared\Config $config_shared */
$config_shared = $dic->getObject('config');

/** @var \Interfaces\Shared\VCS $vcs */
$vcs = $dic->getObject('vcs');

/** @var \Interfaces\Shared\Issue $issue_shared */
$issue_shared = $dic->getObject('issue');

$all_rows = $vcs->getAllRows($current_project, $revision_begins, $publication);

if ($publication) {
	$next_publication = $publication->getNext();
	$publications     = array($next_publication->createRow($all_rows));
} else {
	$publications = array();
	/** @var \Interfaces\Shared\Publication $publication_shared */
	$publication_shared = $dic->getObject('publication');
	foreach ($publication_shared->getPublications($current_project) as $this_publication) {
		$publications[] = $this_publication->createRow($all_rows);
	}
}

$all_rows = array_merge($all_rows, $publications);
usort($all_rows, function(\Interfaces\Object\Row $a, \Interfaces\Object\Row $b) {
	if ($a->getDate() == $b->getDate()) {
		return 0;
	}
	return $a->getDate() < $b->getDate() ? 1 : -1;
});


$row_count = count($all_rows);
/** @var \Interfaces\Object\Row $row */
foreach ($all_rows as $k => $row) {
	$related_object   = $row->getRelatedObject();
	$this_publication = $related_object instanceof \Interfaces\Object\Publication ? $related_object : null;

	$class_alert_type = $this_publication ? ($this_publication->isTemp() ? 'warning' : 'success') : '';
	echo '<div'.($class_alert_type ? ' class="alert alert-'.$class_alert_type.'"' : '').'>';

	echo '<div>';
		echo $this_publication && $this_publication->isTemp() ? 'En préparation' : date('d/m/Y H:i', $row->getDate());
	echo '</div>';

	echo '<div>';
	foreach ($row->getRevisions() as $project_id => $revisions) {
		/** @var \Interfaces\Object\Project $this_project */
		$this_project = $dic->getObject('project_object', $project_id);

		// Set links to revisions
		echo htmlentities($this_project->getVcsBase().$this_project->getVcsPath()).' : ';
		$revisions_display = explode(',', $revisions);
		foreach ($revisions_display as $k => $revision_display) {
			if (strpos($revision_display, '-')) {
				$revision_display      = explode('-', $revision_display);
				$revisions_display[$k] = '<a target="_blank" href="'.$vcs->getRevisionUrl($this_project, $revision_display[0]).'">'.htmlentities($revision_display[0]).'</a>'
										.'-<a target="_blank" href="'.$vcs->getRevisionUrl($this_project, $revision_display[1]).'">'.htmlentities($revision_display[1]).'</a>';
			} else {
				$revisions_display[$k] = '<a target="_blank" href="'.$vcs->getRevisionUrl($this_project, $revision_display).'">'.htmlentities($revision_display).'</a>';
			}
		}
		echo implode(', ', $revisions_display).'<br />';

		$revision_begins[$project_id] = preg_replace('#(^.*|-|,|\\s)('.$vcs->getPregRevision().')$#U', '\\2', $revisions);
	}
	echo '</div>';

	echo '<div>';
	$issues = $row->getIssues();
	if ($this_publication) {
		$issues_bak  = $issues;
		$issues      = $issue_shared->filterIssues($issues, $current_project);
		/** @var \Interfaces\Object\Issue[] $issues_diff */
		$issues_diff = array_diff($issues_bak, $issues);
	}

	$issues_list = $issues;
	require __DIR__.'/include/logs_issues.php';

	if ($this_publication && $issues_diff) {
		echo '<br /><button type="button" class="btn" data-toggle="collapse" data-target="#issues'.$this_publication->getId().'">
			Voir les autres tâches
		</button>
		<div id="issues'.$this_publication->getId().'" class="collapse">';
			$issues_list = $issues_diff;
			require __DIR__.'/include/logs_issues.php';
		echo '</div>';
	}
	echo '</div>';

	echo '<div>';
	echo implode('<br />', array_map('htmlentities', array_unique($row->getChangelog())));
	echo '</div>';

	echo '<div>';
	if ($this_publication) {
		echo '<div class="well">';

		$email_infos = $this_publication->get_email_infos($issues);
		echo '<a target="_blank" href="mailto:'.$email_infos['recipients']
			 							.'?cc='.urlencode($email_infos['cc'])
			 							.'&subject='.htmlentities(urlencode($email_infos['subject']))
			 							.'&body='.htmlentities(urlencode($email_infos['body'])).'">
			<i class="glyphicon glyphicon-envelope"></i>
		</a>';

		echo ' <a href="'.$this_publication->getUrl().'?action=edit"><i class="glyphicon glyphicon-pencil"></i></a>';
		echo ' <a href="'.$this_publication->getUrl().'?action=remove"><i class="glyphicon glyphicon-remove"></i></a>';

		if ($this_publication->isTemp()) {
			echo ' <a href="'.$this_publication->getUrl().'?action=notemp"><i class="glyphicon glyphicon-ok"></i></a>';
		}

		if ($this_publication->getComments()) {
			echo '<p>'.nl2br(htmlspecialchars($this_publication->getComments())).'<p>';
		}

		echo '</div>';
	}
	$i                 = 0;
	$row_comment_count = count($row->getComments());
	foreach ($row->getComments() as $project_id => $comments) {
		$i++;
		/** @var \Interfaces\Object\Project $this_project */
		$this_project = $dic->getObject('project_object', $project_id);
		echo '<strong>_____'.htmlentities($this_project->getName()).'_____</strong><br />';
		$result_comment = htmlentities(implode("\n", array_map('trim', array_unique($comments))));
		$result_comment = preg_replace_callback($issue_shared->getIssueIdPattern(), function($matches) {
			global $dic;
			/** @var \Interfaces\Object\Issue $issue */
			$issue = $dic->getObject('issue_object', $matches[1]);
			return $issue ? '<a target="_blank" href="'.$issue->getUrl().'">'.$issue->getId().'</a>' : '';
		}, $result_comment);
		echo nl2br($result_comment);
		if ($i < $row_comment_count) {
			echo '<br /><br />';
		}
	}
	echo '</div>';

	echo '</div>';
}

if ($publication) {
	$params = json_encode(array(
							   'begins'              => $revision_begins,
							   'last_publication_id' => $publication->getId(),
						  ));
	echo '<div class="alert alert-info" id="see_more">
		<div onclick=\'return see_more('.$params.')\' class="text-center pointer">
			<strong>Voir la suite</strong>
		</div>
	</div>';
}