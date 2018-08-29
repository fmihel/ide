<?php
/*
 https://ws-framework-fmihel.c9users.io/ide/ws/plugins/popup/example.php
*/
if(!isset($Application)){
    require_once '../../utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = false;
    
    require_once UNIT('ws','ws.php');
};

RESOURCE('plugins','splitter/splitter.js');
RESOURCE('plugins','common/jlock.js');
RESOURCE('plugins','common/jhandler.js');

RESOURCE(WARD(__DIR__,'popup.js'));
RESOURCE(WARD(__DIR__,'popup.dcss'));

class TWS extends WS{
        
    private function layout(){
        FRAME()->CSS('
            body{
                overflow-x:hidden;
                overflow-y:hidden;
                -webkit-font-smoothing:antialiased;
                -webkit-user-select: none;
                text-overflow: ellipsis;
                margin:0px;
                padding:0px;
                
            }
            .frame{
                border:1px solid silver;
            }
        ');
        
        $wp = FRAME('workplace',FRAME())
            ->STYLE('position:absolute;margin:0px;padding:0p');
            
        FRAME('modal',FRAME())
            ->STYLE('position:absolute;left:0px;top:0px;width:0px;height:0px;z-index:1000');
        
        $page = FRAME('page',$wp)
            ->STYLE('position:absolute;border:0px')
            ->ALIGN('JX.workplace(Qs.workplace,Qs.page);');        
        
        $own = $page
            ->ALIGN('
                JX.arrange([Qs.top,Qs.middle,Qs.bottom],{direct:"vert",stretch:Qs.middle});
                JX.arrange([Qs.left,Qs.splitter_lc,Qs.center,Qs.right],{direct:"horiz",stretch:Qs.center});
            ');
                
        FRAME('top',$own)
            ->STYLE('position:absolute;height:32px;');
        
        $middle = FRAME('middle',$own)
            ->STYLE('position:absolute');
            
        FRAME('bottom',$own)
            ->STYLE('position:absolute;height:32px');

        FRAME('left',$middle)
            ->STYLE('position:absolute;width:250px');

        FRAME('splitter_lc',$middle)
            ->STYLE('position:absolute;width:5px;cursor:ew-resize')
            ->READY('{$}.splitter({horiz:true})');
            
        $center = FRAME('center',$middle)
            ->STYLE('position:absolute');
            
        //---------------------------------------------------------------------

        FRAME('right',$middle)
            ->STYLE('position:absolute;width:32px');
        
        //---------------------------------------------------------------------
    }
    
    private function menu($own){
        /*com:run */
        $run=FRAME('run',$own)
        ->VALUE('run')
        ->STYLE('width:99%;height:32px;border:1px solid gray;text-align:center;line-height:32px;cursor:pointer')
        ->SCRIPT('var iii=0;')
        ->INIT('{$}.on("click",function(){
            iii=iii+1;
            popup({
                caption:"Файл "+iii+" удален",
                context:{file:"name"+iii+".js"},
                btnCaption:"отменить",
                onButton:function(context){
                    console.info(context);
                },
                onDelayClose:function(context){
                    console.info(context);
                }
            });            
        })');
        
        /*com:run2 */
        $run=FRAME('run2',$own)
        ->VALUE('run2')
        ->STYLE('width:99%;height:32px;border:1px solid gray;text-align:center;line-height:32px;cursor:pointer')
        ->INIT('{$}.on("click",function(){
            iii=iii+1;
            popup({
                caption:"text text text",
                css:{frame:"popup_frame_ex"}
            });            
        })');
        /*com:align */
        $run=FRAME('set',$own)
        ->VALUE('set')
        ->STYLE('width:99%;height:32px;border:1px solid gray;text-align:center;line-height:32px;cursor:pointer')
        ->INIT('{$}.on("click",function(){
            
            popup({
                css:{frame:"popup_frame_ex"},
                align:{type:"top",align:"right"},
                alignChild:{margin:{right:50}},
                reverse:false,
            });            
        })');
    
    }
    public function CONTENT(){
        $this->layout();
        FRAME('top')->CLASSES('frame');
        FRAME('bottom')->CLASSES('frame');
        FRAME('center')->CLASSES('frame');
        FRAME('splitter_lc')->CLASSES('frame');
        $own = FRAME('page');
        
        $this->menu(FRAME('left'));
        
        $own = FRAME('modal');

        /*com:md */
        $md=FRAME('md',$own)
            ->STYLE('position:absolute');
            
    }
  

}      

if($Application->is_main(__FILE__)){
  
    $app = new TWS(); 
    $app->RUN();

}

?>
    