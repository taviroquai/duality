<?php

/**
 * HTML document structure
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\System\Structure;

use Duality\System\Core\Structure;
use Duality\System\File\TextFile;

/**
 * HTML document class
 */
class HtmlDoc 
extends Structure
{
    /**
     * Holds the DOM document
     * @var \DOMDocument
     */
	protected $doc;

    /**
     * Creates a new HTML document
     */
	public function __construct()
	{
		$this->doc = new \DOMDocument('1.0', 'UTF-8');
		$this->doc->loadHTML( "<!DOCTYPE html>\n<html><head><title></title></head><body></body></html>" );
	}

    /**
     * Imports the HTML from a text file
     * @param \Duality\System\File\TextFile $file
     */
	public function loadFile(TextFile $file)
	{
		$this->doc->loadHTML($file->getContent());
	}

    /**
     * Gets the full document string
     * @return string
     */
	public function save()
	{
		return $this->doc->saveHTML();
	}

    /**
     * Sets the document title
     * @param string $title
     */
	public function setTitle($title)
	{
		$textNode = $this->doc->createTextNode($title);
		$node = $this->doc->getElementsByTagName('title')->item(0);
		$node->appendChild($textNode);
	}

    /**
     * Appends HTML to the queried elements
     * @param string $query
     * @param string $html
     */
	public function appendTo($query, $html)
	{
		$newdoc = new \DOMDocument;
		$newdoc->loadXML($html);

		$xpath = new \DOMXpath($this->doc);
		$elems = $xpath->query($query);
		foreach ($elems as $el) {
			$node = $this->doc->importNode($newdoc->documentElement, true);
			$el->appendChild($node);
		}
	}

	/**
	 * Creates an HTML document from file path
	 */
	public static function createFromFilePath($path)
	{
		$doc = new HtmlDoc();
		$template = new TextFile($path);
		$template->getContent();
		$doc->loadFile($template);
		return $doc;
	}
}