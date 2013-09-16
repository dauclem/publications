<?php

namespace Interfaces;

use DIC;

/**
 * An Object class can be instanced only once by DIC
 */
interface Object {
	/**
	 * Use to store dic object in this object
	 *
	 * @param DIC $dic
	 */
	public function set_dic(DIC $dic);

	/**
	 * Return array with list of dependencies ident (Only Shared object)
	 *
	 * @return string[]
	 */
	public function get_dependencies_list();

	/**
	 * Add an object depended (Shared) into this object
	 *
	 * @param string $ident
	 * @param Shared $object
	 * @return
	 */
	public function add_dependence_object($ident, Shared $object);

	/**
	 * Call immediately after dependencies injected
	 */
	public function initialize();

	/**
	 * Initialize object with its id. Must be call for non Shared objects
	 *
	 * @param int|string $object_id
	 */
	public function initialize_id($object_id);

	/**
	 * Return true if object is correctly instanced
	 *
	 * @return bool
	 */
	public function is_valid();
}