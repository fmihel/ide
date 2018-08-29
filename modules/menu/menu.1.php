<?php
RESOURCE('plugins','shadow/jshadow.js');
RESOURCE('plugins/jmenu/jmenu.js');
RESOURCE('modules/menu/menu.js');


MODULES::ADD('MENU');

class MENU extends WS_MODULE{

    public function CONTENT(){
        $own = FRAME('top')
        ->ALIGN('
            JX.arrange({$}.children(),{direct:"horiz",type:"stretch",align:"stretch",stretch:Qs.menu_center});
            JX.arrange(Qs.menu_left.children(),{direct:"horiz",type:"center",align:"center",margin:{left:10},gap:0})
            JX.arrange(Qs.menu_center.children(),{direct:"horiz",type:"stretch",align:"center",stretch:[{idx:1}],margin:{left:20},gap:0})
            JX.arrange(Qs.menu_right.children(),{direct:"horiz",type:"right",align:"center",margin:{right:0},gap:5})
            ');
            
        $left   = FRAME('menu_left',$own)->STYLE('position:absolute;width:300px');
        $center = FRAME('menu_center',$own)->STYLE('position:absolute;width:100px');
        $right  = FRAME('menu_right',$own)->STYLE('position:absolute;width:305px');
        

        FRAME('btn_exit',$right)
        ->CLASSES('menu')
        ->READY('{$}.on("click",menu_action.exit)')
        ->VALUE('Exit');
        
        FRAME('menu',FRAME('modal'))
        ->STYLE('position:absolute;border:1px solid $border;')
        ->SCRIPT('var menu=null;')
        ->ALIGN('
            var p=JX.abs(Qs.btn_menu);
            JX.abs({$},{x:p.x+10,y:p.y+p.h-2});
        ')
        ->READY(' 
        menuX=new JMENU({
            own:{$},
            css:{item:"menu_item"},
            opacity:0.03,
            items:[
                {caption:"Options",id:"options"},
                {caption:"-"},
                {caption:"Save"},
                {caption:"Find"},
                {caption:"Run"},
                {caption:"-"},
                {caption:"Exit",id:"exit"}
            ],
            click:function(o){
                menuX.show(false);
                if (o.id=="options") menu_action.options()
                if (o.id=="exit") menu_action.exit()
            }
        });
        setTimeout(function(){
            $("document").tooltip();
        },1000);
        ');

        FRAME('btn_menu',$left)
        ->CLASSES('menu')
        ->EVENT('click','menuX.show(true);')
        ->VALUE('Menu..');
        
        FRAME('btn_save',$left)
        ->CLASSES('menu')
        ->ATTR('title','save all [ctrl-s]')        
        ->EVENT('click','menu_action.save();')
        ->VALUE('Save');

        /*FRAME('btn_find',$left)
        ->CLASSES('menu')
        ->EVENT('click','menu_action.find();')
        ->VALUE('Find');
        */


        FRAME('btn_run',$center)
        ->CLASSES('menu_btn url_left')
        ->ATTR('title','run [ctrl-F9]')        
        ->EVENT('click','menu_action.run();')
        ->VALUE('');

        FRAME('url',$center)
        ->TAG_NAME('input')
        ->STYLE('position:absolute;border-left:0px;border-right:0px')
        ->CLASSES('edit')
        ->ATTR('placeholder','url for running')
        ->READY('')
        ->VALUE('');
        
        FRAME('btn_run_options',$center)
        ->CLASSES('menu_btn url_right')
        ->EVENT('click','menu_action.run_options();');
        
    }
    
};


?>