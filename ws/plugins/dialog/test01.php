<?php

/*

  
  https://ws-framework-fmihel.c9users.io/in_development/dialog/test01.php
  

*/


require_once '../../ide/ws/utils/application.php';
$Application->LOG_FILENAME = '../../ws.log';
$Application->LOG_ENABLE = true;

require_once UNIT('utils','dir.php');
require_once UNIT('ws','ws.php');

RESOURCE('jdialog.js');
RESOURCE('jdialog.css');

RESOURCE('plugins','common/ut.js');
RESOURCE('plugins','shadow/jshadow.js');

RESOURCE("../../ide/style/ui_light/jquery-ui.js");
RESOURCE("../../ide/style/ui_light/jquery-ui.css");



class MENU_TREE extends WS{

   public function CONTENT(){
     
    FRAME('create',FRAME())
    ->STYLE('
        position:absolute;
        left:10px;
        top:10px;
        border:1px dashed gray;
        width:100px;
        height:24px;
        line-height:24px;
        text-align:center;
    ')
    ->VALUE('create')
     
     ->EVENT('click','
        JDIALOG({
            caption:"New caption",
            msg:"My text in this global frame and <b>bold</b> text in this frame..",
            modal:false,
            onClick:function(o){
                console.info(o.id);
            }
        });
     ');
     
     
     FRAME('stick',FRAME())
     ->STYLE('position:absolute;
        left:100px;
        top:100px;
        border:1px solid red;
        width:100px;
        height:10px;
     ')->READY('{$}.draggable()');
     
      FRAME()
      ->CSS('body{
          font-family: Roboto, sans-serif;
          text-overflow: ellipsis;
          font-size:12px;
          
      }
      ')
      ->SCRIPT('');

  }
  
  public function AJAX(&$response){
    global $REQUEST;    
    return false;
  }

}      

if($Application->is_main(__FILE__)){
  $app = new MENU_TREE();
  $app->RUN();
  
}
?>