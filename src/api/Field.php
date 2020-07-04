<?php

namespace Collectors\API;

use Collectors\API\APIController;

class Field extends APIController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index($request, $response)
	{
		$result = $this->query('SELECT * FROM fields WHERE collection_id = :collection_id ORDER BY `order` ASC', [
			'collection_id' => $request->getQueryParam('collection_id')
		]);
		while ($field = $result->fetchArray(SQLITE3_ASSOC)) {
			$fields[] = $field;
		}

		return $response->withJson($fields ?? []);
	}

	public function get($request, $response, $args)
	{
		$result = $this->query('SELECT * FROM fields WHERE id = :id LIMIT 1', [
			'id' => $args['id']
		]);
		$field = $result->fetchArray(SQLITE3_ASSOC);

		return $response->withJson($field);
	}

	public function create($request, $response)
	{
		$field = $request->getParsedBody();

		$this->query('INSERT INTO fields (collection_id, name, `order`, hidden, shown, description) VALUES (:collection_id, :name, :order, :hidden, :shown, :description)', [
			'collection_id' => $field['collection_id'],
			'name' => $field['name'],
			'order' => $field['order'],
			'hidden' => $field['hidden'],
			'shown' => $field['shown'],
			'description' => $field['description']
		]);
		$id = $this->db->lastInsertRowID();

		return $response->withJson(['id' => $id]);
	}

	public function update($request, $response)
	{
		$field = $request->getParsedBody();

		$this->query('UPDATE fields SET name = :name, hidden = :hidden, shown = :shown, description = :description WHERE id = :id', [
			'id' => $field['id'],
			'name' => $field['name'],
			'hidden' => $field['hidden'],
			'shown' => $field['shown'],
			'description' => $field['description']
		]);

		return $response->withStatus(200);
	}

	public function updateOrder($request, $response)
	{
		$field = $request->getParsedBody();

		$this->query('UPDATE fields SET `order` = :order WHERE id = :id', [
			'id' => $field['id'],
			'order' => $field['order']
		]);

		return $response->withStatus(200);
	}

	public function delete($request, $response)
	{
		$field = $request->getParsedBody();

		$this->query('DELETE FROM fields WHERE id = :id', [
			'id' => $field['id']
		]);

		return $response->withStatus(200);
	}
}
