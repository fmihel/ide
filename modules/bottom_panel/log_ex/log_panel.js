/*global $,Qs,Dcss,ut,JX,Ws,TIMER*/
var log_panel={
param:{},
init:function(o){
    var t=log_panel;
    t.param=$.extend(true,
    {
        own:Qs.body,
        bottom:undefined,
        panel_right_margin:20,
        change:undefined,
        lock_change:0,
        lock_update:false,
        updateTimer:undefined,
        css:{
            btn_add:'lp_btn_add',
            btn_run:'lp_btn_run',
            btn_clear_all:'lp_btn_clear_all',
            
            frame:'lp_frame',
            item:'lp_item',
            content:'lp_content',
            panel:'lp_panel',
            splitter:'lp_splitter',
            
            panel_log:'lp_panel_log',
            panel_frame:'lp_panel_frame',
            
            subst:'lp_subst',
            subst2:'lp_subst2',
            btn_on:'lp_btn_on',
            btn_off:'lp_btn_off',
            btn_clear:'lp_btn_clear',
            btn_close:'lp_btn_close',
            btn_update:'lp_btn_update',
            auto:'lp_auto',

            log_name:'lp_log_name',
            log_name_input:'lp_input',
            log_name_select:'lp_select',
            
            log_msg_info:'lp_msg_info',
            log_msg:'lp_msg',
            log_iframe:'lp_iframe'
            
        }        
    },o);
    var p = t.param;

    p.id = ut.id('lp');
    p.frame = ut.tag({id:p.id,css:p.css.frame,style:'position:absolute'});
    p.own.append(p.frame);
    p.frame = p.own.find('#'+p.id);

    p.frame.sortable({
        start(){
            p.lock_update = true;  
        },
        stop(){
            t.align();
            t.change();
            p.lock_update = false;
        },
        handle:'.'+p.css.panel
        
    });
    //p.frame.disableSelection();
    
    
    /*log panel buttons -------------------*/        
    var id = ut.id('bad');
    p.bottom.btn_panel.append(ut.tag({id:id,css:p.css.btn_add,style:'position:absolute'}));        
    p.btn_add = p.bottom.btn_panel.find('#'+id);
    p.btn_add.on("click",function(){
        var item = t.add();
        item.log_name.mselect('focus');
    });

    id = ut.id('bar');
    p.bottom.btn_panel.append(ut.tag({id:id,css:p.css.btn_run,style:'position:absolute'}));        
    p.btn_run = p.bottom.btn_panel.find('#'+id);
    p.btn_run.on("click",function(){
        var item = t.frame().item;
        if (!item)
            t.add({type:'frame'});
        else
            t.del(item);
    });
    id = ut.id('bar');
    p.bottom.btn_panel.append(ut.tag({id:id,css:p.css.btn_update,style:'position:absolute'}));        
    p.btn_update = p.bottom.btn_panel.find('#'+id);
    p.btn_update.on("click",function(){
        //t.faster();
        t._update();
    });

    id = ut.id('bar');
    p.bottom.btn_panel.append(ut.tag({id:id,css:p.css.auto,style:'position:absolute'}));        
    p.auto = p.bottom.btn_panel.find('#'+id);
    
    p.auto.mselect({data:[
        {value:'manual',id:0},
        {value:'2 sec',id:2},
        {value:'5 sec',id:5},
        {value:'10 sec',id:10}
        ],
        onSelect(o){
            var data = p.auto.mselect('data','selected');
            if (t.timerUpdate!==undefined)
                clearInterval(t.timerUpdate);
            if (data.id>0){
                t.timerUpdate = setInterval(function(){
                    try{
                        t._update();
                    }catch(e){
                        
                    }   
                },data.id*1000);
            }else    
                t.timerUpdate = undefined;
    }});
    

    p.bottom.btn_panel.append(ut.tag({style:'position:absolute;width:20px'}));        

    id = ut.id('bar');
    p.bottom.btn_panel.append(ut.tag({id:id,css:p.css.btn_clear_all,style:'position:absolute'}));        
    p.btn_clear_all = p.bottom.btn_panel.find('#'+id);
    p.btn_clear_all.on("click",function(){
        t.clear_all();
    });
    
    /*--------------------------------------*/
    
    t.align();
    Ws.align({func(){t.align();}});
    /*--------------------------------------*/
    Qs.body.on('mousemove',function(e){
        p.cursor = {x:e.pageX,y:e.pageY};
        if (p.splitter!==undefined){
            var w_left = JX.pos(p.splitter_left);
            var w_right = JX.pos(p.splitter_right);
            var delta = p.splitter_pos.x-p.cursor.x;
            p.splitter_pos.x = p.cursor.x;
            
            JX.pos(p.splitter_left,{w:w_left.w-delta});
            JX.pos(p.splitter_right,{w:w_right.w+delta});
            t.align();
        }
        
    });
    Qs.body.on('mouseup',function(){
            p.splitter = undefined;
    });
    /*--------------------------------------*/
    //TIMER.INIT({name:'log_panel',single:false,delay:p.delay.common,enable:true});
    
    //setInterval(function(){
    //    if (TIMER.CHECK('log_panel'))
    //        t._update();
    //},100);
},
begin_change:function(){
    var t=log_panel,p=t.param;
    p.lock_change++;
},

change:function(event){
    var t=log_panel,p=t.param;
    
    if ((p.lock_change==0)&&(p.change))
        p.change({sender:t,event:event});
},

end_change:function(){
  var t=log_panel,p=t.param;
  p.lock_change--;
  if ((p.lock_change==0)&&((arguments.length===0)||(arguments[0])))
        t.change('group');  
  
},

load:function(o){
    
    var t=log_panel,i;
    t.begin_change();
    
    t.del(-1);
    
    let logs = o.logs;
    let data = o.files;
    
    if (logs)
    for(i=0;i<logs.length;i++){
        
        let a=$.extend(true,{
            type:'log',
            file:'',
            enable:0,
            width:200
        },logs[i]);

        let item=t.add(a);

        if (item.log_name){ 
            item.log_name.mselect({data});
            item.log_name.mselect('value',a.file);
        }    
        t.enable(item.btn_on,a.enable);
        
        JX.pos(item.item,{w:a.width});
    }
    t.align();
    t.end_change(false);
},

save:function(){
    var t=log_panel,items=t.items(),i,j,out=[],files=[],v;
    
    for(i=0;i<items.length;i++){
        var d=items[i];
        
        out.push({
            id:d.id,
            type:d.type,
            file:(d.log_name?d.log_name.mselect('value'):''),
            enable:t.enable(d.btn_on),
            width:JX.pos(d.item).w
                
        });
        
        v = d.log_name.mselect('data');
        for(j=0;j<v.length;j++){
            if (files.indexOf(v[j].value)==-1)
                files.push(v[j].value);
        }    
        
    }
    

    return {logs:out,files};
},

enable:function(btn){
    var t=log_panel,p=t.param,css=p.css;
    if (arguments.length>1){
        
        if (arguments[1]==1){
            btn.removeClass(css.btn_off);
            btn.text('on');
        }else{
            btn.addClass(css.btn_off);
            btn.text('off');
        }    
        
    }else
        return (btn.hasClass(css.btn_off)?0:1);
},

add:function(o){
    var t=log_panel,p=t.param,css=p.css;
    var c= '';
    var a=$.extend(true,{
        id:ut.id('lpn'),
        type:'log',
        enable:true,
    },o);
    
    t.begin_change();
    
    
    c =ut.tag('<',{id:a.id,css:css.item,style:'position:absolute'});
    c+=ut.tag({css:css.content+' '+(a.type=='log'?css.panel_log:css.panel_frame)+' ws_scrollbar',style:'position:absolute'});
    c+=ut.tag('<',{css:css.panel,style:'position:absolute'});
    
    
    if (a.type=='log'){
        c+=ut.tag('<',{css:css.subst,style:'position:absolute'});
        c+=ut.tag({css:css.btn_on,style:'position:absolute'});
        c+=ut.tag({css:css.log_name,style:'position:absolute'});
        c+=ut.tag({css:css.btn_clear,style:'position:absolute'});
        c+=ut.tag({css:css.btn_close,style:'position:absolute'});
        c+=ut.tag('>');
    }else{
        c+=ut.tag('<',{css:css.subst2,style:'position:absolute'});
        c+=ut.tag({css:css.btn_on,style:'position:absolute'});
        c+=ut.tag({css:css.btn_close,style:'position:absolute'});
        c+=ut.tag('>');
    }
    
    c+=ut.tag('>');
    
    c+=ut.tag({css:css.splitter,style:'position:absolute'});
    
    c+= ut.tag('>');
    
    p.frame.append(c);
    
    
    
    a.item      =p.frame.find('#'+a.id);
    a.content   =a.item.find('.'+css.content);
    a.splitter  =a.item.find('.'+css.splitter);
    a.panel     =a.item.find('.'+css.panel);
    a.subst     =a.panel.find('.'+(a.type=='log'?css.subst:css.subst2));
    
    

    a.btn_close = a.item.find('.'+css.btn_close);
    p.line = 0;
    
    a.btn_on    = a.item.find('.'+css.btn_on);
    if (a.type=='log'){
        a.btn_clear = a.item.find('.'+css.btn_clear);
        a.log_name  = a.item.find('.'+css.log_name);
        a.log_name.mselect({
            editable:true,
            css:{
                input:css.log_name_input,
                select:css.log_name_select,
            }
        });
    }else{
        
    }
    
    
    JX.pos(a.item,{w:400});
    $.data(a.item[0],'data',a);
    t._event(a);
    
    t.enable(a.btn_on,a.enable);
    t.align();
    
    t.end_change();
    
    return a;        
    
},

_event:function(a){
    var t=log_panel,p=t.param;
    a.splitter.on('mousedown',function(){
        var o=$(this);
        if (!t.last(t.item_by_child(o))){
            p.splitter = a.splitter;            
            p.splitter_pos = {x:p.cursor.x,y:p.cursor.y};
            p.splitter_left = p.splitter.parent();
            p.splitter_right = p.splitter_left.next();
            let f = t.frame();
            if (f.iframe) f.iframe.hide();
        }

    });
    a.splitter.on('mouseup',function(){
        p.splitter = undefined;
        t.change('width');
        let f = t.frame();
        if (f.iframe) f.iframe.show();
    });
    
    a.btn_close.on("click",function(){
        var item = t.item_by_child($(this));
        t.del({item:item});
    });
    
    if (a.btn_clear)
    a.btn_clear.on("click",function(){
        var item = t.item_by_child($(this));
        t._clear($.data(item[0],'data'));
    });
    
    if(a.log_name)
        a.log_name.mselect({onSelect(){
            
        let v = a.log_name.mselect("value").trim();
        let data = a.log_name.mselect("values");
        if (data.indexOf(v)===-1){
            data.push(v);
            a.log_name.mselect("clear");
            a.log_name.mselect(data);
            a.log_name.mselect("value",v);
        }    

        t.change('change');
    }});
    if (a.btn_on)
        a.btn_on.on("click",function(){
            var b=$(this);
            t.enable(b,(t.enable(b)==0?1:0));
            t.change();
        });
    
},
_clear:function(data){
    var t=log_panel,a = data;/*$.data(item[0],'data');*/
    a.line = -1;
    Ws.ajax({id:"log_clear",value:{
        filename:a.log_name.mselect('value')
    }});
    a.content.text('');
    t.change('clear');
},
clear_all:function(){
    var t=log_panel,items=t.items(),i;
    for(i=0;i<items.length;i++){
        if (items[i].type=='log')
            t._clear(items[i]);
    }    
},

del:function(o){
    var t=log_panel,p=t.param,f=p.frame;
    if (o==-1){
        f.html('');
        t.change('del');
    }else
    if (o.item){
        o.item.remove();
        t.align();
        t.change('del');
    }    
    

},

item_by_child:function(o){
    var t=log_panel,p=t.param,css=p.css;
    var obj = o;
    while(obj[0].tagName!=='BODY')
        if (obj.hasClass(css.item)) 
            return obj;
        else
            obj=obj.parent();
    return null;
},

children:function(){
    var t=log_panel,p=t.param,f=p.frame;
    return f.children();
},

items:function(){
    var t=log_panel;
    var ch=t.children(),res=[];
    $.each(ch,function(i,o){
        res.push($.data(o,'data'));
    });
    
    return res;
},

index:function(o){
    var t=log_panel,it=t.items();
    for(var i=0;i<it.length;i++){
        if (o.item){
            if(o.item[0].id==it[i].id) return i;
        }else{
            if(o[0].id==it[i].id) return i;
        }    
        
    }
    return -1;
},

last:function(o){
    var t=log_panel;
    return (t.index(o)==t.count()-1);
    
},

count:function(){
    return log_panel.children().length;
},

align:function(){
    var t=log_panel,p=t.param,i,it;
    JX.stretch(p.frame);
    
    var ch=t.children();
    if (ch.length>0){
        JX.arrange(ch,{direct:"horiz",align:"stretch",type:"stretch",stretch:[{idx:0}],margin:{left:20}});
        var items = t.items();
        
        for(i=0;i<items.length;i++){
            it=items[i];
            JX.arrange([it.content,it.splitter],{direct:"horiz",align:"stretch",type:"stretch",stretch:[{idx:0}]});    

            var ps = JX.pos(it.item);
            JX.pos(it.panel,{x:5,y:0,w:ps.w-p.panel_right_margin-5});
            
            JX.arrange([it.subst],{direct:"horiz",align:"center",type:"right"});    
            JX.arrange(it.subst.children(),{direct:"horiz",align:"center",type:"right",gap:3,margin:{right:2}});    
        }
    }
    
    JX.arrange(p.bottom.btn_panel.children(),{direct:"horiz",align:"center",type:"left",gap:5});
    
    var f=t.frame();
    if (f.iframe)
        JX.stretch(f.iframe,{margin:{left:2,top:2,bottom:2,right:2}});
},

_log:function(log,to){
    var t=log_panel,p=t.param,css=p.css,i,msg,info,code;
    
    for(i=0;i<log.length;i++){
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
        
            to.append(code);
        }    
    }    
    
    var last = to.children().last();
    if (last.length){
        var c=JX.pos(last);        
        to.animate({ scrollTop: c.y+c.h });
    }
    
},
update:function(){
    var t=log_panel,
        p=t.param;
    if (t.timerUpdate==undefined)
        setTimeout(function(){ t._update();},100);
    
},
_update:function(){
    var t=log_panel,
        p=t.param,
        i,
        items=t.items(),
        len = items.length;

    if ((len>0)&&(!p.lock_update)){
        for(i=0;i<len;i++){
            
            var it=items[i];
            t._updateItem(it); 
        
        }
    }
},
_updateItem:function(it){
    var t=log_panel,p=t.param,fname;

    if ((!p.lock_update)&&(it.type=='log')&&(t.enable(it.btn_on))&&(it._lockUpdate!==true)){
        
        it._lockUpdate = true;
        fname = it.log_name.mselect('value');
        if (fname!==it.prevFile)
            it.full = false;    
        
        it.prevFile = fname;
        
        Ws.ajax({
            context:it,
            id:'log_refresh',
            value:{
                line:(it.line===undefined?-1:it.line),
                filename:fname,
                full:it.full===undefined?0:it.full,
            },
            error(){
                it._lockUpdate = false;        
            },
            done(data,id,context){
                if (data.res==1){
                    if (context.line!=data.line){
                        
                        context.line  = data.line;
                        context.count = data.count;
                        context.full  = 1;
                        
                        t._log(data.log,context.content)
                        context._lockUpdate = false;
                        
                        if ((data.line!==-1)&&(data.line!==data.count-1)){
                            t._updateItem(context);
                        }
                    }else{
                        if (context.count > data.count){
                            context.line  = data.count-1;
                            context.count = data.count;
                        }
                    }
                }else
                    console.info(data.msg);
                context._lockUpdate = false;                
            }
        })    
    }
},

frame:function(){
    var t=log_panel,p=t.param,i,items=t.items(),frame;
    if (arguments.length==0){
        frame=false;
        for(i=0;i<items.length;i++){
            if (items[i].type=="frame"){
                frame=items[i];
                break;
            }
        }
        var to = (frame?frame.content:false);
        var iframe = to?to.find("."+p.css.log_iframe):false;
        
        if ((!iframe)||(iframe.length==0)) iframe=false;
        
        return {
            item:frame,
            to:to,
            enable:(frame&&t.enable(frame.btn_on)?true:false),
            iframe:iframe
        };
    }else{
        var a=$.extend(true,{
            url:''
        },arguments[0]);
        
        frame = t.frame();
        if (frame.enable){
            if (a.url!==''){
                frame.to.html("<iframe class='"+p.css.log_iframe+"' id="+ut.id('frame')+" src='"+a.url+"' ></iframe>");
                t.align();
                setTimeout(function(){t.align()},2000);
            }
        }
        
    }    
}
    
};


