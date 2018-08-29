/**
 * jaction - реализует два варианта actionManager
 * 1. Позволяет создать правило оценки состояния, и в зависимости от рекзультата правила
 * вызывает закрепленные за данным правилом обработчик
 * Ex:
 * 
 * создадим правило для возможности отображения кнопки print
 * jaction.define("print",function(){
 *     return printer.ready();    
 * });
 * привяжем к данному правилу обработчик
 * jaction.bind("print",function(o){
 *    if (o)
 *          $("my").css("color","red");
 *    else 
 *          $("my").css("color","white")
 * })
 * 
 * Перепроверку правила
 * jaction.update() иди jaction.update("print");
 * 
 * 
 * 
 * 
 * 2. с периодичностью jaction.interval, вызывает 
 * сохраненные в него (посредством jaction.add ) обработчики . 
 * Использутся для централизованного управлением видимостью интерфейса.
 *
 * Очень рекомендуется не перегружать обработчики!!!
 *
 * Ex.
 *   jaction.add({
 *      name:"func",
 *      func:function(){
 *            
 *      }
 *   });
 *   
 *   jaction.enable = true;
 *   
 *   jaction.remove({name:"func"});
 * 
 * 
 * jaction.add({
 *      name:string,
 *      group:string,
 *      func:function(){
 *                    
 *      },
 *      pause:true,
 * });
 * 
 * 
 * {group:'all' | string| [] , func:false | string | array}
 * jaction.remove(name|[name,name...]|all)
 * 
*/
var jaction={
 
 interval:1000,
 enable:true,
 funcs : new jhandler(),
 _define:[],
 

/**
 * Добавление обработчика
 * @param o - добавляемая ф-ция   = function | { func:function , name:undef | string , group:undef | string , pause:false } 
*/
add:function(o){
    var t=this;
    if( (typeof o !=="function") && ("name" in o) && (t.funcs.exist(o))){
        console.error("["+o.name+"] func already exists!");
        return false;
    }
        
        
    t.funcs.add(o);
    t.once();
    return true;
},
once:function(){
    var t=this;
    if (!t._once){
        
        t._once = true;
        t._action();      
        
    }    
},
/** внеочередной запуск менеджера */
do:function(){
    this.funcs.do("all");
},
_action:function(){
    var t=this;
    

    if (t.timer===undefined){
        t.timer = setTimeout(function(){
            
            if (t.enable)
                t.funcs.do("all");
                
                
            
            t.timer = undefined;
            t._action();
        },t.interval);
    }   
},
/* {group:'all' | string| [] , name:false | string | array} , need:bool*/
pause:function(o,need){ 
    this.funcs.pause(o,need);
},
/**
 * удаляем все обработчики указанные в фильтре  
 * {group:'all' | string| [] , func:false | string | array}
 */ 
remove:function(o){
    this.funcs.remove(o);
},

_item:function(o,asindex){
    var t=this,i,b,type=typeof o;    
    for(i=0;i<t._define.length;i++){
        
        if ((type==='string')&&(t._define[i].name===o))
            return asindex===true?i:t._define[i];
        
        if ((type==='number')&&(i===o))
            return asindex===true?i:t._define[i];

    }
    return undefined;
},
_bind:function(bind,name,asindex){
    var i,b;
    for(i=0;i<bind.length;i++){
        b = bind[i];
        if(b.name === name)
            return (asindex===true?i:b);
    }
    return undefined;
},
_eq:function(a,b){
    var type = typeof a,i,key;
    
    try{
        if ((type==='string')||(type==='number')||(type==='boolean'))
            return a===b;

        if (type==="object"){
            if ($.isArray(a)){
                if (a.length===b.length){
                    for(i=0;i<a.length;i++){
                        if (a[i]!==b[i])
                            return false;
                    }
                    return true;
                }    
                
                    
            }else{
                for (key in a){    
                    if (!(key in b)||(a[key]!==b[key]))
                        return false;    
                }    
                for (key in b){    
                    if (!(key in a)||(a[key]!==b[key]))
                        return false;    
                }
                return true;
            }    
        }
    }catch(err){
        
    }
    return false;
},
update:function(name,forced,to){
    if (typeof zAction!=='undefined') zAction.update();
    
    var t = this,i,d;
    
    if (to===undefined) to='cond';
    
    
    var binds=function(def){
        var j,b,cond;
        if (def.func){
            
            try{
                cond = def.func();
                
            }catch(err){
                cond = false;
            }
            
            if ((forced===true)||(( to==='cond') && (!t._eq(cond,def.prev)))||(to!=='cond')){
                for(j=0;j<def.bind.length;j++){
                    b = def.bind[j];
                    
                    if ((b.to==='all')||(b.to===to)){ 
                        try{
                            if (b.func) 
                                b.func(cond);
                        }catch(err){
                        }
                    }    
                    
                }
                def.prev = cond;
            }
        }
    };
    
    if (name===undefined){
        for(i=0;i<t._define.length;i++){
            d = t._define[i];
            binds(d);
        }
    }else{
        d = t._item(name);
        if (d!==undefined)
            binds(d);    
        
    }
},
run:function(name,params){
    var t = this,d=t._item(name);
    if ((d!==undefined)&&(d.run)){
        d.run(params);
        t.update(name,true,'run');
    }    
},
/** определяет условие action */
define:function(name,cond,run,bind){
    var t = this,d=t._item(name);
    
    if (d===undefined){
        d = {name:name,func:cond,prev:undefined,bind:[],run:run};
        t._define.push(d);
    }else{
        
        if (cond!==undefined)
            d.func = cond;
            
        if (run!==undefined)
            d.run =run;
    }
    
    if (typeof bind ==='function')
        t.bind(name,bind);
    

    return d;
},
/** возвращает результат условия */
defined:function(name){
    var t = this,d=t._item(name);
    
    if ((d!==undefined)&&(d.func))
            return d.func();
    return undefined;        
},
/** удаляет условие */
undefine:function(name){
    var t = this,d=t._item(name,true);
    
    if (d!==undefined)
        t._define.splice(d,1);    
    
},
/** name | fromName,name*/
unbind:function(/*name | fromName,name*/){ 
    var t = this,d,b,i,idx,name,fromName;
    
    if(arguments.length===1){
        name = arguments[0];
        for(i=0;i<t._define.length;i++)
            t.unbind(t._define[i].name,name);    
        
    }else if(arguments.length>1){
        name    = arguments[1];
        fromName = arguments[0];
        
        d=t._item(fromName);
        idx = t._bind(d.bind,name,true);
        if (idx!==undefined)
            d.bind.splice(idx,1);
    };
},
bind:function(toName,o){
    var t = this,d=t._item(toName),i;
    /*
    if (typeof o === 'undefined'){
        if (d===undefined) return;
        for(i=0;i<d.bind.length;i++)
            try{d.bind[i].func()}catch(e){};
        return
    }
    */    

    if (typeof o === 'function')
        o = {func:o};
        
    if (typeof o!=='object') 
        return false;
        
    /** to= cond| run| all*/    
    var a=$.extend(true,{
        func:undefined,
        name:ut.id('fun'),
        to:'cond'
    },o);    
    
    if (d===undefined)
        d = t.define(toName);
    
    var b = t._bind(d.bind,a.name);
    
    if (b===undefined)
        d.bind.push(a);
    else
        b.func = a.func;
    
    return a.name;    
        
}

};