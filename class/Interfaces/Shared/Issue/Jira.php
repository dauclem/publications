<?php

namespace Interfaces\Shared\Issue;

use \Interfaces\Shared\Issue;

/**
 * Interface Jira as Bug Issue
 */
interface Jira extends Issue {
	/**
	 * Get begin of exec instruction for jira rest api call
	 *
	 * @return string
	 */
	public function getApiExecBegin();
}