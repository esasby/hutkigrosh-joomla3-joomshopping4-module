<?php
/*
* @info     Платёжный модуль Hutkigrosh для JoomShopping
* @package  hutkigrosh
* @author   esas.by
* @license  GNU/GPL
*/

namespace esas\hutkigrosh\view\admin;

use esas\hutkigrosh\view\admin\fields\ConfigField;
use esas\hutkigrosh\view\admin\fields\ConfigFieldCheckbox;
use esas\hutkigrosh\view\admin\fields\ConfigFieldList;
use esas\hutkigrosh\view\admin\fields\ConfigFieldPassword;
use esas\hutkigrosh\view\admin\fields\ConfigFieldStatusList;
use esas\hutkigrosh\view\admin\fields\ConfigFieldTextarea;
use esas\hutkigrosh\view\admin\fields\ListOption;

defined('_JEXEC') or die();

class ConfigFormJoom extends ConfigFormHtml
{
    private $orderStatuses;

    /**
     * ConfigFormJoom constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $orders = \JModelLegacy::getInstance('orders', 'JshoppingModel');
        foreach ($orders->getAllOrderStatus() as $orderStatus) {
            $this->orderStatuses[] = new ListOption($orderStatus->status_id, $orderStatus->name);
        }
    }

    private static function addValidationError(ConfigField $configField)
    {
        $validationResult = $configField->getValidationResult();
        if ($validationResult != null && !$validationResult->isValid())
            return '<td class="alert alert-danger">' . $validationResult->getErrorTextSimple() . '</td>';
        else
            return "";
    }

    private static function addLabel(ConfigField $configField)
    {
        return '<td class="key" width="300" title="' . $configField->getDescription() . '">' . $configField->getName() . '</td>';
    }

    function generateTextField(ConfigField $configField)
    {
        return '<tr>'
            . self::addLabel($configField)
            . '<td>'
            . '<input type="text" name="pm_params[' . $configField->getKey() . ']" class="inputbox" value="' . $configField->getValue() . '" placeholder="' . $configField->getName() . '"/>'
            . '</td>'
            . '</tr>'
            . '<tr><td></td>' . self::addValidationError($configField) . '</tr>';
    }

    function generateTextAreaField(ConfigFieldTextarea $configField)
    {
        $editor = \JFactory::getEditor();
        return '<tr>'
            . self::addLabel($configField)
            . '<td>'
            . $editor->display("pm_params[" . $configField->getKey() . "]", $configField->getValue(), '100%', '350', '75', '20')
            .
            ' </td>'
            . '</tr>'
            . '<tr><td></td>' . self::addValidationError($configField) . '</tr>';
    }


    public function generatePasswordField(ConfigFieldPassword $configField)
    {
        return '<tr>'
            . self::addLabel($configField)
            . '<td>'
            . '<input type="password" name="pm_params[' . $configField->getKey() . ']" class="inputbox" value="' . $configField->getValue() . '" placeholder="' . $configField->getName() . '"/>'
            . '</td>'
            . '</tr>'
            . '<tr><td></td>' . self::addValidationError($configField) . '</tr>';
    }


    function generateCheckboxField(ConfigFieldCheckbox $configField)
    {
        return '<tr>'
            . self::addLabel($configField)
            . '<td>'
            . '<input type="checkbox" name="pm_params[' . $configField->getKey() . ']" class="inputbox" value="1" ' . ($configField->getValue() ? 'checked="checked"' : "") . '/>'
            . '</td>'
            . '</tr>';

    }

    function generateStatusListField(ConfigFieldStatusList $configField)
    {
        $configField->setOptions($this->orderStatuses);
        return $this->generateListField($configField);
    }

    function generateListField(ConfigFieldList $configField)
    {
        return '<tr>'
            . self::addLabel($configField)
            . '<td>'
            . '<select class="inputbox" 
                            id="input-' . $configField->getKey() . '" 
                            name="pm_params[' . $configField->getKey() . ']">' . $this->createOptions($configField) . '
                        </select>'
//            . JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[' . $configField->getKey() . ']', 'class="inputbox" size="1"', 'status_id', 'name', $configField->getValue())
            . '</td>'
            . '</tr>';
    }


    function createOptions(ConfigFieldList $configField)
    {
        $ret = "";
        foreach ($configField->getOptions() as $option) {
            if ($option->getValue() == $configField->getValue()) {
                $ret .= '<option value="' . $option->getValue() . '" selected="selected">' . $option->getName() . '</option>';
            } else {
                $ret .= '<option value="' . $option->getValue() . '">' . $option->getName() . '</option>';
            }
        }
        return $ret;
    }

    public function generate()
    {
        return '<div class="col100">
                    <fieldset class="adminform">
                        <table class="admintable" width="100%">' . parent::generate() . '</table>
                    </fieldset>
                </div>
                <div class="clr"></div>';
    }
}