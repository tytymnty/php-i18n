<?php

use MultiLanguage\Util;

class MultiLanguageUtilTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        parent::setUp();
        $this->langFolder = BASE_PATH.'/lang';
    }

    public function tearDown()
    {
        parent::tearDown();   
    }

    public function testSetLang()
    {
        $allows = 'en-us, zh-tw, zh-cn, zh-hk, en-gb, es-es';
        $default = 'en-us';
        Util::init($allows, $default, $this->langFolder);

        // perfect match
        $current = 'en-US';
        Util::setLang($current);
        $this->assertEquals('en-us', Util::getLang());

        $current = 'zh-HK';
        Util::setLang($current);
        $this->assertEquals('zh-hk', Util::getLang());

        $current = 'es-ES';
        Util::setLang($current);
        $this->assertEquals('es-es', Util::getLang());

        // fuzzy match
        $current = 'en';
        Util::setLang($current);
        $this->assertEquals('en-us', Util::getLang());

        $current = 'zh';
        Util::setLang($current);
        $this->assertEquals('zh-tw', Util::getLang());

        $current = 'es';
        Util::setLang($current);
        $this->assertEquals('es-es', Util::getLang());

        // no match
        $current = 'pt';
        Util::setLang($current);
        $this->assertEquals('en-us', Util::getLang());

        $current = 'ta';
        Util::setLang($current);
        $this->assertEquals('en-us', Util::getLang());

        // no default
        $allows = 'en-us, zh-tw, zh-cn, zh-hk, en-gb, es-es';
        $default = null;
        $current = 'ta';
        Util::init($allows, $default, $this->langFolder);
        Util::setLang($current);
        $this->assertTrue(Util::getLang()===null);

        // no allows
        $allows = null;
        $default = 'en-us';
        $current = 'ta';
        Util::init($allows, $default, $this->langFolder);
        Util::setLang($current);
        $this->assertEquals('en-us', Util::getLang());
    }

    public function testSetLangList()
    {
        $allows = 'en-us, zh-tw, zh-cn, zh-hk, en-gb, es-es';
        $default = 'en-us';
        Util::init($allows, $default, $this->langFolder);

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-US,zh-CN;q=0.8,zh;q=0.5,en;q=0.3';
        $accepts = Util::getHTTPAcceptLangs();

        Util::setLang($accepts);
        $this->assertEquals('en-us', Util::getLang());

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'zh-CN;q=0.8,zh;q=0.5,en;q=0.3';
        $accepts = Util::getHTTPAcceptLangs();
        Util::setLang($accepts);
        $this->assertEquals('zh-cn', Util::getLang());

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'zh;q=0.5,en;q=0.3';
        $accepts = Util::getHTTPAcceptLangs();
        Util::setLang($accepts);
        $this->assertEquals('zh-tw', Util::getLang());

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'zh;q=0.8,zh-CN;q=0.5,en;q=0.3';
        $accepts = Util::getHTTPAcceptLangs();
        Util::setLang($accepts);
        $this->assertEquals('zh-cn', Util::getLang());

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = '';
        $accepts = Util::getHTTPAcceptLangs();
        Util::setLang($accepts);
        $this->assertEquals('en-us', Util::getLang());
    }

    public function testGetWord()
    {   
        $allows = 'en-us, zh-tw, zh-cn, zh-hk, en-gb, es-es';
        $default = 'en-us';
        Util::init($allows, $default, $this->langFolder);

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-US,zh-CN;q=0.8,zh;q=0.5,en;q=0.3';
        $accepts = Util::getHTTPAcceptLangs();
        Util::setLang($accepts);
        $this->assertEquals('Hello', Util::getWord('hello'));
        $this->assertEquals('', Util::getWord('helloxx'));

        // no language package file found
        Util::setLang('en-gb');
        $this->assertEquals('', Util::getWord('hello'));
    }

    public function testInjectWords()
    {
        $allows = 'en-us, zh-tw, zh-cn, zh-hk, en-gb, es-es';
        $default = 'en-us';
        Util::init($allows, $default, $this->langFolder);

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-US,zh-CN;q=0.8,zh;q=0.5,en;q=0.3';
        $accepts = Util::getHTTPAcceptLangs();
        Util::setLang($accepts);

        $this->assertEquals('I am Tom, I\'m 19 old', Util::injectWords('i_am_who_and_age_is', ['Tom', 19]));
        
    }
}