<?php

namespace Object\Tracker;

use Object\Tracker;

class Jira extends Tracker implements \Interfaces\Object\Tracker\Jira {
	/**
	 * {@inheritDoc}
	 */
	public function get_dependencies_list() {
		return array_merge(parent::get_dependencies_list(), array(
																 'jira',
																 'config',
															));
	}

	/**
	 * {@inheritDoc}
	 */
	public function initialize_id($object_id) {
		$object_id = trim($object_id);
		if (!$object_id) {
			return null;
		}

		$cache_key = __CLASS__.'|'.$object_id;
		$task_data = apc_fetch($cache_key);
		if ($task_data === false) {
			/** @var \Interfaces\Shared\Tracker\Jira $tracker_shared */
			$tracker_shared = $this->dependence_objects['jira'];
			exec($tracker_shared->get_api_exec_begin().'issue/'.$object_id, $output);
			$task_data = json_decode(end($output));
			apc_store($cache_key, $task_data, 0);
		}

		if (isset($task_data, $task_data->fields)) {
			$this->id    = isset($task_data->key) ? $task_data->key : '';
			$this->title = isset($task_data->fields->summary) ? $task_data->fields->summary : '';
			$this->type  = isset($task_data->fields->issuetype->name) ? $task_data->fields->issuetype->name : '';
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_url() {
		/** @var \Interfaces\Shared\Config $config_shared */
		$config_shared = $this->dependence_objects['config'];
		return $config_shared->get_bug_tracker_url().'/browse/'.$this->id;
	}
}