<?php

/**
 * DualityException class
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
 * Core exception class
 * 
 * Provides all errors codes.
 * All errors codes should are in the format E_NAMESPACE_ERRORMSG.
 * ie. \Duality\Core\DualityException::E_APP_PATHNOTFOUND
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class DualityException extends \Exception
{
    const E_APP_PATHNOTFOUND            = 1;
    const E_SERVER_CTRLNOTFOUND         = 2;
    const E_SERVER_ACTIONNOTFOUND       = 3;
    const E_CONFIG_NOTFOUND             = 4;
    const E_EXTENSION_NOTFOUND          = 5;
    const E_FILE_NOTFOUND               = 6;
    const E_FILE_NOTWRITABLE            = 7;
    const E_FILE_NOTREADABLE            = 8;
    const E_FILE_INVALIDTYPE            = 9;
    const E_LOCALE_NOTFOUND             = 10;
    const E_REMOTE_NOTCONNECTED         = 11;
    const E_REMOTE_FINGERPRINTNOTFOUND  = 12;
    const E_REMOTE_AUTHFAILED           = 13;
    const E_CMD_FAILED                  = 14;
    const E_HTTP_METHODNOTFOUND         = 15;
    const E_HTTP_INVALIDTIMESTAMP       = 16;
    const E_HTTP_INVALIDCOOKIE          = 17;
    const E_VALIDATION_INVALIDFILTER    = 18;
    const E_HTTP_INVALIDREQUEST         = 19;
}