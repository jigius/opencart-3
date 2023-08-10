<?php

/**
 * Faked config instance
 */
final class FakedConfig
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
	 * @param string $key
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	public function get(string $key) {
		if (!isset($this->params[$key])) {
			throw new InvalidArgumentException("unknown key");
		}
		return $this->params[$key];
	}
}