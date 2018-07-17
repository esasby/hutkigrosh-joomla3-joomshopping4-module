<?php
/*
* @info Платёжный модуль hgrosh для JoomShopping
* @package JoomShopping for Joomla!
* @subpackage payment
* @author hgrosh.by
*/

use esas\hutkigrosh\lang\TranslatorJoom;
use esas\hutkigrosh\wrappers\ConfigurationWrapperJoomshopping;

defined('_JEXEC') or die();


function createTextField(ConfigurationWrapperJoomshopping $configurationWrapper, $key)
{
    $output = '<tr>';
    $output .= '<td class="key" width="300" title="' . TranslatorJoom::translate($key . "_desc") . '">' . TranslatorJoom::translate($key) . '</td>';
    $output .= '<td>';
    $output .= '<input type="text" name="pm_params[' . $key . ']" class="inputbox" value="' . $configurationWrapper->get($key) . '"/>';
    $output .= '</td>';
    $output .= '</tr>';
    return $output;
}

function createCheckboxField($configurationWrapper, $key)
{
    $output = '<tr>';
    $output .= '<td class="key" width="300" title="' . TranslatorJoom::translate($key . "_desc") . '">' . TranslatorJoom::translate($key) . '</td>';
    $output .= '<td>';
    $output .= '<input type="checkbox" name="pm_params[' . $key . ']" class="inputbox" value="1" ' . ($configurationWrapper->get($key) ? 'checked="checked"' : "") . '/>';
    $output .= '</td>';
    $output .= '</tr>';
    return $output;
}

function createStatusSelectField($configurationWrapper, $key)
{
    $orders = JModelLegacy::getInstance('orders', 'JshoppingModel');
    $output = '<tr>';
    $output .= '<td class="key" width="300" title="' . TranslatorJoom::translate($key . "_desc") . '">' . TranslatorJoom::translate($key) . '</td>';
    $output .= '<td>';
    $output .= JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[' . $key . ']', 'class="inputbox" size="1"', 'status_id', 'name', $configurationWrapper->get($key));
    $output .= '</td>';
    $output .= '</tr>';
    return $output;
}

?>
<div class="col100">
    <fieldset class="adminform">
        <table class="admintable" width="100%">
            <?php
            /** @var ConfigurationWrapperJoomshopping $configurationWrapper */
            echo createCheckboxField($configurationWrapper, $configurationWrapper::CONFIG_HG_SANDBOX);
            echo createTextField($configurationWrapper, $configurationWrapper::CONFIG_HG_SHOP_NAME);
            echo createTextField($configurationWrapper, $configurationWrapper::CONFIG_HG_ERIP_ID);
            echo createTextField($configurationWrapper, $configurationWrapper::CONFIG_HG_LOGIN);
            echo createTextField($configurationWrapper, $configurationWrapper::CONFIG_HG_PASSWORD);
            echo createCheckboxField($configurationWrapper, $configurationWrapper::CONFIG_HG_SMS_NOTIFICATION);
            echo createCheckboxField($configurationWrapper, $configurationWrapper::CONFIG_HG_EMAIL_NOTIFICATION);
            echo createStatusSelectField($configurationWrapper, $configurationWrapper::CONFIG_HG_BILL_STATUS_PENDING);
            echo createStatusSelectField($configurationWrapper, $configurationWrapper::CONFIG_HG_BILL_STATUS_PAYED);
            echo createStatusSelectField($configurationWrapper, $configurationWrapper::CONFIG_HG_BILL_STATUS_CANCELED);
            echo createStatusSelectField($configurationWrapper, $configurationWrapper::CONFIG_HG_BILL_STATUS_FAILED);
            echo createCheckboxField($configurationWrapper, $configurationWrapper::CONFIG_HG_ALFACLICK_BUTTON);
            echo createCheckboxField($configurationWrapper, $configurationWrapper::CONFIG_HG_WEBPAY_BUTTON);
            ?>
        </table>
    </fieldset>
</div>
<div class="clr"></div>