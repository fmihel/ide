<?php

require_once UNIT('ws','ws.php');

MODULES::ADD('LAYOUT');
class LAYOUT extends WS_MODULE{
    
    function __construct($owner){
        parent::__construct($owner);
    
    }
    
    public function CONTENT(){
        $wp = FRAME::ADD('workplace')
            ->STYLE('position:absolute;margin:0px;padding:0p;display:none')
            ->READY('{$}.hide().fadeIn(1000,function(){Ws.align();})');
        FRAME::ADD('modal')
            ->STYLE('left:0px;top:0px;position:absolute;width:0px;height:0px;z-index:1000');
    
        $page = FRAME::ADD('page',$wp)
        ->STYLE('border:0px dashed blue;position:absolute')
        ->ALIGN('JX.workplace(Qs.workplace,Qs.page);');        
        
        $own = $page
        ->ALIGN('
            JX.arrange([Qs.top,Qs.middle,Qs.bottom],{direct:"vert",stretch:Qs.middle});
            JX.arrange([Qs.left,Qs.splitter_lc,Qs.center,Qs.splitter_cr,Qs.right],{direct:"horiz",stretch:Qs.center});
        ');
        
        FRAME('top',$own)
            ->CLASSES('layer top')
            ->STYLE('z-index:100');
        
        $middle = FRAME('middle',$own)
            ->CLASSES('layer');
        
        FRAME('left',$middle)
            ->CLASSES('layer left')
            ->STYLE('width:250px');
        
        FRAME('splitter_lc',$middle)
            ->CLASSES('layer splitter-left')
            ->STYLE('width:3px;cursor:ew-resize;z-index:100')
            ->READY('{$}.splitter({horiz:true})');
        
        FRAME('center',$middle)
            ->CLASSES('layer');

        FRAME('splitter_cr',$middle)
            ->CLASSES('layer splitter-right')
            ->STYLE('width:3px;cursor:ew-resize;z-index:100')
            ->READY('{$}.splitter({horiz:true})');
            
        FRAME('right',$middle)
            ->CLASSES('layer right')
            ->STYLE('width:300px');
        /*
        FRAME('splitter_cb',$own)
            ->CLASSES('layer splitter-vert')
            ->STYLE('height:4px;cursor:ns-resize;z-index:100')
            ->READY('{$}.splitter({horiz:false})');
        */
        FRAME('bottom',$own)
            ->CLASSES('layer bottom')
            ->STYLE('height:200px;z-index:100');

    }
    
};

if ($Application->is_main(__FILE__)){
    echo 'module1';
}
?>