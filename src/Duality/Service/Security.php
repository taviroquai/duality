<?php

/**
 * Security service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.12.1
 */

namespace Duality\Service;

use Duality\Core\AbstractService;
use Duality\Core\InterfaceSecurity;
use Duality\Structure\Http\Request;

/**
 * Security service
 * 
 * Provides operations for dealing with data security
 * 
 * PHP Version 5.3.4
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
        
    }
    
    /**
     * Secures HTTP request params
     * 
     * @return void
     */
    public function secureHTTPRequest(Request &$request)
    {
        $params = $request->getParams();
        foreach ($params as $key => &$value) {
            $request->filter($value);
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
        if ($this->app->getConfigItem('security')) {
            if ($this->app->getConfigItem('security.salt')) {
                $salt = $this->app->getConfigItem('security.salt');
            }
            if ($this->app->getConfigItem('security.algo') 
                && in_array($this->app->getConfigItem('security.algo'), hash_algos())
            ) {
                $algo = $this->app->getConfigItem('security.algo');
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
}