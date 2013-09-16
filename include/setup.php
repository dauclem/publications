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