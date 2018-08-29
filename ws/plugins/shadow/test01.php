<?php
/*
https://ws-framework-fmihel.c9users.io/ws/plugins/shadow/test01.php
*/

require_once '../../utils/application.php';
require_once UNIT('ws','ws.php');

RESOURCE('plugins','shadow/jshadow.js');

class TEST extends WS{

   public function CONTENT(){
        $wp = FRAME::ADD('workplace')
            ->STYLE('position:absolute;margin:0px;padding:0p;display:none')
            ->READY('{$}.hide().fadeIn(1000,function(){Ws.align();})');
        
        FRAME::ADD('modal')
            ->STYLE('left:0px;top:0px;position:absolute;width:0px;height:0px');
    
        $page = FRAME::ADD('page',$wp)
            ->STYLE('border:0px dashed blue;position:absolute')
            ->ALIGN('JX.workplace(Qs.workplace,Qs.page);')
            ->READY('');        
        
        FRAME('btn',$page)->TAG_NAME('input')->ATTR('type','button')->STYLE('position:absolute')
            ->VALUE('shadow')
            ->READY('

            {$}.on("click",function(){
                {$}.jshadow({
                    show:true,
                    click:function(e){
                        e.jshadow("hide");
                    },
                    onhide:function(){
                      console.info("hide");  
                    },
                    onshow:function(){
                      console.info("show");  
                    }
                });
            
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