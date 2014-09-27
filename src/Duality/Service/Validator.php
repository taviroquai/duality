<?php

/**
 * Validator service
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\Service;

use Duality\Core\DualityException;
use Duality\Core\InterfaceService;
use Duality\Core\InterfaceStorage;
use Duality\Core\InterfaceValidator;
use Duality\App;

/**
 * Default validator service
 */
class Validator 
implements InterfaceStorage, InterfaceService, InterfaceValidator
{
	/**
	 * The dependent application container
	 * @var Duality\App
	 */
	protected $app;

	/**
	 * Holds the validation messages
	 * @var array
	 */
	protected $messages;

	/**
	 * Creates a new error handler
	 * @param Duality\App $app
	 */
	public function __construct(App &$app)
	{
		$this->app = & $app;
	}

	/**
	 * Initiates the service
	 */
	public function init()
	{
		$this->reset();
	}

	/**
	 * Terminates the service
	 */
	public function terminate()
	{
		$this->reset();
	}

	/**
	 * Process Form Assist validation
	 * @param Duality\Http\Request $req
	 * @param array $rules
	 * @param array $outputJson
	 */
	public function validateAssist(&$req, $rules, &$outputJson)
	{
		$input = $req->getParams();
		if (
			$req->hasParam('_assist_rule') 
			&& array_key_exists($input['_assist_rule'], $rules)
		) {
			$key = $req->getParam('_assist_rule');
			$outputJson['result'] = (int) $this->validate($key, $rules[$key]);
			$outputJson['msg'] = $this->get($key);
			if ($outputJson['result'] === 0) {
				$outputJson['type']	= 'has-error';
			}
		}
	}

	/**
	 * Validate all rules
	 * @param array $rules
	 */
	public function validateAll($rules)
	{
		$this->reset();
		$this->result = true;
		foreach ($rules as $key => $params) {
			$this->result = $this->result & $this->validate($key, $params);
		}
		return $this->result;
	}

	/**
	 * Validate all rules
	 * @param string $key
	 * @param array $params
	 * @return boolean
	 */
	public function validate($key, $params)
	{
		$result = true;
		if (!isset($params['rules'])) {
			throw new DualityException("Error Validation: rules required", 1);
		}
		if (!isset($params['value'])) {
			throw new DualityException("Error Validation: value required", 1);
		}
		if (!isset($params['fail']) || !isset($params['info'])) {
			throw new DualityException("Error Validation: fail and info messages are required", 2);
		}
		$rules = explode('|', $params['rules']);
		foreach ($rules as $item) {
			$values = explode(':', $item);
			$method = 'is'.ucfirst(array_shift($values));
			if (!method_exists($this, $method)) {
				throw new DualityException("Error Validation: invalid rule name: ".$method, 3);
			}
			$tresult = call_user_func_array(array($this, $method), array($params['value'], $values));
			if (!$tresult) {
				$this->set($key, $params['fail']);
			} else {
				$this->set($key, $params['info']);
			}
			$result = $result & $tresult;
		}
		return $result;
	}

	/**
	 * Get result
	 * @return boolean
	 */
	public function ok()
	{
		return $this->result ? true : false;
	}

	/**
	 * Get all messages
	 * @return array
	 */
	public function getMessages()
	{
		return $this->messages();
	}

	/**
	 * Save item
	 * @param string $key
	 * @param string $value
	 */
	public function set($key, $value)
	{
		$this->messages[$key] = $value;
	}

	/**
	 * Return item
	 * @param string $key
	 * @return mixed
	 */
	public function get($key)
	{
		return isset($this->messages[$key]) ? $this->messages[$key] : NULL;
	}

	/**
	 * Reset a session
	 * @return boolean
	 */
	public function reset()
	{
		$this->result = false;
		$this->messages = array();
		return true;
	}

	/**
	 * Validate required
	 * @param string $value
	 * @return boolean
	 */
	public function isRequired($value)
	{
		return !empty($value);
	}

	/**
	 * Validate number
	 * @param string $value
	 * @return boolean
	 */
	public function isNumber($value)
	{
		return is_numeric($value);
	}

	/**
	 * Validate value containing only letters
	 * @param string $value
	 * @return boolean
	 */
	public function isAlpha($value)
	{
		return ctype_alpha($value);
	}

	/**
	 * Validate email
	 * @param string $value
	 * @return boolean
	 */
	public function isEmail($value)
	{
		return (boolean) filter_var($value, FILTER_VALIDATE_EMAIL);
	}

	/**
	 * Validate equal values
	 * @param string $value
	 * @param array $params
	 * @return boolean
	 */
	public function isEquals($value, $params)
	{
		if (!isset($params[0])) {
			return false;
		}
		return $value === $params[0];
	}

	/**
	 * Validate password
	 * @param string $value
	 * @return boolean
	 */
	public function isPassword($value)
	{
		return preg_match("#.*^(?=.{6,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $value);
	}

	/**
	 * Validate lenght
	 * @param string $value
	 * @param array $params
	 * @return boolean
	 */
	public function isLength($value, $params)
	{
		return $this->isBetween(strlen($value), $params);
	}

	/**
	 * Validate value between
	 * @param string $value
	 * @param array $params
	 * @return boolean
	 */
	public function isBetween($value, $params)
	{
		$result = true;
		if (isset($params[0])) {
			$result = $result & ($value >= (int) $params[0]);
		}
		if (isset($params[1])) {
			$result = $result & ($value <= (int) $params[1]);
		}
		return $result;
	}

}