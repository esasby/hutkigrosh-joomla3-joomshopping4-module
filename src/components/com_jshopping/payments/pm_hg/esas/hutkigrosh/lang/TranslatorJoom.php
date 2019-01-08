<?php
/*
* @info     Платёжный модуль Hutkigrosh для JoomShopping
* @package  hutkigrosh
* @author   esas.by
* @license  GNU/GPL
*/
namespace esas\hutkigrosh\lang;
defined('_JEXEC') or die;

/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 10.07.2018
 * Time: 11:45
 */
use Joomla\CMS\Factory;

class TranslatorJoom extends TranslatorImpl
{
    private static $locale = null;

    public function getLocale()
    {
        if (null === self::$locale) {
            self::$locale = Factory::getLanguage()->getTag();
        }
        return self::$locale;
    }

    public function formatLocaleName($locale) {
        return str_replace("-", "_", $locale);
    }
}