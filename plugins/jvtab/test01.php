<?php
require_once '../../../ws/utils/application.php';
$Application->LOG_ENABLE = true;

require_once UNIT('ws','ws.php');

RESOURCE('jvtab.js');
RESOURCE('jvtab.css');



class WSI extends WS{
        
  public function CONTENT(){
   
   FRAME('frame',FRAME())
    ->STYLE('left:250px;top:50px;width:200px;height:400px;position:absolute;overflow:hidden')
    ->SCRIPT('var tab;')
    ->READY('
        tab=new jvtab({own:{$}});
            tab.add({caption:"Collaborate",height:110,id:"one"});
            tab.add({caption:"Outline",height:80,id:"two"});
            tab.add({caption:"Debugger",id:"three"});
    ');

   FRAME('btn',FRAME())
    ->TAG_NAME('input')
    ->ATTR('type','button')
    ->VALUE('add')
    ->SCRIPT('var tab;')
    ->READY('
        {$}.on("click",function(){
            tab.add({caption:"TEMPLATE"});
        });
    ');
   
  }
  
  public function AJAX(&$response){
      
      
      
      return false;
  }

}      

if($Application->is_main(__FILE__)){
  $app = new WSI();
  $app->RUN();
}
?>