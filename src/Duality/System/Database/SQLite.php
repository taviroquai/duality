<?php

namespace Duality\System\Database;

/**
 * Database SQLite query writer
 */
class SQLite extends \Duality\System\Structure\Database
{
    /**
     * Returns a select query
     * @param string $fields
     * @param string $from
     * @param string $limit
     * @param string $offset
     * @return string
     */
	public function getSelect($fields, $from, $limit, $offset)
	{
		$sql = "SELECT $fields FROM ".strtolower((string) $from);
		if ($limit > 0) {
            $sql .= ' LIMIT '.$limit.' OFFSET '.$offset;
        }
		return $sql;
	}
}