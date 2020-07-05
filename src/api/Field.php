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

		$this->query('INSERT INTO fields (collection_id, name, prefix, suffix, `order`, hidden, shown, description) VALUES (:collection_id, :name, :prefix, :suffix, :order, :hidden, :shown, :description)', [
			'collection_id' => $field['collection_id'],
			'name' => $field['name'],
			'prefix' => $field['prefix'],
			'suffix' => $field['suffix'],
			'order' => 0,
			'hidden' => $field['hidden'],
			'shown' => $field['shown'],
			'description' => $field['description']
		]);
		$id = $this->db->lastInsertRowID();

		// Add the new field to all existing items

		$stmt = $this->db->prepare('UPDATE items SET data = :data WHERE id = :id');

		$result = $this->query('SELECT * FROM items WHERE collection_id = :collection_id', [
			'collection_id' => $field['collection_id']
		]);

		while ($item = $result->fetchArray(SQLITE3_ASSOC)) {

			$itemData = json_decode($item['data'], true);
			$itemData[$id] = '';
			$item['data'] = json_encode($itemData);

			$stmt->bindValue(':id', $item['id']);
			$stmt->bindValue(':data', $item['data']);
			$stmt->execute();

		}

		return $response->withJson(['id' => $id]);
	}

	public function update($request, $response)
	{
		$field = $request->getParsedBody();

		$this->query('UPDATE fields SET name = :name, prefix = :prefix, suffix = :suffix, hidden = :hidden, shown = :shown, description = :description WHERE id = :id', [
			'id' => $field['id'],
			'name' => $field['name'],
			'prefix' => $field['prefix'],
			'suffix' => $field['suffix'],
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

		// Remove the field from all existing items

		$stmt = $this->db->prepare('UPDATE items SET data = :data WHERE id = :id');

		$result = $this->query('SELECT * FROM items WHERE collection_id = :collection_id', [
			'collection_id' => $field['collection_id']
		]);

		while ($item = $result->fetchArray(SQLITE3_ASSOC)) {

			$itemData = json_decode($item['data'], true);
			unset($itemData[$field['id']]);
			$item['data'] = json_encode($itemData);

			$stmt->bindValue(':id', $item['id']);
			$stmt->bindValue(':data', $item['data']);
			$stmt->execute();

		}

		return $response->withStatus(200);
	}
}
