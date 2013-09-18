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

/** @var \Interfaces\Shared\Tracker $tracker_shared */
$tracker_shared = $dic->getObject('tracker');

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

	echo '<tr'.($this_publication ? ' class="alert alert-info"' : '').'>';

	echo '<td>'.date('d/m/Y H:i', $row->getDate()).'</td>';

	echo '<td style="width:15%">';
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

		// TODO get real previous revision (instead of -1)
		$this_last_revision          = (int)preg_replace('#(^.*|-|,|\\s)('.$vcs->getPregRevision().')$#U', '\\2', $revisions) - 1;
		$revision_begins[$project_id] = isset($revision_begins[$project_id]) ? min($revision_begins[$project_id], $this_last_revision) : $this_last_revision;
	}
	echo '</td>';

	echo '<td style="width:25%">';
	$trackers = array();
	foreach ($row->getComments() as $project_id => $comments) {
		foreach ($comments as $comment) {
			$trackers = array_merge($trackers, $tracker_shared->getTrackersFromMessage($comment));
		}
	}
	$trackers = array_unique($trackers, SORT_REGULAR);
	usort($trackers, function(\Interfaces\Object\Tracker $a, \Interfaces\Object\Tracker $b) {
		if ($a->getType() == $b->getType()) {
			return 0;
		}
		return $a->getType() < $b->getType() ? 1 : -1;
	});

	if ($this_publication) {
		$trackers_bak  = $trackers;
		$trackers      = $tracker_shared->filterTrackers($trackers, $current_project);
		/** @var \Interfaces\Object\Tracker[] $trackers_diff */
		$trackers_diff = array_diff($trackers_bak, $trackers);
	}

	$trackers_list = $trackers;
	require __DIR__.'/include/logs_trackers.php';

	if ($this_publication && $trackers_diff) {
		echo '<br /><button type="button" class="btn" data-toggle="collapse" data-target="#trackers'.$this_publication->getId().'">
			Voir les autres tâches
		</button>
		<div id="trackers'.$this_publication->getId().'" class="collapse">';
			$trackers_list = $trackers_diff;
			require __DIR__.'/include/logs_trackers.php';
		echo '</div>';
	}
	echo '</td>';

	echo '<td style="width:20%">';
	echo implode('<br />', array_unique($row->getChangelog()));
	echo '</td>';

	echo '<td style="width:40%">';
	if ($this_publication) {
		echo '<div class="well">';

		$recipients = implode(';', $current_project->getRecipients());
		$cc = implode(';', $config_shared->getRecipients());
		$subject = 'Publication de '.$current_project->getName();
		$nl = urlencode("\n");
		$body = 'Bonjour'.$nl.$nl.'Une publication va avoir lieu contenant les changements suivants :'.$nl.$nl;
		$current_type = '';
		foreach ($trackers as $tracker) {
			if ($current_type != $tracker->getType()) {
				if ($current_type) {
					$body .= $nl;
				}
				$current_type = $tracker->getType();
				$body .= $current_type.' :'.$nl;
			}
			$body .= $tracker->getId().' : '.$tracker->getTitle().$nl;
		}
		$body .= $nl.'Bonne journée';
		echo '<a target="_blank" href="mailto:'.$recipients.'?cc='.$cc.'&subject='.htmlentities($subject).'&body='.htmlentities($body).'">
			<i class="glyphicon glyphicon-envelope"></i>
		</a>';

		echo ' <a href="'.$this_publication->getUrl().'?action=edit"><i class="glyphicon glyphicon-pencil"></i></a>';
		echo ' <a href="'.$this_publication->getUrl().'?action=remove"><i class="glyphicon glyphicon-remove"></i></a>';

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
		$result_comment = preg_replace_callback($tracker_shared->getTrackerIdPattern(), function($matches) {
			global $dic;
			/** @var \Interfaces\Object\Tracker $tracker */
			$tracker = $dic->getObject('tracker_object', $matches[1]);
			return $tracker ? '<a target="_blank" href="'.$tracker->getUrl().'">'.$tracker->getId().'</a>' : '';
		}, $result_comment);
		echo nl2br($result_comment);
		if ($i < $row_comment_count) {
			echo '<br /><br />';
		}
	}
	echo '</td>';

	echo '</tr>';
}

if ($publication) {
	$params = json_encode(array(
							   'begins'              => $revision_begins,
							   'last_publication_id' => $publication->getId(),
						  ));
	echo '<tr id="see_more">
		<td colspan="5" onclick=\'return see_more('.$params.')\' class="alert alert-info text-center pointer">
			<strong>Voir la suite</strong>
		</td>
	</tr>';
}