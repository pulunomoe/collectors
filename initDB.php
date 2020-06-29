<?php

$dbFile = empty($argv[1]) ? 'collectors.db' : 'collectors_test.db';

$db = new SQLite3($dbFile);

$sql = 'CREATE TABLE collections (';
$sql .= '	id INTEGER PRIMARY KEY,';
$sql .= '	name TEXT NOT NULL,';
$sql .= '	description TEXT';
$sql .= ')';
$db->exec($sql);
