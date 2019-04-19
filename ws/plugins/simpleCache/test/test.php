<?php

if(!isset($Application)){
    require_once '../../../utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = false;
    
    require_once UNIT('ws','ws.php'); 
};

require_once '../simpleCache.php';
require_once '../drivers/simpleCacheBaseDriver.php';
require_once UNIT('plugins','base/base.php');

define ('SERVER_NAME2', WS_CONF::GET('deco_server'));
define ('SERVER_USER2', WS_CONF::GET('deco_user')); 
define ('SERVER_PASS2', WS_CONF::GET('deco_pass')); 
define ('BASE_NAME2',   WS_CONF::GET('deco_base'));    

base::connect(SERVER_NAME2,SERVER_USER2,SERVER_PASS2,BASE_NAME2,'deco');
base::charSet('cp1251','deco');


$buffer = new SimpleCache('SimpleCacheBaseDriver',array('timeout'=>60));

function test($arg1,$arg2,$arg3=''){
    global $buffer;
    
    $key = $buffer->toKey(__FUNCTION__,func_get_args());

    if ($data = $buffer->get($key)){
        echo 'read : '.count($data).'<hr>';
        return $data;
    }    

    $data = array();
    for($i=0;$i<$arg2;$i++)
        $data[] = $arg1.STR::random(10).$arg2;
        
    
   if ($buffer->set($key,$data,array('group'=>'alpha','notes'=>'func test ')))
        echo 'write : '.count($data).'<hr>';
    else
        echo base::error('deco');
        
}

if(true){
    //$buffer->reset();
    //$buffer->clear(array('group'=>'more'));
    $buffer->clear(array('where'=>' :group LIKE "price%"'));
    echo 'reset..';
}else{
    test(11,2);
}




?>