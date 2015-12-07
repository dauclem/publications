<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);
ini_set('max_execution_time', 0);
session_start();

$base_dir = dirname(__DIR__);

require __DIR__.'/../class/DIC.php';
$dic = new DIC(array(
					'path'           => $base_dir,
					'path_templates' => $base_dir.'/templates',
			   ));

/** @var \Interfaces\Shared\Config $config_shared */
$config_shared = $dic->getObject('config');
$dic->setObjectDefinition('issue', '\\Shared\\Issue\\'.ucfirst($config_shared->getBugTrackerType()), true);
$dic->setObjectDefinition('issue_object', '\\Object\\Issue\\'.ucfirst($config_shared->getBugTrackerType()), false);
$dic->setObjectDefinition('vcs', '\\Shared\\VCS\\'.ucfirst($config_shared->getVcsType()), true);