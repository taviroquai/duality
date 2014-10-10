<?php

/**
 * Interface for pagination
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Core;

/**
 * Paginator interface
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
interface InterfacePaginator
{
    /**
     * Configures paginator
     * 
     * @param string $url          Give the URL to create pagination links
     * @param int    $total        Give the number of total items
     * @param int    $itemsPerPage Give the number of items per page
     * 
     * @return void
     */
    public function config($url, $total, $itemsPerPage);

    /**
     * Returns first page link
     * 
     * @return string The page URL
     */
    public function getFirstPageUrl();

    /**
     * Returns last page link
     * 
     * @return string The page URL
     */
    public function getLastPageUrl();

    /**
     * Returns the previous page link
     * 
     * @return string The page URL
     */
    public function getPreviousPageUrl();

    /**
     * Returns the next page link
     * 
     * @return string The page URL
     */
    public function getNextPageUrl();

    /**
     * Returns the page link
     * 
     * @param int $number Give the page number
     * 
     * @return string The page URL
     */
    public function getPageUrl($number);

    /**
     * Returns total pages
     * 
     * @return int The number of total pages
     */
    public function getTotalPages();

    /**
     * Returns items offset
     * 
     * @return int The current page item offset
     */
    public function getCurrentOffset();

}