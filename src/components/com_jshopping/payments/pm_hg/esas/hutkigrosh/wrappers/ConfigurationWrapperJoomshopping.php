<?php

namespace esas\hutkigrosh\wrappers;

use esas\hutkigrosh\ConfigurationFields;
use Exception;
use JSFactory;
use pm_hg;

/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 13.03.2018
 * Time: 14:44
 */
class ConfigurationWrapperJoomshopping extends ConfigurationWrapper
{
    private $pmconfigs;

    /**
     * ConfigurationWrapperJoomshopping constructor.
     * @param array $pmconfigs
     */
    public function __construct($pmconfigs = null)
    {
        parent::__construct();
        // если массив настроек не передан в параметрах, загружаем его из БД
        if ($pmconfigs == null) {
            $pm_method = JSFactory::getTable('paymentMethod', 'jshop');
            $pm_method->loadFromClass(pm_hg::MODULE_MACHINE_NAME);
            $pmconfigs = $pm_method->getConfigs();
        }
        $this->pmconfigs = $pmconfigs;
    }



    /**
     * Итоговый текст, отображаемый клменту после успешного выставления счета
     * Получаем из БД текст успешного выставления счета
     * В отличие от других CMS Joomls не может хранить его прямо в параметрах модуля.
     * Для больших текстов (с html) используется отдельная таблица
     * @return string
     */
    public function getCompletionText()
    {
        $statictext = JSFactory::getTable("statictext", "jshop");
        $rowstatictext = $statictext->loadData(ConfigurationFields::completionText());
        return $this->warnIfEmpty($rowstatictext->text, "completion_text");
    }

    /**
     * @param $key
     * @return string
     * @throws Exception
     */
    public function getCmsConfig($key)
    {
        return $this->pmconfigs[$key];
    }

    public function convertToBoolean($cmsConfigValue)
    {
        return $cmsConfigValue == '1' || $cmsConfigValue == "true";
    }


}