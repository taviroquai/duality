<?php

/**
 * Interface for mailer
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Core;

/**
 * Mailer interface
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
interface InterfaceMailer
{
    /**
     * Send mail
     * 
     * @param \Closure $callback Give the callback after sent
     * 
     * @return void
     */
    public function send(\Closure $callback);

    /**
     * Set to recipient
     * 
     * @param string $to The recipient
     * 
     * @return void
     */
    public function to($to);

    /**
     * Set from origin email
     * 
     * @param string $email The sender email
     * @param string $name  The sender name
     * 
     * @return void
     */
    public function from($email, $name);

    /**
     * Set subject
     * 
     * @param string $subject The email subject
     * 
     * @return void
     */
    public function subject($subject);

    /**
     * Set body
     * 
     * @param string $body The email body
     * 
     * @return void
     */
    public function body($body);

    /**
     * Set attachments
     * 
     * @param array $list The list of file attachments
     * 
     * @return void
     */
    public function attach($list);

}