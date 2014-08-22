<?php

namespace Duality\System\Database;

use \Duality\System\Structure\Database;

/**
 * MySql database query writer
 */
class MySql extends Database
{

    public function __construct($dsn, $user, $pass, $options = array())
    {
        parent::__construct($dsn, $user, $pass, $options = array());
    }

    /**
     * Returns a select query
     * @param string $fields
     * @param string $from
     * @param string $limit
     * @param string $offset
     * @return string
     */
    public function getSelect($fields, $from, $where, $limit, $offset)
    {
        $sql = "SELECT $fields FROM ".strtolower((string) $from);
        if (!empty($where)) {
            $sql .= " WHERE ".$where;
        }
        if ($limit > 0) {
            $sql .= ' LIMIT '.$limit.' OFFSET '.$offset;
        }
        return $sql;
    }
}