<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);
session_start();

$base_dir = dirname(__DIR__);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../class/DIC.php';
$dic = new DIC(array(
					'path'           => $base_dir,
					'path_templates' => $base_dir.'/templates',
			   ));

if (!defined('UPGRADE')) {
	/** @var \Interfaces\Shared\Config $config_shared */
	$config_shared = $dic->getObject('config');
}