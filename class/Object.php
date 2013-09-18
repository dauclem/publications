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
	public function setDic(DIC $dic) {
		$this->dic = $dic;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependenciesList() {
		return array();
	}

	/**
	 * {@inheritDoc}
	 */
	public function addDependenceObject($ident, Shared $object) {
		$this->dependence_objects[$ident] = $object;
	}

	/**
	 * {@inheritDoc}
	 */
	public function initialize() {
	}
}