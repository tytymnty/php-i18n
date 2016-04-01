<?php

require PROJECT_ROOT.'/src/I18N/Lang.php';

class LangTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        parent::setUp();
        $this->langFolder = PROJECT_ROOT.'/tests/lang';
    }

    public function tearDown()
    {
        parent::tearDown();   
    }

    public function testSetLang()
    {
        $allows = 'en-us, zh-tw, zh-cn, zh-hk, en-gb, es-es';
        $default = 'en-us';
        I18N\Lang::init($allows, $default, $this->langFolder);

        // perfect match
        $current = 'en-US';
        I18N\Lang::setLang($current);
        $this->assertEquals('en-us', I18N\Lang::getLang());

        $current = 'zh-HK';
        I18N\Lang::setLang($current);
        $this->assertEquals('zh-hk', I18N\Lang::getLang());

        $current = 'es-ES';
        I18N\Lang::setLang($current);
        $this->assertEquals('es-es', I18N\Lang::getLang());

        // fuzzy match
        $current = 'en';
        I18N\Lang::setLang($current);
        $this->assertEquals('en-us', I18N\Lang::getLang());

        $current = 'zh';
        I18N\Lang::setLang($current);
        $this->assertEquals('zh-tw', I18N\Lang::getLang());

        $current = 'es';
        I18N\Lang::setLang($current);
        $this->assertEquals('es-es', I18N\Lang::getLang());

        // no match
        $current = 'pt';
        I18N\Lang::setLang($current);
        $this->assertEquals('en-us', I18N\Lang::getLang());

        $current = 'ta';
        I18N\Lang::setLang($current);
        $this->assertEquals('en-us', I18N\Lang::getLang());

        // no default
        $allows = 'en-us, zh-tw, zh-cn, zh-hk, en-gb, es-es';
        $default = null;
        $current = 'ta';
        I18N\Lang::init($allows, $default, $this->langFolder);
        I18N\Lang::setLang($current);
        $this->assertTrue(I18N\Lang::getLang()===null);

        // no allows
        $allows = null;
        $default = 'en-us';
        $current = 'ta';
        I18N\Lang::init($allows, $default, $this->langFolder);
        I18N\Lang::setLang($current);
        $this->assertEquals('en-us', I18N\Lang::getLang());
    }

    public function testSetLangList()
    {
        $allows = 'en-us, zh-tw, zh-cn, zh-hk, en-gb, es-es';
        $default = 'en-us';
        I18N\Lang::init($allows, $default, $this->langFolder);

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-US,zh-CN;q=0.8,zh;q=0.5,en;q=0.3';
        $accepts = I18N\Lang::getHTTPAcceptLangs();

        I18N\Lang::setLang($accepts);
        $this->assertEquals('en-us', I18N\Lang::getLang());

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'zh-CN;q=0.8,zh;q=0.5,en;q=0.3';
        $accepts = I18N\Lang::getHTTPAcceptLangs();
        I18N\Lang::setLang($accepts);
        $this->assertEquals('zh-cn', I18N\Lang::getLang());

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'zh;q=0.5,en;q=0.3';
        $accepts = I18N\Lang::getHTTPAcceptLangs();
        I18N\Lang::setLang($accepts);
        $this->assertEquals('zh-tw', I18N\Lang::getLang());

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'zh;q=0.8,zh-CN;q=0.5,en;q=0.3';
        $accepts = I18N\Lang::getHTTPAcceptLangs();
        I18N\Lang::setLang($accepts);
        $this->assertEquals('zh-cn', I18N\Lang::getLang());

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = '';
        $accepts = I18N\Lang::getHTTPAcceptLangs();
        I18N\Lang::setLang($accepts);
        $this->assertEquals('en-us', I18N\Lang::getLang());
    }

    public function testGetWord()
    {   
        $allows = 'en-us, zh-tw, zh-cn, zh-hk, en-gb, es-es';
        $default = 'en-us';
        I18N\Lang::init($allows, $default, $this->langFolder);

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-US,zh-CN;q=0.8,zh;q=0.5,en;q=0.3';
        $accepts = I18N\Lang::getHTTPAcceptLangs();
        I18N\Lang::setLang($accepts);
        $this->assertEquals('Hello', I18N\Lang::getWord('hello'));
        $this->assertEquals('', I18N\Lang::getWord('helloxx'));

        // no language package file found
        I18N\Lang::setLang('en-gb');
        $this->assertEquals('', I18N\Lang::getWord('hello'));
    }

    public function testInjectWords()
    {
        $allows = 'en-us, zh-tw, zh-cn, zh-hk, en-gb, es-es';
        $default = 'en-us';
        I18N\Lang::init($allows, $default, $this->langFolder);

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-US,zh-CN;q=0.8,zh;q=0.5,en;q=0.3';
        $accepts = I18N\Lang::getHTTPAcceptLangs();
        I18N\Lang::setLang($accepts);

        $this->assertEquals('I am Tom, I\'m 19 old', I18N\Lang::injectWords('i_am_who_and_age_is', ['Tom', 19]));
        
    }
}