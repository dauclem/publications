<?php

abstract class Shared extends Object implements \Interfaces\Shared {
	/**
	 * {@inheritDoc}
	 */
	public function initializeId($object_id) {
		throw new Exception('Cannot initialize id for Shared object');
	}

	/**
	 * {@inheritDoc}
	 */
	public function isValid() {
		return true;
	}
}