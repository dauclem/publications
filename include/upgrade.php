<?php

require __DIR__.'/setup.php';

/** @var \Interfaces\Shared\Database $database */
$database   = $dic->getObject('database');
$connection = $database->getConnection();

// Config
@$connection->exec('ALTER TABLE config ADD bug_tracker_query TEXT');
$connection->exec('CREATE TABLE IF NOT EXISTS config_recipients(
							email TEXT)');
$connection->exec('CREATE UNIQUE INDEX IF NOT EXISTS config_recipient ON config_recipients (email)');

// Project
@$connection->exec('ALTER TABLE project ADD description TEXT');
@$connection->exec('ALTER TABLE project ADD tracker_id TEXT');

// Publication
@$connection->exec('ALTER TABLE publication ADD is_temp INTEGER');
$connection->exec('DROP INDEX IF EXISTS project_date');
$connection->exec('CREATE UNIQUE INDEX IF NOT EXISTS project_temp_date ON publication (project_id, is_temp, date)');
$connection->exec('UPDATE publication SET is_temp = 0 WHERE is_temp IS NULL');