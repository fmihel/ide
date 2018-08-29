<?php
if(!isset($Application)){
    require_once '../../../ws/utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = false; 
    
    require_once UNIT('ws','ws.php');
};

RESOURCE('plugins','eventCollector/evc.js');

class TWS extends WS{
    
    public function CONTENT(){
        $own = FRAME()
        ->READY('')
        ->READY('
        var a = {
            
            onChange:function(){
                console.info("onChange");
            }
        };
        
        
        var t = {
        
        ev:null,
        
            init:function(){
        
                t.ev = getEventCollector({
                    change:t.change
                });
            
            },
        
            proc1:function(){
                t.ev.begin("change");
                
                
                
                t.ev.end("change");    
            },
        
            change:function(param){
                t.ev.wrap(function(param){
                
                    console.info("change",param);

                },"change",param);
            }
        

        };
        
        
        
        t.init();
        t.proc1();
        
        
        
        
        
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
    $app->title = 'index';

    // ----------------------------------------------------------
    //  $app->tabColor - in mobile Chrome tab color
    $app->tabColor = '#DCDCDC';
    
    $app->RUN();

}
?>