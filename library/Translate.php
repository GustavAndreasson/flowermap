<?php

require_once("config.php");

class Translate {
    private $language = 'sv';
    private $lang = array();

    public function __construct($language = null) {
        if ($language) {
            $this->language = $language;
        } else {
            $test_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $langs = $this->getAvailableLanguages();
            if (in_array($test_lang, $langs)) {
                $this->language = $test_lang;
            } else {
                $this->language = $langs[0];
            }
        }
    }

    public function getLanguage() {
        return $this->language;
    }

    private function findString($str) {
        if (array_key_exists($str, $this->lang[$this->language])) {
            return $this->lang[$this->language][$str];
        } else {
            return $str;
        }
    }

    private function splitStrings($str) {
        return explode('=',trim($str));
    }

    public function __($str) {
        if (!array_key_exists($this->language, $this->lang)) {
            if (file_exists(TRANSLATIONS_PATH . $this->language.'.txt')) {
                $strings = array_map(array($this,'splitStrings'),file(TRANSLATIONS_PATH . $this->language.'.txt'));
                foreach ($strings as $k => $v) {
                    $this->lang[$this->language][$v[0]] = $v[1];
                }
                return $this->findString($str);
            } else {
                return $str;
            }
        } else {
            return $this->findString($str);
        }
    }

    public function getTranslations() {
        if (!array_key_exists($this->language, $this->lang)) {
            if (file_exists(TRANSLATIONS_PATH . $this->language.'.txt')) {
                $strings = array_map(array($this,'splitStrings'),file(TRANSLATIONS_PATH . $this->language.'.txt'));
                foreach ($strings as $k => $v) {
                    $this->lang[$this->language][$v[0]] = $v[1];
                }
            }
        }
        return $this->lang;
    }

    public function getAvailableLanguages() {
        $langFiles = array_diff(scandir(TRANSLATIONS_PATH), array('..', '.'));
        $langs = array();
        foreach ($langFiles as $l) {
            if (preg_match('/^[a-z]{2}\.txt$/', $l)) {
                $langs[] = substr($l, 0, 2);
            }
        }
        return $langs;
    }
}
