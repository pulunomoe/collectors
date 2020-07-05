<?php

namespace Collectors\Tests\API;

class ItemTest extends APITestCase
{
	private static $collection;
	private static $fields;

	public static function setUpBeforeClass(): void
	{
		self::$collection = self::createCollection();
		for ($i = 0; $i < 5; $i++) {
			self::$fields[] = self::createField(self::$collection['id'], $i + 1);
		}
	}

	public function tearDown(): void
	{
		self::$db->exec('DELETE FROM items');
	}

	////////////////////////////////////////////////////////////////////////////

	public function testIndex()
	{
		$response = self::$client->get('items?collection_id='.self::$collection['id']);
		$this->assertEquals(200, $response->getStatusCode());

		$body = json_decode($response->getBody());
		$this->assertEmpty($body);

		$items = [];
		for ($i = 0; $i < 5; $i++) {
			$items[$i] = self::createItem(self::$collection['id'], self::$fields);
		}

		$response = self::$client->get('items?collection_id='.self::$collection['id']);
		$this->assertEquals(200, $response->getStatusCode());

		$body = json_decode($response->getBody(), true);
		for ($i = 0; $i < 5; $i++) {
			$this->assertEqualsCanonicalizing($items[$i], $body[$i]);
		}
	}

	public function testGet()
	{
		$item = self::createItem(self::$collection['id'], self::$fields);

		$response = self::$client->get('items/'.$item['id']);
		$this->assertEquals(200, $response->getStatusCode());

		$body = json_decode($response->getBody(), true);
		$this->assertEqualsCanonicalizing($item, $body);
	}

	public function testCreate()
	{
		$item = [
			'collection_id' => self::$collection['id'],
			'name' => self::$faker->word,
			'description' => self::$faker->sentence
		];
		$itemData = [];
		foreach (self::$fields as $field) {
			$itemData[$field['name']] = self::$faker->word;
		}
		$item['data'] = json_encode($itemData);

		$response = self::$client->post('items', [
			'json' => $item
		]);
		$this->assertEquals(200, $response->getStatusCode());

		$body = json_decode($response->getBody());
		$item['id'] = $body->id;
		$this->assertNotEmpty($field['id']);

		$data = self::$db->querySingle('SELECT * FROM items WHERE id = '.$item['id'], true);
		$this->assertEqualsCanonicalizing($item, $data);
	}

	public function testUpdate()
	{
		$item = self::createItem(self::$collection['id'], self::$fields);
		$item['name'] = self::$faker->word;
		$item['description'] = self::$faker->sentence;
		$itemData = [];
		foreach (self::$fields as $field) {
			$itemData[$field['name']] = self::$faker->word;
		}
		$item['data'] = json_encode($itemData);

		$response = self::$client->put('items', [
			'json' => $item
		]);
		$this->assertEquals(200, $response->getStatusCode());

		$data = self::$db->querySingle('SELECT * FROM items WHERE id = '.$item['id'], true);
		$this->assertEqualsCanonicalizing($item, $data);
	}

	public function testDelete()
	{
		$item = self::createItem(self::$collection['id'], self::$fields);

		$response = self::$client->delete('items', [
			'json' => ['id' => $item['id']]
		]);
		$this->assertEquals(200, $response->getStatusCode());

		$data = self::$db->querySingle('SELECT * FROM items WHERE id = '.$item['id']);
		$this->assertEmpty($data);
	}
}
