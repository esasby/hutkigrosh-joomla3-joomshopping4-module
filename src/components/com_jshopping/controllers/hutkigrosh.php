<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 09.02.2018
 * Time: 14:51
 */
defined('_JEXEC') or die();

use esas\hutkigrosh\controllers\ControllerAlfaclick;
use esas\hutkigrosh\controllers\ControllerNotifyJoomshopping;
use esas\hutkigrosh\wrappers\ConfigurationWrapperJoomshopping;

require_once(JPATH_SITE . '/components/com_jshopping/payments/pm_hg/pm_hg.php');
require_once(JPATH_SITE . '/components/com_jshopping/models/hutkigrosh.php');

class JshoppingControllerHutkigrosh extends JshoppingControllerBase
{
    /**
     * Выставляет счет в альфаклик
     */
    function alfaclick()
    {
        $controller = new ControllerAlfaclick(new ConfigurationWrapperJoomshopping());
        $controller->process($_REQUEST['billid'], $_REQUEST['phone']);
        die();
    }

    /**
     * В Joomla после оформления заказа и перехода на стадию "finish". Происходит очистка
     * сессии. И если необходимо повторно отобразить итоговую страницу с инструкцией по оплате счета
     * приходится или подпихивать в сессию переменную jshop_end_order_id или делать через этот метож контроллера
     */
    function complete()
    {
        $order_id = $_REQUEST['order_id'];
        $bill_id = $_REQUEST['bill_id'];
        $order = JSFactory::getTable('order', 'jshop');
        $order->load($order_id);
        $pm_method = $order->getPayment();
        $paymentsysdata = $pm_method->getPaymentSystemData();
        $payment_system = $paymentsysdata->paymentSystem;
        // проверяем что для указанного заказа оплата производилась через ХуткиГрош
        if ($payment_system
            && $pm_method->payment_class == pm_hg::MODULE_MACHINE_NAME
            && $order->transaction == $bill_id) {
            $pmconfigs = $pm_method->getConfigs();
            $payment_system->complete($pmconfigs, $order, $pm_method);
        }
    }

    /**
     * Callback, который вызывает сам ХуткиГрош для оповещение об оплате счета в ЕРИП
     * Тут выполняется дополнительная проверка статуса счета на шлюза и при необходимости изменение его статус заказа
     * в локальной БД
     */
    function notify()
    {
        try {
            $billId = $_REQUEST['purchaseid'];
            $order = jshopHutkigrosh::getOrderByTrxId($billId);
            if (!isset($order) || !isset($order->order_id)) {
                throw new Exception('Hutkigrosh: Can not detect order by billid[' . $billId . "]");
            }
            $pm_method = $order->getPayment();
            $pmconfigs = $pm_method->getConfigs();
            $controller = new ControllerNotifyJoomshopping(new ConfigurationWrapperJoomshopping($pmconfigs));
            $controller->process($billId);
        } catch (Exception $e) {
            saveToLog("payment.log", $e->getMessage());
        }
    }
}