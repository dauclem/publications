<?php

namespace Shared\Issue;

use Interfaces\Object\Project;
use Shared\Issue;

class Jira extends Issue implements \Interfaces\Shared\Issue\Jira {
	/**
	 * {@inheritDoc}
	 */
	public function getDependenciesList() {
		return array_merge(parent::getDependenciesList(), array(
			'config',
		));
	}

	/**
	 * {@inheritDoc}
	 */
	public function getApiExecBegin() {
		/** @var \Interfaces\Shared\Config $config_shared */
		$config_shared = $this->dependence_objects['config'];
		return 'curl -D- -u '.$config_shared->getBugTrackerUser().':'.$config_shared->getBugTrackerPassword()
			   .' -X GET -H "Content-Type: application/json" '
			   .$config_shared->getBugTrackerUrl()
			   .'/rest/api/latest/';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIssueIdPattern() {
		return '#([A-Z]{2,}-[0-9]+)#';
	}

	/**
	 * {@inheritDoc}
	 */
	public function filterIssues($issues, Project $project = null) {
		$ids = array();
		foreach ($issues as $issue) {
			if ($issue instanceof \Interfaces\Object\Issue) {
				$ids[] = $issue->getId();
			}
		}
		$ids = array_unique($ids);

		/** @var \Interfaces\Shared\Config $config_shared */
		$config_shared = $this->dependence_objects['config'];
		$jql = str_replace('{PROJECT_ID}', $project->getBugTrackerId(), $config_shared->getBugTrackerQuery());
		$jql = ($jql ? '('.$jql.') AND ' : '').'key IN ('.implode(',', $ids).')';
		exec($this->getApiExecBegin().'search?jql='.urlencode($jql).'&maxResults=200&fields=key', $output);
		$task_data = json_decode(end($output));

		$list = array();
		if (isset($task_data, $task_data->issues)) {
			foreach ($task_data->issues as $this_issue) {
				if (isset($this_issue->key)) {
					$issue = $this->dic->getObject('issue_object', $this_issue->key);
					if ($issue) {
						$list[] = $issue;
					}
				}
			}
		}

		usort($list, function (\Interfaces\Object\Issue $a, \Interfaces\Object\Issue $b) {
			if ($a->getType() == $b->getType()) {
				return 0;
			}
			return $a->getType() < $b->getType() ? 1 : -1;
		});

		return $list;
	}
}