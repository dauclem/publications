<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);
session_start();

$base_dir = dirname(__DIR__);

require __DIR__.'/../class/DIC.php';
$dic = new DIC(array(
					'path'           => $base_dir,
					'path_templates' => $base_dir.'/templates',
			   ));

/** @var \Interfaces\Shared\Config $config_shared */
$config_shared = $dic->get_object('config');
$dic->set_object_definition('tracker', '\\Shared\\Tracker\\'.ucfirst($config_shared->get_bug_tracker_type()), true);
$dic->set_object_definition('tracker_object', '\\Shared\\Object\\'.ucfirst($config_shared->get_bug_tracker_type()), true);
$dic->set_object_definition('vcs', '\\Shared\\VCS\\'.ucfirst($config_shared->get_vcs_type()), true);