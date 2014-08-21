<?php

namespace Duality\System\Structure;

class Property extends \Duality\System\Core\Structure {
	
	public function __construct($name = '')
	{
		parent::__construct();

		if (!empty($name)) {
			$this->setName($name);
		}
	}
}