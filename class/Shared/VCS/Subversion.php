<?php

namespace Shared\VCS;

use Shared\VCS;
use Interfaces\Shared\Config;
use Interfaces\Object\Project;

class Subversion extends VCS implements \Interfaces\Shared\VCS\Subversion {
	/**
	 * {@inheritDoc}
	 */
	public function get_dependencies_list() {
		return array_merge(parent::get_dependencies_list(), array(
																 'config',
																 'project',
															));
	}

	public function initialize() {
		/** @var Config $config_shared */
		$config_shared = $this->dependence_objects['config'];

		svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, $config_shared->get_vcs_user());
		svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $config_shared->get_vcs_password());
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_preg_revision() {
		return '[0-9]+';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_logs(Project $project, $revision_begin, $revision_end) {
		$key    = md5($project->get_id().'|'.$revision_begin.'|'.$revision_end);
		$result = apc_fetch($key);
		if ($result === false) {
			$result = array();
			foreach (svn_log($project->get_vcs_repository(), $revision_begin, $revision_end) as $log) {
				$result[] = array(
					'date' => strtotime($log['date']),
					'rev'  => $log['rev'],
					'msg'  => $log['msg'],
				);
			}

			apc_store($key, $result, 0);
		} elseif ($revision_begin == -1) {
			if (isset($result[0])) {
				$revision_end = $result[0]['rev'];
			};

			$result_delta = array();
			$logs         = svn_log($project->get_vcs_repository(), $revision_begin, $revision_end);
			if ($logs) {
				foreach ($logs as $log) {
					$result_delta[] = array(
						'date' => strtotime($log['date']),
						'rev'  => $log['rev'],
						'msg'  => $log['msg'],
					);
				}
			}
			array_pop($result_delta);

			$result = array_merge($result_delta, $result);
			apc_store($key, $result, 0);
		}

		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_diff_revisions_and_changelog($project, $revision_end, $revision_begin) {
		$key    = md5($project->get_id().'|'.$revision_end.'|'.$revision_begin);
		$result = apc_fetch($key);
		if ($result === false) {
			$repository = $project->get_vcs_repository();
			/** @var Config $config_shared */
			$config_shared  = $this->dependence_objects['config'];
			$changelog_preg = preg_quote($config_shared->get_changelog_path(), '#');

			$diff = @svn_diff($repository, $revision_end, $repository, $revision_begin);
			$diff = stream_get_contents($diff[0]);

			$result = array(
				'revisions' => array(),
				'changelog' => array(),
			);

			preg_match_all('#Property changes on: \\.[\\s_]+Modified: svn:mergeinfo\\s+Merged\\s+([a-zA-Z0-9\\.\\-\\/]+):r([0-9-,]+)\\s#U', $diff, $matches);
			if (isset($matches[1], $matches[2])) {
				/** @var \Interfaces\Shared\Project $project_shared */
				$project_shared = $this->dependence_objects['project'];

				foreach ($matches[1] as $k => $repo_path) {
					$this_project                                 = $project_shared->get_from_vcs_path($project->get_vcs_base(), $repo_path);
					$result['revisions'][$this_project->get_id()] = $matches[2][$k];
				}
			}

			$start = false;
			foreach (explode("\n", $diff) as $line) {
				if (preg_match('#^\\+{3} '.$changelog_preg.'\\s#', $line)) {
					$start = true;
					continue;
				}

				if ($start) {
					$line = trim($line);
					if (substr($line, 0, 1) == '+') {
						$result['changelog'][] = substr($line, 1);
					} elseif (substr($line, 0, 6) == 'Index:') {
						break;
					}
				}
			}

			apc_store($key, $result, 0);
		}

		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_revision_url(Project $project, $revision) {
		/** @var Config $config_shared */
		$config_shared = $this->dependence_objects['config'];
		return $config_shared->get_vcs_url()
			   .'/listing.php?repname='.urlencode($project->get_vcs_base())
			   .'&path='.urlencode($project->get_vcs_path())
			   .'&rev='.$revision;
	}
}
