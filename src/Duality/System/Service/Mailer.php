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
	 * @param array $attachments
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

		//$mail->SMTPDebug = 3;                               // Enable verbose debug output

		// TODO: SMTP as optional
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = $this->smtp['host'];  				  // Specify main and backup SMTP servers
		$mail->SMTPAuth = !empty($this->smtp['user']);        // Enable SMTP authentication
		$mail->Username = $this->smtp['user'];                // SMTP username
		$mail->Password = $this->smtp['pass'];                // SMTP password
		$mail->SMTPSecure = $this->smtp['encr'];              // Enable TLS encryption, `ssl` also accepted
		$mail->Port = $this->smtp['port'];                    // TCP port to connect to

		// Set params
		$mail->From = $this->current['from']['email'];
		$mail->FromName = $this->current['from']['name'];
		$mail->addAddress($this->current['to']); 			  // Add a recipient
		$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		// TODO as optional
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = $this->current['subject'];
		$mail->Body    = $this->current['message'];
		// TODO as optional
		// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		// TODO
		/*
		$mail->addAddress('ellen@example.com');               // Name is optional
		$mail->addReplyTo('info@example.com', 'Information');
		$mail->addCC('cc@example.com');
		$mail->addBCC('bcc@example.com');
		*/

		// Set attachments		
		foreach ($this->current['attachments'] as $item) {
			$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments	
			// TODO as optional
			// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
		}
		
		// Finally, sent mail
		$result = $mail->send();
		return $callback($result, $mail);
	}

}