<?php

namespace Duality\System\File;

use Duality\System\Structure\File;

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
		parent::__construct();
		$this->setPath($path);
	}

}