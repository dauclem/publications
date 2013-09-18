<?php

namespace Interfaces\Shared\Tracker;

use \Interfaces\Shared\Tracker;

/**
 * Interface Jira as Bug tracker
 */
interface Jira extends Tracker {
	/**
	 * Get begin of exec instruction for jira rest api call
	 *
	 * @return string
	 */
	public function getApiExecBegin();
}