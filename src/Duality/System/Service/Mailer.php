<?php

/**
 * Mailer service
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\System\Service;

use Duality\System\Core\InterfaceService;
use Duality\System\Core\InterfaceMailer;
use Duality\System\App;

/**
 * Default mailer service
 */
class Mailer 
implements InterfaceMailer, InterfaceService
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
			'to' 			=> array(),
			'cc'			=> '',
			'bcc'			=> '',
			'reply'			=> array('email' => '', 'name' => ''),
			'subject'		=> '',
			'body'			=> '',
			'altBody'		=> '',
			'attachments'	=> array()
		);
		$this->smtp = array(
			'host' => '',
			'user' => '',
			'pass' => '',
			'encr' => '',
			'port' => '',
			'dbgl' => 0
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
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @param string $encr
	 * @param string $port
	 * @return Duality\System\Service\Mailer
	 */
	public function setSMTP($host, $user = '', $pass = '', $encr = 'tls', $port = 587, $debugLevel = 0)
	{
		$this->smtp = array(
			'host' => $host,
			'user' => $user,
			'pass' => $pass,
			'encr' => $encr,
			'port' => $port,
			'dbgl' => $debugLevel
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
	 * Add address
	 * @param string $address
	 * @return Duality\System\Service\Mailer
	 */
	public function addAdress($address)
	{
		$this->current['to'][] = $address;
		return $this;
	}

	/**
	 * Add copy
	 * @param string $address
	 * @param boolean $bcc
	 * @return Duality\System\Service\Mailer
	 */
	public function copy($address, $bcc = true)
	{
		if ($bcc) {
			$this->current['bcc'] = $address;	
		} else {
			$this->current['cc'] = $address;
		}
		return $this;
	}

	/**
	 * Set reply address
	 * @param string $address
	 * @param string $name
	 * @return Duality\System\Service\Mailer
	 */
	public function reply($address, $name)
	{
		$this->current['reply']['email'] = $address;
		$this->current['reply']['name'] = $name;
		return $this;
	}

	/**
	 * Set mail from
	 * @param string $from
	 * @param string $name
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
	 * Set body and alternate text body
	 * @param string $html
	 * @param string $altBody
	 * @return Duality\System\Service\Mailer
	 */
	public function body($html, $altBody = 'Message in plain text for non-HTML mail clients')
	{
		$this->current['body'] = $html;
		$this->current['altbody'] = $altBody;
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

		// Setup SMTP
		if (!empty($this->smtp['host'])) {
			$mail->SMTPDebug 	= $this->smtp['debugLevel'];
			$mail->isSMTP();
			$mail->Host 		= $this->smtp['host'];
			$mail->SMTPAuth 	= !empty($this->smtp['user']);
			$mail->Username 	= $this->smtp['user'];
			$mail->Password 	= $this->smtp['pass'];
			$mail->SMTPSecure	= $this->smtp['encr'];
			$mail->Port 		= $this->smtp['port'];	
		}
		
		// Set params
		$mail->From = $this->current['from']['email'];
		$mail->FromName = $this->current['from']['name'];
		foreach ($this->current['to'] as $item) {
			$mail->addAddress($item);
		}
		
		// Set message body options
		$mail->isHTML(true);
		$mail->Subject 		= $this->current['subject'];
		$mail->Body    		= $this->current['body'];
		$mail->AltBody 		= $this->current['altBody'];
		$mail->WordWrap 	= 50;

		// Set extra options: reply, cc and bcc
		if (!empty($this->current['rely']['email'])) {
			$replyEmail 	= $this->current['rely']['email'];
			$replyName 		= $this->current['rely']['name'];
			$mail->addReplyTo($replyEmail, $replyName);
		}
		if (!empty($this->current['cc'])) {
			$mail->addCC($this->current['cc']);	
		}
		if (!empty($this->current['bcc'])) {
			$mail->addBCC($this->current['bcc']);
		}

		// Set attachments		
		foreach ($this->current['attachments'] as $item) {
			$mail->addAttachment($item);
		}
		
		// Finally, sent mail
		$result = $mail->send();
		return $callback($result, $mail);
	}

}