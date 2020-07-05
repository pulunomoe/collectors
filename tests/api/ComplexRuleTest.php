<?php

namespace Collectors\Tests\API;

class ComplexRuleTest extends APITestCase
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

	////////////////////////////////////////////////////////////////////////////

	public function testAddFieldWithExistingItems()
	{
		// Given I have several items on the database
		$items = [];
		for ($i = 0; $i < 5; $i++) {
			$items[] = self::createItem(self::$collection['id'], self::$fields);
		}

		// When I add a new field
		$field = [
			'collection_id' => self::$collection['id'],
			'name' => self::$faker->word,
			'order' => 6,
			'hidden' => self::$faker->randomElement([0, 1]),
			'shown' => self::$faker->randomElement([0, 1]),
			'description' => self::$faker->sentence
		];
		$response = self::$client->post('fields', [
			'json' => $field
		]);

		$body = json_decode($response->getBody());
		$fieldId = $body->id;

		// Then all the existing items should have the new field in their data
		foreach ($items as $item) {

			$itemData = json_decode($item['data'], true);
			$itemData[$fieldId] = '';
			$item['data'] = json_encode($itemData);

			$data = self::$db->querySingle('SELECT * FROM items WHERE id = '.$item['id'], true);
			$this->assertEqualsCanonicalizing($item, $data);

		}
	}

	public function testDeleteFieldWithExistingItems()
	{
		// Given I have several items on the database
		$items = [];
		for ($i = 0; $i < 5; $i++) {
			$items[] = self::createItem(self::$collection['id'], self::$fields);
		}

		// When I delete an existing field
		$field = self::$faker->randomElement(self::$fields);
		$response = self::$client->delete('fields', [
			'json' => $field
		]);

		// Then all the existing items should have removed the deleted field in their data
		foreach ($items as $item) {

			$itemData = json_decode($item['data'], true);
			unset($itemData[$field['id']]);
			$item['data'] = json_encode($itemData);

			$data = self::$db->querySingle('SELECT * FROM items WHERE id = '.$item['id'], true);
			$this->assertEqualsCanonicalizing($item, $data);

		}
	}

}
