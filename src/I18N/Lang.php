<?php
/**
 * Multi language package util for PHP
 *
 * @author tytymnty@gmail.com
 * @since 2015-12-05 17:29:44
 */

namespace I18N;

Class Lang 
{
    private static $cookieKey = 'lang';
    private static $currLang = null;
    private static $defaultLang = null;
    private static $packageFolder = null;
    private static $allows = array();
    private static $dict = array();

    /**
     * @param string $allows allow languages
     * @param string $default default language
     * @param string $packageFolder
     */
    public static function init($allows, $default, $packageFolder) 
    {
        self::$allows = preg_split('/\,\s*/', $allows);
        self::$defaultLang = $default;
        self::$packageFolder = $packageFolder;
    }

    /**
     * Get language lists by HTTP Accepts language
     * @param string $default
     */
    public static function getHTTPAcceptLangs() 
    {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return null;
        }
        preg_match_all('/([-a-z]+){2}/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matcheLangs);

        return $matcheLangs[0];
    }

    /**
     * require the language package
     * @param string $lang language type
     */
    public static function requireLangPackage($lang) 
    {
        $langFile = self::$packageFolder.'/'.$lang.'.lang';
        if (file_exists($langFile)) {
            self::$dict = parse_ini_file($langFile);
        } else {
            self::$dict = array();
        }
    }

    /**
     * Language support init
     * See also: https://en.wikipedia.org/wiki/Language_localisation
     * @param string|array $lang language
     */
    public static function setLang($lang=null) 
    {
        if (empty($lang)) {
            self::$currLang = self::$defaultLang;
            self::requireLangPackage(self::$currLang);
            return;
        }

        $currLang = null;
        if (is_string($lang)) {
            foreach (self::$allows as $allow) {
                if (strcasecmp($lang, $allow)==0) {
                    $currLang = $allow;
                    break;
                }
            }

            if (empty($currLang) && !preg_match('/\-/', $lang)) {
                $preg = '/^'.$lang.'\-\S{2}$/i';
                foreach (self::$allows as $allow) {
                    if (preg_match($preg, $allow)) {
                        $currLang = $allow;
                        break;
                    }
                }
            }

        } else {
            // language list
            foreach ($lang as $langItem) {
                $langItem = preg_replace('/\-/', '\\-', $langItem);
                $matches = preg_grep('/^'.$langItem.'$/i', self::$allows);
                if (!empty($matches)) {
                    $currLang = current($matches);
                    break;
                }
            }

            if (empty($currLang)) {
                foreach ($lang as $langItem) {
                    if(!preg_match('/\-/', $langItem)) {
                        $preg = '/^'.$langItem.'\-\S{2}$/';
                        foreach (self::$allows as $allow) {
                            if (preg_match($preg, $allow)) {
                                $currLang = $allow;
                                break 2;
                            }
                        }
                    }
                }
            }
        }

        if (empty($currLang)) {
            $currLang = self::$defaultLang;
        }
        self::$currLang = $currLang;
        self::requireLangPackage(self::$currLang);
    }

    /**
     * get User Language
     */
    public static function getLang() 
    {
        return self::$currLang;
    }

    /**
     * get word
     * @param string $key
     * @return string
     */
    public static function getWord($key) 
    {
        return isset(self::$dict[$key]) ? self::$dict[$key] : '';
    }

    /**
     * inject words and return string
     * @param string $key
     * @param array $key
     * @return string
     */
    public static function injectWords($key, $words)
    {
        if (isset(self::$dict[$key])) {
            $str = self::$dict[$key];
            preg_match_all('/#{2}([\da-zA-Z]+)#{2}/', $str, $matches);
            if (!empty($matches[0])) {
                foreach ($matches[0] as $i=>$match) {
                    if (isset($words[$i])) {
                        $str = str_replace($match, $words[$i], $str);
                    }
                }
                return $str;
            }
        }
        return '';
    }
}