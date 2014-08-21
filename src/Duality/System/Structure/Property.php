<?php

namespace Duality\System\Structure;

/**
 * Property class
 */
class Property extends \Duality\System\Core\Structure {
	
    /**
     * Creates a new property
     * @param string $name
     */
	public function __construct($name = '')
	{
		parent::__construct();
		if (!empty($name)) {
			$this->setName($name);
		}
	}
}