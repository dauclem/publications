<?php

namespace Interfaces\Shared;

use Interfaces\Shared;

/**
 * Class with some utilities methods for forms
 */
interface FormUtils extends Shared {
	/**
	 * Check if parameter is right formated url
	 *
	 * @param string $url
	 * @return bool
	 */
	public function check_url($url);

	/**
	 * Check if parameter is right formated email
	 *
	 * @param string $email
	 * @return bool
	 */
	public function check_email($email);
}