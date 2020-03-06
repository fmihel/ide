<?php

/**
 * Module:SESSION_MOD
 * for include to ws application use
 * require_once UNIT('trial/session/session_mod.php');
 * Модуль обработчик событий от session.js
 * 
*/

if(!isset($Application)){
    require_once '../../wsi/ide/ws/utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = true;
    
    require_once UNIT('ws','ws.php');
};

require_once UNIT(WARD(__DIR__,"session.php"));

MODULES::ADD('SESSION_MOD');
class SESSION_MOD extends WS_MODULE{

    function __construct($owner){
        parent::__construct($owner);
        
        RESOURCE(WARD(__DIR__,"session.js"));
        
        RESOURCE('plugins','common/local_storage.js');

        
    }
    public function CONTENT(){
        /*
        вызов session.auto перенсен в движок ws.php, так как требуется загружать контен только 
        после загрузки сессии, иначе запросы в момент когда сессия еще не инициализирована
        могут обнулить сессию. :(
        */
        FRAME()->SCRIPT('
            session.tokenName="'.session::$tokenName.'";
            session.type="'.session::$sessionType.'";
            /*session.auto();*/
        ');
        
    }
    
    public function AJAX_SESSION_INIT(&$response){
        global $REQUEST;
        
        if ($REQUEST->ID=='SESSION_INIT'){
            
            $token = $REQUEST->VALUE['token'];
            $dev   = $REQUEST->VALUE['dev'];
            
            //_LOG("SESSION_INIT",__FILE__,__LINE__);
    
            $response = session::start(array('token'=>$token,'dev'=>$dev));
            return true;
        }
        return false;
    }
    
    public function AJAX_SESSION_AUTORIZE(&$response){
        global $REQUEST;
        if ($REQUEST->ID=='SESSION_AUTORIZE'){
            
            $data = $REQUEST->VALUE['data'];
            $dev  = $REQUEST->VALUE['dev'];
            
            //_LOG("SESSION_AUTORIZE",__FILE__,__LINE__);
            
            $response = session::start(array('data'=>$data,'dev'=>$dev));
            return true;
        }
        return false;
    }
    
    public function AJAX_SESSION_RELEVANCE(&$response){
        global $REQUEST;
        if ($REQUEST->ID=='SESSION_RELEVANCE'){
            $token = $REQUEST->VALUE['token'];
            $dev   = $REQUEST->VALUE['dev'];
            
            //_LOG("SESSION_RELEVANCE",__FILE__,__LINE__);
            
            $response = session::check(array('token'=>$token,'dev'=>$dev));
            return true;
        }
        return false;
    }
    
    public function AJAX_SESSION_STOP(&$response){
        global $REQUEST;
        if ($REQUEST->ID=='SESSION_STOP'){
            $token = $REQUEST->VALUE['token'];
            $dev   = $REQUEST->VALUE['dev'];
            
            //_LOG("SESSION_STOP",__FILE__,__LINE__);
                
            $response = session::stop(array('token'=>$token,'dev'=>$dev));
            return true;
        }
        return false;
    }
    
    public function AJAX_SESSION_DATA_CHECK(&$response){
        global $REQUEST;
        if ($REQUEST->ID=='SESSION_DATA_CHECK'){
            $data = $REQUEST->VALUE['data'];
            $dev   = $REQUEST->VALUE['dev'];
            
            $response = session::check(array('data'=>$data,'dev'=>$dev));
            return true;
        }
        return false;
    }
        

    public function AJAX(&$response){

        if ($this->AJAX_SESSION_INIT($response)) return true;
        if ($this->AJAX_SESSION_AUTORIZE($response)) return true;
        if ($this->AJAX_SESSION_RELEVANCE($response)) return true;
        if ($this->AJAX_SESSION_STOP($response)) return true;
        if ($this->AJAX_SESSION_DATA_CHECK($response)) return true;
        
        return false;
    }
};



if($Application->is_main(__FILE__)){
  
    echo 'Module SESSION_MOD: Ok.';
    
}
?>