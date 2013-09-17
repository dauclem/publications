<?php

namespace Shared\Tracker;

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
	public function get_object_definition() {
		return 'jira_object';
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
	public function filter_trackers($trackers) {
		$ids = array();
		foreach ($trackers as $tracker) {
			if ($tracker instanceof \Interfaces\Object\Tracker) {
				$ids[] = $tracker->get_id();
			}
		}
		$ids = array_unique($ids);

		$cache_key = __CLASS__.'|'.__FUNCTION__.'|'.implode('|', $ids);
		$task_data = apc_fetch($cache_key);
		if ($task_data === false) {
			/** @var \Interfaces\Shared\Config $config_shared */
			$config_shared = $this->dependence_objects['config'];
			// TODO : get jql from config
			$jql = 'key IN ('.implode(',', $ids).')';
			exec('curl -D- -u '.$config_shared->get_bug_tracker_user().':'.$config_shared->get_bug_tracker_password()
				 .' -X GET -H "Content-Type: application/json" '
				 .$config_shared->get_bug_tracker_url().'/rest/api/search?jql='.$jql.'&maxResults=200&fields=key', $output);
			$task_data = json_decode(end($output));
			apc_store($cache_key, $task_data, 3600);
		}
	}
}