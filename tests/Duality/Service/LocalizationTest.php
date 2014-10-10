<?php

class LocalizationTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test localization service
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testLocalizationWithoutConfig()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $auth = $app->call('locale');
    }

    /**
     * Test localization service with invalid directory
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testLocalizationInvalidDirectory()
    {
        $config = array(
            'locale' => array(
                'default'   => 'en_US',
                'dir'       => 'dummy',
                'timezone'  => 'Europe/Lisbon'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $auth = $app->call('locale');
    }

    /**
     * Test localization service with missing translations messages
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testLocalizationInvalidMessagesFile()
    {
        $config = array(
            'locale' => array(
                'default'   => 'en_US',
                'dir'       => './tests/data/lang',
                'timezone'  => 'Europe/Lisbon'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $app->call('locale')->setLocale('pt_PT');
    }

    /**
     * Test localization service
     */
    public function testLocalization()
    {
        $config = array(
            'locale' => array(
                'default'   => 'en_US',
                'dir'       => './tests/data/lang',
                'timezone'  => 'Europe/Lisbon'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $auth = $app->call('locale');
    }
}