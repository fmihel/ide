<?php
/*
https://ws-framework-fmihel.c9users.io/ide/plugins/jhtab/test01.php
*/
require_once '../../../ws/utils/application.php';
$Application->LOG_ENABLE = true;

require_once UNIT('ws','ws.php');

RESOURCE('jhtab.js');
RESOURCE('jhtab.css');



class WSI extends WS{
        
  public function CONTENT(){
   
   FRAME('frame',FRAME())
    ->STYLE('left:250px;top:50px;width:500px;height:100px;position:absolute;overflow:hidden')
    ->SCRIPT('var tab;')
    ->READY('
        var item = false;
        tab=new jhtab({own:{$}});
        item = tab.add({caption:"log",height:110,id:"one"});
        item.btn_panel.text("....");
        item = tab.add({caption:"find",height:80,id:"two"});
        item.btn_panel.text("....");
    
    ');
        //tab=new jhtab({own:{$}});
            //tab.add({caption:"log",height:110,id:"one"});
            //tab.add({caption:"Outline",height:80,id:"two"});
            //tab.add({caption:"Debugger",id:"three"});

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