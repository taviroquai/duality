<?php

/**
 * Interface for HTTP URL
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
 * Interface for Url
 * 
 * Provides extended functionality for URLs
 * ie. \Duality\Structure\Url
 * Used by \Duality\Core\InterfaceClient
 * Used by \Duality\Core\InterfaceValidator
 * Used by \Duality\Core\InterfacePaginator
 * Used by \Duality\Core\InterfaceServer
 * Used by \Duality\Structure\Http
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
interface InterfaceUrl
{
    /**
     * Sets the URL hostname
     * 
     * @param string $host Give the URL host
     * 
     * @return void
     */
    public function setHost($host);

    /**
     * Gets the requested uri path
     * 
     * @return string The URI part
     */
    public function getUri();

    /**
     * Gets the full URL string
     * 
     * @return string The URL as a string
     */
    public function __toString();
}