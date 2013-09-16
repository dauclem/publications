<?php

abstract class Shared extends Object implements \Interfaces\Shared {
	/**
	 * {@inheritDoc}
	 */
	public function initialize_id($object_id) {
		throw new Exception('Cannot initialize id for Shared object');
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_valid() {
		return true;
	}
}