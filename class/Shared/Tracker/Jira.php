<?php

namespace Shared\Tracker;

use Interfaces\Object\Project;
use Shared\Tracker;

class Jira extends Tracker implements \Interfaces\Shared\Tracker\Jira {
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
	public function getTrackerIdPattern() {
		return '#([A-Z]{2,}-[0-9]+)#';
	}

	/**
	 * {@inheritDoc}
	 */
	public function filterTrackers($trackers, Project $project = null) {
		$ids = array();
		foreach ($trackers as $tracker) {
			if ($tracker instanceof \Interfaces\Object\Tracker) {
				$ids[] = $tracker->getId();
			}
		}
		$ids = array_unique($ids);

		/** @var \Interfaces\Shared\Config $config_shared */
		$config_shared = $this->dependence_objects['config'];
		$jql = str_replace('{PROJECT_ID}', $project->getTrackerId(), $config_shared->getBugTrackerQuery());
		$jql = ($jql ? '('.$jql.') AND ' : '').'key IN ('.implode(',', $ids).')';
		exec($this->getApiExecBegin().'search?jql='.urlencode($jql).'&maxResults=200&fields=key', $output);
		$task_data = json_decode(end($output));

		$list = array();
		if (isset($task_data, $task_data->issues)) {
			foreach ($task_data->issues as $this_issue) {
				if (isset($this_issue->key)) {
					$tracker = $this->dic->getObject('tracker_object', $this_issue->key);
					if ($tracker) {
						$list[] = $tracker;
					}
				}
			}
		}

		return $list;
	}
}