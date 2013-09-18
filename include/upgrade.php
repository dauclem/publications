<?php

require __DIR__.'/setup.php';

/** @var \Interfaces\Shared\Database $database */
$database = $dic->getObject('database');
$connection = $database->getConnection();

@$connection->exec('ALTER TABLE config ADD bug_tracker_query TEXT');

@$connection->exec('ALTER TABLE project ADD description TEXT');
@$connection->exec('ALTER TABLE project ADD tracker_id TEXT');

$connection->exec('CREATE TABLE IF NOT EXISTS config_recipients(
							email TEXT)');
$connection->exec('CREATE UNIQUE INDEX IF NOT EXISTS config_recipient ON config_recipients (email)');