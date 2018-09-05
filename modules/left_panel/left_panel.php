<?php

require_once UNIT('ws','ws.php');


MODULES::ADD('LEFT_PANEL');
class LEFT_PANEL extends WS_MODULE{
 
    function __construct($owner){
        parent::__construct($owner);
        
        RESOURCE('modules/left_panel/left_panel.dcss');
        RESOURCE('modules/left_panel/left_panel.js');
        RESOURCE('plugins/jworkplaces/jworkplaces.js');
        RESOURCE('plugins/jworkplaces/jworkplaces.dcss');
                
    }
    
    public function CONTENT(){

        $own = FRAME('left')
        ->ALIGN('left_panel.align();')
        ->SCRIPT('var _prh={
                press:false,
                fix:{x:0,y:0},
                current:{x:0,y:0},
                h:{ex:100,pr:100}
        };')
        ->READY('left_panel.ready();');
        
        $h = 32;
        $hc= $h-4;
        $explorer_header = FRAME('explorer_header',$own)->CLASSES('layer tmpl_chapter')
        ->STYLE('height:'.$hc.'px');
        
        FRAME('ex_label',$explorer_header)->CLASSES('ph_com')->VALUE('Explorer');
        FRAME('ex_refresh',$explorer_header)->CLASSES('ph_com ph_btn')
        ->EVENT('click','explorer.refresh()');            

        FRAME('ex_stretch',$explorer_header)->CLASSES('ph_com');
            
        FRAME('ex_open_close',$explorer_header)->CLASSES('ph_com ph_btn')
        ->EVENT('click','left_panel.click_explorer_header();');            
        
        
        
        $explorer = FRAME('explorer',$own)
        ->CLASSES('layer')
        ->STYLE('height:100px');
        
        $problem_header = FRAME('problem_header',$own)
        ->CLASSES('problem_header layer tmpl_chapter')
        ->STYLE('height:'.$hc.'px');
            

        FRAME('ph_label',$problem_header)
        ->CLASSES('ph_com')
        ->VALUE('Workspace');

        FRAME('ph_add',$problem_header)
        ->CLASSES('ph_com ph_btn')
        ->EVENT('click','wps.add({caption:"new..",parent:"#",edit:true}); ');
 
        FRAME('ph_collapse',$problem_header)
        ->CLASSES('ph_com ph_btn')
        ->EVENT('click','wps.collapse(); ');
                
        FRAME('ph_stretch',$problem_header)
        ->STYLE('cursor:ns-resize;')
        ->CLASSES('ph_com');
            
        FRAME('ph_open_close',$problem_header)
        ->CLASSES('ph_com ph_btn')
        ->EVENT('click','left_panel.click_problem_header();');

       

        $problem = FRAME('problem',$own)
        ->CLASSES('layer ws_scrollbar')
        ->SCRIPT('var wps;')
        ->READY('
            var ud=$.extend(true,[],Ws.user_data.workspace);    
            wps=new jwp({own:{$}},ud);
            delete Ws.user_data.workspace;                    
		');
    }
    
    public function AJAX(&$response){
        global $REQUEST;
        global $USERS;
        global $Application;
        if ($REQUEST->ID=='change_workspace'){
            
            $data =  $REQUEST->VALUE['data'];

            $USERS->put('workspace',$data);
            $USERS->save_current();
            $response = array('res'=>1);

            return true;    
        }
        if ($REQUEST->ID == 'run_from_tree'){
                
    
            $filename = $REQUEST->VALUE['filename'];
            $url = $Application->DOMEN.APP::slash($USERS->get('workplace',''),false,true).$filename;
            
            $response = array('res'=>1,'url'=>$url);
            return true;
            
        }
        
        return false;
    }
    
};

if ($Application->is_main(__FILE__)){
    echo 'module1';
}
?>