<?php

namespace Duality\System\Structure;

class Database {

	protected $pdo;

	protected $tables = array();

	public function __construct($dsn, $user, $pass, $options = array())
	{
		$this->pdo = new \PDO($dsn, $user, $pass, $options);
	}

	public function addTable(Table $table)
	{
		$this->tables[] = $table;
	}

	public function getTables()
	{
		return $this->tables;
	}

	public function getPDO()
	{
		return $this->pdo;
	}

}