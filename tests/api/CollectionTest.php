<?php

namespace Collectors\Tests\API;

use Collectors\Tests\APITestCase;

class CollectionTest extends APITestCase
{
	public function tearDown(): void
	{
		self::$db->exec('DELETE FROM collections');
	}

	public function testIndex()
	{
		$response = self::$client->get('collections');
		$this->assertEquals(200, $response->getStatusCode());

		$body = json_decode($response->getBody());
		$this->assertEmpty($body);

		$collections = [];
		for ($i = 0; $i < 5; $i++) {
			$collections[$i] = $this->insert();
		}

		$response = self::$client->get('collections');
		$this->assertEquals(200, $response->getStatusCode());

		$body = json_decode($response->getBody(), true);
		for ($i = 0; $i < 5; $i++) {
			$this->assertEqualsCanonicalizing($collections[$i], $body[$i]);
		}
	}

	public function testGet()
	{
		$collection = $this->insert();

		$response = self::$client->get('collections/'.$collection['id']);
		$this->assertEquals(200, $response->getStatusCode());

		$body = json_decode($response->getBody(), true);
		$this->assertEqualsCanonicalizing($collection, $body);
	}

	public function testCreate()
	{
		$collection = [
			'name' => self::$faker->word,
			'description' => self::$faker->sentence
		];

		$response = self::$client->post('collections', [
			'json' => $collection
		]);
		$this->assertEquals(200, $response->getStatusCode());

		$body = json_decode($response->getBody());
		$collection['id'] = $body->id;
		$this->assertNotEmpty($collection['id']);

		$data = self::$db->querySingle('SELECT * FROM collections WHERE id = '.$collection['id'], true);
		$this->assertEqualsCanonicalizing($collection, $data);
	}

	public function testUpdate()
	{
		$collection = $this->insert();
		$collection['name'] = self::$faker->word;
		$collection['description'] = self::$faker->sentence;

		$response = self::$client->put('collections', [
			'json' => $collection
		]);
		$this->assertEquals(200, $response->getStatusCode());

		$data = self::$db->querySingle('SELECT * FROM collections WHERE id = '.$collection['id'], true);
		$this->assertEqualsCanonicalizing($collection, $data);
	}

	public function testDelete()
	{
		$collection = $this->insert();

		$response = self::$client->delete('collections', [
			'json' => ['id' => $collection['id']]
		]);
		$this->assertEquals(200, $response->getStatusCode());

		$data = self::$db->querySingle('SELECT * FROM collections WHERE id = '.$collection['id']);
		$this->assertEmpty($data);
	}

	////////////////////////////////////////////////////////////////////////////

	private function insert()
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
}
