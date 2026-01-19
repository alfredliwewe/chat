<?php

/**
 * URL
 */
class URL
{
	public $link = "";

	function __construct($link)
	{
		$this->link = $link;
	}

	public function getQuery()
	{
		$query = parse_url($this->link, PHP_URL_QUERY);
		$chars = explode("&", $query);
		$data = [];

		foreach ($chars as $part) {
			$dd = explode("=", $part);
			if (count($dd) > 1) {
				$data[$dd[0]] = $dd[1];
			}
			else{
				$data[$dd[0]] = "";
			}
		}

		return $data;
	}
}