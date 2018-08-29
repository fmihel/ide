/*global $,Qs,ut,JX,Ws,editors,save_user_data,bdata*/
var Log={
param:{},
init:function(o){
    var t=Log,c='';    
    t.param = $.extend(true,
    {
        own    :Qs.log,
        line:[-1,-1],
        input  :[],
        enable:[],
        clear:[],
        interval:2000,
        _on_timer:false,
        css:{
            input:'log_input',
            enable:'log_enable',
            disable:'log_disable',
            
            clear:'log_clear',
            panel:'log_panel ws_scrollbar',
            splitter:'log_splitter',
            log_msg:'log_msg',
            log_msg_info:'log_msg_info'
        }
    },o);

    var p=t.param,
    input_id = "log_input",
    enable_id='log_enable',
    clear_id='log_clear',
    panel_id='log_panel',
    splitter_id='log_splitter';

    c+= ut.tag({id:enable_id+'1',css:p.css.enable,style:'position:absolute'});
    c+= ut.tag({id:input_id+'1',tag:'input',css:'edit '+p.css.input,attr:{type:'text','placeholder':'filename.log'},style:'position:absolute'});
    c+= ut.tag({id:clear_id+'1',css:p.css.clear,style:'position:absolute'});

    c+= ut.tag({style:'position:absolute',pos:{w:52}});

    c+= ut.tag({id:enable_id+'2',css:p.css.enable,style:'position:absolute'});
    c+= ut.tag({id:input_id+'2',tag:'input',css:'edit '+p.css.input,attr:{type:'text','placeholder':'filename.log'},style:'position:absolute'});
    c+= ut.tag({id:clear_id+'2',css:p.css.clear,style:'position:absolute'});

    p.own.btn_panel.append(c);
    
    p.input  = [p.own.btn_panel.find('#'+input_id+'1'),p.own.btn_panel.find('#'+input_id+'2')];
    p.enable = [p.own.btn_panel.find('#'+enable_id+'1'),p.own.btn_panel.find('#'+enable_id+'2')];
    p.clear = [p.own.btn_panel.find('#'+clear_id+'1'),p.own.btn_panel.find('#'+clear_id+'2')];
    
    p.input[0].val('php_errors.log');
    
    JX.arrange(p.own.btn_panel.children(),{direct:"horiz",type:"left",align:"center",gap:3,margin:5});

    c =ut.tag({id:panel_id+'1',css:p.css.panel,style:'position:absolute;overflow:auto'});
    c+=ut.tag({id:splitter_id,css:p.css.splitter,style:'position:absolute',pos:{w:7}});
    c+=ut.tag({id:panel_id+'2',css:p.css.panel,style:'position:absolute;overflow:auto',pos:{w:500}});
    p.own.panel.append(c);
    p.panel=[p.own.panel.find('#'+panel_id+'1'),p.own.panel.find('#'+panel_id+'2')];
    
    p.splitter = p.own.panel.find('#'+splitter_id);
    p.splitter.splitter({horiz:true,onsplit:function(){t.align();}});
    
    t._event();
    t._timer();

    Ws.align({func:function(){t.align();},id:'log_align'});
    
    t.enable_log(0,false);
    t.enable_log(1,false);
    t.align();
    
    t._data_to_form();
    //save_user_data(
},
_data_to_form:function(){
    var t=Log,p=t.param,u=Ws.user_data;
    if (!u.log_files) u.log_files=['',''];
    
    var le = bdata.json_get({key:'log_enable',def:[false,false]});
    for(var i=0;i<2;i++){
        p.input[i].val(u.log_files[i]);
        t.enable_log(i,le[i]);
    }

},
_form_to_data:function(){
    var t=Log,p=t.param,u=Ws.user_data;
    for(var i=0;i<2;i++){
        u.log_files[i]=p.input[i].val();
        
        bdata.set({key:'log_enable',val:t.enable_log()});
    }
    
    save_user_data();
    
},
_event:function(){
    var t=Log,p=t.param,e;
    
    p.input[0].on('change keypress',function(){t._form_to_data();});
    
    p.enable[0].on("click",function(){
        e=!t.enable_log(0);
        t.enable_log(0,e);
        t._form_to_data();
    });
    
    p.clear[0].on("click",function(){t.clear(0);});
    
    p.enable[1].on("click",function(){
        e=!t.enable_log(1);
        t.enable_log(1,e);
        t._form_to_data();
    });
    p.clear[1].on("click",function(){t.clear(1);});

},
_timer:function(){
    var t=Log,p=t.param,css=p.css;
    setInterval(function(){
        if (!p._on_timer){
            p._on_timer=true;
            t.log_refresh(0,function(){
                t.log_refresh(1,function(){
                    p._on_timer=false;    
                });    
            });
        }
    },p.interval);    
},
enable_log:function(num,enable){
    var t=Log,p=t.param,css=p.css;
    if (num==undefined) return [t.enable_log(0),t.enable_log(1)];
    if (enable==undefined)
        return p.enable[num].hasClass(css.enable);    
    else{
        if (enable){
            p.enable[num].removeClass(css.disable);
            p.enable[num].addClass(css.enable);
            p.enable[num].text('on');
        }else{
            p.enable[num].removeClass(css.enable);
            p.enable[num].addClass(css.disable);
            p.enable[num].text('off');
        }
    }
},
clear:function(idx){
    var t=Log,p=t.param;
    Ws.ajax({
        id:'log_clear',
        value:{
                idx:idx,
                filename:p.input[idx].val()
        },
        done:function(data){
            if (data.res==1){
                p.panel[idx].html('');
                p.line[idx]=-1;
            }    
        }
    });
},
align:function(){
    var t=Log,p=t.param,css=p.css;
    JX.arrange(p.own.panel.children(),{direct:"horiz",type:"stretch",align:"stretch",stretch:[{idx:0}],margin:{left:20,right:20}});
    
},
_add:function(log,idx){
    var t=Log,p=t.param,css=p.css,pnl=p.panel[idx],last,c,code,info,msg;
    for(var i=0;i<log.length;i++){
        if (typeof log[i]==='string'){
            var reg = /^\[\d\d[^\]]*\][\s\S]*\:(\d+|xxxx)/g;
        
            if (reg.test(log[i]))
                msg = log[i].replace(reg,'');
            else
                msg = log[i].replace(/^\[\d\d[^\]]*\]/g,'');
        
            info= log[i].substring(0,log[i].length-msg.length);
        
            code=ut.tag('<',{css:css.log_msg});
            code+=ut.tag({tag:'span',css:css.log_msg_info,value:info});
            code+=msg;
            code+=ut.tag('>');
        
            pnl.append(code);
        }    
    }    
    last = pnl.children().last();
    if (last.length){
        c=JX.pos(last);        
        p.panel[idx].animate({ scrollTop: c.y+c.h });
    }
},
log_refresh:function(idx,done){
    var t=Log,p=t.param;
    
    if (t.enable_log(idx)){
    Ws.ajax({
        id:'log_refresh',
        value:{
            enable:t.enable_log(idx),
            line:p.line[idx],
            filename:p.input[idx].val()
        },
        error:function(){
            if (done)done();  
        },
        done:function(data){
            if (data.res==1){
                if (p.line[idx]!=data.line){
                    p.line[idx]=data.line;
                    t._add(data.log,idx);
                }
            }else
                console.info(data.msg);
            
            if (done)done();    
        }
    });
    }else
        if (done)done();
}
        
};