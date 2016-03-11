<?php

namespace Shared\VCS;

use Exception;
use Shared\VCS;
use Interfaces\Shared\Config;
use Interfaces\Object\Project;

class Git extends VCS implements \Interfaces\Shared\VCS\Subversion {
	/**
	 * {@inheritDoc}
	 */
	public function getDependenciesList() {
		return array_merge(parent::getDependenciesList(), array(
			'config',
			'project',
		));
	}

	/**
	 * Update or clone repository code
	 *
	 * @param string $repository_url
	 * @throws Exception
	 */
	protected function checkout_repository($repository_url) {
		static $checkout_done = array();
		if (isset($checkout_done[$repository_url])) {
			return;
		}

		$target_dir = __DIR__.'/../../../git-clone/'.basename($repository_url);
		if (file_exists($target_dir)) {
			exec('cd '.$target_dir.'; git pull origin master 2>&1', $output, $return_var);
		} else {
			if (!file_exists(dirname($target_dir))) {
				mkdir(dirname($target_dir), 0777, true);
			}
			exec('cd '.dirname($target_dir).'; git clone '.$repository_url.' '.basename($target_dir).' 2>&1', $output, $return_var);
		}
		if ($return_var) {
			throw new Exception('Cannot checkout project : '.implode("\n", $output));
		}
		$checkout_done[$repository_url] = true;
	}

	/**
	 * @param string $repository_url
	 * @param string $revision_begin
	 * @param string $revision_end
	 * @param bool   $with_changelog_diff
	 * @return string[]
	 */
	protected function get_logs($repository_url, $revision_begin, $revision_end, $with_changelog_diff = false) {
		$this->checkout_repository($repository_url);

		/** @var Config $config_shared */
		$config_shared   = $this->dependence_objects['config'];
		$changelog_preg  = preg_quote($config_shared->getChangelogPath(), '#');
		$target_dir      = __DIR__.'/../../../git-clone/'.basename($repository_url);
		$list            = $commit = array();
		$changelog_found = false;

		// https://git-scm.com/docs/gitrevisions
		if ($revision_end) {
			if ($revision_begin === -1) {
				$interval = $revision_end.'..HEAD';
			} else {
				$interval = $revision_end.'..^'.$revision_begin;
			}
		} else {
			if ($revision_begin === -1) {
				$interval = '';
			} else {
				$interval = $revision_begin.'^@';
			}
		}
		$with_diff = $with_changelog_diff ? ' -p' : '';
		exec('cd '.$target_dir.'; git log '.$interval.$with_diff, $output, $return_var);

		foreach ($output as $line) {
			if (substr($line, 0, 7) === 'commit ') {
				if ($commit) {
					$list[] = $commit;
				}
				$changelog_found = false;
				$commit          = array(
					'rev'  => '',
					'date' => 0,
					'msg'  => '',
				);
				if ($with_changelog_diff) {
					$commit['changelog'] = array();
				}
				$commit['rev'] = substr($line, 7);
			} elseif (substr($line, 0, 5) === 'Date:') {
				$commit['date'] = strtotime(substr($line, 5));
			} elseif (substr($line, 0, 4) === '    ' && $commit['date'] && !$commit['msg']) {
				$commit['msg'] = substr($line, 4);
			} elseif ($with_changelog_diff && $commit['msg']) {
				if (preg_match($changelog_preg, $line)) {
					$changelog_found = true;
				} elseif ($changelog_found && substr($line, 0, 1) === '+') {
					$commit['changelog'][] = trim(substr($line, 1));
				}
			}
		}
		return $list;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPregRevision() {
		return '[0-9a-z]{40}';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getLogs(Project $project, $revision_begin, $revision_end) {
		$key    = md5($project->getId().'|'.$revision_begin.'|'.$revision_end);
		$result = apc_fetch($key);
																												$result= false;
		if ($result === false) {
			$result = array();
			$logs   = $this->get_logs($project->getVcsRepository(), $revision_begin, $revision_end);
			if ($logs) {
				foreach ($logs as $log) {
					$result[] = array(
						'date' => $log['date'],
						'rev'  => $log['rev'],
						'msg'  => $log['msg'],
					);
				}
			}

			apc_store($key, $result, 0);
		} elseif ($revision_begin == -1) {
			if (isset($result[0])) {
				$revision_end = $result[0]['rev'];
			};

			$result_delta = array();
			$logs         = $this->get_logs($project->getVcsRepository(), $revision_begin, $revision_end);
			if ($logs) {
				foreach ($logs as $log) {
					$result_delta[] = array(
						'date' => $log['date'],
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
	protected function getDiffRevisionsAndChangelog(\Interfaces\Object\Project $project, $revision_end, $revision_begin) {
		$key    = md5($project->getId().'|'.$revision_end.'|'.$revision_begin);
		$result = apc_fetch($key);
																												$result= false;
		if ($result === false) {
			$result = array(
				'revisions' => array(),
				'changelog' => array(),
			);

			$logs = $this->get_logs($project->getVcsRepository(), $revision_begin, $revision_end, true);
			foreach ($logs as $log) {
				$result['revisions'][] = $log['rev'];
				if ($log['changelog']) {
					$result['changelog'] = array_merge($result['changelog'], $log['changelog']);
				}
			}

			apc_store($key, $result, 0);
		}

		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRevisionUrl(Project $project, $revision) {
		/** @var Config $config_shared */
		$config_shared = $this->dependence_objects['config'];
		return $config_shared->getVcsWebUrl()
			   .'/'.$project->getVcsBase()
			   .preg_replace('#\.git$#', '', $project->getVcsPath())
			   .'/commit/'.$revision;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRepository(Project $project) {
		/** @var Config $config_shared */
		$config_shared = $this->dependence_objects['config'];
		return $config_shared->getVcsUrl()
			   .':'.$project->getVcsBase()
			   .$project->getVcsPath();
	}
}
