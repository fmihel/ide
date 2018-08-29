<?php
/*
https://ws-framework-fmihel.c9users.io/ide/plugins/jfunc_hint/test.php
*/
require_once '../../ws/utils/application.php';
$Application->LOG_ENABLE = true;

require_once UNIT('ws','ws.php');

RESOURCE('jfunc_hint.js');
RESOURCE('jfunc_hint.dcss');


class WSI extends WS{
        
  public function CONTENT(){
   
   FRAME('frame',FRAME())
    ->STYLE('left:250px;top:50px;width:500px;height:100px;position:absolute;overflow:hidden')
    ->CSS('body{
        background-color:#626262;
    }')
    ->SCRIPT('var tab;')
    ->READY('
    ');

   FRAME('btn',FRAME())
    ->TAG_NAME('input')
    ->ATTR('type','button')
    ->VALUE('test..')
    ->STYLE(' background-color:#626262;border:1px solid black;  ')
    ->SCRIPT('var tab;')
    ->READY('jfunc_hint.init({stick:Qs.stick});')
    ->EVENT('click','jfunc_hint_test();');
  
  
    FRAME('stick',FRAME())
      ->STYLE('border:1px dashed black;position:absolute;width:400px;height:400px;left:100px;top:100px');
  
  }
  
  
  
}      

if($Application->is_main(__FILE__)){
  $app = new WSI();
  $app->RUN();
}
?>