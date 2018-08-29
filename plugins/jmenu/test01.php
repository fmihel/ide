<?php
/*
https://ws-framework-fmihel.c9users.io/ide/plugins/jmenu/test01.php
*/

require_once '../../../ws/utils/application.php';
require_once UNIT('ws','ws.php');

RESOURCE('jmenu.js');
RESOURCE('plugins','shadow/jshadow.js');

RESOURCE("../../style/ui_light/jquery-ui.js");
RESOURCE("../../style/ui_light/jquery-ui.css");

class TEST extends WS{

   public function CONTENT(){
        $wp = FRAME::ADD('workplace')
            ->STYLE('position:absolute;margin:0px;padding:0p;display:none')
            ->READY('{$}.hide().fadeIn(1000,function(){Ws.align();})');
        
        $modal=FRAME::ADD('modal')
            ->STYLE('left:0px;top:0px;position:absolute;width:0px;height:0px');
    
        $page = FRAME::ADD('page',$wp)
            ->STYLE('border:0px dashed blue;position:absolute')
            ->ALIGN('JX.workplace(Qs.workplace,Qs.page);')
            ->READY('');        
            
        FRAME::ADD('menu',$modal)
            ->STYLE('border:0px dashed blue;position:absolute;left:100px;top:100px;height:0px');

        FRAME('btn',$page)->TAG_NAME('input')->ATTR('type','button')->STYLE('position:absolute')
            ->VALUE('menu')
            ->SCRIPT('
            var menu=null;
            ')
            ->CSS('
            .test{
                min-height:24px;
                line-height:24px;
                min-width:100px;
            }')
            ->READY('
            menu = new JMENU({
                own:Qs.menu,
                width:200,
                css:{item:"test"},
                items:[
                    {caption:"Item1",id:1,enable:true},
                    {caption:"Item2",id:2,child:[
                            {caption:"Item6",id:6},    
                            {caption:"Item7",id:7,child:[
                                {caption:"Item9",id:9},
                                {caption:"Item10",id:10}
                            ]},
                            {caption:"Item8",id:8},
                    ]},
                    {caption:"Item3",id:3,child:[
                            {caption:"Item4",id:4},    
                            {caption:"Item5",id:5}
                    ]}
                ],
                click:function(o){
                    menu.show(false);
                    console.info(o.id);
                }
            });
            
            menu.enable("8",false);
            
            {$}.on("click",function(){
                menu.show(!menu.show());
                return false;
            });
            ');
    }
  

}      

if($Application->is_main(__FILE__)){
  $app = new TEST();
  $app->RUN();
}
?>