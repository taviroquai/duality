<?php

/**
 * Validator service
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service;

use Duality\Core\DualityException;
use Duality\Core\AbstractService;
use Duality\Core\InterfaceValidator;
use Duality\Structure\Storage;

/**
 * Default validator service
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Validator
extends AbstractService
{
    /**
     * Holds the messages storage
     * 
     * @var \Duality\Core\InterfaceStorage Holds the messages storage
     */
    protected $storage;

    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        $this->storage = new Storage;
        $this->storage->reset();
    }

    /**
     * Terminates the service
     * 
     * @return void
     */
    public function terminate()
    {
        $this->storage->reset();
    }

    /**
     * Validates an array of rules
     * 
     * @param array $rules Give the rules to validate
     * 
     * @return boolean The validation result
     */
    public function validateAll($rules)
    {
        $this->storage->reset();
        $this->result = true;
        foreach ($rules as $key => $params) {
            $this->result = $this->result & $this->validate($key, $params);
        }
        return $this->result;
    }

    /**
     * Validate one named rule
     * 
     * @param string $key    Give the message key
     * @param array  $params Give the rule configuration
     * 
     * @return boolean The rule result
     */
    public function validate($key, $params)
    {
        $result = true;
        if (empty($params['rules'])
            || empty($params['value'])
            || empty($params['fail']) 
            || empty($params['info'])
        ) {
            throw new DualityException(
                "Error Validation: required params are rules, value, fail and info", 1
            );
        }
        
        $rules = explode('|', $params['rules']);
        foreach ($rules as $item) {
            $values = explode(':', $item);
            $method = 'is'.ucfirst(array_shift($values));
            if (!method_exists($this, $method)) {
                throw new DualityException(
                    "Error Validation: invalid rule name: ".$method, 4
                );
            }
            $tresult = call_user_func_array(
                array($this, $method), array($params['value'], $values)
            );
            if (!$tresult) {
                $this->storage->set($key, $params['fail']);
            } else {
                $this->storage->set($key, $params['info']);
            }
            $result = $result & $tresult;
        }
        return $result;
    }

    /**
     * Get validation result
     * 
     * @return boolean Tells the current validation result
     */
    public function ok()
    {
        return $this->result ? true : false;
    }

    /**
     * Get all messages
     * 
     * @return array Returns all stored messages
     */
    public function getMessages()
    {
        return $this->storage->asArray();
    }

    /**
     * Get message by key
     * 
     * @param string $key Give the key of rule to get resulting message
     * 
     * @return string Returns stored message by key
     */
    public function getMessage($key)
    {
        return $this->storage->get($key);
    }

    /**
     * Rule required
     * 
     * @param string $value Give the value to validate
     * 
     * @return boolean The rule result
     */
    public function isRequired($value)
    {
        return !empty($value);
    }

    /**
     * Rule number
     * 
     * @param string $value Give the value to validate
     * 
     * @return boolean The rule result
     */
    public function isNumber($value)
    {
        return is_numeric($value);
    }

    /**
     * Rule alpha
     * Validate the value containing only letters
     * 
     * @param string $value Give the value to validate
     * 
     * @return boolean The rule result
     */
    public function isAlpha($value)
    {
        return ctype_alpha($value);
    }

    /**
     * Rule email
     * 
     * @param string $value Give the value to validate
     * 
     * @return boolean The rule result
     */
    public function isEmail($value)
    {
        return (boolean) filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Rule equals
     * 
     * @param string $value  Give the value to validate
     * @param array  $params Give the rule parameters
     * 
     * @return boolean The rule result
     */
    public function isEquals($value, $params)
    {
        $params[0] = empty($params[0]) ? '' : $params[0];
        return $value === $params[0];
    }

    /**
     * Rule password
     * 
     * @param string $value Give the value to validate
     * 
     * @return boolean The rule result
     */
    public function isPassword($value)
    {
        return preg_match(
            "#.*^(?=.{6,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $value
        );
    }

    /**
     * Rule length
     * 
     * @param string $value  Give the value to validate
     * @param array  $params Give the Rule parameters
     * 
     * @return boolean The rule result
     */
    public function isLength($value, $params)
    {
        return $this->isBetween(strlen($value), $params);
    }

    /**
     * Rule between
     * 
     * @param string $value  Give the value to validate
     * @param array  $params Give the rule parameters
     * 
     * @return boolean The rule result
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