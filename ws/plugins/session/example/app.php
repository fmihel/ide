<?php


    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL & ~E_NOTICE);
       

if(!isset($Application)){
    require_once '../../../../ws/utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = false; 
    
    require_once UNIT('ws','ws.php');
};

require_once UNIT('plugins','session/session_mod.php');
require_once UNIT('autorize.php');

RESOURCE('plugins','common/jhandler.js');
RESOURCE('plugins','common/jaction.js');

/** авторизация будет произведена после загрузки приложения */
session::init(array(
        'autorize'=>new Autorize(),
        'tokenName'=>'_token_',
        'relevanceInterval'=>2000,
        'sessionType'=>'session',
        'start'=>true
));


    
class TWS extends WS{
    
    public function CONTENT(){
        $own = FRAME('out',FRAME())
        ->READY('
            jaction.add(function(){
                
                var text = session.enable?"session start..":"..session stop";
                if (text != {$}.text())
                    {$}.text(text);
                
            });
        ');
    


    }
    
    public function AJAX(&$response){
        global $REQUEST;

        if ($REQUEST->ID=='test'){
            
            if (session::$enable)
                _LOG("session TRUE",__FILE__,__LINE__);
            else    
                _LOG("session no",__FILE__,__LINE__);
    
            $response = array("res"=>1);
            return true;
        }
        return false;
    }
    
  
}      

if($Application->is_main(__FILE__)){
  
    $app = new TWS();
    // ----------------------------------------------------------
    //  $app->version = ''; - custom clear cache 
    //  $app->version = 'nocache'; no caching data always
    //  $app->verison = 'XXXXX'; caching data (version control)
    $app->version = ''; 
    
    // ----------------------------------------------------------
    //  $app->title - browser tab name
    $app->title = ' app';

    // ----------------------------------------------------------
    //  $app->tabColor - in mobile Chrome tab color
    $app->tabColor = '#DCDCDC';
    
    $app->RUN();

}
?>
    