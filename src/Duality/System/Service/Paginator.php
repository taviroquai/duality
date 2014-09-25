<?php

/**
 * Paginator service
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\System\Service;

use Duality\System\Core\InterfaceService;
use Duality\System\Core\InterfacePaginator;
use Duality\System\App;

/**
 * Paginator Service
 */
class Paginator 
implements InterfaceService, InterfacePaginator
{

	/**
	 * The dependent application container
	 * @var Duality\System\App
	 */
	protected $app;

	/**
	 * Holds the base url
	 * @var string
	 */
	protected $url;

	/**
	 * Holds the total items
	 * @var integer
	 */
	protected $total;

	/**
	 * Holds the number of items per page
	 * @var integer
	 */
	protected $itemsPerPage;

	/**
	 * Holds the current logged username
	 * @var string
	 */
	protected $current;

	/**
	 * Creates a new error handler
	 * @param Duality\System\App $app
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
	}

	/**
	 * Initiates the service
	 */
	public function init()
	{
		$this->total = 1;
	}

	/**
	 * Terminates the service
	 */
	public function terminate()
	{

	}

	/**
	 * Configures paginator
	 * @param string $url
	 * @param int $total
	 * @param int $itemsPerPage
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
	 */
	protected function parseCurrentPageInput()
	{
		$params = $this->app->call('server')->getRequest()->getParams();
		if (isset($params['page'])) {
			if ((int) $params['page'] < 0 || (int) $params['page'] > $this->getTotalPages()) {
				$params['page'] = 1;
			}
			$this->current = (int) $params['page'];
		}
	}

	/**
	 * Returns first page link
	 * @return string
	 */
	public function getFirstPageLink()
	{
		return $this->url.'?page=1';
	}

	/**
	 * Returns last page link
	 * @return string
	 */
	public function getLastPageLink()
	{
		return $this->url.'?page='. $this->getTotalPages();
	}

	/**
	 * Returns the previous page link
	 * @param int $number
	 * @return string 
	 */
	public function getPreviousPageLink()
	{
		return $this->getPageLink($this->current - 1);
	}

	/**
	 * Returns the next page link
	 * @param int $number
	 * @return string 
	 */
	public function getNextPageLink()
	{
		return $this->getPageLink($this->current + 1);
	}

	/**
	 * Returns the page link
	 * @param int $number
	 * @return string 
	 */
	public function getPageLink($number)
	{
		if ($number < 0 || $number > $this->getTotalPages()) {
			return '';
		}
		return $this->url.'?page='. $number;
	}

	/**
	 * Returns total pages
	 * @return int
	 */
	public function getTotalPages()
	{
		return ceil($this->total / $this->itemsPerPage);
	}

	/**
	 * Returns items offset
	 * @return int
	 */
	public function getCurrentOffset()
	{
		return (int) ceil($this->current * $this->itemsPerPage) - $this->itemsPerPage;
	}

}