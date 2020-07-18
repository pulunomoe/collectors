<?php

require __DIR__.'/./vendor/autoload.php';

use Faker\Factory;

$dbFile = getenv('COLLECTORS_ENV') == 'DEV' ? 'collectors_test.db' : 'collectors.db';
$db = new \SQLite3($dbFile);

$faker = Factory::create();

$stmt = $db->prepare('INSERT INTO collections (name, description) VALUES (:name, :description)');
$stmt->bindValue(':name', $faker->word);
$stmt->bindValue(':description', $faker->sentence);
$stmt->execute();
$collectionId = $db->lastInsertRowID();

$fieldIds = [];
$stmt = $db->prepare('INSERT INTO fields (collection_id, name, prefix, suffix, `order`, hidden, shown, description) VALUES (:collection_id, :name, :prefix, :suffix, :order, 0, 0, :description)');
for ($i = 0; $i < 5; $i++) {
	$stmt->bindValue(':collection_id', $collectionId);
	$stmt->bindValue(':name', $faker->word);
	$stmt->bindValue(':prefix', $faker->word);
	$stmt->bindValue(':suffix', $faker->word);
	$stmt->bindValue(':order', $i + 1);
	$stmt->bindValue(':description', $faker->sentence);
	$stmt->execute();
	$fieldIds[] = $db->lastInsertRowID();
}

$stmt = $db->prepare('INSERT INTO items (collection_id, name, description, data) VALUES (:collection_id, :name, :description, :data)');
for ($i = 0; $i < 10; $i++) {
	$itemData = [];
	foreach ($fieldIds as $fieldId) {
		$itemData[$fieldId] = $faker->word;
	}
	$stmt->bindValue(':collection_id', $collectionId);
	$stmt->bindValue(':name', $faker->word);
	$stmt->bindValue(':description', $faker->sentence);
	$stmt->bindValue(':data', json_encode($itemData));
	$stmt->execute();
}
