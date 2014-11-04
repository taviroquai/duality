<?php

/**
 * Stream file structure
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Structure\File;

use Duality\Core\DualityException;
use Duality\Structure\File;

/**
 * The stream class
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class StreamFile extends File
{
    /**
     * Stream resource handler
     * 
     * @var resource The stream resource handler
     */
    protected $handler;

    /**
     * Holds the stream file path
     * 
     * @param string $path The file path
     */
    public function __construct($path)
    {
        parent::__construct($path);
    }

    /**
     * Opens the stream
     * 
     * @param string $options Give the stream open options, ie. 'w+b'
     * 
     * @throws \Duality\Core\DualityException When cannot open file
     * 
     * @return void
     */
    public function open($options = 'w+b')
    {
        if (!is_resource($this->handler)) {
            $this->handler = @fopen($this->path, $options);
        }
        if (!is_resource($this->handler)) {
            throw new DualityException(
                "Could not open stream: ".$this->getPath(), 5
            );
        }
    }

    /**
     * Closes the stream
     * 
     * @return boolean The close result
     */
    public function close()
    {
        if (is_resource($this->handler)) {
            return fclose($this->handler);
        }
        return false;
    }

    /**
     * Sets up the load callback
     * 
     * @param \Closure $callback Give the after load callback
     * 
     * @throws \Duality\Core\DualityException When the stream is not opened
     * 
     * @return void
     */
    public function load(\Closure $callback = null)
    {
        if (is_resource($this->handler) && filesize($this->getPath()) > 0) {
            rewind($this->handler);
            $length = filesize($this->getPath()) < 4096 ? 
                filesize($this->getPath()) : 4096;
            while ($chunck = fread($this->handler, $length)) {
                $this->content .= $chunck;
                if (!is_null($callback)) {
                    $callback($chunck);
                }
            }
        }
    }

    /**
     * Saves the full file stream
     * 
     * @throws \Duality\Core\DualityException When the stream is not opened
     * 
     * @return int|false The number of saved bytes or false
     */
    public function save()
    {
        if (is_resource($this->handler)) {
            rewind($this->handler);
            return fwrite($this->handler, $this->content);
        }
        return false;
    }

    /**
     * Quick write content to file
     * 
     * @param string $data Give the date to be written to stream
     * 
     * @throws \Duality\Core\DualityException When the stream is not opened
     * 
     * @return int|false The write result
     */
    public function write($data)
    {
        if (is_resource($this->handler)) {
            return fwrite($this->handler, $data);
        }
        return false;
    }
}