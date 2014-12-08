<?php

/**
 * Validator service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service;

use Duality\Core\AbstractService;
use Duality\Core\InterfaceValidator;
use Duality\Structure\Storage;
use Duality\Structure\RuleItem;

/**
 * Default validator service
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Validator
extends AbstractService
implements InterfaceValidator
{
    /**
     * Holds the messages msgStorage
     * 
     * @var \Duality\Core\InterfaceStorage Holds the messages storage
     */
    protected $msgStorage;

    /**
     * Holds the rules items storage
     * 
     * @var \Duality\Core\InterfaceStorage Holds the rules items storage
     */
    protected $itemsStorage;

    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        $this->msgStorage = new Storage;
        $this->itemsStorage = new Storage;
    }

    /**
     * Terminates the service
     * 
     * @return void
     */
    public function terminate()
    {
        $this->msgStorage->reset();
        $this->itemsStorage->reset();
    }

    /**
     * Adds a rule to be validated
     * 
     * @param \Duality\Structure\RuleItem The item to be validated
     * 
     * @return void
     */
    public function addRuleItem(RuleItem $item)
    {
        $this->itemsStorage->set($item->getKey(), $item);
    }

    /**
     * Validates all the rules
     * 
     * @return boolean The validation result
     */
    public function validate()
    {
        $this->msgStorage->reset();
        $this->result = true;
        foreach ($this->itemsStorage->asArray() as $key => $item) {
            $this->result = $this->result & $this->validateRule($item);
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
    protected function validateRule(RuleItem $rule)
    {
        $result = true;
        
        $filters = $rule->getFilters();
        foreach ($filters as $item) {
            $values = explode(':', $item);
            $method = 'is'.ucfirst(array_shift($values));
            $rule->setResult(call_user_func_array(
                array($this, $method), array($rule->getValue(), $values)
            ));
            $this->msgStorage->set($rule->getKey(), $rule->getMessage());
            $result = $result & $rule->getResult();
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
        return $this->msgStorage->asArray();
    }
    
    /**
     * Get only error messages
     * 
     * @return array Returns only error messages
     */
    public function getErrorMessages()
    {
        $messages = array();
        foreach ($this->itemsStorage->asArray() as $rule) {
            if (!$rule->getResult()) {
                $messages[$rule->getKey()] = $rule->getMessage();
            }
        }
        return $messages;
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
        return $this->msgStorage->get($key);
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