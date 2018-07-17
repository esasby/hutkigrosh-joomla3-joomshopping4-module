<?php

require_once(JPATH_SITE . '/components/com_jshopping/payments/pm_hg/vendor/autoload.php');

/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 27.06.2018
 * Time: 16:18
 */
class SimpleAutoloader
{
    static public function loader($class)
    {
        $className = str_replace("\\", DIRECTORY_SEPARATOR, $class);
        $path = JPATH_SITE . '/components/com_jshopping/payments/pm_hg/' . $className . '.php';
        if (file_exists($path)) {
            require_once($path);
            if (class_exists($class)) {
                return TRUE;
            }
        }
        return FALSE;
    }
}

spl_autoload_register('SimpleAutoloader::loader');

Logger::configure(array(
    'rootLogger' => array(
        'appenders' => array('fileAppender'),
        'level' => 'INFO',
    ),
    'appenders' => array(
        'fileAppender' => array(
            'class' => 'LoggerAppenderFile',
            'layout' => array(
                'class' => 'LoggerLayoutPattern',
                'params' => array(
                    'conversionPattern' => '%date{Y-m-d H:i:s,u} | %logger{0} | %-5level | %msg %n%throwable',
                )
            ),
            'params' => array(
                'file' => JPATH_SITE . '/hutkigrosh.log',
                'append' => true
            )
        )
    )
));