<?php

/**
 * Interface for data validator
 *
 * @since 		0.7.0
 * @author 		Marco Afonso <mafonso333@gmail.com>
 * @license 	MIT
 */

namespace Duality\System\Core;

/**
 * Validator interface
 */
interface InterfaceValidator
{
	/**
	 * Validate rule name (key) with params
	 *
	 * Example:
	 * array(
	 *     'value'	=> 'email@domain.pt',
	 *     'rules'	=> 'required|email',
	 *     'fail'	=> 'Invalid password',
	 *     'info'	=> 'Password is OK!'
	 * )
	 *
	 * @param string $key
	 * @param array $params
	 * @return boolean
	 */
	public function validate($key, $params);

	/**
	 * Validate all rules. Each rule as a key.
	 * 
	 * Example:
	 * $rules = array(
	 *     'email' => array(
	 *         'value'	=> 'email@domain.pt',
	 *         'rules'	=> 'required|email',
	 *         'fail'	=> 'Invalid password',
	 *         'info'	=> 'Password is OK!'
	 *     )
	 * )
	 * 
	 * @param array $rules
	 */
	public function validateAll($rules);

	/**
	 * Get all validation messages
	 * @return array
	 */
	public function getMessages();

	/**
	 * Get validation result
	 * @return boolean
	 */
	public function ok();

}