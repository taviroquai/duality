<?php

class TranslationTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test translation service
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testTranslationWithoutConfig()
    {
        $app = new \Duality\App();
        $app->call('idiom');
    }

    /**
     * Test translation service with invalid directory
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testTranslationInvalidDirectory()
    {
        $config = array(
            'idiom' => array(
                'default'   => 'en_US',
                'dir'       => 'dummy',
                'timezone'  => 'Europe/Lisbon'
            )
        );
        $app = new \Duality\App($config);
        $app->call('idiom');
    }

    /**
     * Test translation service with missing translations messages
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testTranslationInvalidMessagesFile()
    {
        $config = array(
            'idiom' => array(
                'default'   => 'en_US',
                'dir'       => 'tests/data/lang',
                'timezone'  => 'Europe/Lisbon'
            )
        );
        $app = new \Duality\App($config);
        $idiom = $app->call('idiom');
        $idiom->setLocale('pt_PT');
    }

    /**
     * Test translation service
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testTranslationInvalidTranslateCode()
    {
        $config = array(
            'idiom' => array(
                'default'   => 'en_US',
                'dir'       => 'tests/data/lang',
                'timezone'  => 'Europe/Lisbon'
            )
        );
        $app = new \Duality\App($config);
        $idiom = $app->call('idiom');
        $idiom->t('key', array(), 'dummy');
    }

    /**
     * Test localization service
     */
    public function testTranslation()
    {
        $config = array(
            'idiom' => array(
                'default'   => 'en_US',
                'dir'       => 'tests/data/lang',
                'timezone'  => 'Europe/Lisbon'
            )
        );
        $app = new \Duality\App($config);
        $idiom = $app->call('idiom');

        $result = $idiom->getDisplayLanguage();
        $this->assertEquals('en_US', $result);

        $result = $idiom->t('key');
        $this->assertEquals('value', $result);

        $expected = 'valor';
        $result = $idiom->t('key', array(), 'es_ES');
        $this->assertEquals($expected, $result);

        $idiom->terminate();
    }
}