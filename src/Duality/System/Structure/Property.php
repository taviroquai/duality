<?php

namespace Duality\System\Structure;

use Duality\System\Core\Structure;

/**
 * Property class
 */
class Property extends Structure {
	
    /**
     * Creates a new property
     * @param string $name
     */
	public function __construct($name = '')
	{
		if (!empty($name)) {
			$this->setName($name);
		}
	}
}