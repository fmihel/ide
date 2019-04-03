<?php
/** 
 * Пример запуска скрипта с предварительной авторизацией 
 * Авторизация будет проведена до запуска скрипта.
 * Для запуска используем :
 * 
 * 
*/


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
    
if(!isset($Application)){

    require_once '../../../../ws/utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = false; 
    
};

    
require_once UNIT("plugins",'session/session.php');
require_once UNIT(WARD(__DIR__,'autorize.php'));
    
/*---------------------------------------------------------------------------------------------------*/
/** авторизация из параметров в href запросе, не использует модуль и включается до загрузки контента*/
session::init(array(
        'autorize'=>new Autorize(),
        'tokenName'=>'_token_',
        'start'=>false
));

session::start($_REQUEST);
/*---------------------------------------------------------------------------------------------------*/


if (session::$enable){
    echo 'session ok!';
}else {
    echo "no";
}


?>