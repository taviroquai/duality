<?php

/**
 * HTTP response structure
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Structure\Http;

use Duality\Structure\Http;

/**
 * HTTP response class
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Response extends Http
{
    /**
     * Sends HTTP Headers if supported by SAPI
     * 
     * @return void
     */
    public function sendHeaders()
    {
        $sapi_type = php_sapi_name();
        $no_support = array('cli', 'cli-server');
        if (!in_array($sapi, $no_suport)) {       
            http_response_code($this->getStatus());
            foreach ($this->getHeaders() as $k => $v) {
                header($k.': '.$v);
            }
        }
    }
}