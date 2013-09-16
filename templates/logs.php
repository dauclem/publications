<?php

if (!isset($revision_begins) || !is_array($revision_begins)) {
	$revision_begins = array();
}
if (!isset($publication)) {
	$publication = null;
}
if (!isset($current_project)) {
	/** @var \Interfaces\Shared\Project $project_shared */
	$project_shared  = $dic->get_object('project');
	$current_project = $project_shared->get_current_project();
}

/** @var \Interfaces\Shared\Config $config_shared */
$config_shared = $dic->get_object('config');

/** @var \Interfaces\Shared\VCS $vcs */
$vcs = $dic->get_object($config_shared->get_vcs_type());
if (!($vcs instanceof \Interfaces\Shared\VCS)) {
	exit ('VCS Type invalid : "'.$config_shared->get_vcs_type().'"');
}

/** @var \Interfaces\Shared\Tracker $tracker_shared */
$tracker_shared = $dic->get_object($config_shared->get_bug_tracker_type());
if (!($tracker_shared instanceof \Interfaces\Shared\Tracker)) {
	exit ('Bug tracker Type invalid : "'.$config_shared->get_bug_tracker_type().'"');
}

$all_rows = $vcs->get_all_rows($current_project, $revision_begins, $publication);

if ($publication) {
	$next_publication = $publication->get_next();
	$publications     = array($next_publication->create_row($all_rows));
} else {
	$publications = array();
	/** @var \Interfaces\Shared\Publication $publication_shared */
	$publication_shared = $dic->get_object('publication');
	foreach ($publication_shared->get_publications($current_project) as $this_publication) {
		$publications[] = $this_publication->create_row($all_rows);
	}
}

$all_rows = array_merge($all_rows, $publications);
usort($all_rows, function(\Interfaces\Object\Row $a, \Interfaces\Object\Row $b) {
	if ($a->get_date() == $b->get_date()) {
		return 0;
	}
	return $a->get_date() < $b->get_date() ? 1 : -1;
});


$row_count = count($all_rows);
/** @var \Interfaces\Object\Row $row */
foreach ($all_rows as $k => $row) {
	echo '<tr'.($row->get_publication() ? ' class="alert alert-info"' : '').'>';

	echo '<td>'.date('d/m/Y H:i', $row->get_date()).'</td>';

	echo '<td style="width:15%">';
	foreach ($row->get_revisions() as $project_id => $revisions) {
		/** @var \Interfaces\Object\Project $this_project */
		$this_project = $dic->get_object('project_object', $project_id);

		// Set links to revisions
		echo htmlentities($this_project->get_vcs_base().$this_project->get_vcs_path()).' : ';
		$revisions_display = explode(',', $revisions);
		foreach ($revisions_display as $k => $revision_display) {
			if (strpos($revision_display, '-')) {
				$revision_display      = explode('-', $revision_display);
				$revisions_display[$k] = '<a target="_blank" href="'.$vcs->get_revision_url($this_project, $revision_display[0]).'">'.htmlentities($revision_display[0]).'</a>'
										.'-<a target="_blank" href="'.$vcs->get_revision_url($this_project, $revision_display[1]).'">'.htmlentities($revision_display[1]).'</a>';
			} else {
				$revisions_display[$k] = '<a target="_blank" href="'.$vcs->get_revision_url($this_project, $revision_display).'">'.htmlentities($revision_display).'</a>';
			}
		}
		echo implode(', ', $revisions_display).'<br />';

		// TODO get real previous revision (instead of -1)
		$this_last_revision          = (int)preg_replace('#(^.*|-|,|\\s)('.$vcs->get_preg_revision().')$#U', '\\2', $revisions) - 1;
		$revision_begins[$project_id] = isset($revision_begins[$project_id]) ? min($revision_begins[$project_id], $this_last_revision) : $this_last_revision;
	}
	echo '</td>';

	echo '<td style="width:25%">';
	$trackers = array();
	foreach ($row->get_comments() as $project_id => $comments) {
		foreach ($comments as $comment) {
			$trackers = array_merge($trackers, $tracker_shared->get_trackers_from_message($comment));
		}
	}
	$trackers = array_unique($trackers, SORT_REGULAR);
	usort($trackers, function(\Interfaces\Object\Tracker $a, \Interfaces\Object\Tracker $b) {
		if ($a->get_type() == $b->get_type()) {
			return 0;
		}
		return $a->get_type() < $b->get_type() ? 1 : -1;
	});
	$current_type = '';
	/** @var \Interfaces\Object\Tracker $tracker */
	foreach ($trackers as $tracker) {
		if ($current_type != $tracker->get_type()) {
			if ($current_type) {
				echo '<br />';
			}

			$current_type = $tracker->get_type();
			echo '<strong>_____'.htmlentities($current_type).'_____</strong><br />';
		}

		echo '<a target="_blank" title="'.htmlentities($tracker->get_title()).'" href="'.$tracker->get_url().'">'
			 .htmlentities($tracker->get_id()).' : '.htmlentities($tracker->get_title())
			 .'</a><br />';
	}
	echo '</td>';

	echo '<td style="width:20%">';
	echo implode('<br />', array_unique($row->get_changelog()));
	echo '</td>';

	echo '<td style="width:40%">';
	$this_publication = $row->get_publication();
	if ($this_publication) {
		echo '<div class="well">';

		$recipients = implode(';', $current_project->get_recipients());
		$cc = implode(';', $config_shared->get_recipients());
		$subject = 'Publication de '.$current_project->get_name();
		$nl = urlencode("\n");
		$body = 'Bonjour'.$nl.$nl.'Une publication va avoir lieu contenant les changements suivants :'.$nl.$nl;
		$current_type = '';
		foreach ($trackers as $tracker) {
			if ($current_type != $tracker->get_type()) {
				if ($current_type) {
					$body .= $nl;
				}
				$current_type = $tracker->get_type();
				$body .= $current_type.' :'.$nl;
			}
			$body .= $tracker->get_id().' : '.$tracker->get_title().$nl;
		}
		$body .= $nl.'Bonne journée';
		echo '<a target="_blank" href="mailto:'.$recipients.'?cc='.$cc.'&subject='.htmlentities($subject).'&body='.htmlentities($body).'">
			<i class="glyphicon glyphicon-envelope"></i>
		</a>';

		echo ' <a href="'.$this_publication->get_url().'?action=edit"><i class="glyphicon glyphicon-pencil"></i></a>';
		echo ' <a href="'.$this_publication->get_url().'?action=remove"><i class="glyphicon glyphicon-remove"></i></a>';

		if ($this_publication->get_comments()) {
			echo '<p>'.nl2br(htmlspecialchars($this_publication->get_comments())).'<p>';
		}

		echo '</div>';
	}
	$i                 = 0;
	$row_comment_count = count($row->get_comments());
	foreach ($row->get_comments() as $project_id => $comments) {
		$i++;
		/** @var \Interfaces\Object\Project $this_project */
		$this_project = $dic->get_object('project_object', $project_id);
		echo '<strong>_____'.htmlentities($this_project->get_name()).'_____</strong><br />';
		$result_comment = htmlentities(implode("\n", array_map('trim', array_unique($comments))));
		$result_comment = preg_replace_callback($tracker_shared->get_tracker_id_pattern(), function($matches) {
			global $dic;
			/** @var \Interfaces\Shared\Config $config_shared */
			$config_shared = $dic->get_object('config');
			/** @var \Interfaces\Shared\Tracker $tracker_shared */
			$tracker_shared = $dic->get_object($config_shared->get_bug_tracker_type());
			/** @var \Interfaces\Object\Tracker $tracker */
			$tracker = $dic->get_object($tracker_shared->get_object_definition(), $matches[1]);
			return $tracker ? '<a target="_blank" href="'.$tracker->get_url().'">'.$tracker->get_id().'</a>' : '';
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
							   'last_publication_id' => $publication->get_id(),
						  ));
	echo '<tr id="see_more">
		<td colspan="5" onclick=\'return see_more('.$params.')\' class="alert alert-info text-center pointer">
			<strong>Voir la suite</strong>
		</td>
	</tr>';
}