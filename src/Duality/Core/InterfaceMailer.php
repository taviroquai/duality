<?php

/**
 * Interface for mailer
 *
 * @since 		0.7.0
 * @author 		Marco Afonso <mafonso333@gmail.com>
 * @license 	MIT
 */

namespace Duality\Core;

/**
 * Mailer interface
 */
interface InterfaceMailer
{
	/**
	 * Send mail
	 * @param \Closure $callback
	 */
	public function send(\Closure $callback);

	/**
	 * Set to
	 * @param string $to
	 */
	public function to($to);

	/**
	 * Set from
	 * @param string $email
	 * @param string $name
	 */
	public function from($email, $name);

	/**
	 * Set subject
	 * @param string $subject
	 */
	public function subject($subject);

	/**
	 * Set body
	 * @param string $body
	 */
	public function body($body);

	/**
	 * Set attachments
	 * @param array $list
	 */
	public function attach($list);

}