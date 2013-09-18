<?php

namespace Shared\Tracker;

use Interfaces\Object\Project;
use Shared\Tracker;

class Jira extends Tracker implements \Interfaces\Shared\Tracker\Jira {
	/**
	 * {@inheritDoc}
	 */
	public function get_dependencies_list() {
		return array_merge(parent::get_dependencies_list(), array(
																 'config',
															));
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_api_exec_begin() {
		/** @var \Interfaces\Shared\Config $config_shared */
		$config_shared = $this->dependence_objects['config'];
		return 'curl -D- -u '.$config_shared->get_bug_tracker_user().':'.$config_shared->get_bug_tracker_password()
			   .' -X GET -H "Content-Type: application/json" '
			   .$config_shared->get_bug_tracker_url()
			   .'/rest/api/latest/';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_tracker_id_pattern() {
		return '#([A-Z]{2,}-[0-9]+)#';
	}

	/**
	 * {@inheritDoc}
	 */
	public function filter_trackers($trackers, Project $project = null) {
		$ids = array();
		foreach ($trackers as $tracker) {
			if ($tracker instanceof \Interfaces\Object\Tracker) {
				$ids[] = $tracker->get_id();
			}
		}
		$ids = array_unique($ids);

		/** @var \Interfaces\Shared\Config $config_shared */
		$config_shared = $this->dependence_objects['config'];
		$jql = str_replace('{PROJECT_ID}', $project->get_tracker_id(), $config_shared->get_bug_tracker_query());
		$jql = ($jql ? '('.$jql.') AND ' : '').'key IN ('.implode(',', $ids).')';
		exec($this->get_api_exec_begin().'search?jql='.urlencode($jql).'&maxResults=200&fields=key', $output);
		$task_data = json_decode(end($output));

		$list = array();
		if (isset($task_data, $task_data->issues)) {
			foreach ($task_data->issues as $this_issue) {
				if (isset($this_issue->key)) {
					$tracker = $this->dic->get_object('tracker_object', $this_issue->key);
					if ($tracker) {
						$list[] = $tracker;
					}
				}
			}
		}

		return $list;
	}
}