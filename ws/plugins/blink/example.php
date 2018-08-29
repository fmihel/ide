<?php
if(!isset($Application)){
    require_once '../../../ws/utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = false; 
    
    require_once UNIT('ws','ws.php');
};

RESOURCE('http://windeco.su/wsi/ide/style/ui_light/jquery-ui.min.js');
RESOURCE('http://windeco.su/wsi/ide/style/ui_light/jquery-ui.css');
require_once UNIT('utils','framet.php');
RESOURCE('plugins','blink/blink.js');


class TWS extends WS{
    public function CONTENT(){
        FRAMET('
            
            <test1 {left:300px;top:50px;width:200px;height:200px;background:#BCD2F7} |test1| "box"
            
            > 
            <text {left:550px;top:50px;color:#CA8030;border:1px solid gray;height:32px;line-height:32px;background:silver;text-align:center} |click on text|>
            
            <tab:table "box" {width:500px;height:60px;top:300px;left:50px}
                <r1:tr
                    <c1:td "col" |col1| >
                    <c2:td "col" |col2|>
                >
                <r2:tr {background:green}
                    <c3:td "col" |col3| >
                    <c4:td "col" |col4|>
                >
                <r3:tr
                    <c5:td "col" |col5|>
                    <c6:td "col" |col6|>
                >
            >
        
        ',FRAME())->CSS('
        .box{
            border:1px solid gray;
            cursor:pointer;
            background:white;
        }
        .col{
            border:1px solid gray;
            cursor:pointer;
        }
        ')->SCRIPT('
        function done(to){
            console.info("done");
        }
        
        ');
        
        FRAME('test1')
        ->READY('{$}.draggable();')
        ->EVENT('click','blink({$},{
            done:done,
            type:"restory",
            opacity:0,
            scale:10,
        })');
        
        FRAME('tab')
        ->READY('
        {$}.draggable();
        {$}.find("tr").on("click",function(o){
            console.log(o);
            blink($(o.currentTarget),{
                done:done,
                opacity:0,
                type:"opacity",
                scale:20,
                animate:{
                    "background-color":"red",
                    "font-size":"1.2em",
                    
                }
                
            })                
        });
        ');
        
        
        FRAME('text')
        ->EVENT('click','
            blink({$},{
                done:done,
                opacity:0,
                type:"restory",
                scale:30,
                duration:300,
                animate:{
                    "font-size":"1.4em",
                    "line-height":"+=60px",
                    
                }
                
            });                
        ');

        
        
        
    }
}      

if($Application->is_main(__FILE__)){
  
    $app = new TWS();
    // ----------------------------------------------------------
    //  $app->version = ''; - custom clear cache 
    //  $app->version = 'nocache'; no caching data always
    //  $app->verison = 'XXXXX'; caching data (version control)
    $app->version = ''; 
    
    // ----------------------------------------------------------
    //  $app->title - browser tab name
    $app->title = 'blink example';

    // ----------------------------------------------------------
    //  $app->tabColor - in mobile Chrome tab color
    $app->tabColor = '#DCDCDC';
    
    $app->RUN();

}
?>
    