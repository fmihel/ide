/*global $,ace,ut,Ws,jtab,global,popup,JX,nil,menu_action,Templates,code_complit,JDIALOG*/
function jeditors(o){

    var t=this;
    t.param=$.extend({
        own:null,
        tabs:null,
        theme:"ace/theme/"+((Ws&&Ws.user_data.theme==='dark')?'monokai':'xcode'),
        onopen:undefined,
        onchange:undefined,
        onaction:undefined,
        onscroll:undefined,
        panel_btn_size:{w:32,h:24},
        _story_opened:false,
        updating:true,
        menu_short:null,
        check_code_page_on_open:true,
        css:{
            panel_left:'ed_panel_left',
            panel_right:'ed_panel_right',
            panel_class:'ed_panel_class',
            panel_func:'ed_panel_func',
            panel_input:'ed_panel_input',
            panel_btn_run:'ed_panel_btn_run',
            panel_btn_set_as_start:'ed_panel_btn_set_as_start'

        }
        
    },o);
    var p=t.param;
    
    p.tabs = new jtab({
        own:p.own,
        onActivate:function(event,item,sender){
            if ((p.updating)&&(p.onopen)) p.onopen({event:"open",sender:t,item:item});
            t.align({delayed:true});
            t.story_opened();
            t._update_code_page();
        },
        onSortable:function(){
            t.story_opened();
        },
        onClickDel:function(o){
            if (o.item.changed){
            JDIALOG({
                    caption:'File is changed..',
                    msg:'File <font style="color:red">'+ut.extFileName(o.item.filename)+'</font> is changed. Close?',
                    buttons:['Close file','Not close'],
                    strip:true,
                    close_btn_enable:false,
                    css:{header:"jd_header_strip"},
                    onClick:function(a){
                        if (a.id == 0) 
                            o.sender.del(o.item);
                    }
                    
            });}else{
                o.sender.del(o.item);
            }
        },
        onAfterDel:function (o) {
            t.story_opened();
            t._update_code_page();
        }
    });
    
    
    
    setInterval(function(){t.action();},1000);
}
jeditors.prototype._update_code_page=function(){
    var t=this,item=t.current();
    if ((Qs.code_page))
        Qs.code_page.text(((item&&item.code_page)?item.code_page:''));
};

jeditors.prototype.add=function(o){
    var t=this,p=t.param,tabs=p.tabs;
    
    var a=$.extend(true,{
        node:undefined,
        filename:"",
        done:undefined
    },o);
    if (a.filename == '')
        if (a.node) a.filename=a.node[0].id;
    

    var item=t.search(a); 
    
    if (item===null){
        

        Ws.ajax({
                id:'get_file',
                value:{filename:a.filename},
                error:function(){
                     if (a.done) a.done({sender:t,item:null,type:'error',res:0});
                },
                done:function(data){
                    
                    if (data.res==1){

                        /*create tab*/
                        var eid = 'editor-'+p.tabs.param._id;
                        var item = p.tabs.add({name:ut.extFileName(a.filename)});
        
                        item.content[0].id = eid;
                        item.node=a.node;
                        item.filename = a.filename;
                        item.ext = ut.ext(a.filename);
                        item.code_page = data.code_page;
                        
                        if (data.type==='editor'){

                            /*create editor*/                            
                            item.editor = ace.edit(eid);
                            item.editor.setTheme(p.theme);
                            item.editor.setShowPrintMargin(false);
                            item.editor.$blockScrolling = Infinity;
                            t._setmode(item.editor,a.filename);
                            item.editor.insert(data.content);
                            item.changed = false;

                            t._key_event(item.editor);
                            
                            t._panel_add(item);
                            t.panel({input:{name:data.url}});
                            item.editor.on("change",function(){
                                
                                if (p.updating){ 
                                    if (p.onchange){
                                        item.changed = true;
                                        p.onchange({event:"change",sender:t,item:item});
                                        t._update_tab_name();
                                    };
                                    
                                    t._do_code_complit();
                                }
                                
                            });
                            

                            if ((p.check_code_page_on_open)&&((item.code_page!=='UTF-8')&&(item.code_page!=='ISO-8859-5'))){
                            JDIALOG({
                                caption:"Внимание!",
                                msg:"Кодировка открытого файла не соотвествует стандарту UTF-8. Во избежания проблем с кириллицей, рекомендуем Вам преобразовать данный файл к UTF-8",
                                strip:true,
                                close_btn_enable:false,
                                pos:{w:400},
                                shadow_opacity:0.5,
                                css:{header:"jd_header_strip"},
                                buttons:['Преобразовать','Отменить'],
                                /*buttons:['Закрыть'],*/
                                onClick:function(o){
                                    if (o.id==0){
                                        t.save_item({item:item,forced:true,toUTF8:1,done:function(){
                                            item.code_page='UTF-8';
                                            t._update_code_page();
                                            
                                        }});
                                    }        
                                }
                                
                            });}
                            
                            
                            t.story_opened();
                            
                        }else{
                            item.content.text(data.type+':'+a.filename);                    
                        }
                        
                        item.type = data.type;
                        if ((p.updating)&&(p.onopen)) p.onopen({event:"open",sender:t,item:item});   
                        
                        t._update_code_page();
                    }else
                        popup({type:"alert",msg:'cannot load '+a.filename});
                        
                        
                    
                    if (a.done) a.done({sender:t,item:item,was_open:false,res:data.res});    
                    
                    t.align();    
                }
                
        });
    }else{
        tabs.current(item);
        if (a.done) a.done({sender:t,item:item,was_open:true,res:1});
    }
};

jeditors.prototype.save_item=function(o){
    var t=this,item=o.item,done=o.done,forced=(o.forced!==undefined?o.forced:false),toUTF8=(o.toUTF8!==undefined?o.toUTF8:0);
    
    if(((item)&&(item.changed))||((item)&&(forced))){
        Ws.ajax({
            id:'set_file',
            value:{filename:item.filename,type:item.type,content:item.editor.getValue(),code_page:item.code_page,toUTF8:toUTF8},
            done:function(data){
                if (data.res==1){
                    popup({msg:"save: "+ut.extFileName(item.filename)});
                    item.changed = false;    
                }else
                    popup({type:'alert',msg:"err save: "+ut.extFileName(item.filename)});

                if (done) done({sender:t,item:item,res:data.res});    
            }
        });
    }else
        if (done) done({sender:t,item:item,res:1});
};

jeditors.prototype.save_current=function(){
    var t=this,item =t.current();
    t.save_item({item:item,done:function(){
        t._update_tab_name();
    }});
};

jeditors.prototype.get_first_changed=function(){
    var t=this,p=t.param,tabs=p.tabs,i,c=tabs.count(),item;
    
    for(i=0;i<c;i++){
        item = tabs.item(i);
        if (item.changed)
            return item;
    }
    return null;
};    

jeditors.prototype.save_all=function(){
    var t=this,item;
    item = t.get_first_changed();
    if (item!==null){
        t.save_item({item:item,done:function(e){ 
            t.save_all();
        }});
    }else
        t._update_tab_name();
    
};

jeditors.prototype._setmode=function(editor,file){
    var e=ut.ext(file);
    var modes={'js':'javascript',
                'php':'php',
                'dcss':'scss',
                'css':'css',
                'xml':'xml'
        
    };
    var mode = (modes[e]!==undefined?modes[e]:'text');
    editor.getSession().setMode("ace/mode/"+mode);
};

jeditors.prototype.search=function(o){
    var t=this,p=t.param,tabs=p.tabs,i,c=tabs.count(),
    a=$.extend({
        filename:'',
        node:null
    },o);
    for(i=0;i<c;i++){
        var item = tabs.item(i);
        if (a.filename!==''){
            if(item.filename == a.filename) return item;
        }else if (a.node){
            if(item.filename == a.node[0].id) return item;
        }
    }
    
    return null;
    
};

jeditors.prototype.panel=function(o){
    var t=this;
    var item = t.current();
    if ((nil(item))||(nil(item.panel_content))) return;
    var pc = item.panel_content;
    
    if('clas'in o){
        pc.clas.text(o.clas.name);
        $.data(pc.clas[0],'dat',o.clas.dat);
    }
    if('func'in o){
        pc.func.text(o.func.name);
        $.data(pc.func[0],'dat',o.func.dat);
    }
    if('input'in o){
        pc.input.val(o.input.name);
        $.data(pc.input[0],'dat',o.input.dat);
    }
    
    t.align();
    
};

jeditors.prototype._panel_add=function(item){
    var t=this,p=t.param,css=p.css,c='',pn=item.panel;

    c+=ut.tag('<',{css:css.panel_left,style:'position:absolute;width:380px'});        
        c+=ut.tag({css:css.panel_class,style:'position:absolute;width:150px',value:'PANEL'});        
        c+=ut.tag({css:css.panel_func,style:'position:absolute',value:'_construct'});        
    c+=ut.tag('>');
    
    c+=ut.tag('<',{css:css.panel_right,style:'position:absolute'});        
        c+=ut.tag({tag:'input',css:css.panel_input,style:'position:absolute'});        
        c+=ut.tag({css:css.panel_btn_set_as_start,style:'position:absolute',pos:{w:10}});        
        c+=ut.tag({css:css.panel_btn_run,style:'position:absolute',pos:p.panel_btn_size});        
    
    c+=ut.tag('>');
    
    pn.append(c);
    item.panel_content={
        left:pn.find('.'+css.panel_left),
        right:pn.find('.'+css.panel_right),
        clas:pn.find('.'+css.panel_class),
        func:pn.find('.'+css.panel_func),
        input:pn.find('.'+css.panel_input),
        btn_run:pn.find('.'+css.panel_btn_run),
        btn_set_as_start:pn.find('.'+css.panel_btn_set_as_start)
    };
    
    item.panel_content.clas.on("click",function(){
        var d=$.data(this,'dat');
        if (!nil(d))
            d.editor.gotoLine(d.line,0,false);
    });
    item.panel_content.func.on("click",function(){
        var d=$.data(this,'dat');
        if (!nil(d))
            d.editor.gotoLine(d.line,0,false);
    });
    
    item.panel_content.btn_run.on("click",function(){
        menu_action.run_opened();
    });
    
        
};

jeditors.prototype._panel_align=function(item){
    var pc=item.panel_content;
    if (pc){
        JX.arrange([pc.left,pc.right],{direct:'horiz',type:'stretch',align:'stretch',stretch:[{idx:1}]});        
        JX.arrange([pc.clas,pc.func],{direct:'horiz',type:'stretch',align:'stretch',stretch:[{idx:1}],margin:{left:20}});
        var h=pc.clas.height()+'px';
        pc.clas.css('line-height',h);
        pc.func.css('line-height',h);
        
        JX.arrange([pc.input,pc.btn_set_as_start,pc.btn_run],{direct:'horiz',type:'stretch',align:'center',stretch:[{idx:0}],gap:5});
        
    }
};

jeditors.prototype.align=function(o){
    var t=this,p=t.param,item;
    
    var a=$.extend({
        forced:false,
        delayed:false
    },o);
    
    p.tabs.refresh();
    
    if (a.forced){
        var i,c=p.tabs.count();
        for(i=0;i<c;i++){
            item=p.tabs.item(i);
            (item.editor?item.editor.resize():0);
            t._panel_align(item);
        }
    }else{
        item = p.tabs.current();
        if (item){
            (item.editor?item.editor.resize():0);
            t._panel_align(item);
        }
    }
    
    if (a.delayed)
        setTimeout(function(){t.align({forced:a.forced,delayed:false})},50);    
};

jeditors.prototype._update_tab_name=function(){
    var t=this,p=t.param,i,c=p.tabs.count(),item,name;
    for(i=0;i<c;i++){
        item=p.tabs.item(i);
        name = ut.extFileName(item.filename);
        p.tabs.tab_text({item:item,caption:name+(item.changed?'<b> *</b>':'')});
    }
};

jeditors.prototype.current=function(){
    var t=this,p=t.param,tabs=p.tabs,item;
    if(arguments.length>0){
        item=t.search(arguments[0]);
        tabs.current(item);
    }else{
        item=tabs.current();
        return item;
    }    
};

jeditors.prototype.action=function(){
    var t=this,p=t.param;    
    if ((p.updating)&&(p.onaction)) p.onaction({event:"action",sender:t});       
};

jeditors.prototype.abs_cursor_coord=function(editor){
    if (nil(editor)) editor = this.current().editor;
    return JX.abs($(editor.container).find('.ace_cursor'));
};
jeditors.prototype._short_template_key_event=function(editor){
    var t=this,p=t.param,i,data;
    if (nil(editor)) return;
    
    var range = editor.getSelectionRange();
    var currline = range.start.row;
    var currcol   = range.start.column;
            
    var key = editor.session.getLine(currline);
        key=key.replace(/\s*$/g,'').replace(/^[\s\S]*\s/g,"");
    
    if (key.trim()==='') return;
    
    var items=Templates.find_short({short:key});
    if (items.length>0){

        editor.selection.selectWordLeft();

        if (items.length==1)                
            items[0].jq.trigger("click");
        else{
            data=[];

            for(i=0;i<items.length;i++)
                data.push({n:items[i].caption,o:'',item:items[i]});
            code_complit.data['template']=data;
            code_complit.update({key:[key],ext:['template'],filled:true});
            code_complit.onEnter=function(o){
                o.current.data.item.jq.trigger("click");
            };

        }
    }
    

};

jeditors.prototype.set_cursor_instead=function(o){
    if ((nil(o.cursor))||(o.cursor==='')) return;
    if (nil(o.editor)) o.editor=this.current().editor;
    
    var range = o.editor.find(o.cursor);
    if (range)
        o.editor.session.replace(range, '');
    
};

jeditors.prototype._key_event=function(e){
    var t=this;
    e.commands.addCommand({               
        name: "CtrlF2", 
        bindKey: {win: "Ctrl-f2",  mac: "Command-f2"}, 
        exec: function(editor){
            menu_action.save();
        },                 
        readOnly: true
    });

    e.commands.addCommand({               
        name: "CtrlS", 
        bindKey: {win: "Ctrl-s",  mac: "Command-s"}, 
        exec: function(editor){
            menu_action.save();
        },                 
        readOnly: true
    });

    
    e.commands.addCommand({               
        name: "F9", 
        bindKey: {win: "f9",  mac: "f9"}, 
        exec: function(editor){
            menu_action.run_opened();

        },                 
        readOnly: true
    });               
    
    e.commands.addCommand({               
        name: "CtrlF9", 
        bindKey: {win: "Ctrl-f9",  mac: "Command-f9"}, 
        exec: function(editor){ 
            menu_action.run();

        },                 
        readOnly: true
    });               

    e.commands.addCommand({               
        name: "CtrlJ", 
        bindKey: {win: "Ctrl-j",  mac: "Command-j"}, 
        exec: function(editor){
            code_complit.show(false);
            t._short_template_key_event(editor);            
        },                 
        readOnly: true
    });
    
    e.commands.addCommand({               
        name: "Enter", 
        bindKey: {win: "Enter",  mac: "Enter"}, 
        exec: function(editor){ 
            if (code_complit.show()){
                code_complit.dokey({key:"Enter",editor:editor});
                return true
            }else
                return false;    

        },                 
        readOnly: true
    });               
    e.on('click',function(){
       code_complit.show(false);
       return true;
    });
    e.commands.addCommand({               
        name: "ESC", 
        bindKey: {win: "ESC",  mac: "ESC"}, 
        exec: function(editor){ 
            code_complit.show(false);

        },                 
        readOnly: true
    });               
    e.commands.addCommand({               
        name: "Down", 
        bindKey: {win: "Down",  mac: "Down"}, 
        exec: function(editor){ 
            if (code_complit.show()){
                code_complit.dokey({key:"Down",editor:editor});
                return true
            }else
                return false;    
        },                 
        readOnly: true
    });               
    e.commands.addCommand({               
        name: "Up", 
        bindKey: {win: "Up",  mac: "Up"}, 
        exec: function(editor){ 
            if (code_complit.show()){
                code_complit.dokey({key:"Up",editor:editor});
                return true
            }else
                return false;    
        },                 
        readOnly: true
    });               
    
    e.commands.addCommand({               
        name: "CtrlSpace", 
        bindKey: {win: "Ctrl-space",  mac: "Command-space"}, 
        exec: function(editor){ 
            var ed =t.current();
            if (!nil(ed)){
                t._do_code_complit();
                /*
                ed.editor.selection.selectWordLeft();
                var key=ed.editor.getSelectedText();
                ed.editor.selection.moveCursorWordRight();
                
                var list = code_complit.get_funcs({key:key});
                console.info(list);
                */
            }
        },                 
        readOnly: true
    });                   
    
};

jeditors.prototype._get_right_word=function(e){
    if (nil(e)) return '';    
    
    var range = e.getSelectionRange();
    var cursor = {r:range.start.row,c:range.start.column};
    
    var line = e.session.getLine(cursor.r);
    var out=[''],i,c;
    //var stop=' :;+-*&%#!)(=.';
    var stop=' ;+*&%#!)(=.[]';
    var light = ':';
    
    for(i=cursor.c;i>-1;i--){
        c=line[i];
        if (!nil(c)){
            
            if (stop.indexOf(c)==-1){
                if(light.indexOf(c)!==-1){
                    out[1]=c+out[0];
                    break;
                }
                out[0]=c+out[0];

            }else
                break;
        }
    }
    var res = [];
    for(i=0;i<out.length;i++)
    {
        c=out[i].trim();
        if ((c.length>1)||((c.length==1)&&(light.indexOf(c)==-1)))
            res.push(c);
    }    
    return res;    

};

jeditors.prototype._do_code_complit=function(){
    var t=this,e =t.current(),

    key=t._get_right_word(e.editor),ext=[];
    
    if ((e.ext=='css')||(e.ext=='dcss'))
        ext=['css'];
        
    
    code_complit.update({key:key,ext:ext});
    code_complit.onEnter=function(o){
        
        var add = o.current.data;
        
        e.editor.insert(add.o.substring(add.key.length));
        t.set_cursor_instead({editor:e.editor,cursor:'<{$cursor}>'});
        
        code_complit.show(false);

    } 

};


jeditors.prototype._story_opened=function(){
    var t=this,p=t.param,tabs=p.tabs,i,item,out=[],c=tabs.count();
    for(i=0;i<c;i++){
        item = tabs.item(i);
        if (item.filename)
            
            out.push({name:item.filename,active:(t.current().filename==item.filename?1:0)});
            
    }
    Ws.ajax({id:'story_opened',
        value:{opened:out},
        done:function(data){
            if (data.res!=1)
                popup({type:"error",msg:data.msg});
        }
    })

    p._story_opened = false;
};

jeditors.prototype.story_opened=function(){
    var t=this,p=t.param;
    var a=$.extend(true,{
        forced:false
    },arguments);
    
    if (p._roi_active) return;
    
    if (p._story_opened!==false) 
        clearTimeout(p._story_opened);
    
    if (a.forced) 
        t._story_opened();    
    else
        p._story_opened = setTimeout(function(){t._story_opened();},1000);
    
    
    
};

jeditors.prototype._restory_opened=function(){
    var t=this,p=t.param,i;
    t.add({
        filename:p._roi_list[p._roi].name,
        done:function(){
            p._roi++;
            if(p._roi<p._roi_list.length)
                t._restory_opened();
            else{
                p._roi_active=false;
                var active = -1;
                for(i=0;i<p._roi_list.length;i++){
                    if (p._roi_list[i].active==1) {
                        active=i;
                        break;
                    }
                }
                if (active>-1){
                    p.updating =true;
                    t.current({filename:p._roi_list[active].name});
                    p.check_code_page_on_open=true;
                }
                
            }    
        }
    });
};

jeditors.prototype.restory_opened=function(opened){
    var t=this,p=t.param;
    p._roi_list=$.extend(true,[],opened);
    p.check_code_page_on_open=false;
    setTimeout(function(){
        p.updating =false;
        if (p._roi_list.length>0){
            p._roi_active = true;
            p._roi=0;
            t._restory_opened();
        }else{
            p.updating =true;
            p.check_code_page_on_open=true;
        }    
    },2000);
};
