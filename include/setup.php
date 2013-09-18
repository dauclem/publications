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
$config_shared = $dic->getObject('config');
$dic->setObjectDefinition('tracker', '\\Shared\\Tracker\\'.ucfirst($config_shared->getBugTrackerType()), true);
$dic->setObjectDefinition('tracker_object', '\\Object\\Tracker\\'.ucfirst($config_shared->getBugTrackerType()), false);
$dic->setObjectDefinition('vcs', '\\Shared\\VCS\\'.ucfirst($config_shared->getVcsType()), true);