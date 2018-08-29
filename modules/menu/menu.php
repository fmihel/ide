<?php


MODULES::ADD('MENU');

class MENU extends WS_MODULE{
    function __construct($owner){
        parent::__construct($owner);
        
        RESOURCE('plugins','common/thread.js');
        RESOURCE('plugins','splitter/splitter.js');
        RESOURCE('plugins/popup/popup.js');
        RESOURCE('plugins/popup/popup.dcss');
        
        RESOURCE('plugins','dialog/jdialog.js');
        RESOURCE('style/jdialog.dcss');
        
        
        RESOURCE('plugins','shadow/jshadow.js');
        RESOURCE('plugins','menu/jmenu.js');
        RESOURCE('style/menu.dcss');
        RESOURCE('modules/menu/menu.js');
        
    }
    public function CONTENT(){
        global $USERS;
        
        $own = FRAME('top')
        ->ALIGN('
            JX.arrange([Qs.menu_left,Qs.menu_center,Qs.menu_right],{direct:"horiz",type:"stretch",align:"stretch",stretch:Qs.menu_right});
            JX.arrange(Qs.menu_left.children(),{direct:"horiz",type:"right",align:"center",margin:{left:10},gap:0})
            JX.arrange(Qs.menu_center.children(),{direct:"horiz",type:"stretch",align:"center",stretch:[{idx:1}],margin:{left:20},gap:0})
            JX.arrange(Qs.menu_right.children(),{direct:"horiz",type:"right",align:"center",margin:{right:0},gap:5})
            ');
            
        $left   = FRAME('menu_left',$own)->STYLE('position:absolute;width:231px');
        $center = FRAME('menu_center',$own)->STYLE('position:absolute;width:600px');
        $right  = FRAME('menu_right',$own)->STYLE('position:absolute;width:305px');
        
        
        $cd = $USERS->current_data();
        
        $unv=FRAME('user_name_view',$right)
        ->STYLE('width:150px;color:#79ACD5;text-align:right;cursor:default;font-size:0.7em;font-style:italic;')
        ->CLASSES('menu');
        
        FRAME('unv_login',$unv)
            ->STYLE('height:48%;line-height:18px')
            ->VALUE($USERS->get('login'));
        FRAME('unv_email',$unv)
            ->STYLE('height:48%;line-height:12px')
            ->VALUE($cd['email']);
        

        FRAME('btn_exit',$right)
        ->CLASSES('menu')
        ->READY('{$}.on("click",menu_action.exit)')
        ->VALUE('Exit');
        
        FRAME('btn_menu',$left)
        ->CLASSES('menu')
        ->EVENT('click','menuX.show();')
        ->VALUE('Menu..')
        ->SCRIPT('var menuX=null;')
        ->READY('
        
        menuX=new jmenu({
            own:Qs.modal,
            stick:{$},
            width:150,
            off:{sx:10,sy:-2},
            data:[
                {caption:"Options..",id:"options"},
                "-",
                {caption:"Save all",id:"save_all"},
                {caption:"Save current",id:"save_current"},
                "-",
                {caption:"Run",id:"run"},
                {caption:"Run current",id:"run_current"},
                "-",
                {caption:"Update..",id:"check_update"},
                {caption:"Close all",id:"close_all"},
                "-",
                {caption:"Exit",id:"exit"}
            ],
            onClick:function(o){
            
                if (o.id=="options") menu_action.options()
                if (o.id=="exit") menu_action.exit()
                if (o.id=="save_all") menu_action.save()
                if (o.id=="save_current") menu_action.save_current()

                if (o.id=="run") menu_action.run();
                if (o.id=="run_current") menu_action.run_opened();
                if (o.id=="check_update") menu_action.check_update();
                if (o.id=="close_all") menu_action.close_all();

                return true;
                
            }
        });
        setTimeout(function(){
            $("document").tooltip();
        },1000);
        
        ');

        
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
        ->STYLE('position:absolute;border-left:0px;border-right:0px')
        ->CLASSES('url_center')
        ->ALIGN('{$}.urlLight("align");')
        ->READY('{$}.urlLight({
            css:{
                frame:"uli2_frame",
                edit:"uli2_edit",
            
                protocol:"uli2_protocol",
                domen:"uli2_domen",
                path:"uli2_path",
                file:"uli2_file",
                params:"uli2_params",
                placeholder:"uli2_placeholder"
            }            
        });')
        ->VALUE('');
        
        FRAME('btn_run_options',$center)
        ->CLASSES('menu_btn url_right')
        ->EVENT('click','menu_action.run_options();');
        
    }
    
};


?>