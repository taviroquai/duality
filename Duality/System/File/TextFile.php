<?php

namespace Duality\System\File;

use Duality\System\Structure\File;

class TextFile extends File {

	public function __construct($path)
	{
		parent::__construct();
		$this->setPath($path);
	}

}