<?php
/*

https://ws-framework-fmihel.c9users.io/ide/ws/plugins/url_lighter/test.php

*/
if(!isset($Application)){
    require_once '../../utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = true;

};

require_once UNIT('ws','ws.php');
RESOURCE('url_lighter.js');
RESOURCE('url_lighter_define.dcss');
RESOURCE('url_lighter.dcss');

class TWS extends WS{
        

    public function CONTENT(){
        FRAME('to_url',FRAME())
        ->STYLE('width:80%;height:36px;border:1px solid gray;position:absolute')
        ->INIT('{$}.urlLight({url:"https://windeco.online/path1/path2/file.php?param1=738&param2=9839&param3=kwje"});');
        
    }
  

}      

if($Application->is_main(__FILE__)){
  
    $app = new TWS(); 
    $app->RUN();
    

}
?>
    