<?php

namespace Collectors\Tests\API;

class FieldTest extends APITestCase
{
	private static $collection;

	public static function setUpBeforeClass(): void
	{
		self::$db->exec('DELETE FROM collections');
		self::$collection = self::createCollection();
	}

	public static function tearDownAfterClass(): void
	{
		self::$db->exec('DELETE FROM collections');
	}

	public function tearDown(): void
	{
		self::$db->exec('DELETE FROM fields');
	}

	////////////////////////////////////////////////////////////////////////////

	public function testIndex()
	{
		$response = self::$client->get('fields?collection_id='.self::$collection['id']);
		$this->assertEquals(200, $response->getStatusCode());

		$body = json_decode($response->getBody());
		$this->assertEmpty($body);

		$fields = [];
		for ($i = 0; $i < 5; $i++) {
			$fields[$i] = self::createField(self::$collection['id'], $i);
		}

		$response = self::$client->get('fields?collection_id='.self::$collection['id']);
		$this->assertEquals(200, $response->getStatusCode());

		$body = json_decode($response->getBody(), true);
		for ($i = 0; $i < 5; $i++) {
			$this->assertEqualsCanonicalizing($fields[$i], $body[$i]);
		}
	}

	public function testGet()
	{
		$field = self::createField(self::$collection['id']);

		$response = self::$client->get('fields/'.$field['id']);
		$this->assertEquals(200, $response->getStatusCode());

		$body = json_decode($response->getBody(), true);
		$this->assertEqualsCanonicalizing($field, $body);
	}

	public function testCreate()
	{
		$field = [
			'collection_id' => self::$collection['id'],
			'name' => self::$faker->word,
			'order' => 0,
			'hidden' => self::$faker->randomElement([0, 1]),
			'shown' => self::$faker->randomElement([0, 1]),
			'description' => self::$faker->sentence
		];

		$response = self::$client->post('fields', [
			'json' => $field
		]);
		$this->assertEquals(200, $response->getStatusCode());

		$body = json_decode($response->getBody());
		$field['id'] = $body->id;
		$this->assertNotEmpty($field['id']);

		$data = self::$db->querySingle('SELECT * FROM fields WHERE id = '.$field['id'], true);
		$this->assertEqualsCanonicalizing($field, $data);
	}

	public function testUpdate()
	{
		$field = self::createField(self::$collection['id']);
		$field['name'] = self::$faker->word;
		$field['hidden'] = self::$faker->randomElement([0, 1]);
		$field['shown'] = self::$faker->randomElement([0, 1]);
		$field['description'] = self::$faker->sentence;

		$response = self::$client->put('fields', [
			'json' => $field
		]);
		$this->assertEquals(200, $response->getStatusCode());

		$data = self::$db->querySingle('SELECT * FROM fields WHERE id = '.$field['id'], true);
		$this->assertEqualsCanonicalizing($field, $data);
	}

	public function testUpdateOrder()
	{
		$field = self::createField(self::$collection['id']);
		$field['order'] = self::$faker->randomDigit;

		$response = self::$client->put('fields/order', [
			'json' => $field
		]);
		$this->assertEquals(200, $response->getStatusCode());

		$data = self::$db->querySingle('SELECT * FROM fields WHERE id = '.$field['id'], true);
		$this->assertEqualsCanonicalizing($field, $data);
	}

	public function testDelete()
	{
		$field = self::createField(self::$collection['id']);

		$response = self::$client->delete('fields', [
			'json' => ['id' => $field['id']]
		]);
		$this->assertEquals(200, $response->getStatusCode());

		$data = self::$db->querySingle('SELECT * FROM fields WHERE id = '.$field['id']);
		$this->assertEmpty($data);
	}
}
