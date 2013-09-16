<?php

namespace Shared;

use Shared;
use Exception;
use SQLite3;

class Database extends Shared implements \Interfaces\Shared\Database {
	/** @var SQLite3 */
	protected $connection;

	/**
	 * {@inheritDoc}
	 */
	public function initialize() {
		if (!class_exists('SQLite3')) {
			throw new Exception('SQLite3 not suppported');
		}
		$this->connection = new SQLite3($this->dic->get_param('path').'/include/publications.db');
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_connection() {
		return $this->connection;
	}
}