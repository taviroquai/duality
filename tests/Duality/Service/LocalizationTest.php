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
        $app = new \Duality\App(dirname(__FILE__), null);
        $auth = $app->call('locale');
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
        $app = new \Duality\App(dirname(__FILE__), $config);
        $auth = $app->call('locale');
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
                'dir'       => './tests/data/lang',
                'timezone'  => 'Europe/Lisbon'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);

        $code = 'pt_PT';
        var_dump($code);
        $locale = $app->call('locale');
        $current = \Locale::canonicalize($code);
        var_dump($current);
        var_dump(is_dir($app->getConfigItem('locale.dir').DIRECTORY_SEPARATOR.$current));
        // Validate locale and translations directory
        if (\Locale::acceptFromHttp($code) === null
            || !is_dir($app->getConfigItem('locale.dir').DIRECTORY_SEPARATOR.$current)
        ) {
            $current = \Locale::canonicalize(
                $app->getConfigItem('locale.default')
            );
        }

        // Define default locale
        var_dump($current);
        \Locale::setDefault($current);
        $directory = $app->getConfigItem('locale.dir').DIRECTORY_SEPARATOR.$current;
        var_dump($directory.DIRECTORY_SEPARATOR.'messages.php');
        if (!file_exists($directory.DIRECTORY_SEPARATOR.'messages.php')) {
            throw new \Duality\Core\DualityException(
                "Error locale: invalid messages file ".$current, 3
            );
        }
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
                'dir'       => './tests/data/lang',
                'timezone'  => 'Europe/Lisbon'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
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
                'dir'       => './tests/data/lang',
                'timezone'  => 'Europe/Lisbon'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
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
                'dir'       => './tests/data/lang',
                'timezone'  => 'Europe/Lisbon'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
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