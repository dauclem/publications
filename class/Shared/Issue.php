<?php

namespace Shared;

use Shared;

abstract class Issue extends Shared implements \Interfaces\Shared\Issue {
	/**
	 * {@inheritDoc}
	 */
	public function getIssuesFromMessage($message) {
		$issues = array();
		if (preg_match_all($this->getIssueIdPattern(), $message, $matches)) {
			if (isset($matches[1])) {
				foreach ($matches[1] as $issue_id) {
					$issue = $this->dic->getObject('issue_object', $issue_id);
					if ($issue) {
						$issues[] = $issue;
					}
				}
			}
		}
		return $issues;
	}
}