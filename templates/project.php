<?php
require __DIR__.'/common/header.php';
require __DIR__.'/common/top_nav.php';

/** @var \Interfaces\Shared\Project $project_shared */
$project_shared = $dic->getObject('project');
/** @var \Interfaces\Shared\Config $config_shared */
$config_shared = $dic->getObject('config');

$current_project = $project_shared->getCurrentProject();

echo '<div class="row" id="project_header_row"><h2>'.$current_project->getName().'</h2>';

$columns_hide         = explode(',', isset($_COOKIE['column_hide']) ? $_COOKIE['column_hide'] : '');
$columns_hide_classes = $columns_hide ? 'column_hide_'.implode(' column_hide_', $columns_hide) :'';
?>

	<div id="display_columns_block">
		<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
			Colonnes
			<span class="caret"></span>
		</button>
		<ul role="menu" class="dropdown-menu <?php echo $columns_hide_classes; ?>" id="display_columns">
			<li role="presentation" class="dropdown-header">Visibilité</li>
			<li>
				<a href="#" class="column_hide_action"><i class="glyphicon glyphicon-eye-open"></i> Révisions</a>
				<a href="#" class="column_show_action"><i class="glyphicon glyphicon-eye-close"></i> Révisions</a>
			</li>
			<li>
				<a href="#" class="column_hide_action"><i class="glyphicon glyphicon-eye-open"></i> Jira</a>
				<a href="#" class="column_show_action"><i class="glyphicon glyphicon-eye-close"></i> Jira</a>
			</li>
			<li>
				<a href="#" class="column_hide_action"><i class="glyphicon glyphicon-eye-open"></i> Changelog</a>
				<a href="#" class="column_show_action"><i class="glyphicon glyphicon-eye-close"></i> Changelog</a>
			</li>
			<li>
				<a href="#" class="column_hide_action"><i class="glyphicon glyphicon-eye-open"></i> Libellés</a>
				<a href="#" class="column_show_action"><i class="glyphicon glyphicon-eye-close"></i> Libellés</a>
			</li>
		</ul>
	</div>

	<?php
	if ($current_project->hasProd()) {
		echo '<a id="show_prod_only" href="#" onclick="$(\'tr\').not(\'.alert\').hide();$(this).hide();$(\'#show_all\').show();return false;">';
		echo '<i class="glyphicon  glyphicon-resize-small"></i> Ne voir que les publications et notes';
		echo '</a>';

		echo '<a id="show_all" href="#" onclick="$(\'tr\').not(\'.alert\').show();$(this).hide();$(\'#show_prod_only\').show();return false;">';
		echo '<i class="glyphicon  glyphicon-resize-full"></i> Voir tout';
		echo '</a>';

		echo '<a id="show_all_rev" href="'.$current_project->getUrlSeeAll().'">Afficher toutes les révisions (très long)</a>';
	}
	?>

</div>

<?php
if ($current_project->hasProd()) {
	echo '<div id="add_prod_publication_link">';
	echo '<a href="'.$current_project->getUrlAddPublication().'">+ Publication</a>';
	echo '</div>';

	if ($current_project->getDescription()) {
		echo '<pre>'.$current_project->getDescription().'</pre>';
	}

	$recipients = $current_project->getRecipients();
	if ($recipients) {
		echo '<pre>Destinataires des mails de publication : '.implode(', ', $recipients).'</pre>';
	}
}
?>

<div id="rows_content" class="<?php echo $columns_hide_classes; ?>">
	<div>
		<div>Date</div>
		<div>Révisions</div>
		<div>Jira</div>
		<div>Changelog</div>
		<div>Libellés</div>
	</div>

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

</div>

<script type="text/javascript">
	function see_more(params) {
		$('#see_more strong').addClass('loader').html('');

		params.r = Math.random();
		$.ajax('<?php echo $current_project->getUrlSeeMore(); ?>', {
			data: params,
			success: function(data, textStatus, jqXHR) {
				$('#see_more').remove();
				$('#rows_content').append(data);
				$('#see_more').insertAfter($('#rows_content'));
			}
		});
		return false;
	}

	$(function() {
		$('#see_more').insertAfter($('#rows_content'));

		$('.column_hide_action').click(function() {
			var index = $(this).parent('li:first').index() + 1;
			$('#rows_content, #display_columns').addClass('column_hide_'+index);

			var column_hide_indexes = $.cookie('column_hide');
			column_hide_indexes = column_hide_indexes ? column_hide_indexes.split(',') : [];
			var column_hide_indexes_length = column_hide_indexes.length;
			var found = false;
			for (var i = 0; i < column_hide_indexes_length; i++) {
				if (column_hide_indexes[i] == index) {
					found = true;
					break;
				}
			}
			if (!found) {
				column_hide_indexes.push(index);
				$.cookie('column_hide', column_hide_indexes, { expires: 365 });
			}

			return false;
		});
		$('.column_show_action').click(function() {
			var index = $(this).parent('li:first').index() + 1;
			$('#rows_content, #display_columns').removeClass('column_hide_'+index);

			var column_hide_indexes = $.cookie('column_hide');
			column_hide_indexes = column_hide_indexes ? column_hide_indexes.split(',') : [];
			var column_hide_indexes_length = column_hide_indexes.length;
			var column_hide_indexes_new = [];
			for (var i = 0; i < column_hide_indexes_length; i++) {
				if (column_hide_indexes[i] != index) {
					column_hide_indexes_new.push(column_hide_indexes[i]);
				}
			}
			$.cookie('column_hide', column_hide_indexes_new, { expires: 365 });

			return false;
		});
	});
</script>

<?php
require __DIR__.'/common/footer.php';
?>
