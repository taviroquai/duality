<?php

class LocalizationTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test localization service
     * 
     * @requires extension intl
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testLocalizationWithoutConfig()
    {
        $app = new \Duality\App(dirname(__FILE__).'/../../..', null);
        $app->call('locale');
    }

    /**
     * Test localization service with invalid directory
     * 
     * @requires extension intl
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
        $app = new \Duality\App(dirname(__FILE__).'/../../..', $config);
        $app->call('locale');
    }

    /**
     * Test localization service with missing translations messages
     * 
     * @requires extension intl
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testLocalizationInvalidMessagesFile()
    {
        $config = array(
            'locale' => array(
                'default'   => 'en_US',
                'dir'       => 'tests/data/lang',
                'timezone'  => 'Europe/Lisbon'
            )
        );
        $app = new \Duality\App(dirname(__FILE__).'/../../..', $config);
        $locale = $app->call('locale');
        $filename = $app->getPath()
            . DIRECTORY_SEPARATOR
            . $app->getConfigItem('locale.dir')
            . DIRECTORY_SEPARATOR
            . \Locale::canonicalize('pt_PT');
        var_dump($filename); var_dump(file_exists($filename));
        $locale->setLocale('pt_PT');
    }

    /**
     * Test invalid locale code
     * 
     * @requires extension intl
     */
    public function testLocalizationInvalidCode()
    {
        $config = array(
            'locale' => array(
                'default'   => 'en_US',
                'dir'       => 'tests/data/lang',
                'timezone'  => 'Europe/Lisbon'
            )
        );
        $app = new \Duality\App(dirname(__FILE__).'/../../..', $config);
        $locale = $app->call('locale');
        $locale->setLocale('dummy');
    }

    /**
     * Test localization service
     * 
     * @requires extension intl
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testLocalizationInvalidTranslateCode()
    {
        $config = array(
            'locale' => array(
                'default'   => 'en_US',
                'dir'       => 'tests/data/lang',
                'timezone'  => 'Europe/Lisbon'
            )
        );
        $app = new \Duality\App(dirname(__FILE__).'/../../..', $config);
        $locale = $app->call('locale');
        $result = $locale->t('key', array(), 'dummy');
    }

    /**
     * Test localization service
     * 
     * @requires extension intl
     */
    public function testLocalization()
    {
        $config = array(
            'locale' => array(
                'default'   => 'en_US',
                'dir'       => 'tests/data/lang',
                'timezone'  => 'Europe/Lisbon'
            )
        );
        $app = new \Duality\App(dirname(__FILE__).'/../../..', $config);
        $locale = $app->call('locale');

        $locale->getDisplayLanguage();
        $locale->getNumber(1001.11);
        $locale->parseNumber('1.001,11');
        $locale->getCurrency(1001.11);
        $locale->getNumberFormatter();
        $locale->getDateFormatter();
        $locale->getCalendar();
        $locale->getTimeZone();
        $locale->t('key');

        $expected = 'valor';
        $result = $locale->t('key', array(), 'es_ES');
        $this->assertEquals($expected, $result);

        $locale->terminate();
    }
}