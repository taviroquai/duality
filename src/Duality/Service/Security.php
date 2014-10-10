<?php

/**
 * Security service
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.12.1
 */

namespace Duality\Service;

use Duality\Core\AbstractService;
use Duality\Core\InterfaceSecurity;

/**
 * Security interface
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.12.1
 */
class Security
extends AbstractService
implements InterfaceSecurity
{
    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        if (!empty($_SERVER['REQUEST_METHOD'])) {
            foreach ($_SERVER[$_SERVER['REQUEST_METHOD']] as $key => &$value) {
                if (!is_array($value)) {
                    $this->filter($value);
                }
            }
        }
    }

    /**
     * Terminates the service
     * 
     * @return void
     */
    public function terminate()
    {

    }

    /**
     * Encrypt
     * 
     * @param string $data Give the data to be encrypt
     * 
     * @return string The resulting encrypted data
     */
    public function encrypt($data)
    {
        // Set defaults
        $algo = 'sha256';
        $salt = '';

        // Apply user configuration if exists
        if ($this->getConfigItem('security')) {
            if ($this->getConfigItem('security.salt')) {
                $salt = $this->getConfigItem('security.salt');
            }
            if ($this->getConfigItem('security.algo') 
                && in_array($this->getConfigItem('security.algo'), hash_algos())
            ) {
                $algo = $this->getConfigItem('security.algo');
            }
        }
        return hash($algo, (string) $data . $salt);
    }

    /**
     * Decrypt
     * 
     * @param string $data Give the data to be decrypted
     * 
     * @return string The resulting decrypted data
     */
    public function decrypt($data)
    {
        return $data;
    }

    /**
     * Filter data
     * 
     * @param string &$data Give the data to be decrypted
     * @param int    $type  Give the type of filter
     * 
     * @return string The resulting decrypted data
     */
    public function filter(&$data, $type = FILTER_SANITIZE_STRING)
    {
        return filter_var($data, $type);
    }
}