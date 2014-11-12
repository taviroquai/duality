<?php

/**
 * Validation rule item
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   1.0.0-beta
 */

namespace Duality\Structure;

use Duality\Core\DualityException;
use Duality\Core\InterfaceRuleItem;

/**
 * Validation rule item
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   1.0.0-beta
 */
class RuleItem
implements InterfaceRuleItem
{
	/**
	 * Holds the item key
	 * 
	 * @var string
	 */
	protected $key;

	/**
	 * Holds the value to validate
	 * 
	 * @var string
	 */
	protected $value;

	/**
	 * Holds the validation filters
	 * 
	 * @var array
	 */
	protected $filters;

	/**
	 * Holds the validation result
	 * 
	 * @var boolean
	 */
	protected $result;

	/**
	 * Holds the pass message
	 * 
	 * @var string
	 */
	protected $passMsg = 'The %s is valid';

	/**
	 * Holds the fail message
	 * 
	 * @var string
	 */
	protected $failMsg = 'The %s is invalid';

	/**
	 * Creates a new validation item
	 * 
	 * @param string $key     The item key
	 * @param string $value   The value to validate
	 * @param string $filters The list of filters separated by "|"
	 */
	public function __construct($key, $value, $filters)
	{
		$this->result 	= null;
		$this->key 		= $key;
		$this->value 	= $value;
		$this->filters	= array_filter(explode('|', $filters));

		// Validate configuration
		$this->validateConfig();
	}

	/**
	 * Validates the item configuration
	 * 
	 * @return void
	 */
	protected function validateConfig()
	{
		if (empty($this->filters)) {
			throw new DualityException(
                "Error Validation: validation filters cannot be empty", 1
            );
		}
		foreach ($this->filters as $item) {
			$values = explode(':', $item);
			$method = 'is'.ucfirst(array_shift($values));
	        if (!method_exists('\Duality\Service\Validator', $method)) {
	            throw new DualityException(
	                "Error Validation: invalid rule name: ".$method, 2
	            );
	        }
		}
	}

	/**
	 * Sets the pass message
	 * 
	 * @param string $msg The pass message
	 * 
	 * @return void
	 */
	public function setPassMessage($msg)
	{
		$this->passMsg = $msg;
	}

	/**
	 * Sets the fail message
	 * 
	 * @param string $msg The fail message
	 * 
	 * @return void
	 */
	public function setFailMessage($msg)
	{
		$this->failMsg = $msg;
	}

	/**
	 * Sets the validation result
	 * 
	 * @param boolean $msg The result as true or false
	 * 
	 * @return void
	 */
	public function setResult($result)
	{
		$this->result = (boolean) $result;
	}

	/**
	 * Gets the validation result
	 * 
	 * @return boolean
	 */
	public function getResult()
	{
		return (boolean) $this->result;
	}

	/**
	 * Gets the rule key
	 * 
	 * @return string
	 */
	public function getKey()
	{
		return (string) $this->key;
	}

	/**
	 * Gets the value to be validated
	 * 
	 * @return string
	 */
	public function getValue()
	{
		return (string) $this->value;
	}

	/**
	 * Gets the filters
	 * 
	 * @return array
	 */
	public function getFilters()
	{
		return (array) $this->filters;
	}

	/**
	 * Gets the validation message
	 * 
	 * @return void
	 */
	public function getMessage()
	{
		$msg = sprintf($this->failMsg, $this->key);
		if ($this->result) {
			$msg = sprintf($this->passMsg, $this->key);
		}
		return $msg;
	}
}