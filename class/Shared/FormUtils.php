<?php

namespace Shared;

use Shared;

class FormUtils extends Shared implements \Interfaces\Shared\FormUtils {
	/**
	 * {@inheritDoc}
	 */
	public function checkUrl($url) {
		return (bool)preg_match('#^https?://[a-z0-9][a-z0-9\\-\\.]+[a-z0-9]\\.[a-z]{2,}[\\43-\\176]*$#i', $url);
	}

	/**
	 * {@inheritDoc}
	 */
	public function checkEmail($email) {
		return (bool)preg_match('#^[a-z0-9_\\-\\.]+@[a-z0-9\\-\\.]+\\.[a-z0-9]{2,}$#i', $email);
	}
}