<?php

namespace Collectors\API;

use Collectors\API\APIController;

class Item extends APIController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index($request, $response)
	{
		$result = $this->query('SELECT * FROM items WHERE collection_id = :collection_id', [
			'collection_id' => $request->getQueryParam('collection_id')
		]);
		while ($item = $result->fetchArray(SQLITE3_ASSOC)) {
			$items[] = $item;
		}

		return $response->withJson($items ?? []);
	}

	public function get($request, $response, $args)
	{
		$result = $this->query('SELECT * FROM items WHERE id = :id LIMIT 1', [
			'id' => $args['id']
		]);
		$item = $result->fetchArray(SQLITE3_ASSOC);

		return $response->withJson($item);
	}

	public function create($request, $response)
	{
		$item = $request->getParsedBody();

		$this->query('INSERT INTO items (collection_id, name, description, data) VALUES (:collection_id, :name, :description, :data)', [
			'collection_id' => $item['collection_id'],
			'name' => $item['name'],
			'description' => $item['description'],
			'data' => $item['data']
		]);
		$id = $this->db->lastInsertRowID();

		return $response->withJson(['id' => $id]);
	}

	public function update($request, $response)
	{
		$item = $request->getParsedBody();

		$this->query('UPDATE items SET name = :name, description = :description, data = :data WHERE id = :id', [
			'id' => $item['id'],
			'name' => $item['name'],
			'description' => $item['description'],
			'data' => $item['data']
		]);

		return $response->withStatus(200);
	}

	public function delete($request, $response)
	{
		$item = $request->getParsedBody();

		$this->query('DELETE FROM items WHERE id = :id', [
			'id' => $item['id']
		]);

		return $response->withStatus(200);
	}
}
