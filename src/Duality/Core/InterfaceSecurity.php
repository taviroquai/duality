<?php

/**
 * Interface for security
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Core;

/**
 * Security interface
 * 
 * Provides an interface for all Duality security services
 * ie. \Duality\Service\Security
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
interface InterfaceSecurity
{
    /**
     * Encrypt
     * 
     * @param string $data Give the data to be encrypt
     * 
     * @return string The resulting encrypted data
     */
    public function encrypt($data);

    /**
     * Decrypt
     * 
     * @param string $data Give the data to be decrypted
     * 
     * @return string The resulting decrypted data
     */
    public function decrypt($data);

}