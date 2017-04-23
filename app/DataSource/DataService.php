<?php

namespace YukiMt\DataSource;

use YukiMt\DataSource\Cache\Annotation\UseCache;

class DataService
{
	private $db;

	public function __construct(){
		$this->db = new Database();
	}

	/**
	 * @UseCache
	 */
	public function getById($id, $useCache = true, $saveCache = true) {
		//can be more complicated;
		return $this->db->getById($id);
	}

	public function updateById($id, $score) {
		return $this->db->updateById($id, $score);
	}
}
