<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 22.03.2018
 * Time: 12:38
 */

namespace esas\hutkigrosh\controllers;


use esas\hutkigrosh\wrappers\ConfigurationWrapperJoomshopping;
use esas\hutkigrosh\wrappers\OrderWrapper;
use Joomla\CMS\Uri\Uri;

class ControllerWebpayFormJoomshopping extends ControllerWebpayForm
{
    public function __construct(ConfigurationWrapperJoomshopping $configurationWrapper)
    {
        parent::__construct($configurationWrapper);
    }

    /**
     * Основная часть URL для возврата с формы webpay (чаще всего current_url)
     * @return string
     */
    public function getReturnUrl(OrderWrapper $orderWrapper)
    {
        return Uri::root() . \pm_hg::generateControllerPath("complete") .
            "&order_id=" . $orderWrapper->getOrderId() .
            "&bill_id=" . $orderWrapper->getBillId();
    }
}