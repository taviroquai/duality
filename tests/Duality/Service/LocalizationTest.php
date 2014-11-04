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

        $result = $locale->getDisplayLanguage();
        $this->assertEquals('English', $result);

        $result = $locale->getNumber(1001.11);
        $this->assertEquals('1,001.11', $result);

        $result = $locale->parseNumber('1.001,11');
        $this->assertEquals(1.001, $result);

        $result = $locale->getCurrency(1001.11);
        $this->assertEquals('€1,001.11', $result);

        $result = $locale->getNumberFormatter();
        $this->assertInstanceOf('\NumberFormatter', $result);

        $result = $locale->getDateFormatter();
        $this->assertInstanceOf('\IntlDateFormatter', $result);

        $result = $locale->t('key');
        $this->assertEquals('value', $result);

        $expected = 'valor';
        $result = $locale->t('key', array(), 'es_ES');
        $this->assertEquals($expected, $result);

        $locale->getCalendar();
        $locale->getTimeZone();

        $locale->terminate();
    }
}