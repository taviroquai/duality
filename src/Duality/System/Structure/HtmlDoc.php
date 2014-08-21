<?php

namespace Duality\System\Structure;

use Duality\System\Core\Structure;
use Duality\System\File\TextFile;

class HtmlDoc extends Structure
{
	protected $doc;

	public function __construct()
	{
		$this->doc = new \DOMDocument('1.0', 'UTF-8');
		$this->doc->loadHTML( "<!DOCTYPE html>\n<html><head><title></title></head><body></body></html>" );
	}

	public function loadFile(TextFile $file)
	{
		$this->doc->loadHTML($file->getContent());
	}

	public function save()
	{
		return $this->doc->saveHTML();
	}

	public function setTitle($title)
	{
		$textNode = $this->doc->createTextNode($title);
		$node = $this->doc->getElementsByTagName('title')->item(0);
		$node->appendChild($textNode);
	}

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
}