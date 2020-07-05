<?php

$dbFile = getenv('COLLECTORS_ENV') == 'DEV' ? 'collectors_test.db' : 'collectors.db';
$db = new SQLite3($dbFile);

$sql = 'CREATE TABLE IF NOT EXISTS collections (';
$sql .= '	id INTEGER PRIMARY KEY,';
$sql .= '	name TEXT NOT NULL,';
$sql .= '	description TEXT';
$sql .= ')';
$db->exec($sql);

$sql = 'CREATE TABLE IF NOT EXISTS fields (';
$sql .= '	id INTEGER PRIMARY KEY,';
$sql .= '	collection_id INTEGER,';
$sql .= '	name TEXT NOT NULL,';
$sql .= '	prefix TEXT,';
$sql .= '	suffix TEXT,';
$sql .= '	`order` INTEGER NOT NULL DEFAULT 0,';
$sql .= '	hidden INTEGER NOT NULL DEFAULT 0,';
$sql .= '	shown INTEGER NOT NULL DEFAULT 0,';
$sql .= '	description TEXT,';
$sql .= '	FOREIGN KEY (collection_id) REFERENCES collections(id) ON UPDATE CASCADE ON DELETE CASCADE';
$sql .= ')';
$db->exec($sql);

$sql = 'CREATE TABLE IF NOT EXISTS items (';
$sql .= '	id INTEGER PRIMARY KEY,';
$sql .= '	collection_id INTEGER,';
$sql .= '	name TEXT NOT NULL,';
$sql .= '	description TEXT,';
$sql .= '	data TEXT,';
$sql .= '	FOREIGN KEY (collection_id) REFERENCES collections(id) ON UPDATE CASCADE ON DELETE CASCADE';
$sql .= ')';
$db->exec($sql);
