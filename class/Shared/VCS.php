<?php

namespace Shared;

use Shared;
use Interfaces\Object\Row;

abstract class VCS extends Shared implements \Interfaces\Shared\VCS {
	/**
	 * Get list of logs
	 *
	 * @param \Interfaces\Object\Project $project
	 * @param int                        $revision_begin
	 * @param null|int                   $revision_end
	 * @return array[]
	 */
	abstract protected function getLogs(\Interfaces\Object\Project $project, $revision_begin, $revision_end);

	/**
	 * Get info on diff between 2 revisions
	 * array(
	 *    revisions => associative array with project_id => revisions (get all revisions in merge info of other projects)
	 *    changelog => list of string added into changelog file
	 * )
	 *
	 * @param \Interfaces\Object\Project $project
	 * @param int                        $revision_end
	 * @param int                        $revision_begin
	 * @return array
	 */
	abstract protected function getDiffRevisionsAndChangelog(\Interfaces\Object\Project $project, $revision_end, $revision_begin);

	/**
	 * Get list of logs from revision declaration (like merge infos)
	 *
	 * @param \Interfaces\Object\Project $project
	 * @param string                     $revisions
	 * @return array[]
	 */
	protected function getLogsFromRevisions(\Interfaces\Object\Project $project, $revisions) {
		$logs = array();

		$revisions = explode(',', $revisions);
		foreach ($revisions as $revision) {
			if (strpos($revision, '-')) {
				list($start, $end) = explode('-', $revision);
				$logs = array_merge($logs, $this->getLogs($project, $start, $end));
			} else {
				$logs = array_merge($logs, $this->getLogs($project, $revision, $revision));
			}
		}

		return $logs;
	}

	/**
	 * @param \Interfaces\Object\Project          $project
	 * @param int                                 $revision_begin
	 * @param \Interfaces\Object\Publication|null $publication
	 * @return Row[]
	 */
	protected function getAllRowsFromProject(\Interfaces\Object\Project $project, $revision_begin, $publication) {
		$logs   = $this->getLogs($project, $revision_begin, null);
		$logs[] = $previous_log = null;
		$rows   = $changelog = array();
		$i      = 0;
		foreach ($logs as $log) {
			$i++;
			if ($previous_log) {
				if ($publication && $previous_log['date'] < $publication->getDate()) {
					break;
				}

				// Revisions
				$revisions_and_changelog              = $log
					? $this->getDiffRevisionsAndChangelog($project, $log['rev'], $previous_log['rev'])
					: array('revisions' => array(), 'changelog' => array());
				$result_revisions        = array($project->getId() => $previous_log['rev']) + $revisions_and_changelog['revisions'];

				// Message
				$result_message = array();
				foreach ($revisions_and_changelog['revisions'] as $project_id => $revisions) {
					/** @var \Interfaces\Object\Project $this_project */
					$this_project = $this->dic->getObject('project_object', $project_id);
					$sub_logs     = $this->getLogsFromRevisions($this_project, $revisions);
					if (count($sub_logs)) {
						if (!isset($result_message[$project_id])) {
							$result_message[$project_id] = array();
						}
						foreach ($sub_logs as $sub_log) {
							$result_message[$project_id][] = $sub_log['msg'];
						}
					}
				}
				if (!count($result_message)) {
					$result_message[$project->getId()] = array($previous_log['msg']);
				}

				/** @var Row $row */
				$row = $this->dic->getObject('row_object');
				$row->setDate($previous_log['date']);
				$row->setRevisions($result_revisions);
				$row->setChangelog($revisions_and_changelog['changelog']);
				$row->setComments($result_message);
				$rows[] = $row;
			}

			$previous_log = $log;
		}

		return $rows;
	}

	/**
	 * Calculate revision begin for this project. Return -1 for all
	 *
	 * @param \Interfaces\Object\Project $project
	 * @param int[]                      $revision_begin
	 * @return int
	 */
	protected function getProjectBegin(\Interfaces\Object\Project $project, $revision_begin) {
		return is_array($revision_begin) && isset($revision_begin[$project->getId()]) ? $revision_begin[$project->getId()] : -1;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAllRows(\Interfaces\Object\Project $project, $revision_begins, $publication_limit) {
		$this_begin = $this->getProjectBegin($project, $revision_begins);
		$all_logs   = $this->getAllRowsFromProject($project, $this_begin, $publication_limit);
		foreach ($project->getExternals() as $external_project) {
			$all_logs = array_merge($all_logs, $this->getAllRows($external_project, $revision_begins, $publication_limit));
		}
		return $all_logs;
	}

	/**
	 * {@inheritDoc}
	 */
	public function optimizeRevisions($revisions) {
		$revisions_array = explode(',', $revisions);
		$revisions_final = $previous_rev = '';
		foreach ($revisions_array as $this_rev) {
			if ($previous_rev) {
				$previous_rev   = preg_replace('#^.*-('.$this->getPregRevision().')$#', '\\1', $previous_rev);
				$this_rev_begin = preg_replace('#^('.$this->getPregRevision().')-.*$#', '\\1', $this_rev);
				$revisions_final .= ($previous_rev == $this_rev_begin - 1 ? '-' : ',').$this_rev;
			} else {
				$revisions_final = $this_rev;
			}
			$previous_rev = $this_rev;
		}
		return preg_replace('#(^|,)('.$this->getPregRevision().')-('.$this->getPregRevision().'|-)+-('.$this->getPregRevision().')(,|$)#', '\\1\\2-\\3\\4', $revisions_final);
	}
}
