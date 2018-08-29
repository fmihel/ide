/*global Qs,$,Ws,JX,ut*/
var update={
    form:undefined,
init:function(){
    var t = update;
    t.form = Qs.update_form;
    Ws.align(function(){
        t.align();    
    });
},
open:function(){
    var t=update;
    t.form.mform("open");
},
check_update:function(){
    var t=update;
    Ws.ajax({
        id:"get_new_version",
        error:function(){
            update.new_version = "error";
            update.align();
        },
        done:function(data){
            if (data.res==1){
                update.new_version = data.version;    
            }else
                update.new_version = "error";
                                
            update.align();
        }
    });
},
run:function(){
    var t=update;
    
    Qs.upProcess.append(ut.tag({css:"up_process_block",value:"search: Ok."}));                                

    t.new_version = 'update';
    t.align();
    
    var doInstall=function(){
        Ws.ajax({
            id:'installNewVersion',
            error:function(){
                t._error('ERROR: system error (on install)');            
            },
            done:function(data){
                if (data.res==1){                
                    Qs.upProcess.append(ut.tag({css:"up_process_block",value:"install: Ok."}));
                    setTimeout(function(){
                        Qs.upProcess.append(ut.tag({css:"up_process_block_stop",value:"Restart application to complite. :)"}));
                    },1000);    
                                        
                    
                }else
                    t._error('ERROR: install process is abnormally');            
                t.align();    
            }
        });    
        
    };
    
    Ws.ajax({
        id:'downloadNewVersion',
        error:function(){
            t._error('ERROR: system error (on download)');            
        },
        done:function(data){
            if (data.res==1){                
                Qs.upProcess.append(ut.tag({css:"up_process_block",value:"download: Ok!"}));
                doInstall();                        
            }else
                t._error('ERROR: can`t download new version');            
            t.align();    
        }
    });    
},
_error:function(msg){
    console.error('error on update process');
    Qs.upProcess.append(ut.tag({css:"up_process_block_error",value:msg}));
},
align:function(){
    var t=update,f=t.form;
    if (!f.mform("get","visible")) return;
    var enable = false,txt = "";
    
    if (t.new_version=='check'){
        txt = 'check new version...';
        
    }else if (t.new_version=='update'){
        txt = '';
    }else if ((t.version!==t.new_version)&&(t.new_version!=='error')){
        enable=true;
        txt = 'new version <b>'+t.new_version+'</b> is available,';
    }else
        txt='you have the latest version';

    Qs.lNew.html(txt);
    if (enable)
        Qs.bUpdate.removeClass('up_btn_disable');
    else
        Qs.bUpdate.addClass('up_btn_disable');

    if (t.new_version!=='update'){    
        JX.tile([Qs.lCurrent,Qs.tCurrent,Qs.lNew,Qs.bUpdate,Qs.upProcess],{count:2,tail:"left"});
    }else{
        JX.tile([Qs.lCurrent,Qs.tCurrent,Qs.upProcess],{count:2,tail:"left"});
    }    

}
};/*update*/