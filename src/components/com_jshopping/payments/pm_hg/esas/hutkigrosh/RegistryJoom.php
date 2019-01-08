<?php
/*
* @info     Платёжный модуль Hutkigrosh для JoomShopping
* @package  hutkigrosh
* @author   esas.by
* @license  GNU/GPL
*/
namespace esas\hutkigrosh;

defined('_JEXEC') or die;
use esas\hutkigrosh\lang\TranslatorJoom;
use esas\hutkigrosh\view\admin\ConfigFormJoom;
use esas\hutkigrosh\wrappers\ConfigurationWrapperJoomshopping;
use esas\hutkigrosh\wrappers\OrderWrapperJoomshopping;
use JSFactory;

class RegistryJoom extends Registry
{
    public function createConfigurationWrapper()
    {
        return new ConfigurationWrapperJoomshopping();
    }

    public function createTranslator()
    {
        return new TranslatorJoom();
    }

    public function getOrderWrapper($orderNumber) {
        $order = JSFactory::getModel("hutkigrosh")->getOrderByOrderNumber($orderNumber);
        return new OrderWrapperJoomshopping($order);
    }

    public function createConfigForm() {
        $configForm = new ConfigFormJoom();
        $configForm->addRequired();
        return $configForm;
    }
}