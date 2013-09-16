<?php

use Interfaces\Shared;

abstract class Object implements \Interfaces\Object {
	/** @var DIC */
	protected $dic = null;
	/** @var Shared[] */
	protected $dependence_objects = array();

	/**
	 * {@inheritDoc}
	 */
	public function set_dic(DIC $dic) {
		$this->dic = $dic;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_dependencies_list() {
		return array();
	}

	/**
	 * {@inheritDoc}
	 */
	public function add_dependence_object($ident, Shared $object) {
		$this->dependence_objects[$ident] = $object;
	}

	/**
	 * {@inheritDoc}
	 */
	public function initialize() {
	}
}