<?php
/*
    https://ws-framework-fmihel.c9users.io/ide/ws/plugins/form/example.php
*/

if(!isset($Application)){
    require_once '../../../ws/utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = true;

};

require_once UNIT('ws','ws.php');

RESOURCE('plugins','common/jlock.js');
RESOURCE('plugins','common/jhandler.js');

RESOURCE('plugins','shadow/jshadow.js');
RESOURCE('plugins','form/form.dcss');
RESOURCE('plugins','form/form.js');

RESOURCE('plugins','mbtn/mbtn.dcss');
RESOURCE('plugins','mbtn/mbtn.js');

class TWS extends WS{
        
    public function ONLOAD(){
        global $REQUEST;

        if ($REQUEST->IsAjax()){
            
        }else{
        
        };  
      
        return true;
    }
    private function css(){
        FRAME('page')->CSS('
            .btn{
                width:90px;
                height:40px;
                line-height:40px;
                text-align:center;
                position:absolute;
                border:1px solid silver;
                cursor:pointer;
                
            }
            .label{
                width:70px;
                height:32px;
                border-bottom:1px solid silver;
                position:absolute;
            }
            .input{
                width:60px;
                height:24px;
                output:none;
                border:1px solid #3A8BD0;
                background:white;
                text-align:center;
                position:absolute;
            }
            
            .hint{
                position:absolute;
                width:200px;
            }
            ');
    }
    private function layout(){
        $body = FRAME()
        ->STYLE('                
            overflow-x:hidden;
            overflow-y:hidden;
            -webkit-font-smoothing:antialiased;
            
            margin:0px;
            padding:0px;
            
            font-family: Roboto, sans-serif;
            text-overflow: ellipsis;
            font-size:12px;
            
        ')
        ->ALIGN('
        JX.workplace(Qs.workplace,Qs.page);
        
        ');
        
        $wp = FRAME('workplace',$body)
            ->STYLE('position:absolute;margin:0px;padding:0p;display:none')
            ->READY('{$}.hide().fadeIn(500,function(){Ws.align();})');
            
        FRAME('modal',FRAME())
            ->STYLE('left:0px;top:0px;position:absolute;width:0px;height:0px;z-index:1000');
        
        $page = FRAME('page',$wp)
        ->STYLE('border:0px;position:absolute')
        ->ALIGN('
            JX.tile({$}.children(),{
                count:6,
                align:{
                    vert:"top",
                    horiz:"center"
                },
                margin:{top:50},
                gap:5
            });
        ');
        /*
        $menu = FRAME('menuFrame',$page)->STYLE('position:absolute;width:300px;border:1px solid silver;overflow-x:none;overflow-y:auto')
        ->ALIGN('
            JX.arrange([{$}],{direct:"vert",type:"stretch",align:"left",margin:5});
        ')->ALIGN('
            JX.tile({$}.children(),{
                count:2,
                align:{
                    vert:"top",
                    horiz:"center"
                },
                margin:{top:50},
                gap:5
            });
        ');
        */

    }
    private function menu(){
        $own = FRAME('page');
        
        /* ------------------------------------------------------- */
        $hint = 0;
        FRAME('open',$own)
        ->VALUE('open')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            my.mform("open");    
        }});');
        /* ------------------------------------------------------- */
        FRAME('close',$own)
        ->VALUE('close')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(b){
            my.mform("close");
        }});');

        /* ------------------------------------------------------- */
        FRAME('set_modal',$own)
        ->VALUE('modal')
        ->CLASSES('btn')->INIT('
        {$}.mbtn({
            click:function(b){
                my.mform({modal:!my.mform("modal")});
                b.active(my.mform("modal"));
            }
        });')
        ->READY('{$}.mbtn("active",my.mform("modal"))');
        /* ------------------------------------------------------- */
        FRAME('showHeader',$own)
        ->VALUE('showHeader')
        ->CLASSES('btn')->INIT('
        {$}.mbtn({
            click:function(b){
                my.mform({showHeader:!my.mform("showHeader")});
                b.active(my.mform("showHeader"));
            }
        });')
        ->READY('{$}.mbtn("active",my.mform("showHeader"))');
        /* ------------------------------------------------------- */
        FRAME('showFooter',$own)
        ->VALUE('showFooter')
        ->CLASSES('btn')->INIT('
        {$}.mbtn({
            click:function(b){
                my.mform({showFooter:!my.mform("showFooter")});
                b.active(my.mform("showFooter"));
            }
        });')
        ->READY('{$}.mbtn("active",my.mform("showFooter"))');
        /* ------------------------------------------------------- */
        FRAME('needCloseBtn',$own)
        ->VALUE('needCloseBtn')
        ->CLASSES('btn')->INIT('
        {$}.mbtn({
            click:function(b){
                my.mform({needCloseBtn:!my.mform("needCloseBtn")});
                b.active(my.mform("needCloseBtn"));
            }
        });')
        ->READY('{$}.mbtn("active",my.mform("needCloseBtn"))');
        /* ------------------------------------------------------- */
        FRAME('shadowAsClose',$own)
        ->VALUE('shadowAsClose')
        ->CLASSES('btn')->INIT('
        {$}.mbtn({
            click:function(b){
                my.mform({shadowAsClose:!my.mform("shadowAsClose")});
                b.active(my.mform("shadowAsClose"));
            }
        });')
        ->READY('{$}.mbtn("active",my.mform("shadowAsClose"))');
        /* ------------------------------------------------------- */
        FRAME('placeCloseOnTopRight',$own)
        ->VALUE('plcClsOnTpRgt')
        ->CLASSES('btn')->INIT('
        {$}.mbtn({
            click:function(b){
                my.mform({placeCloseOnTopRight:!my.mform("placeCloseOnTopRight")});
                b.active(my.mform("placeCloseOnTopRight"));
            }
        });')
        ->READY('{$}.mbtn("active",my.mform("placeCloseOnTopRight"))');
        /* ------------------------------------------------------- */
        FRAME('align',$own)
        ->VALUE('align')
        ->CLASSES('btn')->INIT('
        {$}.mbtn({click:function(){
            
            my.mform("align");
            
        }});');
        FRAME('fullscreen',$own)
        ->VALUE('fullscreen')
        ->CLASSES('btn')->INIT('
        {$}.mbtn({
            click:function(b){
                my.mform({fullscreen:!my.mform("fullscreen")});
                b.active(my.mform("fullscreen"));
            }
        });')
        ->READY('{$}.mbtn("active",my.mform("fullscreen"))');
        /* ------------------------------------------------------- */
        FRAME('draggable',$own)
        ->VALUE('draggable')
        ->CLASSES('btn')->INIT('
        {$}.mbtn({
            click:function(b){
                my.mform({draggable:!my.mform("draggable")});
                b.active(my.mform("draggable"));
            }
        });')
        ->READY('{$}.mbtn("active",my.mform("draggable"))');
        /* ------------------------------------------------------- */
        FRAME('resizable',$own)
        ->VALUE('resizable')
        ->CLASSES('btn')->INIT('
        {$}.mbtn({
            click:function(b){
                my.mform({resizable:!my.mform("resizable")});
                b.active(my.mform("resizable"));
            }
        });')
        ->READY('{$}.mbtn("active",my.mform("resizable"))');
        /* ------------------------------------------------------- */

        FRAME('btnPlaceLeft',$own)
        ->VALUE('btnPlaceLeft')
        ->CLASSES('btn')
        ->INIT('{$}.mbtn({click:function(b){btnPlace("left");}});');

        FRAME('btnPlaceCenter',$own)
        ->VALUE('btnPlaceCenter')
        ->CLASSES('btn')
        ->INIT('{$}.mbtn({click:function(b){btnPlace("center");}});');


        FRAME('btnPlaceRight',$own)
        ->VALUE('btnPlaceRight')
        ->CLASSES('btn')
        ->SCRIPT('
        function btnPlace(to){
            Qs.btnPlaceRight.mbtn("active",to==="right");
            Qs.btnPlaceCenter.mbtn("active",to==="center");
            Qs.btnPlaceLeft.mbtn("active",to==="left");
            my.mform({buttonPlace:to});
        }')
        ->INIT('{$}.mbtn({click:function(b){btnPlace("right");}});')
        ->READY('btnPlace(my.mform("buttonPlace"))');

        /* ------------------------------------------------------- */        
        FRAME('btnStretchCustom',$own)
        ->VALUE('stretchCustom')
        ->CLASSES('btn')
        ->INIT('{$}.mbtn({click:function(b){stretch("custom");}});');

        FRAME('btnStretchHoriz',$own)
        ->VALUE('stretchHoriz')
        ->CLASSES('btn')
        ->INIT('{$}.mbtn({click:function(b){stretch("horiz");}});');


        FRAME('btnStretchFullscreen',$own)
        ->VALUE('stretchFullscreen') 
        ->CLASSES('btn')
        ->SCRIPT('
        function stretch(to){
            Qs.btnStretchCustom.mbtn("active",to==="custom");
            Qs.btnStretchHoriz.mbtn("active",to==="horiz");
            Qs.btnStretchFullscreen.mbtn("active",to==="fullscreen");
            my.mform({stretch:to});
        }')
        ->INIT('{$}.mbtn({click:function(b){stretch("fullscreen");}});')
        ->READY('stretch(my.mform("stretch"))'); 

        /* ------------------------------------------------------- */        
        FRAME('typeAlign',$own)
        ->VALUE('typeAlign')
        ->CLASSES('btn')->INIT('
        {$}.mbtn({
            click:function(b){
                my.mform({position:{type:"align"}});
                b.active(true);
                Qs.typeCustom.mbtn("active",false);
            }
        });')
        ->READY('{$}.mbtn("active",my.mform("position").type==="align")');
        
        FRAME('typeCustom',$own)
        ->VALUE('typeCustom')
        ->CLASSES('btn')->INIT('
        {$}.mbtn({
            click:function(b){
                my.mform({position:{type:"custom"}});
                b.active(true);
                Qs.typeAlign.mbtn("active",false);
            }
        });')
        ->READY('{$}.mbtn("active",my.mform("position").type==="custom")');
        

        FRAME('position',$own)
        ->VALUE('position')
        ->CLASSES('btn')->INIT('
        {$}.mbtn({
            click:function(b){
                my.mform({position:{x:ut.random(100,200),y:ut.random(100,200)}});
            }
        });');

        FRAME('size',$own)
        ->VALUE('size')
        ->CLASSES('btn')->INIT('
        {$}.mbtn({
            click:function(b){
                my.mform({width:ut.random(200,300),height:ut.random(200,300)});
            }
        });');

        FRAME('keep',$own)
        ->VALUE('keep')
        ->CLASSES('btn')->INIT('
        {$}.mbtn({
            click:function(b){
                var keep = !my.mform("position").keep;
                my.mform({position:{keep:keep}});
                b.active(keep);
            }
        });')
        ->READY('{$}.mbtn("active",my.mform("position").keep)');

        /* ------------------------------------------------------- */

    }
    
    private function fill_form(){
        $own = FRAME('my');

        $count = rand(10,100);
        
        for($i=0;$i<$count;$i++){
            $type = rand(1,3);
            switch ($type){
                case 1:FRAME('lbl'.$i,$own)->CLASSES('label')->VALUE('label'.$i);break;
                case 2:FRAME('inp'.$i,$own)->TAG_NAME('input')->ATTR('type','text')->CLASSES('input')->VALUE('input'.$i);break;
                case 3:FRAME('btn'.$i,$own)->CLASSES('btn')->VALUE('btn'.$i)->INIT('{$}.mbtn()');break;
            }    
        }
        
    }
    private function form(){
        
        $form = FRAME('my',FRAME('page'))
        ->STYLE('width:400px;height:400px;overflow:auto')
        ->SCRIPT('var my;')
        ->INIT('
        {$}.mform({
            modal:false,
            caption:"mform v1.1",
            onOpen:function(o){
                console.info("onOpen",o);
                JX.tile(Qs.my.children(),{
                    gap:5,
                    align:{   
                            vert:"top", 
                            horiz:"left",
                    },                    
                    inside:{ 
                        vert:"center",
                        horiz:"left"
                    },                    
                });
            },
            onClose:function(o){
                console.info("onClose",o);
            },
            onAlign:function(){
            },
            buttons:{
                ok:{
                    caption:"ok",
                    click:function(o){
                        console.info("ok",o);       
                    }
                },
                close:{
                    caption:"cancel",
                    click:function(o){
                        console.info("cancel",o);       
                        o.sender.close();
                    }
                }                
            }
        });
        my = {$};
        ');
        
     $this->fill_form();   
    }
    public function CONTENT(){
        $this->layout();
        $this->css();
        $this->menu();
        $this->form();
        
    }
  
    public function AJAX(&$response){
        global $REQUEST;
      
        return false;
    }

}      

if($Application->is_main(__FILE__)){
  
    $app = new TWS(); 
    $app->RUN();
    

}
?>
    