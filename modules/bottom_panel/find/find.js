/*global $,Qs,ut,JX,Ws,editors,popup*/
var Find={
param:{},
init:function(o){
    var t=Find,c;
    t.param = $.extend(true,
    {
        item:Qs.find,
        own:Qs.find.btn_panel,
        input:null,
        btn:null,
        _loop:false,
        MAX_FIND_COUNT:1000,/*see MAX_FIND_COUNt in find.php*/
        css:{
            panel:'find_panel',
            file:'find_file',
            list:'find_list',
            btn:'find_btn',
            clear:'find_clear',
            str:'find_str',
            input:'find_input',
            match:'find_match',
            process_frame:'find_process_frame',
            process:'find_process',
            select:'find_select'
            
        }
    },o);
    
    var p=t.param,
    input_id = "find_input",
    ext_id = "find_ext",
    process_frame_id = "find_frame_process",
    process_id = "find_process",
    path_id = "path_find",
    btn_id="find_btn",
    clear_id="clear_btn";
    
    c = ut.tag({id:input_id,tag:'input',css:'edit '+p.css.input,attr:{type:'text',placeholder:'regexp',autocomplete:"off" },style:'position:absolute'});
    c+= ut.tag({id:ext_id,tag:'input',css:'edit',attr:{type:'text',placeholder:'ext',list:'ext_default'},value:'php,js',style:'position:absolute',pos:{w:70}});
    c+= ut.tag('<',{tag:'datalist',id:'ext_default'});
    c+= ut.tag({tag:'option',value:'php'});
    c+= ut.tag({tag:'option',value:'js'});
    c+= ut.tag({tag:'option',value:'css,dcss'});
    c+= ut.tag('>',{tag:'datalist'});
    c+= ut.tag({id:path_id,tag:'input',css:'edit '+p.css.input,attr:{type:'text',placeholder:'root',autocomplete:"off" },style:'position:absolute'});
    c+= ut.tag({id:btn_id,css:p.css.btn,style:'position:absolute',pos:{w:28}});
    c+= ut.tag({id:clear_id,css:p.css.clear,style:'position:absolute',pos:{w:28}});
    
    c+= ut.tag('<',{id:process_frame_id,style:'border:1px solid rgbs(0,0,0,0);position:absolute;',pos:{w:50,h:13}});
        c+= ut.tag({id:process_id,style:'border:0px;position:absolute',css:p.css.process,pos:{h:13,w:0}});
    c+=ut.tag('>');
    
    p.own.append(c);
    
    p.input     = p.own.find('#'+input_id);
    p.btn       = p.own.find('#'+btn_id);
    p.clear     = p.own.find('#'+clear_id);
    p.btn.height(p.input.height()+2);
    p.clear.height(p.input.height()+2);
    
    p.exts      = p.own.find('#'+ext_id);
    p.process   = p.own.find('#'+process_id);
    p.path      = p.own.find('#'+path_id);
    
    p.item.panel.addClass(p.css.panel);

    
    t.align();

    Ws.align(function(){t.align();});
    t._event();
},
path:function(path){
    var t=Find,p=t.param;
    if (path === undefined)
        return p.path.val();
    else    
        return p.path.val(path);
},
align:function(){
    var t=Find,p=t.param;
    JX.arrange(p.item.btn_panel.children(),{direct:"horiz",type:"stretch",align:"center",stretch:[{idx:0}],gap:3,margin:{right:5}});
},
_out_find_result:function(o){
    var t=Find,p=t.param,css=p.css,panel = p.item.panel,c='',i,
    a = $.extend(true,{
        file:'',
        list:[]
    },o);
    if (!a.list.length) return;

    var cnt = Math.min(a.list.length,p.MAX_FIND_COUNT-p._all);
    
    
    c+=ut.tag({value:a.file.path,css:css.file});
    c+=ut.tag('<',{css:css.list});
    var id_start = p._id;
    for(i=0;i<cnt;i++){
        p._all++;
        var L=a.list[i];
        L.filename = a.file.path;
        var s = ut.fill((L.line+1)+':',{len:7,char:"&nbsp;",left:false});
        s+=L.left+'<span class="'+css.match+'">'+L.find+'</span>'+L.right;
        c+=ut.tag({id:p._id,value:s,css:css.str});
        p._id++;
    }
    c+=ut.tag('>');
    
    panel.append(c);
    
    for(i=0;i<cnt;i++){
        var jq = panel.find("#"+(i+id_start));
        $.data(jq[0],'dat',a.list[i]);
    }
    
    if (p.MAX_FIND_COUNT<=p._all){
        p.item.panel.append(ut.tag({value:'and more....'}));
        popup({type:"alert",msg:'finded not all..'});
    }    
    
    
},
_process:function(current,all){
    var t=Find,p=t.param,css=p.css;
    
    if (current==0){
        JX.visible(p.process,false);
        p.process.parent().removeClass(css.process_frame);
    }else{
        p.process.parent().addClass(css.process_frame);
        var w = (current/all)*100+'%';
        p.process.css('width',w);    
        JX.visible(p.process,true);
        
    }
    
    
},
_findi:function(){
    var t=Find,p=t.param;
    
    Ws.ajax({
        id:'find',
        value:{
            key:p._key,
            file:p._files[p._i].path
        },
        context:p._files[p._i],
        error:
        function(){
            
        },
        done:
        function(data,id,context){
            
            if (data.res==1)
                t._out_find_result({file:context,list:data.finds});
            else
                console.error(data);
            
            p._i++;
            
            t._process(p._i,p._count);
            
            if ((p._i<p._count)&&(p.MAX_FIND_COUNT>p._all))
                setTimeout(t._findi,2);
            else
                setTimeout(function(){t._process(0);},1000);        
        }
    });

},    
_find:function(files){
    var t=Find,p=t.param;
    if (arguments.length==1){
        p._all = 0;
        p._i = 0;
        p._count = files.length;
        p._files = files;
        p._id=0;
        
    }
    setTimeout(t._findi,2);
},
_clear:function(){
    var t=Find,p=t.param;
    p.item.panel.html('');
},
_go:function(){
    var t=Find,p=t.param;
    p._key = p.input.val();
    if (p._key!==''){
        p.item.panel.html('');

        Ws.ajax({
            id:'get_dir_list',
            value:{
                ext:p.exts.val(),
                key:p._key,
                path:t.path(),
            },
            error:
            function(e){
                popup({type:'alert',msg:'Error in regular expression:  "'+p._key+'"'});
            },
            done:
            function(data){
                if (data.res==1){
                    if (data.files.length>0)
                        t._find(data.files);
                    else
                        p.item.panel.append(ut.tag({value:'no match'}));
                    
                }else
                    console.error(data);
            }
        });
    }
},
_event:function(){
    var t=Find,p=t.param,css=p.css;
    
    p.btn.on("click",t._go);
    p.clear.on("click",t._clear);
    
    p.input.keypress(function(e){if(e.which == 13) t._go();}); 
    p.exts.keypress(function(e){if(e.which == 13) t._go();}); 
    
    p.item.panel.on("click",function(e){
        var tar = $(e.target),obj=false;
        
        if (tar.hasClass(css.str)) obj = tar;
        if (tar.hasClass(css.match)) obj = tar.parent();
        
        if (obj){
            
            var list = p.item.panel.find('.'+css.list);
            list.find('.'+css.str).removeClass(css.select);
            obj.addClass(css.select);
            
            var dat = $.data(obj[0],'dat');
            
            //var node=[{id:dat.filename}]
            editors.add({filename:dat.filename,done:
            function(o){
                if (o.was_open)
                    o.item.editor.gotoLine(dat.line+1,dat.pos);
                else
                    setTimeout(function(){o.item.editor.gotoLine(dat.line+1,dat.pos);},500);
            }});
        }
    })
}
        
};