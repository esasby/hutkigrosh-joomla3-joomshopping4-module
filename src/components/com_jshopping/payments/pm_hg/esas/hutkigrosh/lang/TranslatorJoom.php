<?php

namespace esas\hutkigrosh\lang;

/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 10.07.2018
 * Time: 11:45
 */
use Joomla\CMS\Factory;

class TranslatorJoom
{
    private static $locale = null;

    public static function translate($msg)
    {
        if (null === self::$locale) {
            self::$locale = $langtag = str_replace("-", "_", Factory::getLanguage()->getTag());
        }
        return Translator::translate($msg, self::$locale);
    }
}