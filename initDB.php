<?php

$dbFile = getenv('COLLECTORS_ENV') == 'DEV' ? 'collectors_test.db' : 'collectors.db';

$db = new SQLite3($dbFile);

$sql = 'CREATE TABLE IF NOT EXISTS collections (';
$sql .= '	id INTEGER PRIMARY KEY,';
$sql .= '	name TEXT NOT NULL,';
$sql .= '	description TEXT';
$sql .= ')';
$db->exec($sql);
