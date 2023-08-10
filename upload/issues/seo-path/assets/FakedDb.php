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
	public function query(string $stmt): object
	{
		$ret = [
			'num_rows' => 0
		];
		if (preg_match("~\bproduct_id\b~", $stmt)) {
			if (!empty($this->params['product'])) {
				$ret['num_rows'] = 1;
				$ret['row'] = ['keyword' => $this->params['product']];
			}
		} elseif (preg_match("~\bcategory_id=(\d+)\b~", $stmt, $m)) {
			if (!empty($this->params['category'])) {
				if (is_array($this->params['category'])) {
					if (isset($this->params['category'][$m[1]])) {
						$ret['num_rows'] = 1;
						$ret['row'] = ['keyword' => $this->params['category'][$m[1]]];
					}
				} else {
					$ret['num_rows'] = 1;
					$ret['row'] = ['keyword' => $this->params['category']];
				}
			}
		}
		return (object)$ret;
	}
	
	/**
	 * @param $val
	 * @return mixed
	 */
	public function escape($val) {
		return $val;
	}
}
