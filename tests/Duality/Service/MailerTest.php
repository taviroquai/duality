<?php

class MailerTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test mailer service
     */
    public function testMailer()
    {
        $config = array(
            'mailer'    => array(
                'from'  => array('email' => 'no-reply@domain.com', 'name' => 'Duality Mailer'),
                'smtp'  => array(
                    'host' => 'smtp.gmail.com',
                    'user' => 'username',
                    'pass' => 'password',
                    'encr' => 'tls',
                    'port' => 587,
                    'dbgl' => 0
                )
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $auth = $app->call('mailer');
    }
}