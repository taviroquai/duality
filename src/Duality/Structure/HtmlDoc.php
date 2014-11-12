<?php

/**
 * HTML document structure
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Structure;

use Duality\Core\Structure;
use Duality\Structure\File\TextFile;

/**
 * HTML document class
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class HtmlDoc 
extends Structure
{
    /**
     * Holds the DOM document
     * 
     * @var \DOMDocument Holds the DOM document
     */
    protected $doc;

    /**
     * Creates a new HTML document
     */
    public function __construct()
    {
        $this->doc = new \DOMDocument('1.0', 'UTF-8');
        $this->doc->loadHTML(
            "<!DOCTYPE html>\n<html><head><title></title></head><body></body></html>"
        );
    }

    /**
     * Imports the HTML from a text file
     * 
     * @param \Duality\File\TextFile $file Give the target file to load
     * 
     * @return boolean The load HTML result
     */
    public function loadFile(TextFile $file)
    {
        return $this->doc->loadHTML($file->getContent());
    }

    /**
     * Gets the full document string
     * 
     * @return string Returns the document as string
     */
    public function save()
    {
        return $this->doc->saveHTML();
    }

    /**
     * Sets the document title
     * 
     * @param string $title Give a document title
     * 
     * @return void
     */
    public function setTitle($title)
    {
        $textNode = $this->doc->createTextNode($title);
        $node = $this->doc->getElementsByTagName('title')->item(0);
        $node->appendChild($textNode);
    }

    /**
     * Appends HTML to the queried elements
     * 
     * @param string $query Give the xpath query to locate the node
     * @param string $html  Give the HTML to insert
     * 
     * @return void
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
     * 
     * @param string $path Give the file to load the HTML into DOM document
     * 
     * @return \Duality\Structure\HtmlDoc A new HtmlDoc instance
     */
    public static function createFromFilePath($path)
    {
        $doc = new HtmlDoc();
        $template = new TextFile($path);
        $template->load();
        $template->getContent();
        $doc->loadFile($template);
        return $doc;
    }

    /**
     * Converts to string
     * 
     * @return void
     */
    public function __toString()
    {
        return $this->save();
    }
}