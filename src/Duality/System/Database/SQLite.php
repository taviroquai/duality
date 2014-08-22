<?php

namespace Duality\System\Database;

use \Duality\System\Structure\Database;

/**
 * SQLite database query writer
 */
class SQLite extends Database
{

    public function __construct($dsn, $options = array())
    {
        parent::__construct($dsn, '', '', $options = array());
    }

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