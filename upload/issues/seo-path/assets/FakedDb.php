<?php

/**
 * Faked instance of Db connector
 */
final class FakedDb
{
	/**
	 * @var array
	 */
	private array $params;
	
	/**
	 * Cntr
	 */
	public function __construct(array $params) {
		$this->params = $params;
	}
	
	/**
	 * Emulates request for products and categories only. For others - always returns negative response
	 * @param string $stmt
	 * @return object
	 */
	public function query(string $stmt) {
		$ret = (object)[];
		if (preg_match("~\bproduct_id\b~", $stmt)) {
			if (!empty($this->params['product'])) {
				$ret->num_rows = 1;
				$ret->row = ['keyword' => $this->params['product']];
			} else {
				$ret->num_rows = 0;
			}
		} elseif (preg_match("~\bcategory_id\b~", $stmt)) {
			if (!empty($this->params['category'])) {
				$ret->num_rows = 1;
				$ret->row = ['keyword' => $this->params['category']];
			} else {
				$ret->num_rows = 0;
			}
		} else {
			$ret->num_rows = 0;
		}
		return $ret;
	}
	
	/**
	 * @param $val
	 * @return mixed
	 */
	public function escape($val) {
		return $val;
	}
}
