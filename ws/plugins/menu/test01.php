<?php
/*

  
  https://ws-framework-fmihel.c9users.io/ws/plugins/menu/test01.php
  

*/


require_once '../../utils/application.php';

require_once UNIT('utils','dir.php');
require_once UNIT('ws','ws.php');

RESOURCE('jmenu.js');
RESOURCE('jmenu.css');

RESOURCE('plugins','common/thread.js');
RESOURCE('plugins','splitter/splitter.js');
RESOURCE('plugins','common/ut.js');
RESOURCE('plugins','common/local_storage.js');
RESOURCE('plugins','shadow/jshadow.js');

RESOURCE("../../../style/ui_light/jquery-ui.js");
RESOURCE("../../../style/ui_light/jquery-ui.css");



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
        menu.show();
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
      ->SCRIPT('var menu;')
      ->READY('
        
        menu = new jmenu({
        stick:Qs.stick,
        data:[
          "itemZ",
          "-",
            {caption:"item2  ",enable:true,
              children:[
                "item7",
                
                {caption:"item8..",children:["item13","item14","item15"]},
                {caption:"item9..",children:["item10","item11","item12"]},
                {caption:"item7",enable:false},
                "item7",
                "item7",
                "item7",
                {caption:"item9..",children:["item10","item11",
                    {caption:"item8283",children:["wejwe","qwekdjqwlkd","qwdwelkj"]}
                ]},
                "item7",
              ]
            },
            {caption:"item3..",children:["item4","item5","item6"]}
        ]
          
        });
        menu.show();


      ')->SCRIPT('

        ');

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