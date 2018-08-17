<?php

namespace esas\hutkigrosh\wrappers;

use esas\hutkigrosh\ConfigurationFields;
use esas\hutkigrosh\lang\TranslatorJoom;
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
        parent::__construct(new TranslatorJoom());
        // если массив настроек не передан в параметрах, загружаем его из БД
        if ($pmconfigs == null) {
            $pm_method = JSFactory::getTable('paymentMethod', 'jshop');
            $pm_method->loadFromClass(pm_hg::MODULE_MACHINE_NAME);
            $pmconfigs = $pm_method->getConfigs();
        }
        $this->pmconfigs = $pmconfigs;
    }


    /**
     * Произольно название интернет-мазагина
     * @return string
     */
    public function getShopName()
    {
        return $this->getOption(ConfigurationFields::SHOP_NAME);
    }

    /**
     * Имя пользователя для доступа к системе ХуткиГрош
     * @return string
     */
    public function getHutkigroshLogin()
    {
        return $this->getOption(ConfigurationFields::LOGIN, true);
    }

    /**
     * Пароль для доступа к системе ХуткиГрош
     * @return string
     */
    public function getHutkigroshPassword()
    {
        return $this->getOption(ConfigurationFields::PASSWORD, true);
    }

    /**
     * Включен ли режим песчоницы
     * @return boolean
     */
    public function isSandbox()
    {
        return $this->checkOn(ConfigurationFields::SANDBOX);
    }

    /**
     * Уникальный идентификатор услуги в ЕРИП
     * @return string
     */
    public function getEripId()
    {
        return $this->getOption(ConfigurationFields::ERIP_ID, true);
    }

    /**
     * Включена ля оповещение клиента по Email
     * @return boolean
     */
    public function isEmailNotification()
    {
        return $this->checkOn(ConfigurationFields::EMAIL_NOTIFICATION);
    }

    /**
     * Включена ля оповещение клиента по Sms
     * @return boolean
     */
    public function isSmsNotification()
    {
        return $this->checkOn(ConfigurationFields::SMS_NOTIFICATION);
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
        $rowstatictext = $statictext->loadData("order_hg_completion_text");
        return $this->warnIfEmpty($rowstatictext->text);
    }

    /**
     * Какой статус присвоить заказу после успешно выставления счета в ЕРИП (на шлюз Хуткигрош_
     * @return string
     */
    public function getBillStatusPending()
    {
        return $this->getOption(ConfigurationFields::BILL_STATUS_PENDING, true);
    }

    /**
     * Какой статус присвоить заказу после успешно оплаты счета в ЕРИП (после вызова callback-а шлюзом ХуткиГрош)
     * @return string
     */
    public function getBillStatusPayed()
    {
        return $this->getOption(ConfigurationFields::BILL_STATUS_PAYED, true);
    }

    /**
     * Какой статус присвоить заказу в случаче ошибки выставления счета в ЕРИП
     * @return string
     */
    public function getBillStatusFailed()
    {
        return $this->getOption(ConfigurationFields::BILL_STATUS_FAILED, true);
    }

    /**
     * Какой статус присвоить заказу после успешно оплаты счета в ЕРИП (после вызова callback-а шлюзом ХуткиГрош)
     * @return string
     */
    public function getBillStatusCanceled()
    {
        return $this->getOption(ConfigurationFields::BILL_STATUS_CANCELED, true);
    }

    private function checkOn($key)
    {
        $value = $this->pmconfigs[$key];
        return $value == '1' || $value == "true";
    }

    /**
     * Описание системы ХуткиГрош, отображаемое клиенту на этапе оформления заказа
     * @return string
     *
     */
    public function getPaymentMethodDetails()
    {
        // TODO: не используется
    }

    /**
     * Необходимо ли добавлять кнопку "выставить в Alfaclick"
     * @return boolean
     */
    public function isAlfaclickButtonEnabled()
    {
        return $this->checkOn(ConfigurationFields::ALFACLICK_BUTTON);
    }

    /**
     * Необходимо ли добавлять кнопку "оплатить картой"
     * @return boolean
     */
    public function isWebpayButtonEnabled()
    {
        return $this->checkOn(ConfigurationFields::WEBPAY_BUTTON);
    }

    public function getOption($key, $warn = false)
    {
        $value = $this->pmconfigs[$key];
        if ($warn)
            return $this->warnIfEmpty($value, $key);
        else
            return $value;
    }

    /**
     * Название системы ХуткиГрош, отображаемое клиенту на этапе оформления заказа
     * @return string
     */
    public function getPaymentMethodName()
    {
        // TODO: не используется
    }

    /***
     * В некоторых CMS не получается в настройках хранить html, поэтому использует текст итогового экрана по умолчанию,
     * в который проставлятся значение ERIPPATh
     * @return string
     */
    public function getEripPath()
    {
        // TODO: не используется
    }

    /**
     * Какой срок действия счета после его выставления (в днях)
     * @return string
     */
    public function getDueInterval()
    {
        return $this->getOption(ConfigurationFields::DUE_INTERVAL, true);
    }
}