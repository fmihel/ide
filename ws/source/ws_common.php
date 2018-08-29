<?php

if(!isset($Application)) 
    require_once '../utils/application.php';

class WS_COMMON{
    
    function __construct(){
        
    }
    
    public function debug_info($cr='<br>'){
        //S: Вывод отладочной информации
        return 'WS_COMMON: ok'.$cr;
        
    }
};

class WS_CONTENT extends WS_COMMON{
    public function CONTENT(){ }
    public function AJAX(&$response){ return false;}
}


if ($Application->is_main(__FILE__)){
    echo $Application->debug_info();    
    echo '<hr>debug:'.__FILE__.'<br>';            
                
}
?>