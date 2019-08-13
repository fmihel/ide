/*global JX,$*/
var Struct={
th:null,
enable:true,
_lock:false,
current:{code:"",dat:[]},
_new:{code:"",dat:[]},
css:{
    
    func_block:"func_block",
    func_block_hover:"func_block_hover",
    func_block_select:"func_block_select",
    func_block_class:"func_block_class",
    func_block_func:"func_block_func",
    func_block_up:"func_block_up",
    func_block_down:"func_block_down"
    
},
_update:function(){
    /*global THREAD*/
    var t = Struct;
    
    t.th = THREAD({
        forced:100,
        suspend:false,
        onstart:t._start,
        func:t._step,
        onstop:function(){t.th=null;}
    });
},
_start:function(data){
    /*global Qs,editors*/
    var t = Struct;
    data._lock = true;
    
    if (!t.panel) {
        t.frame = Qs.Struct.panel;
        Qs.Struct.panel.addClass('ws_scrollbar').css({'overflow-y':'auto','overflow-x':'hidden'});
        t.panel=Qs.Struct.panel;
        t.panel.on("click",t.doclick);
        t.panel.on("mousemove",t.domousemove);
                
    }    
    /*t.panel.html("");*/
    
    
    data.item =editors.current();
    data.val =  data.item.editor.getValue().split("\n");
    data.i = 0;
    data.count = data.val.length;
    data.code = "";
    data.rem=false;
    
},
_step:function (data,th,event){
    /*global $,ut*/
    var t = Struct,ext=data.item.ext,p=t.panel,div;
    var code=false; 

    var str = data.val[data.i].replace(/\/\/([\s\S]*)$/,"").replace(/\/\*([\s\S]*)\*\//,"");
    /*remove comments on C style*/
    var idxL = -1,idxR=-1;
    var L = "\/\*";
    var R = "\*\/";
    var name = '';
    if (!data.rem){
        idxL=str.indexOf(L);
        if (idxL !=-1){
            idxR=str.indexOf(R);
            if (idxR!=-1)
                str = str.substr(0,idxL)+str.substr(idxR+2);
            else
                data.rem = true;
        }
    }else{
        idxR=str.indexOf(R);
        if (idxR!=-1){
            data.rem = false;
            str = str.substr(idxR+2);
        }
    }
    /*----------------------------*/                
    if (event=='start'){
        //p.append(ut.tag({css:t.css.func_block+' '+t.css.func_block_up}));
        //div = p.children().last();
        //$.data(div[0],'dat',{line:0,editor:data.item.editor,div:div});
        t._new.code = "";
        t._new.dat=[];
    }
    /*----------------------------*/                
    
    if (!data.rem){    
    
    if(ext=='php'){
        if( /^\s*class\s*/.test(str)){
            name = str.replace(/\/\/([\s\S]*)$/,"").replace(/\/\*([\s\S]*)\*\//,"").replace(/(\s*|)class(\s*|)/,"").replace(/\s[\s\S]*$/,"").replace('{',"");
            code = ut.tag({css:t.css.func_block+' '+t.css.func_block_class,value:name});                                    
        }else if( /^[\s\S]*\s*function\s/.test(str)){ 
            name = str.replace(/^.*function\s/,"").replace(/\(([\s\S]*)$/,"");                    
            code=ut.tag({css:t.css.func_block+' '+t.css.func_block_func,value:name});                                                        
        }
    }else if(ext=='jsx'){
        if( /^\s*class\s*/.test(str)){
            name = str.replace(/\/\/([\s\S]*)$/,"").replace(/\/\*([\s\S]*)\*\//,"").replace(/(\s*|)class(\s*|)/,"").replace(/\s[\s\S]*$/,"").replace('{',"");
            code = ut.tag({css:t.css.func_block+' '+t.css.func_block_class,value:name});                                    
        }else if (/^\s*([\s\S]*)\s*\([\s\S]*\)\s*\{/.test(str)){
            name = str.replace(/\([\s\S]*$/,"");
            code=ut.tag({css:t.css.func_block+' '+t.css.func_block_func,value:name});                                                        
        };
    }
    else if(ext=='js'){
        
        if (/^\s*var\s.([\s\S]*)\s*=\s*{/.test(str)){
            /*var OBJECT = {...*/        
            if ((str.indexOf(',')==-1)&&(str.indexOf('}')==-1)){        
            name = str.replace(/\s*var\s*/,"").replace(/\s*=\s*{\s*/,"");
            code=ut.tag({css:t.css.func_block+' '+t.css.func_block_class,value:name});
            }
        }else if (/^\s*([\s\S]*)\.([\s\S]*)\s*=\s*function/.test(str)){
            /* OBJECT.prototype.name = function()...*/            
            name = str.replace(/\s*=\s*function([\s\S]*)$/,"").replace(/^([\s\S]*)\./,"");
            code=ut.tag({css:t.css.func_block+' '+t.css.func_block_func,value:name});
                        
        }else if (/^\s*([\s\S]*)\s*:\s*function/.test(str)){
            /* name:function()...*/            
            name = str.replace(/\s*:\s*function([\s\S]*)$/,"");
            code=ut.tag({css:t.css.func_block+' '+t.css.func_block_func,value:name});
        }else if (/^\s*\(function\(\s*\$\s*\)/.test(str)){
            //name = js_plugin_name(file,i+1);
            //func.append("<div class="+UTILS.KOV("func_class")+"onclick="+UTILS.KOV("js_ed_move("+i+")")+">plugin "+name+"</div>");
        }else if( /^\s*(\s*)\s*function\s/.test(str)){ 
            /* function name()...*/            
            name = str.replace(/^.*function\s/,"");                    
            name = name.replace(/\(([\s\S]*)$/,"");
            if(t.js_name_is_class(name,data.val,data.i+1))
                code=ut.tag({css:t.css.func_block+' '+t.css.func_block_class,value:name});
            else
                code=ut.tag({css:t.css.func_block+' '+t.css.func_block_func,value:name});
        }else if( /^\s*class\s*/.test(str)){
            name = str.replace(/\/\/([\s\S]*)$/,"").replace(/\/\*([\s\S]*)\*\//,"").replace(/(\s*|)class(\s*|)/,"").replace(/\s[\s\S]*$/,"").replace('{',"");
            code = ut.tag({css:t.css.func_block+' '+t.css.func_block_class,value:name});    
            // \s*(?<!if|while|switch|function)\(                                
        }else if (/^\s*(\w)+(?<!(if|while|function|switch))\s*\([\w\,\s,=,\"\'\`\{\}\[\]\:\$\_\-\+\/\*\!"]*\)\s*\{/.test(str)){
            name = str.replace(/\([\s\S]*$/,"");
            code=ut.tag({css:t.css.func_block+' '+t.css.func_block_func,value:name});                                                        
        }            


    }else if((ext=='css')||(ext=='dcss')){
         if(/[\S*\s]*{/g.test(str)){
            name = str.replace('{',"")
            code=ut.tag({css:t.css.func_block+' '+t.css.func_block_class,value:name});
         }    
    }
    }/*if (!data.rem){*/
    
    if(code){
        //p.append(code);
        //div = p.children().last();
        //$.data(div[0],'dat',{line:data.i+1,editor:data.item.editor,div:div});
        t._new.code+=code;
        t._new.dat.push({line:data.i+1,editor:data.item.editor});
    }
    
    data.i++;
    if(data.i>=data.count){

        //p.append(ut.tag({css:t.css.func_block+' '+t.css.func_block_down}));
        //div = p.children().last();
        //$.data(div[0],'dat',{line:data.count,editor:data.item.editor,div:div});
        if (t._new.code!==t.current.code){
            p.html(t._new.code);
            t.current.code = t._new.code;
            t.prevCursor={row:0,column:0};
        }
        
        $.each(p.children(),function(i,o){
            t._new.dat[i].div = $(o);
            $.data(o,'dat',t._new.dat[i]);
        });
        
        t.current.dat = t._new.dat; 


        t.th.stop();
        data._lock = false;
        
    }
},

js_name_is_class:function(name,file,i){
    var name2 = '';
    for(var j = i;j<file.length;j++){
        name2 = file[j].replace(/\.([\s\S]*)\s*=\s*function([\s\S]*)$/,"").replace(/^\s*/,"");
        if( name==name2)return true;
    }
    return false;
},
doclick:function(o){
    var t=Struct,ot=$(o.target);
    if(ot.hasClass(t.css.func_block)){
        
        t._lock = true;
        
        var dat=$.data(ot[0],'dat');
        dat.editor.gotoLine(dat.line,0,false);
        //dat.editor.selection.selectLine();
        //dat.editor.moveCursorTo(dat.line-1,0);
        
        t.panel.find("."+t.css.func_block_select).not(ot).removeClass(t.css.func_block_select);
        ot.addClass(t.css.func_block_select);
        
        t.prevCursor={row:dat.editor.row,column:dat.editor.column};
        t._lock = false;
    }
},
domousemove:function(o){
    var t=Struct,ot=$(o.target);
    if(ot.hasClass(t.css.func_block)){
        
        t.panel.find("."+t.css.func_block_hover).not(ot).removeClass(t.css.func_block_hover);
        ot.addClass(t.css.func_block_hover);
    }
},
dochange:function(o){
    
},
doresize:function(o){
    return;
    /*
    var t=Struct;
    if (!t.panel) return;
    var b=JX.pos(t.panel);
    var p = JX.pos(t.panel.parent());
        p={x:10,y:10,w:p.w-20,h:p.h-20};
    var p1 = {};
    (b.x!==p.x?p1.x=p.x:0);
    (b.y!==p.y?p1.y=p.y:0);
    (b.w!==p.w?p1.w=p.w:0);
    (b.h!==p.h?p1.h=p.h:0);
    
    JX.pos(t.panel,p1);
    */
    
},
_update_panel:function(div){
    var t=Struct,css=t.css,clas={name:'',dat:null},func={name:'',dat:null};
    
    if (div.hasClass(css.func_block_class)){
        clas.name=div.text();
        clas.dat=$.data(div[0],'dat');
    }else{
        func.name=div.text(); 
        func.dat=$.data(div[0],'dat');
        
        var prev = div.prev();
        while (prev.length>0){
            if (prev.hasClass(css.func_block_class)){
                clas.name=prev.text();
                clas.dat=$.data(prev[0],'dat');
                
                break;
            }
            prev=prev.prev();
        }
    }
    
    editors.panel({clas:clas,func:func});
      
},    
doaction:function(o){
    var t=Struct,cur=o.item.editor.getCursorPosition();
    if (t._lock) return;
    if(!t.panel) return;
    
    if ((!t.prevCursor)||(t.prevCursor.row!==cur.row)||(t.prevCursor.column!==cur.column)){
        t.prevCursor={row:cur.row,column:cur.column};
        
        var find = null;
        var c=t.panel.children();
        $.each(c,function(i,_o){
            var d=$.data(_o,'dat');
            if (d.line==cur.row+1){ 
                find = d;
                return true;
            }
            
            if (d.line>cur.row+1) 
                return true;
            find = d;
        });
        
        if (find){
            
            
            t.panel.find("."+t.css.func_block_select).not(find.div).removeClass(t.css.func_block_select);
            find.div.addClass(t.css.func_block_select);
            
            t._update_panel(find.div);
            
            var p1 = JX.pos(t.panel);
            var p2 = JX.pos(find.div);
            if (!JX.inline(p2.y-t.panel.scrollTop(),0,p1.h))
                t.panel.scrollTop(JX.pos(find.div).y);
            
        }
        
    }
    
},
stop:function(){
    var t = Struct;
    if (t.th!==null)
        t.th.stop();
},
update:function(){
    var t = Struct;
    if (t.th !==null){
        t.th.param.onstop=t._update;
        t.th.stop();
    }else
        t._update();
}
};
