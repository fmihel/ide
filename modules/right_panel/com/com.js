/*global THREAD,Qs,editors,ut,$,JX*/
var Coms={
_dupdate:false,
th:null,

current:{code:"",dat:[]},
_new:{code:"",dat:[]},
_func:{code:"",dat:null},

_lock:false,
css:{
    
    block:"com_block",
    block_select:"com_block_select",

    block_class:"com_block_class",
    block_func:"com_block_func",
    block_com:"com_block_com"
    

},
/*c-condition,e-[extract,index of match],css-class (redefined) i-icon,n-notes*/
rule:{},
funcs:{},
funcs1:{ 
    
    php:[
        {c:/^\s*class\s*/,e:[/class\s+([^\s]+)(\s+extends|(\s)*)/,1],css:"com_block_class"},
        {c:/function\s+([^\s]*)\s*\([\s\S]*\)/,e:[/function\s+([^\s]*)\s*\([\s\S]*\)/,1],css:"com_block_func"},
        ]
    
},        
rule1:{ /*c-condition,e-[extract,index of match],css-class (redefined) i-icon,n-notes*/
    php:[
        {c:/\/\*(c|com)\:[a-zA-Z,0-9,_]+[\s\S]*\*\//,e:[/(c|com)\:([^\s]*)(\s(.*)|\*\/)$/,2]},
        {c:/FRAME\([\s\S]+\,/,e:[/FRAME\(\s*['"]*([^\s'"]+[^"'])['"]*\,/,1]},
        {c:/COMPONENT\(\s*new/,e:[/COMPONENT\(\s*new\s+\w*\(('|")(\w*)('|")/,2]},
        ]
},
addRules:function(add){
    var t=Coms,rule = t.rule,i;
    
    $.each(add,function(key){
        if (rule[key]===undefined)
            rule[key]=[];
            
        for(i=0;i<add[key].length;i++)
            rule[key].push(add[key][i]);        
    });
},
addFuncs:function(add){
    var t=Coms,func = t.funcs,i;
    
    $.each(add,function(key){
        if (func[key]===undefined)
            func[key]=[];
            
        for(i=0;i<add[key].length;i++)
            func[key].push(add[key][i]);        
    });
    
    
},

doClick:function(o){
    var t=Coms,ot=$(o.target);
    if(ot.hasClass(t.css.block)){
        
        var dat=$.data(ot[0],'dat');
        dat.editor.gotoLine(dat.line,0,false);
        //dat.editor.selection.selectLine();
        //dat.editor.moveCursorTo(dat.line-1,0);
        
        t.panel.find("."+t.css.block_select).not(ot).removeClass(t.css.block_select);
        ot.addClass(t.css.block_select);
        
        t.prevCursor={row:dat.editor.row,column:dat.editor.column};
    }
    
},
doAction:function(o){
    var t=Coms,cur=o.item.editor.getCursorPosition();
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

            t.panel.find("."+t.css.block_select).not(find.div).removeClass(t.css.block_select);
            find.div.addClass(t.css.block_select);
            
            var p1 = JX.pos(t.panel);
            var p2 = JX.pos(find.div);
            if (!JX.inline(p2.y-t.panel.scrollTop(),0,p1.h))
                t.panel.scrollTop(JX.pos(find.div).y);
            
        }
        
    }
    
},

doMousemove:function(){
    
},
_update:function(){
    var t = Coms,
    run = function(){
        t.th = THREAD({
            forced:100,
            suspend:false,
            onstart:t._start,
            func:t._step,
            onstop:t._stop,
        });
        
    };    

    if (t.th !==null){
        t.th.param.onstop=run;
        t.th.stop();
    }else
        run();    
    
    
    
},
update:function(){
    var t=Coms;
    if (t._dupdate)
        clearTimeout(t._dupdate);
    t._dupdate=setTimeout(function(){
        t._dupdate=false;
        t._update();
    },1000);    
},
_start:function(data){
    var t = Coms;
    data._lock = true;
    
    if (!t.panel) {
        t.frame = Qs.Coms.panel;
        Qs.Coms.panel.addClass('ws_scrollbar').css({'overflow-y':'auto','overflow-x':'hidden'});
        t.panel=Qs.Coms.panel;
        t.panel.on("click",t.doClick);
        t.panel.on("mousemove",t.doMousemove);
        
    }    
    
    
    data.item =editors.current();
    data.val =  data.item.editor.getValue().split("\n");
    data.i = 0;
    data.count = data.val.length;
    data.code = "";
    data.rem=false;    
},
_step:function(data,th,event){
    var t = Coms,ext=data.item.ext,p=t.panel,rule = t.rule,func=t.funcs,f,i,name,r,css=t.css,finded=false;
    var code=false; 

    var str = data.val[data.i];
    
    /*----------------------------*/                
    if (event=='start'){
        t._new.code = "";
        t._new.dat = [];
        t._func.code = "";
        t._func.dat = null;
    }
    /*----------------------------*/                
    
    if(ext=='php'){
        
        finded = false;
        for(i=0;i<func.php.length;i++){
            f = func.php[i];
            if (str.match(f.c)){
                name = t._match(str,f.e[0],f.e[1]);
                if (name){
                    code = ut.tag({value:(f.pref?f.pref+" ":'')+name,css:(css.block+" "+(f.css?f.css:css.block_func))});
                    finded = true;
                    break;
                }    
            }
        }    

        if (!finded)
        for(i=0;i<rule.php.length;i++){
            r = rule.php[i];
            if (str.match(r.c)){
                name = t._match(str,r.e[0],r.e[1]);
                if (name){
                    code = ut.tag({value:(r.pref?r.pref+" ":'')+name,css:(css.block+" "+(r.css?r.css:css.block_com))});
                    break;
                }    
            }
        }    
        
    }else if(ext=='js'){
    }else if((ext=='css')||(ext=='dcss')){

    }/*if (!data.rem){*/
    
    if (finded){
        t._func.code=code;
        t._func.dat={line:data.i+1,editor:data.item.editor};
        
    }else if(code){
        
        if (t._func.dat!==null){
            t._new.code+=t._func.code;
            t._new.dat.push(t._func.dat);
            
            t._func.code = "";
            t._func.dat = null;
        }
        
        t._new.code+=code;
        t._new.dat.push({line:data.i+1,editor:data.item.editor});
    }
    
    data.i++;
    
    if(data.i>=data.count){
        
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
    }    
},
_stop:function(){
   var t=Coms;
   t.th=null;
},

_match:function(str,reg,idx){
    let m;
    if ((m = reg.exec(str)) !== null) 
        return m[idx];
    return false;
}
};