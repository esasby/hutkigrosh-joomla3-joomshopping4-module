<?php
/*
* @info Платёжный модуль hgrosh для JoomShopping
* @package JoomShopping for Joomla!
* @subpackage payment
* @author hgrosh.by
*/

use esas\hutkigrosh\ConfigurationFields;
use esas\hutkigrosh\wrappers\ConfigurationWrapperJoomshopping;

defined('_JEXEC') or die();


function createTextField(ConfigurationWrapperJoomshopping $configurationWrapper, $key)
{
    $output = '<tr>';
    $output .= '<td class="key" width="300" title="' . $configurationWrapper->translateFieldDescription($key) . '">' . $configurationWrapper->translateFieldName($key) . '</td>';
    $output .= '<td>';
    $output .= '<input type="text" name="pm_params[' . $key . ']" class="inputbox" value="' . $configurationWrapper->get($key) . '"/>';
    $output .= '</td>';
    $output .= '</tr>';
    return $output;
}

function createCheckboxField(ConfigurationWrapperJoomshopping $configurationWrapper, $key)
{
    $output = '<tr>';
    $output .= '<td class="key" width="300" title="' . $configurationWrapper->translateFieldDescription($key) . '">' . $configurationWrapper->translateFieldName($key) . '</td>';
    $output .= '<td>';
    $output .= '<input type="checkbox" name="pm_params[' . $key . ']" class="inputbox" value="1" ' . ($configurationWrapper->get($key) ? 'checked="checked"' : "") . '/>';
    $output .= '</td>';
    $output .= '</tr>';
    return $output;
}

function createStatusSelectField(ConfigurationWrapperJoomshopping $configurationWrapper, $key)
{
    $orders = JModelLegacy::getInstance('orders', 'JshoppingModel');
    $output = '<tr>';
    $output .= '<td class="key" width="300" title="' . $configurationWrapper->translateFieldDescription($key) . '">' . $configurationWrapper->translateFieldName($key) . '</td>';
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
            echo createCheckboxField($configurationWrapper, ConfigurationFields::SANDBOX);
            echo createTextField($configurationWrapper, ConfigurationFields::SHOP_NAME);
            echo createTextField($configurationWrapper, ConfigurationFields::ERIP_ID);
            echo createTextField($configurationWrapper, ConfigurationFields::LOGIN);
            echo createTextField($configurationWrapper, ConfigurationFields::PASSWORD);
            echo createCheckboxField($configurationWrapper, ConfigurationFields::SMS_NOTIFICATION);
            echo createCheckboxField($configurationWrapper, ConfigurationFields::EMAIL_NOTIFICATION);
            echo createTextField($configurationWrapper, ConfigurationFields::DUE_INTERVAL);
            echo createStatusSelectField($configurationWrapper, ConfigurationFields::BILL_STATUS_PENDING);
            echo createStatusSelectField($configurationWrapper, ConfigurationFields::BILL_STATUS_PAYED);
            echo createStatusSelectField($configurationWrapper, ConfigurationFields::BILL_STATUS_CANCELED);
            echo createStatusSelectField($configurationWrapper, ConfigurationFields::BILL_STATUS_FAILED);
            echo createCheckboxField($configurationWrapper, ConfigurationFields::ALFACLICK_BUTTON);
            echo createCheckboxField($configurationWrapper, ConfigurationFields::WEBPAY_BUTTON);
            ?>
        </table>
    </fieldset>
</div>
<div class="clr"></div>