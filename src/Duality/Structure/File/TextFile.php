<?php

/**
 * Text file structure
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Structure\File;

use Duality\Structure\File;

/**
 * Text file class
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class TextFile extends File
{
    /**
     * Creates a new text file by giving its file path
     * 
     * @param string $path Give the file path
     */
    public function __construct($path)
    {
        parent::__construct($path);
    }

}