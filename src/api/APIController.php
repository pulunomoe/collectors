<?php

namespace Collectors\API;

class APIController
{
	protected $db;

	public function __construct()
	{
		$dbFile = getenv('COLLECTORS_ENV') == 'DEV' ? 'collectors_test.db' : 'collectors.db';
		$this->db = new \SQLite3(__DIR__.'/../../'.$dbFile);
	}

	protected function query($sql, $params = [])
	{
		$stmt = $this->db->prepare($sql);
		foreach ($params as $k => $v) {
			$stmt->bindValue(':'.$k, $v);
		}
		return $stmt->execute();
	}
}
