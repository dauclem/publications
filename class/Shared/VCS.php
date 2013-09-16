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
	abstract protected function get_logs(\Interfaces\Object\Project $project, $revision_begin, $revision_end);

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
	abstract protected function get_diff_revisions_and_changelog($project, $revision_end, $revision_begin);

	/**
	 * Get list of logs from revision declaration (like merge infos)
	 *
	 * @param \Interfaces\Object\Project $project
	 * @param string                     $revisions
	 * @return array[]
	 */
	protected function get_logs_from_revisions($project, $revisions) {
		$logs = array();

		$revisions = explode(',', $revisions);
		foreach ($revisions as $revision) {
			if (strpos($revision, '-')) {
				list($start, $end) = explode('-', $revision);
				$logs = array_merge($logs, $this->get_logs($project, $start, $end));
			} else {
				$logs = array_merge($logs, $this->get_logs($project, $revision, $revision));
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
	protected function get_all_rows_from_project(\Interfaces\Object\Project $project, $revision_begin, $publication) {
		$logs   = $this->get_logs($project, $revision_begin, null);
		$logs[] = $previous_log = null;
		$rows   = $changelog = array();
		$i      = 0;
		foreach ($logs as $log) {
			$i++;
			if ($previous_log) {
				if ($publication && $previous_log['date'] < $publication->get_date()) {
					break;
				}

				// Revisions
				$revisions_and_changelog              = $log
					? $this->get_diff_revisions_and_changelog($project, $log['rev'], $previous_log['rev'])
					: array('revisions' => array(), 'changelog' => array());
				$result_revisions        = array($project->get_id() => $previous_log['rev']) + $revisions_and_changelog['revisions'];

				// Message
				$result_message = array();
				foreach ($revisions_and_changelog['revisions'] as $project_id => $revisions) {
					/** @var \Interfaces\Object\Project $this_project */
					$this_project = $this->dic->get_object('project_object', $project_id);
					$sub_logs     = $this->get_logs_from_revisions($this_project, $revisions);
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
					$result_message[$project->get_id()] = array($previous_log['msg']);
				}

				/** @var Row $row */
				$row = $this->dic->get_object('row_object');
				$row->set_date($previous_log['date']);
				$row->set_revisions($result_revisions);
				$row->set_changelog($revisions_and_changelog['changelog']);
				$row->set_comments($result_message);
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
	protected function get_project_begin(\Interfaces\Object\Project $project, $revision_begin) {
		return is_array($revision_begin) && isset($revision_begin[$project->get_id()]) ? $revision_begin[$project->get_id()] : -1;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_all_rows(\Interfaces\Object\Project $project, $revision_begins, $publication_limit) {
		$this_begin = $this->get_project_begin($project, $revision_begins);
		$all_logs   = $this->get_all_rows_from_project($project, $this_begin, $publication_limit);
		foreach ($project->get_externals() as $external_project) {
			$all_logs = array_merge($all_logs, $this->get_all_rows($external_project, $revision_begins, $publication_limit));
		}
		return $all_logs;
	}

	/**
	 * {@inheritDoc}
	 */
	public function optimize_revisions($revisions) {
		$revisions_array = explode(',', $revisions);
		$revisions_final = $previous_rev = '';
		foreach ($revisions_array as $this_rev) {
			if ($previous_rev) {
				$previous_rev   = preg_replace('#^.*-('.$this->get_preg_revision().')$#', '\\1', $previous_rev);
				$this_rev_begin = preg_replace('#^('.$this->get_preg_revision().')-.*$#', '\\1', $this_rev);
				$revisions_final .= ($previous_rev == $this_rev_begin - 1 ? '-' : ',').$this_rev;
			} else {
				$revisions_final = $this_rev;
			}
			$previous_rev = $this_rev;
		}
		return preg_replace('#(^|,)('.$this->get_preg_revision().')-('.$this->get_preg_revision().'|-)+-('.$this->get_preg_revision().')(,|$)#', '\\1\\2-\\3\\4', $revisions_final);
	}
}
