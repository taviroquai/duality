<?php

namespace Duality\System\Database;

class MySql extends \Duality\System\Structure\Database
{
	public function getSelect($fields, $from, $limit, $offset)
	{
		$sql = "SELECT $fields FROM ".strtolower((string) $from);
		if ($limit > 0) $sql .= ' LIMIT '.$limit.' OFFSET '.$offset;
		return $sql;
	}
}