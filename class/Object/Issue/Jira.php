<?php

namespace Object\Issue;

use Object\Issue;

class Jira extends Issue implements \Interfaces\Object\Issue\Jira {
	/**
	 * {@inheritDoc}
	 */
	public function getDependenciesList() {
		return array_merge(parent::getDependenciesList(), array(
																 'issue',
																 'config',
															));
	}

	/**
	 * {@inheritDoc}
	 */
	public function initializeId($object_id) {
		$object_id = trim($object_id);
		if (!$object_id) {
			return null;
		}

		$cache_key = __CLASS__.'|'.$object_id;
		$task_data = apcu_fetch($cache_key);
		if ($task_data === false) {
			/** @var \Interfaces\Shared\Issue\Jira $issue_shared */
			$issue_shared = $this->dependence_objects['issue'];
			exec($issue_shared->getApiExecBegin().'issue/'.$object_id, $output);
			$task_data = json_decode(end($output));
			apcu_store($cache_key, $task_data, 0);
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
	public function getUrl() {
		/** @var \Interfaces\Shared\Config $config_shared */
		$config_shared = $this->dependence_objects['config'];
		return $config_shared->getBugTrackerUrl().'/browse/'.$this->id;
	}
}