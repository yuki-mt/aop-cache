<?php

namespace YukiMt\DataSource;

/**
 * This is assumed a long-term database such as MySQL
 */
class Database
{
	private $records;

	public function __construct(){
		$this->records = array();

		$this->records[0] = array();
		$this->records[0]['id'] = 1;
		$this->records[0]['score'] = 100.0;

		$this->records[1] = array();
		$this->records[1]['id'] = 2;
		$this->records[1]['score'] = 120.0;
	}

	public function getById(int $id): array {
		foreach ($this->records as $row) {
			if($row['id'] == $id)
				return $row;
		}
		return array();
	}

	public function updateById(int $id, float $score): bool{
		foreach ($this->records as &$row) {
			if($row['id'] == $id){
				$row['score'] = (float)$score;
				return true;
			}
		}
		return false;
	}
}
