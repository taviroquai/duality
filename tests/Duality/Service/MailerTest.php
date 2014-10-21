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
                    'dbgl' => 1
                )
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $mailer = $app->call('mailer');

        // SMTP
        $mailer->setSMTP(
            'smtp.gmail.com',
            'username',
            'password',
            'tls',
            587,
            1
        );

        // Prepare
        $mailer->to('dummy@isp.com')
            ->copy('dummy2@isp.com')
            ->copy('dummy3@isp.com', false)
            ->reply('dummy4@isp.com', 'no-reply')
            ->from('dummy5@isp.com', 'Dummy Duality Test')
            ->subject('Dummy Subject')
            ->body('<p>Dummy Body</p>', 'dummy body')
            ->attach(array('./tests/data/log.txt'))
            ->pretend(true);

        $me = & $this;
        $mailer->send(function($result, $mail) use ($me) {
            $me->assertTrue($result);
        });

        $mailer->terminate();

    }
}