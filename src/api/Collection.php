<?php

namespace Collectors\API;

use Collectors\API\APIController;

class Collection extends APIController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index($request, $response)
	{
		$result = $this->query('SELECT * FROM collections ORDER BY id ASC');
		while ($collection = $result->fetchArray(SQLITE3_ASSOC)) {
			$collections[] = $collection;
		}

		return $response->withJson($collections ?? []);
	}

	public function get($request, $response, $args)
	{
		$result = $this->query('SELECT * FROM collections WHERE id = :id LIMIT 1', [
			'id' => $args['id']
		]);
		$collection = $result->fetchArray(SQLITE3_ASSOC);

		return $response->withJson($collection);
	}

	public function create($request, $response)
	{
		$collection = $request->getParsedBody();

		$this->query('INSERT INTO collections (name, description) VALUES (:name, :description)', [
			'name' => $collection['name'],
			'description' => $collection['description']
		]);
		$id = $this->db->lastInsertRowID();

		return $response->withJson(['id' => $id]);
	}

	public function update($request, $response)
	{
		$collection = $request->getParsedBody();

		$this->query('UPDATE collections SET name = :name, description = :description WHERE id = :id', [
			'id' => $collection['id'],
			'name' => $collection['name'],
			'description' => $collection['description']
		]);

		return $response->withStatus(200);
	}

	public function delete($request, $response)
	{
		$collection = $request->getParsedBody();

		$this->query('DELETE FROM collections WHERE id = :id', [
			'id' => $collection['id']
		]);

		return $response->withStatus(200);
	}
}
