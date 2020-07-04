<?php

namespace Collectors\Tests\API;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use Faker\Factory;

class APITestCase extends TestCase
{
	protected static $client;
	protected static $faker;
	protected static $db;

	public static function setUpBeforeClass(): void
	{
		self::$client = new Client([
			'base_uri' => 'http://localhost:3000/api/',
			'http_errors' => false
		]);

		self::$faker = Factory::create();

		self::$db = new \SQLite3('collectors_test.db');
		self::$db->exec('DELETE FROM collections');
	}

	////////////////////////////////////////////////////////////////////////////

	protected static function createCollection()
	{
		$collection = [
			'name' => self::$faker->word,
			'description' => self::$faker->sentence
		];

		self::$db->exec('INSERT INTO collections (name, description) VALUES ('
			.'"'.$collection['name'].'", '
			.'"'.$collection['description'].'"'
		.')');
		$collection['id'] = self::$db->lastInsertRowID();

		return $collection;
	}

	protected static function createField($collectionId, $order = 0)
	{
		$field = [
			'collection_id' => $collectionId,
			'name' => self::$faker->word,
			'order' => $order,
			'hidden' => self::$faker->randomElement([0, 1]),
			'shown' => self::$faker->randomElement([0, 1]),
			'description' => self::$faker->sentence,
		];

		self::$db->exec('INSERT INTO fields (collection_id, name, `order`, hidden, shown, description) VALUES ('
			.'"'.$field['collection_id'].'", '
			.'"'.$field['name'].'", '
			.'"'.$field['order'].'", '
			.'"'.$field['hidden'].'", '
			.'"'.$field['shown'].'", '
			.'"'.$field['description'].'"'
		.')');
		$field['id'] = self::$db->lastInsertRowID();

		return $field;
	}
}
