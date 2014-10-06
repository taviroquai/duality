<?php

/**
 * Mailer service
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service;

use \Duality\Core\InterfaceService;
use \Duality\Core\InterfaceMailer;
use \Duality\Structure\Storage;
use \Duality\App;

/**
 * Default mailer service
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Mailer 
implements InterfaceMailer, InterfaceService
{
    /**
     * The dependent application container
     * 
     * @var Duality\App The application container
     */
    protected $app;

    /**
     * Holds the current mail params
     * 
     * @var \Duality\Core\InterfaceStorage The mail params
     */
    protected $current;

    /**
     * Holds smtp configuration
     * 
     * @var \Duality\Core\InterfaceStorage The current smtp configuration
     */
    protected $smtp;

    /**
     * Creates a new error handler
     * 
     * @param \Duality\App &$app The application container
     */
    public function __construct(App &$app)
    {
        $this->app = $app;
    }

    /**
     * Initiates the service
     * 
     * @return \Duality\Service\Mailer This instance
     */
    public function init()
    {
        $this->storage = new Storage;
        $this->smtp = new Storage;

        $this->current->importArray(
            array(
                'from'          => array('email' => '', 'name' => ''),
                'to'            => array(),
                'cc'            => '',
                'bcc'           => '',
                'reply'         => array('email' => '', 'name' => ''),
                'subject'       => '',
                'body'          => '',
                'altBody'       => '',
                'attachments'   => array()
            )
        );
        
        $this->smtp->importArray(
            array(
                'host' => '',
                'user' => '',
                'pass' => '',
                'encr' => '',
                'port' => '',
                'dbgl' => 0
            )
        );

        // Load SMTP from configuration
        if ($this->app->getConfigItem('mailer.smtp')) {
            $this->smtp->importArray($this->app->getConfigItem('mailer.smtp'));
        }

        // Load sender address from configuration
        if ($this->app->getConfigItem('mailer.from')) {
            $this->current->set('from', $this->app->getConfigItem('mailer.from'));
        }

        return $this;
    }

    /**
     * Terminates the service
     * 
     * @return Duality\Service\Mailer This instance
     */
    public function terminate()
    {
        return $this;
    }

    /**
     * Set mail smtp
     * 
     * @param string $host       The remote hostname
     * @param string $user       The authentication user
     * @param string $pass       The authentication password
     * @param string $encr       The connection encryptino
     * @param string $port       The remote port
     * @param int    $debugLevel The debug level
     * 
     * @return Duality\Service\Mailer
     */
    public function setSMTP(
        $host, $user = '', $pass = '', $encr = 'tls', $port = 587, $debugLevel = 0
    ) {
        $this->smtp->importArray(
            array(
                'host' => $host,
                'user' => $user,
                'pass' => $pass,
                'encr' => $encr,
                'port' => $port,
                'dbgl' => $debugLevel
            )
        );
        return $this;
    }   

    /**
     * Set mail to
     * 
     * @param string $to The target recipient
     * 
     * @return Duality\Service\Mailer This instance
     */
    public function to($to)
    {
        $this->addAddress($to);
        return $this;
    }

    /**
     * Add address
     * 
     * @param string $address Give the recipient address
     * 
     * @return Duality\Service\Mailer This instance
     */
    public function addAdress($address)
    {
        $list = $this->current->get('to');
        $list[] = $address;
        $this->current->set('to', $list);
        return $this;
    }

    /**
     * Add copy
     * 
     * @param string  $address The cc recipient
     * @param boolean $bcc     Tells whether use bcc or not
     * 
     * @return Duality\Service\Mailer This instance
     */
    public function copy($address, $bcc = true)
    {
        if ($bcc) {
            $this->current->set('bcc', $address);
        } else {
            $this->current->set('cc', $address);
        }
        return $this;
    }

    /**
     * Set reply address
     * 
     * @param string $address The reply address
     * @param string $name    The reply name
     * 
     * @return Duality\Service\Mailer This instance
     */
    public function reply($address, $name)
    {
        $reply = $this->current->get('reply');
        $reply['email'] = $address;
        $reply['name'] = $name;
        $this->current->set('reply', $reply);
        return $this;
    }

    /**
     * Set mail from
     * 
     * @param string $from Give the sender email address
     * @param string $name Give the sender sender name
     * 
     * @return Duality\Service\Mailer This instance
     */
    public function from($from, $name)
    {
        $from = $this->current->get('from');
        $from['email'] = $from;
        $from['name'] = $name;
        $this->current->set('from', $reply);  
        return $this;
    }

    /**
     * Set mail subject
     * 
     * @param string $subject Give the message subject
     * 
     * @return Duality\Service\Mailer This instance
     */
    public function subject($subject)
    {
        $this->current->set('subject', $subject);
        return $this;
    }

    /**
     * Set body and alternate text body
     * 
     * @param string $html    Give the HTML body
     * @param string $altBody Give the alternate text message
     * 
     * @return Duality\Service\Mailer This instance
     */
    public function body($html, $altBody = 'Text message')
    {
        $this->current->set('body', $html);
        $this->current->set('altbody', $altBody);
        return $this;
    }

    /**
     * Set mail attachments
     * 
     * @param array $list The list of files as array
     * 
     * @return Duality\Service\Mailer This instance
     */
    public function attach($list)
    {
        $this->current->set('attachments', $list);
        return $this;
    }

    /**
     * Send mail
     * 
     * @param \Closure $callback Give the after sent callback
     * 
     * @return Duality\Service\Mailer This instance
     */
    public function send(\Closure $callback)
    {

        // TODO: choose driver
        $mail = new \PHPMailer;

        // Setup SMTP
        if (!$this->smtp->get('host')) {
            if ($this->smtp->get('dbgl')) {
                $mail->SMTPDebug    = $this->smtp->get('dbgl');
            }
            $mail->isSMTP();
            $mail->Host         = $this->smtp->get('host');
            $mail->SMTPAuth     = !$this->smtp->get('user');
            $mail->Username     = $this->smtp->get('user');
            $mail->Password     = $this->smtp->get('pass');
            $mail->SMTPSecure   = $this->smtp->get('encr');
            $mail->Port         = $this->smtp->get('port');  
        }
        
        // Set params
        $from = $this->current->get('from');
        $mail->From = $from['email'];
        $mail->FromName = $from['name'];
        foreach ($this->current->get('to') as $item) {
            $mail->addAddress($item);
        }
        
        // Set message body options
        $mail->isHTML(true);
        $mail->Subject      = $this->current->get('subject');
        $mail->Body         = $this->current->get('body');
        $mail->AltBody      = $this->current->get('altBody');
        $mail->WordWrap     = 50;

        // Set extra options: reply, cc and bcc
        $reply = $this->current->get('reply');
        if (!empty($reply['email'])) {
            $replyEmail     = $reply['email'];
            $replyName      = $reply['name'];
            $mail->addReplyTo($replyEmail, $replyName);
        }
        if (!$this->current->get('cc')) {
            $mail->addCC($this->current->set('cc'); 
        }
        if (!$this->current->set('bcc')) {
            $mail->addBCC($this->current->set('bcc'));
        }

        // Set attachments      
        foreach ($this->current->set('attachments') as $item) {
            $mail->addAttachment($item);
        }
        
        // Finally, sent mail
        $result = $mail->send();
        return $callback($result, $mail);
    }

}