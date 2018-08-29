/*global $,ut*/
/**
 * JHandler - коллекция обработчиков, 
 * позволяет создавать списки обработчиков 
 * и управлять их выполнением
 * Ex.1 Быстрый старт:
 * 
 * var h = new jhandler();
 * h.add(function(){ console.info("test");});
 * h.do();
 * 
 * Ex.2 Группы обработчиков
 * 
 * var h= new jhandler();
 * h.add({func:function(){console.info("f1");},group:"align"});
 * h.add({func:function(){console.info("f2");},group:"align"});
 * h.add({func:function(){console.info("f3");}}); // by default  group = "common"
 * 
 * h.do("align"); 
 * 
 * ВНИМАНИЕ! По умолчанию JHandler работает с  группой common, поэтому выполнение
 * h.do(); будет эквивалентно h.do('common');
 * Для того что бы были запущены все обработчики, нужно выполнить
 * h.do('all');
 * 
 * 
 * 
 * 
*/ 
function jhandler(){
    var t=this;
    t.groups = {
        common:{
            pause:false,
            funcs:[]
        }
    };
    t._pause=false;
}

jhandler.prototype.destroy=function(){
    this.remove({group:'all'});
};    
/**
 * Добавление обработчика
 * @param o - добавляемая ф-ция   = function | { func:function , name:undef | string , group:undef | string , pause:false } 
*/
jhandler.prototype.add=function(o){ 
    /*function | {func:function,name:undef,group:undef,pause:false}*/
    var t=this,g=t.groups;
    
    if (typeof(o)==='function') o={func:o};
    
    var a=$.extend(true,{
        func:undefined,
        name:ut.id('jhand'),
        group:'common',
        pause:false
    },o);
    
    if (g[a.group]===undefined)
        g[a.group] ={pause:false,funcs:[],lock:0};
        
    g[a.group].funcs.push(a); 
    
    return g[a.group].funcs[g[a.group].funcs.length-1];
};

jhandler.prototype.exist=function(o){
    var t=this,g=t.groups;
    
    if (!("group" in o)) 
        o.group = "common";
    
    if (!("name" in o)) 
        return o.group in g;
        
    var f = g[o.group].funcs;
    
    for(var i=0;i<f.length;i++)
        if (f[i].name===o.name) return true;
    
    return false;
};
jhandler.prototype.lock=function(group){
    var t=this,g=t.groups;
    
    if (g[group]===undefined)
        g[group] ={pause:false,funcs:[],lock:0};
        
    g[group].lock++;    
        
    return g[group].lock===1;/** первая блокировка */    
};    
jhandler.prototype.unLock=function(group){
    var t=this,g=t.groups;
    if (g[group]!==undefined)
        g[group].lock--;
    if (g[group].lock<0) console.error('Stack order error...');    
    return g[group].lock===0;/** первая блокировка */    
};    
jhandler.prototype.isLock=function(group){
    var t=this,g=t.groups;
    if (g[group]!==undefined)
        return (g[group].lock>0);
    
    return false;
};
/*undef | group:string | [] | {group: undefined| string | [] , name:undef | false | string | [] }*/
jhandler.prototype.do=function(o){ 
    
    var t=this,g=t.groups,i,j,h,a,gr;
    
    if (t._pause) return;
    
    
    if ($.isArray(o)||(typeof(o)==='string')){
        a={
            group:o,
            name:false,
            param:undefined,
            error:undefined
        };
    }else{
        a = $.extend(true,{
            group:'common',
            name:false,
            param:undefined,
            error:undefined
        },o);
    }    
    
    if (a.group==='all') a.group = t._groups();
    
    if (typeof(a.name)  ==='string') a.name     =   [a.name];
    if (typeof(a.group) ==='string') a.group    =   [a.group];
    
    for(i=0;i<a.group.length;i++){
        gr = g[a.group[i]];
        if ((gr!==undefined)&&(!gr.pause)&&((gr.lock===undefined)||(gr.lock===0)))
        for(j=0;j<gr.funcs.length;j++){
            h=gr.funcs[j];
            
            if ((!h.pause)&&((!a.name)||(a.name.indexOf(h.name)!==-1))){
                var f = h.func;
                
                try{
                    if (f) f(a.param);
                }catch(e){
                    console.error('JHandler:'+h.name,h.func);
                }

            }             
        }
    }
};    

jhandler.prototype._groups=function(){
    var r=[];
    $.each(this.groups,function(n){r.push(n);});
    return r;
};

/* {group:'all' | string| [] , func:false | string | array} , need:bool*/
jhandler.prototype.pause=function(o,need){ 
    
    var t=this,g=t.groups,i,j,h,a,gr;
    
    if (typeof(o)==='boolean'){
        t._pause = o;
        return;
    }
    
    if ((o===undefined)&&(need === undefined)) 
        return t._pause;
    
    if ($.isArray(o)||(typeof(o)==='string')){
        a={
            group:o,
            name:false,
        };
    }else{
        a = $.extend(true,{
            group:'common',
            name:false
        },o);
    }    

    if (a.group==='all') a.group = t._groups();
    
    if (typeof(a.name)  === 'string') a.name     =   [a.name];
    if (typeof(a.group) === 'string') a.group    =   [a.group];
    
    
    if (o===undefined){ 
        if (need === undefined)
            return t._pause;
        else    
            t._pause = need;

    }        
    
    for(i=0;i<a.group.length;i++){
            
        gr = g[a.group[i]];
        if ((gr!==undefined)){
            if (!a.name){
                
                if (need!==undefined)
                    gr.pause = need;        
                else
                    if (!gr.pause) return false;
                    
            }else for(j=0;j<gr.funcs.length;j++){
                h=gr.funcs[j];
                if (a.name.indexOf(h.name)!==-1){
                    if (need!==undefined)
                        h.pause = need;
                    else
                        if (!h.pause) return false;
                }    
            }
        }
    }
        
    if (need === undefined)
        return true;

};

/**
 * удаляем все обработчики указанные в фильтре  
 * {group:'all' | string| [] ,name:false | string | array}
 */ 
jhandler.prototype.remove=function(o){
    var t=this,i,j,h,gr,g=t.groups;
    
    if (o===undefined) return;
    
    var a=$.extend(true,{
        group:'common',
        name:false
    },o);

    if (typeof(a.name)  === 'string') a.name  = [a.name];
    if (typeof(a.group) === 'string') a.group = [a.group];
    
    
    if (a.group==='all'){
        t.groups={
            common:{
                pause:false,
                funcs:[]
            }
    };;
        return;
    }
    
    for(i=0;i<a.group.length;i++){
        gr = g[a.group[i]];    
        if ((gr!==undefined)){
            if (!a.name)
                t.groups[a.group[i]]={pause:false,funcs:[]};
            else{
                j=gr.funcs.length-1;
                while(j>=0){
                    h=gr.funcs[j];
                    if (a.name.indexOf(h.name)!==-1){
                        gr.funcs.splice(j,1);
                    }
                    j--;
                }
            }    
        }    
    }
    
};