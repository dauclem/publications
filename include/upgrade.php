<?php

require __DIR__.'/setup.php';

/** @var \Interfaces\Shared\Database $database */
$database   = $dic->getObject('database');
$connection = $database->getConnection();

// Config
@$connection->exec('ALTER TABLE config ADD bug_tracker_query TEXT');
@$connection->exec('ALTER TABLE config ADD mail_content TEXT');
$connection->exec('CREATE TABLE IF NOT EXISTS config_recipients(
							email TEXT)');
$connection->exec('CREATE UNIQUE INDEX IF NOT EXISTS config_recipient ON config_recipients (email)');
$connection->exec('UPDATE config SET mail_content = "Bonjour,

Une publication va avoir lieu sur {PROJECT}, contenant les changements suivants :

{ISSUES}

Cordialement" WHERE mail_content IS NULL');

// Project
@$connection->exec('ALTER TABLE project ADD description TEXT');
@$connection->exec('ALTER TABLE project ADD tracker_id TEXT');
@$connection->exec('ALTER TABLE project ADD mail_content TEXT');

// Publication
@$connection->exec('ALTER TABLE publication ADD is_temp INTEGER');
$connection->exec('DROP INDEX IF EXISTS project_date');
$connection->exec('CREATE UNIQUE INDEX IF NOT EXISTS project_temp_date ON publication (project_id, is_temp, date)');
$connection->exec('UPDATE publication SET is_temp = 0 WHERE is_temp NOT IN (0, 1)');