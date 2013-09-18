<?php
require __DIR__.'/common/header.php';
require __DIR__.'/common/top_nav.php';

/** @var \Interfaces\Shared\Project $project_shared */
$project_shared = $dic->getObject('project');
/** @var \Interfaces\Shared\Config $config_shared */
$config_shared = $dic->getObject('config');

$current_project = $project_shared->getCurrentProject();

echo '<h2>'.$current_project->getName().'</h2>';
if ($current_project->hasProd()) {
	echo '<div id="add_prod_publication_link">';
		echo '<a href="'.$current_project->getUrlAddPublication().'">+ Publication</a>';
	echo '</div>';

	echo '<a id="show_prod_only" href="#" onclick="$(\'tr\').not(\'.alert-info\').hide();$(this).hide();$(\'#show_all\').show();return false;">';
		echo '<i class="glyphicon  glyphicon-resize-small"></i> Ne voir que les publications';
	echo '</a>';

	echo '<a id="show_all" href="#" onclick="$(\'tr\').not(\'.alert-info\').show();$(this).hide();$(\'#show_prod_only\').show();return false;">';
		echo '<i class="glyphicon  glyphicon-resize-full"></i> Voir tout';
	echo '</a>';

	echo '<a id="show_all_rev" href="'.$current_project->getUrlSeeAll().'">Afficher toutes les révisions (très long)</a>';

	if ($current_project->getDescription()) {
		echo '<pre>'.$current_project->getDescription().'</pre>';
	}

	$recipients = $current_project->getRecipients();
	if ($recipients) {
		echo '<pre>Destinataires des mails de publication : '.implode(', ', $recipients).'</pre>';
	}
}

?>

<table class="table table-bordered table-hover">
	<thead>
		<tr>
			<th>Date</th>
			<th>Révisions</th>
			<th>Jira</th>
			<th>Changelog</th>
			<th>Libellés</th>
		</tr>
	</thead>
	<tbody>

<?php

if (isset($force_publication)) {
	$publication = $force_publication instanceof \Interfaces\Object\Publication ? $force_publication : null;
} else {
	/** @var \Interfaces\Shared\Publication $publication_shared */
	$publication_shared = $dic->getObject('publication');

	$publication = $publication_shared->getLastPublication($current_project);
	$publication = $publication ? $publication->getPrevious() : null;
}

require __DIR__.'/logs.php';

?>

	</tbody>
</table>

<script type="text/javascript">
	function see_more(params) {
		$("#see_more strong").addClass("loader").html("");

		params.r = Math.random();
		$.ajax("<?php echo $current_project->getUrlSeeMore(); ?>", {
			data: params,
			success: function(data, textStatus, jqXHR) {
				$("#see_more").remove();
				$(".table tbody").append(data);
			}
		});
		return false;
	}
</script>

<?php
require __DIR__.'/common/footer.php';
?>
