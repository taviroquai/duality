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
        $mailer = $app->call('mailer');

        // SMTP
        $mailer->setSMTP('smtp.gmail.com');

        // Prepare
        $mailer->to('dummy@isp.com')
            ->copy('dummy2@isp.com')
            ->copy('dummy3@isp.com', false)
            ->reply('dummy4@isp.com', 'no-reply')
            ->from('dummy5@isp.com', 'Dummy Duality Test')
            ->subject('Dummy Subject')
            ->body('<p>Dummy Body</p>', 'dummy body')
            ->attach(array('./tests/data/log.txt'));

        /*
        $mailer->send(function($result, $mail) {
            var_dump($result);
        });
        */

        $mailer->terminate();

    }
}