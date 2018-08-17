<?php

namespace esas\hutkigrosh\lang;

/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 10.07.2018
 * Time: 11:45
 */
use Joomla\CMS\Factory;

class TranslatorJoom extends Translator
{
    private static $locale = null;

    public function getLocale()
    {
        if (null === self::$locale) {
            self::$locale = $langtag = str_replace("-", "_", Factory::getLanguage()->getTag());
        }
        return self::$locale;
    }
}