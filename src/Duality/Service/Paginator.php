<?php

/**
 * Paginator service
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
use Duality\Core\InterfacePaginator;

/**
 * Paginator Service
 * 
 * Provides operations for dealing with pagination
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Paginator
extends AbstractService
implements InterfacePaginator
{
    /**
     * Holds the base url
     * 
     * @var string Holds the pagination base url
     */
    protected $url;

    /**
     * Holds the total items
     * 
     * @var integer Holds the total items
     */
    protected $total;

    /**
     * Holds the number of items per page
     * 
     * @var integer Holds the number of items per page
     */
    protected $itemsPerPage;

    /**
     * Holds the current page
     * 
     * @var string Holds the current page number
     */
    protected $current;

    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        $this->total = 1;
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
     * Configures paginator
     * 
     * @param string $url          Give the base url
     * @param int    $total        Give the total items
     * @param int    $itemsPerPage Give the number of items per page
     * 
     * @return void
     */
    public function config($url, $total, $itemsPerPage)
    {
        $this->url = $url;
        $this->total = $total;
        $this->itemsPerPage = $itemsPerPage;
        $this->parseCurrentPageInput();
    }

    /**
     * Parse current page from input
     * 
     * @return void
     */
    protected function parseCurrentPageInput()
    {
        $params = $this->app->call('server')->getRequest()->getParams();
        if (isset($params['page'])) {
            if ((int) $params['page'] < 0 
                || (int) $params['page'] > $this->getTotalPages()
            ) {
                $params['page'] = 1;
            }
            $this->current = (int) $params['page'];
        }
    }

    /**
     * Returns first page URL
     * 
     * @return string The first page URL
     */
    public function getFirstPageUrl()
    {
        return $this->url.'?page=1';
    }

    /**
     * Returns last page URL
     * 
     * @return string The last page URL
     */
    public function getLastPageUrl()
    {
        return $this->url.'?page='. $this->getTotalPages();
    }

    /**
     * Returns the previous page URL
     * 
     * @return string The previous page URL
     */
    public function getPreviousPageUrl()
    {
        return $this->getPageUrl($this->current - 1);
    }

    /**
     * Returns the next page URL
     * 
     * @return string The next page URL
     */
    public function getNextPageUrl()
    {
        return $this->getPageUrl($this->current + 1);
    }

    /**
     * Returns the page URL
     * 
     * @param int $number Give the page number
     * 
     * @return string The page URL
     */
    public function getPageUrl($number)
    {
        if ($number < 1 || $number > $this->getTotalPages()) {
            return '';
        }
        return $this->url.'?page='. $number;
    }

    /**
     * Returns total pages
     * 
     * @return int The resulting total pages from configuration
     */
    public function getTotalPages()
    {
        return ceil($this->total / $this->itemsPerPage);
    }

    /**
     * Returns items offset
     * 
     * @return int The current item offset, from current page
     */
    public function getCurrentOffset()
    {
        return (int) ceil($this->current * $this->itemsPerPage) 
            - $this->itemsPerPage;
    }

}