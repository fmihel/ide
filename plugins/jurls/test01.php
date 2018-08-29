<?php

/*
https://ws-framework-fmihel.c9users.io/ide/plugins/jurls/test01.php
*/

require_once '../../../ws/utils/application.php';
$Application->LOG_ENABLE = true;
require_once UNIT('ws','ws.php');

RESOURCE('jurls.js');
RESOURCE('jurls.dcss');
RESOURCE('plugins','shadow/jshadow.js');

RESOURCE("../../style/ui_light/jquery-ui.js");
RESOURCE("../../style/ui_light/jquery-ui.css");

class Test extends WS{
        
   public function CONTENT(){
        $own = FRAME();
        FRAME('edit',$own)
        ->TAG_NAME('input')
        ->STYLE('width:400px;height:32px;left:50px;top:20px;position:absolute');
        
        FRAME()
        ->READY('
            jurls.create({
                pos:{x:50,y:80,w:400,h:250},
                onclick:function(o){
                    Qs.edit.val(o.url);                    
                    
                },
                data:[
                    {url:"www.yandex.ru"},
                    {url:"qwedqwde"},
                    {url:"www.decoinfo.ru"},
                    {url:"wedwedwedwe"},
                    {url:"www.rambler.ru"},
                    {url:"www.pikabu.ru"},
                    {url:"www.pikabu.ruasdsdc"},
                    {url:"www.pikabu.ru wedqwed"}
                    ]
            });
        ');    
        
        $y = 20;
        $dy= 38;
        
        FRAME('btn_add',$own)
        ->CSS('.btn{
            width:100px;
            height:32px;
            left:500px;
            position:absolute;
        }')
        ->CLASSES('btn')
        ->TAG_NAME('input')
        ->ATTR('type','button')
        ->STYLE("top:".$y."px")
        ->VALUE('add')
        ->EVENT('click','
            jurls.add(Qs.edit.val());
        ');
        
        $y+=$dy;
        FRAME('btn_get',$own)
        ->CLASSES('btn')
        ->TAG_NAME('input')
        ->ATTR('type','button')
        ->STYLE("top:".$y."px")
        ->VALUE('get')
        ->EVENT('click','
            var data = jurls.get_data();
            jurls.set_data(data);
        ');
        $y+=$dy;
        FRAME('btn_show',$own)
        ->CLASSES('btn')
        ->TAG_NAME('input')
        ->ATTR('type','button')
        ->STYLE("top:".$y."px")
        ->VALUE('show')
        ->EVENT('click','
            jurls.show(true);
        ');
  }
  
};      

if($Application->is_main(__FILE__)){
  $app = new Test();
  $app->RUN();
  //echo $Application->debug_info();
}
?>