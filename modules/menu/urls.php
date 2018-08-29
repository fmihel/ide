<?php

MODULES::ADD('URLS');
class URLS extends WS_MODULE{
   function __construct($owner){
        parent::__construct($owner);
        
        RESOURCE('plugins/jurls/jurls.js');
        RESOURCE('plugins/jurls/jurls.dcss');

   }    

    public function CONTENT(){
    
        FRAME()
        ->READY('
            jurls.create({
                own:Qs.modal,
                click:function(o){
                    /*Qs.url.val(o.url);        */
                    Qs.url.urlLight({url:o.url});
                },
                change:function(o){
                    Ws.user_data.urls = jurls.get_data();
                    Ws.user_data.url = Qs.url.urlLight("get","url");
                    
                    save_user_data();
                    console.info("jurls.change");
                }
            }).show(false);
            
            setTimeout(function(){
                if(Ws.user_data.urls)
                    jurls.set_data(Ws.user_data.urls);
                if(Ws.user_data.url){
                    Qs.url.urlLight({url:Ws.user_data.url});
                    /*Qs.url.val(Ws.user_data.url);*/
                }
            },1000);
            
            
        ')
        ->ALIGN('if (jurls.show()){
            var pos = JX.abs(Qs.url);
            var pbr = JX.pos(Qs.btn_run);
            var w = pos.w+pbr.w+JX.pos(Qs.btn_run_options).w;
            var x = pos.x-pbr.w;
            jurls.put({pos:{x:x,y:pos.y+pos.h+3,w:w}});
            jurls.align();
        }');

    }
    
};


?>