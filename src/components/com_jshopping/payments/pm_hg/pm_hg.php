<?php
/*
* @info Платёжный модуль Hutki grosh для JoomShopping
* @package JoomShopping for Joomla!
* @subpackage payment
* @author Esas.by
*/


use esas\hutkigrosh\controllers\ControllerAddBill;
use esas\hutkigrosh\controllers\ControllerWebpayFormJoomshopping;
use esas\hutkigrosh\protocol\BillNewRs;
use esas\hutkigrosh\wrappers\ConfigurationWrapperJoomshopping;
use esas\hutkigrosh\wrappers\OrderWrapperJoomshopping;

defined('_JEXEC') or die('Restricted access');
require_once(JPATH_SITE . '/components/com_jshopping/payments/pm_hg/SimpleAutoloader.php');

class pm_hg extends PaymentRoot
{
    const MODULE_MACHINE_NAME = 'pm_hg';

    private $logger;

    /**
     * pm_hg constructor.
     */
    public function __construct()
    {
        $this->logger = Logger::getLogger(pm_hg::class);
    }

    /**
     * Отображение формы с настройками платежного шлюза (админка)
     * @param $params
     */
    function showAdminFormParams($params)
    {
        $configurationWrapper = new ConfigurationWrapperJoomshopping($params);
        include(dirname(__FILE__) . '/adminparamsform.php');
    }

    const HG_RESP_CODE_OK = '0';
    const HG_RESP_CODE_ERROR = '2018';


    function checkTransaction($pmconfigs, $order, $act)
    {
        $request_params = JFactory::getApplication()->input->request->getArray();
        // все переменные передаются в запросе, можно передевать через сессию
        $hgStatusCode = $request_params['hg_status'];
        $billId = $request_params['bill_id'];
        if ($hgStatusCode != '0') {
            // в hutkigrosh большое кол-во кодов неуспешного выставления счета, поэтому для упрощения сводим их все к одному
            $respCode = self::HG_RESP_CODE_ERROR;
            $message = "Ошибка выставления счета";
        } else {
            $respCode = self::HG_RESP_CODE_OK;
            $message = 'Order[' . $order->order_id . '] was successfully added to Hutkigrosh with billid[' . $billId . ']';
        }
        //пока счет не будет оплачен в ЕРИП у заказа будет статус Pending
        return array($respCode, $message, $billId);
    }

    /**
     * На основе кода ответа от платежного шлюза задаем статус заказу
     * @param int $rescode
     * @param array $pmconfigs
     * @return mixed
     */
    function getStatusFromResCode($rescode, $pmconfigs)
    {
        $configurationWrapper = new ConfigurationWrapperJoomshopping($pmconfigs);
        if ($rescode != '0') {
            $status = $configurationWrapper->getBillStatusFailed();
        } else {
            $status = $configurationWrapper->getBillStatusPending();
        }
        return $status;
    }

    /**
     * При каких кодах ответов от платежного шлюза считать оплату неуспешной.
     * @return array
     */
    function getNoBuyResCode()
    {
        // в hutkigrosh большое кол-во кодов неуспешного выставления счета, поэтому для упрощения сводим их все к одному
        return array(self::HG_RESP_CODE_ERROR);
    }

    /**
     * Форма отображаемая клиенту на step7. В теории должна содердать поля, которые надо задать клиенту перед отправкой
     * на плетежный шлюз. В случае с ХуткиГрош никаких полей клиенту показывать не надо и тут сразу выполняется запрос на
     * выставления счета к шлюзу и редирект на следующий step
     * @param $pmconfigs
     * @param $order
     * @throws Exception
     */
    function showEndForm($pmconfigs, $order)
    {
        $configurationWrapper = new ConfigurationWrapperJoomshopping($pmconfigs);
        $orderWrapper = new OrderWrapperJoomshopping($order);
        $controller = new ControllerAddBill($configurationWrapper);
        /**
         * @var BillNewRs
         */
        $addBillRs = $controller->process($orderWrapper);
        /**
         * На этом этапе мы только выполняем запрос к HG для добавления счета. Мы не показываем итоговый экран
         * (с кнопками webpay и alfaclick), а выполняем автоматический редирект на step7
         **/
        $redirectUrl = "index.php?option=com_jshopping&controller=checkout&task=step7" .
            "&js_paymentclass=" . self::MODULE_MACHINE_NAME .
            "&hg_status=" . $addBillRs->getResponseCode() .
            "&order_id=" . $order->order_id;
        if ($addBillRs->getBillId())
            $redirectUrl .= "&bill_id=" . $addBillRs->getBillId();
        JFactory::getApplication()->redirect($redirectUrl);
    }


    function getUrlParams($pmconfigs)
    {
        $reqest_params = JFactory::getApplication()->input->request->getArray();
        $params = array();
        $params['order_id'] = $reqest_params['order_id'];
        $params['hash'] = '';
        $params['checkHash'] = false;
        $params['checkReturnParams'] = false;
        return $params;
    }


    /**
     * В теории, тут должно отправлятся уведомление на шлюз об успешном оформлении заказа.
     * В случае с ХуткиГрош мы тут отображаем итоговый экран с доп. кнопками.
     * @param $pmconfigs
     * @param $order
     * @param $payment
     */
    function complete($pmconfigs, $order, $payment)
    {
        $configurationWrapper = new ConfigurationWrapperJoomshopping($pmconfigs);
        $orderWrapper = new OrderWrapperJoomshopping($order);
        $completion_text = $configurationWrapper->cookCompletionText($orderWrapper);
        if ($configurationWrapper->isAlfaclickButtonEnabled()) {
            $alfaclick_billID = $orderWrapper->getBillId();
            $alfaclick_phone = $order->phone;
            $alfaclick_url = self::generateControllerPath("alfaclick");
        }
        if ($configurationWrapper->isWebpayButtonEnabled()) {
            $controller = new ControllerWebpayFormJoomshopping($configurationWrapper);
            $webpayResp = $controller->process($orderWrapper);
            $webpay_form = $webpayResp->getHtmlForm();
            $webpay_status = $_REQUEST['webpay_status']; // ???
        }
        include(JPATH_SITE . '/components/com_jshopping/payments/pm_hg/completion.php');
    }

    /**
     * Генерация редиректа на контроллер.
     * @param $task
     * @return string
     */
    public static function generateControllerPath($task)
    {
        return "index.php?option=com_jshopping&controller=hutkigrosh&task=" . $task;
    }
}
?>