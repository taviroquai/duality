<?php

/**
 * Basic property structure
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\System\Structure;

use Duality\System\Core\Structure;

/**
 * Property class
 */
class Property 
extends Structure
{	
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