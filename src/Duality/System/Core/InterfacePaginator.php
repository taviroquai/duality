<?php

namespace Duality\System\Core;

/**
 * Paginator interface
 */
interface InterfacePaginator
{
	
	/**
	 * Configures paginator
	 * @param string $url
	 * @param int $total
	 * @param int $itemsPerPage
	 */
	public function config($url, $total, $itemsPerPage);

	/**
	 * Returns first page link
	 * @return string
	 */
	public function getFirstPageLink();

	/**
	 * Returns last page link
	 * @return string
	 */
	public function getLastPageLink();

	/**
	 * Returns the previous page link
	 * @param int $number
	 * @return string 
	 */
	public function getPreviousPageLink();

	/**
	 * Returns the next page link
	 * @param int $number
	 * @return string 
	 */
	public function getNextPageLink();

	/**
	 * Returns the page link
	 * @param int $number
	 */
	public function getPageLink($number);

	/**
	 * Returns total pages
	 * @return int
	 */
	public function getTotalPages();

	/**
	 * Returns items offset
	 * @return int
	 */
	public function getCurrentOffset();

}