<?php

defined('_JEXEC') or die();

class JshoppingModelHutkigrosh extends JModelLegacy
{

    /**
     * Получаем из БД заказ не по order_id, а по индентификатору транзакции внешней системы
     * Для ХуткиГрош это billID
     * @param $transaction
     * @return order
     */
    static function getOrderByTrxId($transaction)
    {
        $db = JFactory::getDBO();
        $query = "SELECT order_id FROM `#__jshopping_orders` WHERE transaction = '" . $db->escape($transaction) . "' ORDER BY order_id DESC";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if (count($rows) != 1) {
            saveToLog("payment.log", 'Can not load order by transaction[' . $transaction . "]");
            return null;
        }
        $order = JSFactory::getTable('order', 'jshop');
        $order->load($rows[0]->order_id);
        return $order;
    }


    /**
     * Получаем из БД заказ по order_number
     * @param $order_number
     * @return order
     */
    static function getOrderByOrderNumber($order_number)
    {
        $db = JFactory::getDBO();
        $query = "SELECT order_id FROM `#__jshopping_orders` WHERE order_number = '" . $db->escape($order_number) . "' ORDER BY order_id DESC";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if (count($rows) != 1) {
            saveToLog("payment.log", 'Can not load order by order_number[' . $order_number . "]");
            return null;
        }
        $order = JSFactory::getTable('order', 'jshop');
        $order->load($rows[0]->order_id);
        return $order;
    }
}