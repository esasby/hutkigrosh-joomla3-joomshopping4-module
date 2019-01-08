<?php
/*
* @info     Платёжный модуль Hutkigrosh для JoomShopping
* @package  hutkigrosh
* @author   esas.by
* @license  GNU/GPL
*/
define('PATH_JSHOPPING', JPATH_SITE . '/components/com_jshopping/');

use esas\hutkigrosh\ConfigurationFields;
use esas\hutkigrosh\Registry;

defined('_JEXEC') or die();
jimport('joomla.filesystem.folder');


class PlgjshoppinghutkigroshInstallerScript
{
    public function update()
    {
    }

    public function install($parent)
    {
    }

    public function postflight($type, $parent)
    {
        $pmPath = JPATH_SITE . '/plugins/jshopping/hutkigrosh/components';
        $newPath = JPATH_SITE . '/components';
        if (!JFolder::copy($pmPath, $newPath, "", true)) {
            $this->success = false;
            echo JText::sprintf('COM_PFMIGRATOR_FOLDER_RENAME_FAILED', $newPath);
            return false;
        }
        require_once(PATH_JSHOPPING . 'lib/factory.php');
        require_once(PATH_JSHOPPING . 'payments/pm_hg/init.php');
        if (strtolower($type) === 'install') {
            $this->dbAddPaymentMethod();
            $this->dbActivatePlugin();
            $this->dbAddCompletionText();
        }
    }

    public function uninstall($parent)
    {
        require_once(PATH_JSHOPPING . 'lib/factory.php');
        require_once(PATH_JSHOPPING . 'payments/pm_hg/init.php');
        $this->dbDeletePaymentMethod();
        $this->dbDeleteCompletionText();
        $this->deleteWithLogging(PATH_JSHOPPING . 'models/hutkigrosh.php');
        $this->deleteWithLogging(PATH_JSHOPPING . 'controllers/hutkigrosh.php');
        $this->deleteWithLogging(PATH_JSHOPPING . 'payments/pm_hg');
        return $this->success;
    }

    private function dbAddPaymentMethod()
    {
        $paymentMethod = new stdClass();
        $paymentMethod->payment_code = "hutkigrosh";
        $paymentMethod->scriptname = pm_hg::MODULE_MACHINE_NAME;
        $paymentMethod->payment_class = pm_hg::MODULE_MACHINE_NAME;
        $paymentMethod->payment_publish = 1;
        $paymentMethod->payment_ordering = 0;
        $paymentMethod->payment_params = '';
        $paymentMethod->payment_type = 2;
        $paymentMethod->price = 0.00;
        $paymentMethod->price_type = 0;
        $paymentMethod->tax_id = 1;
        $paymentMethod->image = ''; //todo
        $paymentMethod->show_descr_in_email = 0;
        $paymentMethod->show_bank_in_order = 1;
        $paymentMethod->order_description = '';
        $paymentMethod->order_description = '';
        $jshoppingLanguages = JSFactory::getTable('language', 'jshop');
        foreach ($jshoppingLanguages::getAllLanguages() as $lang) {
            $i18nField = 'name_' . $lang->language;
            $paymentMethod->$i18nField = Registry::getRegistry()->getTranslator()->getConfigFieldDefault(ConfigurationFields::paymentMethodName(), $lang->language);
            $i18nField = 'description_' . $lang->language;
            $paymentMethod->$i18nField = Registry::getRegistry()->getTranslator()->getConfigFieldDefault(ConfigurationFields::paymentMethodDetails(), $lang->language);
        }
        $result = JFactory::getDbo()->insertObject('#__jshopping_payment_method', $paymentMethod);
    }

    private function dbActivatePlugin()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update('#__extensions');
        $query->set($db->quoteName('enabled') . ' = 1');
        $query->where($db->quoteName('element') . ' = ' . $db->quote('hutkigrosh'));
        $query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
        $db->setQuery($query);
        $db->execute();
    }

    private function dbAddCompletionText()
    {
        $staticText = new stdClass();
        $staticText->alias = ConfigurationFields::completionText();
        $staticText->use_for_return_policy = 0;
        $jshoppingLanguages = JSFactory::getTable('language', 'jshop');
        foreach ($jshoppingLanguages::getAllLanguages() as $lang) {
            $i18nField = 'text_' . $lang->language;
            $staticText->$i18nField = Registry::getRegistry()->getTranslator()->getConfigFieldDefault(ConfigurationFields::completionText(), $lang->language);
        }
        $result = JFactory::getDbo()->insertObject('#__jshopping_config_statictext', $staticText);
    }

    private function dbDeletePaymentMethod()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $conditions = array(
            $db->quoteName('payment_code') . ' = ' . $db->quote('hutkigrosh')
        );
        $query->delete($db->quoteName('#__jshopping_payment_method'));
        $query->where($conditions);

        $db->setQuery($query);

        $result = $db->execute();
    }


    private function deleteWithLogging($file)
    {
        if (is_dir($file)) {
            JFolder::delete($file);
            $deleted = !JFolder::exists($file);
        } else
            $deleted = JFile::delete($file);
        if (!$deleted) {
            $this->success = false;
            echo JText::sprintf('JLIB_INSTALLER_ERROR_FILE_FOLDER', $file) . '<br />';;
        }
    }

    private function dbDeleteCompletionText()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $conditions = array(
            $db->quoteName('alias') . ' = ' . $db->quote(ConfigurationFields::completionText())
        );
        $query->delete($db->quoteName('#__jshopping_config_statictext'));
        $query->where($conditions);

        $db->setQuery($query);

        $result = $db->execute();
    }
}