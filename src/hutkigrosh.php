<?php
/*
* @info     Платёжный модуль Hutkigrosh для JoomShopping
* @package  hutkigrosh
* @author   esas.by
* @license  GNU/GPL
*/

use esas\hutkigrosh\ConfigurationFields;
use esas\hutkigrosh\Registry;
use esas\hutkigrosh\view\admin\ConfigFormJoom;

defined('_JEXEC') or die;
require_once(JPATH_SITE . '/components/com_jshopping/payments/pm_hg/init.php');

class plgJShoppingHutkigrosh extends JPlugin
{
    function onBeforeSavePayment(&$post)
    {
        try {
            $pm_params = $_REQUEST["pm_params"];// значение приходтся брать не из $post, а из $_REQUEST, чтоюы сохранить html-теги
            if (!Registry::getRegistry()->getConfigForm()->validateAll($pm_params)) {
                $_SESSION["pm_params"] = $pm_params;  // сохраняем в сессии, т.к. не нашел более подходящего способа передачи POST-переменных при редиректе
                JFactory::getApplication()->redirect($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . '&task=edit&payment_id=' . $_REQUEST["payment_id"]);
                exit;
            } else {
                unset($_SESSION["pm_params"]); // если все ок, то удаляем из сессии, чтобы в showAdminFormParams не
            }
            $languagesModel = JSFactory::getModel("languages");
            foreach ($languagesModel->getAllLanguages(1) as $lang) {
                //todo HGCMS-13
                $bind["text_" . $lang->language] = $pm_params[ConfigurationFields::completionText()];
                unset($post["pm_params"][ConfigurationFields::completionText()]); // это текст не надо сохранять с остальными параметрами, поэтому удаляем из массива
            }
            $statictext = JSFactory::getTable("statictext", "jshop");
            $statictext->load(["alias" => ConfigurationFields::completionText()]);
            if ($statictext->id == null) // на случай, если в БД еще нет такой записи
                $bind["alias"] = ConfigurationFields::completionText();
            $statictext->bind($bind);
            $statictext->store();
        } catch (Throwable $e) {
            Logger::getLogger("admin")->error("Exception: ", $e);
        }
    }

}