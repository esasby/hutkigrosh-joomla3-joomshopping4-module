<?php
/*
* @info Платёжный модуль Hutki grosh для JoomShopping
* @package JoomShopping for Joomla!
* @subpackage payment
* @author Esas.by
*/


use esas\hutkigrosh\controllers\ControllerAddBill;
use esas\hutkigrosh\controllers\ControllerWebpayFormSimple;
use esas\hutkigrosh\protocol\BillNewRs;
use esas\hutkigrosh\Registry;
use esas\hutkigrosh\view\client\CompletionPanel;
use esas\hutkigrosh\wrappers\ConfigurationWrapperJoomshopping;
use esas\hutkigrosh\wrappers\OrderWrapperJoomshopping;
use JFactory;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');
require_once(JPATH_SITE . '/components/com_jshopping/payments/pm_hg/init.php');

class pm_hg extends PaymentRoot
{
    const MODULE_MACHINE_NAME = 'pm_hg';

    /**
     * Отображение формы с настройками платежного шлюза (админка)
     * @param $params
     */
    function showAdminFormParams($params)
    {
        try {
            $configForm = Registry::getRegistry()->getConfigForm();
            if (is_array($_SESSION["pm_params"]) && !$configForm->validateAll($_SESSION["pm_params"])) {
                JFactory::getApplication()->enqueueMessage("Wrong settings", 'error');
            }
            echo $configForm->generate();
        } catch (Throwable $e) {
            Logger::getLogger("admin")->error("Exception: ", $e);
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }
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
     * @throws Throwable
     */
    function showEndForm($pmconfigs, $order)
    {
        try {
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
        } catch (Throwable $e) {
            Logger::getLogger("payment")->error("Exception:", $e);
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }
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
     * @throws Throwable
     */
    function complete($pmconfigs, $order, $payment)
    {
        try {
            $orderWrapper = new OrderWrapperJoomshopping($order);
            $configurationWrapper = new  ConfigurationWrapperJoomshopping($pmconfigs);
            $completionPanel = new CompletionPanel($orderWrapper);
            if ($configurationWrapper->isAlfaclickSectionEnabled()) {
                $completionPanel->setAlfaclickUrl(self::generateControllerPath("alfaclick"));
            }
            if ($configurationWrapper->isWebpaySectionEnabled()) {
                $controller = new ControllerWebpayFormSimple(Uri::root() . self::generateControllerPath("complete") .
                    "&order_number=" . $orderWrapper->getOrderNumber() .
                    "&bill_id=" . $orderWrapper->getBillId());
                $webpayResp = $controller->process($orderWrapper);
                $completionPanel->setWebpayForm($webpayResp->getHtmlForm());
                if (array_key_exists('webpay_status', $_REQUEST))
                    $completionPanel->setWebpayStatus($_REQUEST['webpay_status']);
            }
            $completionPanel->getViewStyle()
                ->setMsgUnsuccessClass("alert alert-error")
                ->setMsgSuccessClass("alert alert-info")
                ->setWebpayButtonClass("btn btn-success")
                ->setAlfaclickButtonClass("btn btn-success");
            $completionPanel->render();
        } catch (Throwable $e) {
            Logger::getLogger("payment")->error("Exception:", $e);
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }
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