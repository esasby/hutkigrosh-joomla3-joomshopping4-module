<?php
/*
* @info     Платёжный модуль Hutkigrosh для JoomShopping
* @package  hutkigrosh
* @author   esas.by
* @license  GNU/GPL
*/

defined('_JEXEC') or die;
require_once(dirname(__FILE__) . '/vendor/esas/hutkigrosh-api-php/src/esas/hutkigrosh/CmsPlugin.php');
use esas\hutkigrosh\CmsPlugin;
use esas\hutkigrosh\RegistryJoom;


(new CmsPlugin(dirname(__FILE__) . '/vendor', dirname(__FILE__)))
    ->setRegistry(new RegistryJoom())
    ->init();
