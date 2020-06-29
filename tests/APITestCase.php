<?php

namespace Collectors\Tests;

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
			'base_uri' => 'http://localhost:3000/api',
			'http_errors' => false
		]);

		self::$faker = Factory::create();

		self::$db = new \SQLite3('collectors_test.db');
	}
}
