<?php
/*
* @info     Платёжный модуль Hutkigrosh для JoomShopping
* @package  hutkigrosh
* @author   esas.by
* @license  GNU/GPL
*/
namespace esas\hutkigrosh\wrappers;
defined('_JEXEC') or die;

use JSFactory;

/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 13.03.2018
 * Time: 14:51
 */
class OrderWrapperJoomshopping extends OrderWrapper
{
    private $order;

    /**
     * OrderWrapperJoomshopping constructor.
     * @param $order
     */
    public function __construct($order)
    {
        parent::__construct();
        $this->order = $order;
    }

    /**
     * Уникальный номер заказ в рамках CMS
     * @return string
     */
    public function getOrderId()
    {
        return $this->order->order_id;
    }

    public function getOrderNumber()
    {
        return $this->order->order_number;
    }


    /**
     * Полное имя покупателя
     * @return string
     */
    public function getFullName()
    {
        return $this->order->f_name . ' ' . $this->order->l_name;
    }

    /**
     * Мобильный номер покупателя для sms-оповещения
     * (если включено администратором)
     * @return string
     */
    public function getMobilePhone()
    {
        return $this->order->phone;
    }

    /**
     * Email покупателя для email-оповещения
     * (если включено администратором)
     * @return string
     */
    public function getEmail()
    {
        return $this->order->email;
    }

    /**
     * Физический адрес покупателя
     * @return string
     */
    public function getAddress()
    {
        return $this->order->city . ' ' .
            $this->order->state . ' ' .
            $this->order->street;
    }

    /**
     * Общая сумма товаров в заказе
     * @return string
     */
    public function getAmount()
    {
        return $this->order->order_total;
    }

    /**
     * Валюта заказа (буквенный код)
     * @return string
     */
    public function getCurrency()
    {
        return $this->order->currency_code;
    }

    /**
     * Массив товаров в заказе
     * @return \esas\hutkigrosh\wrappers\OrderProductWrapper[]
     */
    public function getProducts()
    {
        $products = $this->order->getAllItems();
        foreach ($products as $item) {
            $ret[] = new OrderProductWrapperJoomshopping($item);
        }
        return $ret;
    }

    /**
     * BillId (идентификатор хуткигрош) успешно выставленного счета
     * @return mixed
     */
    public function getBillId()
    {
        return $this->order->transaction;
    }

    /**
     * Текущий статус заказа в CMS
     * @return mixed
     */
    public function getStatus()
    {
        //TODO
//        return $this->order->get('status');
    }

    /**
     * Обновляет статус заказа в БД
     * @param $newStatus
     * @return mixed
     */
    public function updateStatus($newStatus)
    {
        $model = JSFactory::getModel('orderChangeStatus', 'jshop');
        $model->setData($this->getOrderId(), $newStatus, 0); //тут можно включить sendmail
        $model->store();
    }

    /**
     * Сохраняет привязку billid к заказу
     * @param $billId
     * @return mixed
     */
    public function saveBillId($billId)
    {
        //не испольхуется, т.к привязка  сохраняется на уровне самой CMS
    }
}