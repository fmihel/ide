/*global Qs,JX,$,ut,Ws,popup,editors,com_inspector,nil */
var Templates={
    param:{
        css:{
            block:"tmpl_block",
            chapter:"tmpl_chapter",
            chapter_icon:"tmpl_chapter_icon",
            chapter_close:"tmpl_chapter_close",
            chapter_open:"tmpl_chapter_open",
            short:"tmpl_chapter_short",
            group:"tmpl_group",
            item:"tmpl_item",
            
        }
    },
    
update:function(o){
    var t=Templates,p=t.param;
    var a=$.extend(true,{
        data:((o&&o.data)?o.data:p.data),
        forced:false,
    },o);
    
    var init=(!p.panel);
    if (init) {
        p.panel = Qs.Template.panel;
        p.panel.on("click",t.doclick);
        p.panel.on("mousemove",t.domousemove);
    }
    
    if ((init)||(a.forced)){
        p.data = a.data;
        p.panel.html("");
        t._create();
    }
},
find_short:function(o){
    var t=Templates,p=t.param,out=[],d=p.data,i,j,dat,chi,
    a=$.extend(true,{
        short:false
    },o);
    if (a.short===false) return out;
    
    for(i=0;i<d.length;i++){
        chi=d[i].childs;

        for(j=0;j<chi.length;j++){
            dat=chi[j];
            if ((dat.short==a.short)||(dat.short.substr(0,a.short.length)==a.short))
                out.push(dat);
        }
    }


    return out;
    
},
_create:function(){
    var t=Templates,p=t.param,d=p.data,css=p.css,i,j,chi,tmp,item,short;
    var c='';
    var id = 0;
    
    for(i=0;i<d.length;i++){
        c+=ut.tag('<',{css:css.block});
        c+=ut.tag('<',{css:css.chapter,value:d[i].caption});
        c+=ut.tag({css:css.chapter_icon+' '+css.chapter_close,style:'display:inline-block;float:right'});
        c+=ut.tag('>');
        chi=d[i].childs;
        c+=ut.tag('<',{css:css.group});
        for(j=0;j<chi.length;j++){
            tmp=chi[j];
            
            short = '';
            if (tmp.short!=='')
                short=ut.tag({css:css.short,style:"float:right;display:inline",value:tmp.short,attr:{title:"For a quick call,type ["+tmp.short+"] and after [Ctrl+J]"}});
                
            c+=ut.tag({css:css.item,value:tmp.caption+short,id:id});
            
            id++;
        }
        c+=ut.tag('>');
        c+=ut.tag('>');
    }
    
    p.panel.append(c);
    p.panel.find('.'+css.group).slideUp(0);
    
    id=0;
    
    for(i=0;i<d.length;i++){
        chi=d[i].childs;

        for(j=0;j<chi.length;j++){
            tmp=chi[j];
            item=p.panel.find('#'+id);
            tmp.jq=item;
            $.data(item[0],'dat',tmp);
            id++;
        }
    }
    

    
},
_template_culc:function(o,done){
    var a=$.extend(true,{
        file:'',
        vars:[],
        access:'',
        filename:'',
    },o);
    
    Ws.ajax({id:'template_to_code',value:a,
        error:function(){
            popup({type:'alert',msg:'error load template'});
        },
        done:function(data){
            if (data.res==1){
                if(done) done(data);
            }else
                popup({type:'alert',msg:data.msg});
    }});                    

    
},
_set_cursor:function(o){
    if ((nil(o.cursor))||(o.cursor==='')) return;
    var range = o.editor.find(o.cursor);
    if (range){
        o.editor.session.replace(range, '');
    }
},
doclick:function(e){
    var t=Templates,p=t.param,css=p.css;
    var ta=$(e.target);
    
    if (ta.hasClass(css.chapter)||ta.hasClass(css.chapter_icon)){
        
        if (ta.hasClass(css.chapter_icon)) ta = ta.parent();
        
        ta.parent().find('.'+css.group).slideToggle(100,function(){
            var _t=$(this);
            var div=_t.prev().children().last();
            if (JX.visible(_t)){
                div.removeClass(css.chapter_close);
                div.addClass(css.chapter_open);
            }else{
                div.removeClass(css.chapter_open);
                div.addClass(css.chapter_close);
            }
        });
        
    }else
    if (ta.hasClass(css.item)){
        var dat = $.data(ta[0],'dat');
        /*console.info('get template '+dat.path);*/
        
        var ed = editors.current();


        if (!nil(ed)){
            
        
            Ws.ajax({id:'get_template',value:{file:dat.path,access:dat.access,filename:ed.filename},
            error:function(){
                popup({type:'alert',msg:'error load template'});
            },
            done:function(data){
                if (data.res==1){
                    //console.info(data.template);
                    if (data.template.vars.length>0){
                        com_inspector.clear();
                        var pos = JX.abs(Qs.right);
                        com_inspector.show({show:true,pos:{x:pos.x-253,y:pos.y,w:250},caption:dat.caption});
                        com_inspector.add(data.template.vars);
                        com_inspector.assept(function(o){
                            t._template_culc({file:dat.path,vars:o.data,access:dat.access,filename:ed.filename},function(e){
                                ed.editor.insert(e.code);
                                t._set_cursor({editor:ed.editor,cursor:e.cursor});
                                ed.editor.focus();
                            });
                        });/*assept*/
                    }else{
                        t._template_culc({file:dat.path,access:dat.access,filename:ed.filename},function(e){
                                ed.editor.insert(e.code);
                                t._set_cursor({editor:ed.editor,cursor:e.cursor});

                                
                        });                        
                    }

                }else
                    popup({type:'alert',msg:data.msg});
            
            }});
        }else{
            popup({type:'alert',msg:'no opening files!!'});
        }
        
    }
    
},
domousemove:function(){
    
}

};