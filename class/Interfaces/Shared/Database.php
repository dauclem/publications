<?php

namespace Interfaces\Shared;

use Interfaces\Shared;
use SQLite3;

/**
 * Class to manage db connexion
 */
interface Database extends Shared {
	/**
	 * Get SQLite3 object
	 *
	 * @return SQLite3
	 */
	public function get_connection();
}