## Модуль интеграции с CMS Joomla 3.x (с корзиной Joomshopping 4.x)

Данный модуль обеспечивает взаимодействие между интернет-магазином на базе CMS Joomla и сервисом платежей [ХуткiГрош](https://hutkigrosh.by)


### Внимание ### 
Данная библиотека больше не поддерживается. Вся текущая разработка перенесена в проект [cmsgate-joomshopping-hutkigrosh](https://bitbucket.esas.by/projects/CG/repos/cmsgate-joomshopping-hutkigrosh/browse)
  
### Инструкция по установке:
1. Создайте резервную копию вашего магазина и базы данных
1. Загрузите архив с модулем [plg_jshopping_hutkigrosh.zip](https://github.com/esasby/hutkigrosh-joomla3-joomshopping4-module/blob/master/plg_jshopping_hutkigrosh.zip)
1. В административной части Joomla выберите `Расширения - Менеджер расширений - Установка - Загрузить Файл пакета`. Выберите архив и нажмите `Загрузить`.
1. Перейдите в меню `Компоненты — JoomShopping - Опции - Способы оплаты`.
1. Выберите Hutkigrosh, перейдите на вкладку "Конфигурация"
1. Задайте параметры для модуля
    * Логин интернет-магазина – логин в системе ХуткiГрош.
    * Пароль интернет-магазина – пароль в системе ХуткiГрош.
    * Уникальный идентификатор услуги ЕРИП – ID ЕРИП услуги
    * Код услуги – код услуги в деревер ЕРИП. Используется при генерации QR-кода
    * Sandbox - перевод модуля в тестовый режим работы. В этом режиме счета выставляются в тестовую систему wwww.trial.hgrosh.by
    * Email оповещение - включить информирование клиента по email при успешном выставлении счета (выполняется шлюзом Хуткiгрош)
    * Sms оповещение - включить информирование клиента по смс при успешном выставлении счета (выполняется шлюзом Хуткiгрош)
    * Путь в дереве ЕРИП - путь для оплаты счета в дереве ЕРИП, который будет показан клиенту после оформления заказа (например, Платежи > Магазин > Заказы)
    * Срок действия счета - как долго счет, будет доступен в ЕРИП для оплаты    
    * Статус при выставлении счета  - какой статус выставить заказу при успешном выставлении счета в ЕРИП (идентификатор существующего статуса из Магазин > Настройки > Статусы)
    * Статус при успешной оплате счета - какой статус выставить заказу при успешной оплате выставленного счета (идентификатор существующего статуса)
    * Статус при отмене оплаты счета - какой статус выставить заказу при отмене оплаты счета (идентификатор существующего статуса)
    * Статус при ошибке оплаты счета - какой статус выставить заказу при ошибке выставленния счета (идентификатор существующего статуса)
    * Секция "Инструкция" - если включена, то на итоговом экране клиенту будет доступна пошаговая инструкция по оплате счета в ЕРИП
    * Секция QR-code - если включена, то на итоговом экране клиенту будет доступна оплата счета по QR-коду
    * Секция Alfaclick - если включена, то на итоговом экране клиенту отобразится кнопка для выставления счета в Alfaclick
    * Секция Webpay - если включена, то на итоговом экране клиенту отобразится кнопка для оплаты счета картой (переход на Webpay)
    * Текст успешного выставления счета - текст, отображаемый кленту после успешного выставления счета. Может содержать html. В тексте допустимо ссылаться на переменные @order_id, @order_number, @order_total, @order_currency, @order_fullname, @order_phone, @order_address        
1. Сохраните изменения.


### Внимание!
* Для автоматического обновления статуса заказа (после оплаты клиентом выставленного в ЕРИП счета) необходимо сообщить в службу технической поддержки сервиса «Хуткi Грош» адрес обработчика следующим образом (где будет указан ваш домен):
```
http://mydomen.my/index.php?option=com_jshopping&controller=hutkigrosh&task=notify
```
* Модуль ведет лог файл по пути _site_root/components/com_jshopping/payments/pm_hg/vendor/esas/hutkigrosh-api-php/logs/hutkigrosh.log_
Для обеспечения **безопасности** необходимо убедиться, что в настройках http-сервера включена директива _AllowOverride All_ для корневой папки.

### Тестовые данные
Для настрой оплаты в тестовом режиме:
 * воспользуйтесь данными для подключения к тестовой системе, полученными при регистрации в ХуткiГрош
 * включите в настройках модуля "Режим песочницы" 
 * для эмуляции оплаты клиентом выставленного счета воспльзуйтесь личным кабинетом [тестовой системы](https://trial.hgrosh.by) (меню _Тест оплаты ЕРИП_)

_Разработано и протестировано с Joomla v.3.8.3 + Joomshopping v.4.16.3_
