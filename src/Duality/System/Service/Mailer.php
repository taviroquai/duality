<?php

namespace Duality\System\Service;

use Duality\System\Core\InterfaceService;
use Duality\System\Core\InterfaceMailer;
use Duality\System\App;

/**
 * Default mailer service
 */
class Mailer implements InterfaceMailer, InterfaceService
{
	/**
	 * The dependent application container
	 * @var Duality\System\App
	 */
	protected $app;

	/**
	 * Holds the current mail params
	 * @var array
	 */
	protected $current;

	/**
	 * Holds smtp configuration
	 * @var array
	 */
	protected $smtp;

	/**
	 * Creates a new error handler
	 * @param Duality\System\App $app
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
	}

	/**
	 * Initiates the service
	 * @return Duality\System\Service\Mailer
	 */
	public function init()
	{
		$this->current = array(
			'from'			=> array('email' => '', 'name' => ''),
			'to' 			=> '',
			'subject'		=> '',
			'body'			=> '',
			'attachments'	=> array()
		);
		return $this;
	}

	/**
	 * Terminates the service
	 * @return Duality\System\Service\Mailer
	 */
	public function terminate()
	{
		return $this;
	}

	/**
	 * Set mail smtp
	 * @param string $smtp
	 * @return Duality\System\Service\Mailer
	 */
	public function setSMTP($host, $user = '', $pass = '', $encr = 'tls', $port = 587)
	{
		$this->smtp = array(
			'host' => $host,
			'user' => $user,
			'pass' => $pass,
			'encr' => $encr,
			'port' => $port
		);
		return $this;
	}	

	/**
	 * Set mail to
	 * @param string $to
	 * @return Duality\System\Service\Mailer
	 */
	public function to($to)
	{
		$this->current['to'] = $to;
		return $this;
	}

	/**
	 * Set mail from
	 * @param string $from
	 * @return Duality\System\Service\Mailer
	 */
	public function from($from, $name)
	{
		$this->current['from'] = array('email' => $from, 'name' => $name);
		return $this;
	}

	/**
	 * Set mail subject
	 * @param string $subject
	 * @return Duality\System\Service\Mailer
	 */
	public function subject($subject)
	{
		$this->current['subject'] = $subject;
		return $this;
	}

	/**
	 * Set body
	 * @param string $body
	 * @return Duality\System\Service\Mailer
	 */
	public function body($body)
	{
		$this->current['message'] = $body;
		return $this;
	}

	/**
	 * Set mail attachments
	 * @param array $list
	 * @return Duality\System\Service\Mailer
	 */
	public function attach($list)
	{
		$this->current['attachments'] = $list;
		return $this;
	}

	/**
	 * Send mail
	 * @param \Closure $callback
	 * @return Duality\System\Service\Mailer
	 */
	public function send(\Closure $callback)
	{

		// TODO: choose driver
		$mail = new \PHPMailer;

		//$mail->SMTPDebug = 3;

		// TODO: SMTP as optional
		$mail->isSMTP();
		$mail->Host = $this->smtp['host'];
		$mail->SMTPAuth = !empty($this->smtp['user']);
		$mail->Username = $this->smtp['user'];
		$mail->Password = $this->smtp['pass'];
		$mail->SMTPSecure = $this->smtp['encr'];
		$mail->Port = $this->smtp['port'];

		// Set params
		$mail->From = $this->current['from']['email'];
		$mail->FromName = $this->current['from']['name'];
		$mail->addAddress($this->current['to']);
		$mail->WordWrap = 50;
		// TODO as optional
		$mail->isHTML(true);
		$mail->Subject = $this->current['subject'];
		$mail->Body    = $this->current['message'];
		// TODO as optional
		// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		// TODO
		/*
		$mail->addAddress('ellen@example.com');
		$mail->addReplyTo('info@example.com', 'Information');
		$mail->addCC('cc@example.com');
		$mail->addBCC('bcc@example.com');
		*/

		// Set attachments		
		foreach ($this->current['attachments'] as $item) {
			$mail->addAttachment($item);
			// TODO as optional
			// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
		}
		
		// Finally, sent mail
		$result = $mail->send();
		return $callback($result, $mail);
	}

}