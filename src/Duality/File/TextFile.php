<?php

/**
 * Text file structure
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\File;

use Duality\Structure\File;

/**
 * Text file class
 */
class TextFile extends File {

    /**
     * Creates a new text file by giving its file path
     * @param string $path
     */
	public function __construct($path)
	{
		$this->setPath($path);
	}

}