/*global $,Ws,Qs,editors,ut,popup,options,jurls,log_panel,bottom_panel,update*/
var menu_action={
};

menu_action.save=function(){
    editors.save_all();
}; 
menu_action.save_current=function(){
    editors.save_current();
}; 


menu_action.run=function(){
    var url = Qs.url.urlLight("get","url");
    if (url!=''){
        if (jurls) jurls.add(url);
            url = ut.url_nocache(url);
        
        if ((bottom_panel)&&(!bottom_panel.close())&&(log_panel)&&(log_panel.frame().enable))
            log_panel.frame({url:url});
        else
            window.open(url,"run");
        
        if (log_panel) log_panel.update();    
    }else
        popup({msg:'input URL for run',type:'alert'});
}; 

menu_action.run_opened=function(){
    var c=editors.current();
    if(!c) return;
    var url=ut.url_nocache(c.panel_content.input.urlLight('get','url'));
    
    if ((bottom_panel)&&(!bottom_panel.close())&&(log_panel)&&(log_panel.frame().enable))
        log_panel.frame({url:url});
    else
        window.open(url,"run");
        
    if (log_panel) log_panel.update();    
};

menu_action.run_from_tree=function(filename){
    Ws.ajax({
        id:'run_from_tree', /** see in left_panel.php */
        value:{filename:filename},
        done:function(data){
            if (data.res == 1){
                window.open(data.url,"run");
                //console.info(data.url);
            }
        }
    });    
};

menu_action.exit=function(){
    editors.close_all(function(can){
        if (can){       
            delete Ws.share.session;
            Qs.body.fadeOut(200,function(){Ws.navigate({module:"close"});});
        }    
    });    
}; 
menu_action.options=function(){
    options();
}; 
menu_action.find=function(){
    console.info("menu_action.find - no defined ");
}; 

menu_action.run_options=function(){
    jurls.show(true);
    Ws.align();
};

menu_action.check_update=function(){
    if (typeof(update)!=='undefined') update.open();
};

menu_action.close_all=function(){
    editors.close_all();
};
menu_action.reOpen=function(){
    editors.reOpen({
    done(o){
        console.info(o);
    }});
};


function run_url()
{        
    console.info("menu_action.run - no defined ");
    
};
