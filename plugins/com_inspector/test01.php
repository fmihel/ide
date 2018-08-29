<?php

/*
https://ws-framework-fmihel.c9users.io/ide/plugins/com_inspector/test01.php
*/

require_once '../../../ws/utils/application.php';
$Application->LOG_ENABLE = true;

require_once UNIT('ws','ws.php');

RESOURCE("../../style/ui_light/jquery-ui.js");
RESOURCE("../../style/ui_light/jquery-ui.css");

RESOURCE('com_inspector.js');
RESOURCE('com_inspector.dcss');
RESOURCE('plugins','shadow/jshadow.js');



class CI extends WS{
        
   public function CONTENT(){
        FRAME()
        ->VALUE('ok')
        ->SCRIPT('var com;')
        ->READY('com = new comin()');
    
    FRAME('btn',FRAME())
    ->STYLE('border:1px solid gray;width:100px;height:32px;line-height:32px;text-align:center')
    ->VALUE('press')
    ->EVENT('click','
        com.add({
            name:"x",
            value:"my value"
        })    
    ');

    FRAME('btn2',FRAME())
    ->STYLE('border:1px solid gray;width:100px;height:32px;line-height:32px;text-align:center')
    ->VALUE('show')
    ->EVENT('click','
        com.show(true);    
    ');

  }
  

}      

if($Application->is_main(__FILE__)){
  $app = new CI();
  $app->RUN();
}
?>