<?php

namespace esas\hutkigrosh\controllers;

/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 22.03.2018
 * Time: 11:55
 */
use esas\hutkigrosh\wrappers\ConfigurationWrapperJoomshopping;
use esas\hutkigrosh\wrappers\OrderWrapperJoomshopping;
use JSFactory;
use jshopHutkigrosh;

require_once(JPATH_SITE . '/components/com_jshopping/models/hutkigrosh.php');

class ControllerNotifyJoomshopping extends ControllerNotify
{
    /**
     * ControllerNotifyModxMinishop2 constructor.
     */
    public function __construct(ConfigurationWrapperJoomshopping $configurationWrapper)
    {
        parent::__construct($configurationWrapper);
    }


    /**
     * По локальному идентификатору заказа возвращает wrapper
     * @param $orderId
     * @return \esas\hutkigrosh\wrappers\OrderWrapper
     */
    public function getOrderWrapperByOrderNumber($orderNumber)
    {
        $order = jshopHutkigrosh::getOrderByOrderNumber($orderNumber);
        return empty($order) ? null : new OrderWrapperJoomshopping($order);
    }
}