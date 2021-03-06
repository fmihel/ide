/**
 * ActionManager для проекта. Позволет создавать именованые action(действия - процедуры), осуществлять их централизованный запуск
 * и определять ф-ции проверки состояния для них. 
 * Так же , для каждого определимого action можно добавить список действий, которые произойдут непосредственно после выполнения action.
 * 
 * Ex: Определение действия:
 * 
 * zAction.define({
 *      name:"print",
 *      action:function(){
 *          printer.do();  
 *      },
 *      state:function(){
 *          return printer.ready();
 *      }
 *  );
 * 
 * Ex: Вызов action
 * zAction("print");
 * 
 * Ex: Определение действия на момент выполнения action
 * zAction.bind({
 *      on:"action",
 *      to:"print",
 *      funcName:"afterPrintDo"
 *      func:function(){
 *          console.info("...");
 *      }
 *  })
 * Данное действие, будет выполнено автоматически, сразу после вызова 
 * zAction("print")
 * 
 * Ex: Определение ф-ции состояния ( к примеру для подсветки кнопки печати, в зависимости от готовности принтера)
 * ВНИМАНИЕ! Результат ф-ции состояния никак не влияет на вызов action. Для проверки состояния используйте zAction.state(string) - 
 * которая вернет текущее последнее(для обновления состояния необходимо вызвать update или state(string,true) состояние action 
 *  
 * zAction.define({
 *      name:"print",
 *      state:function(){
 *          return printer.ready();
 *      }
 *  );
 * 
 * 
 *   
 */ 

function zAction(name,param){
    return zAction.do({name:name,param:param});        
}

zAction.param = {
    action:[],
    lock:{
        'update':0,
        'do':0
    },
    update:[]
};
/**
 * определение action и/или ф-ции состояния
 * o = {
 *  
 *      name:string                 - имя определяемого action
 *      action:function|undefined   - непосредственно action
 *      state: function|undefined   - ф-ция возващающая текущее состояние для action 
 * }
 */ 
zAction.define=function(o){
    var t=zAction,p=t.param,ex;
    
    a = $.extend(false,{
        name:false,
        action:undefined,
        state:undefined,
        prevState:undefined,
        done:undefined,
        on:{
            action:[],
            state:[],
        },
    },o);
    
    if (a.name===false) return false;
    
    g = t._get(a.name);
    
    if (g===false){
        p.action.push(a);
        return a;
    }else{
        
        if ((a.action!==undefined)&&(g.action===undefined))
            g.action = a.action;

        if ((a.state!==undefined)&&(g.state===undefined)){
            g._firstCallState = false;
            g.state = a.state;
        }    
        if ((a.done!==undefined)&&(g.done===undefined))
            g.done = a.done;
        
        g.prevState = a.prevState;
        
        a.on.action.forEach(function(v,i,ar){
            ex = t._get(v.name,false,g.on.action);
            if (ex) 
                ex.func = v.func;
            else
                g.on.action.push(v);
        });
        
        
        a.on.state.forEach(function(v,i,ar){
            ex = t._get(v.name,false,g.on.state);
            if (ex) 
                ex.func = v.func;
            else
                g.on.state.push(v);
        });
        
        return g;
    }
};

/**
 * Привязка ф-ции к action или state
 * o = function  - добавляет обработчик, который возникает на каждый вызов update
 *   or 
 * o={ 
 *      on:string,          "action"|"state"
 *      to:string,          name of action
 *      funcName:"string"   name of defined func, may be empty
 *      func:function       handler
 * }
 * 
 */
 zAction.bind=function(o){
    let t=zAction,
        p=t.param;
    
    if (typeof o ==='function'){
        
        if (p.update.indexOf(o)<0)
            p.update.push(o);
        
    }else{
        
        let g=t._get(o.to);
    
        if (g===false)
            g = t.define({name:o.to});
    
        if (g===false) return false;
    
        if (o.funcName===undefined)  o.funcName = t._id('bind');
    
        let on = t._get(o.funcName,false,g.on[o.on]);
    
        if (!on)
            g.on[o.on].push({name:o.funcName,func:o.func});    
        else
            on.func = o.func;
        
        if ((o.on==='state')&&(o.func)&&(g._firstCallState))
                o.func(g.prevState);
    }            
};
/** удаление привязанного обработчика 
 * @param {string} actionName - имя action
 * @param {string|undefined} on - к чему относится (action | state), если не указан, то будут удалены из всех категорий
 * @param {string|undefined} funcName - имя функции если не указать, то будут удалены все из секции on
*/
zAction.unbind=function(actionName,on,funcName){
    let t=zAction,p=t.param,_on;
    
    if (typeof actionName ==='function'){
        let idx = p.update.indexOf(actionName);
        if (idx>=0)
            p.update.splice(idx,1);
    }else{
        let g = t._get(actionName);
    
        if (g){
            if (on===undefined){
                $.each(g.on,function(n){
                    g.on[n] = [];
                });
                return;
            }
            if (funcName===undefined){
                g.on[on]=[];
                return;
            }
            _on = t._get(funcName,true,g.on[on]);
            if (_on!==false)
                g.on[on].splice(_on,1);
        
        }
    }    
};

/** пооечередно вызывает все определенные ф-ции state , и если состояние изменилось, то вызываются все приаттаченные (bind) обработчики*/
zAction.update=function(name,done){
    var t=zAction,p=t.param,a=p.action,newState,updNeed=false,updActions=[];
    
    if (typeof name==='function'){
        done = name;
        name = undefined;
    }
    
    if (t._lock('update')){
        
        a.forEach(function(v){
            //console.time('update start '+v.name);
            if (((name===undefined)||(v.name===name))&&(v.state)){
                
                newState =v.state(v.prevState);
                v._firstCallState = true;
                
                if (!t._eq(newState,v.prevState)){
                    updNeed = true;
                    updActions.push(v.name);
                    v.prevState = (typeof newState === "object")?$.extend(false,newState,{}):newState;
                    v.on.state.forEach(function(vv){
                        if (vv.func) try{ vv.func(newState);}catch(e){}    
                    });
                    
                    if (v.done) v.done();
                }
            }
            //console.timeEnd('update start '+v.name);
        });
        
        
        p.update.forEach((f)=>{try{f({change:updNeed,list:updActions});}catch(e){}});
        
        if (typeof done==='function') 
            done();
    }
        
    t._unlock('update');
    
};

zAction.updateAsync=function(name,done){
    var t=zAction,p=t.param,a=p.action,newState,updNeed=false,updActions=[];
    
    if (typeof name==='function'){
        done = name;
        name = undefined;
    }
    
    if (t._lock('update')){
        let countAction = a.length;
        const countDone = ()=>{
            countAction--;
            if (countAction === 0){ 
                t._unlock('update');
                if (typeof done==='function') 
                    done();
            }
        };
        
        const doit =  (v)=>{
            
            setTimeout(()=>{            
                newState =v.state(v.prevState);
                v._firstCallState = true;
                
                if (!t._eq(newState,v.prevState)){
                    updNeed = true;
                    updActions.push(v.name);
                    v.prevState = (typeof newState === "object")?$.extend(false,newState,{}):newState;
                    v.on.state.forEach(function(vv){
                        try{ 
                            if (vv.func) vv.func(newState);
                        }catch(e){
                    
                        }    
                    });
                    
                    if (v.done) v.done();
                }
                countDone();
            },10);
            
        };
        
        
        a.forEach(function(v){
            if (((name===undefined)||(v.name===name))&&(v.state)){
                doit(v);
            }else{
                countDone();
            };
        });

    }else{
        t._unlock('update');
    }
    //if (typeof done==='function') 
    //    done();
        
    //t._unlock('update');
    
};

/** полное удаление action? c соотв state и bind */
zAction.undefine=function(name){
    var t=zAction,p=t.param,a=p.action,
    i = t._get(name,true);
    if (i!==false)
        a.splice(i,1);
};
/** возврашает текущее состояние */
zAction.getState=function(name){
    var t=zAction,p=t.param,g=t._get(name);
    
    if ((g===false)||(g.state===undefined))
        return undefined;
    else    
        return g.state();
};
/** вызов action
*/
zAction.do= async function(o){ 
    var t=zAction,
        g=t._get(o.name),
        res = o.param,
        error = [];
    
    if (g){ 
        try{ 
            if(g.action){
                 res = await g.action(o.param,g.prevState);
                 if (res===undefined)
                    res = o.param;
            }
        }catch(e){
            console.warn(e);
            error.push(e);
        }
        
        g.on.action.forEach(function(v){
            try{
                if (v.func) v.func(res,g.prevState);
            }catch(e){
                console.warn(e,v);    
                error.push(e);
            }
        });
        
    }
    t.update();

    if (error.length)
        throw error;
    else
        return res;
    
};

zAction._get=function(name,asIndex,arr){
    var t=zAction,p=t.param,i,
    a=arr!==undefined?arr:p.action;

    for(i=0;i<a.length;i++){
        if (name===a[i].name)
            return asIndex?i:a[i];
    }

    return false; 
};

zAction._eq=function(a,b){
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
};

zAction._id=function(pref){
    return pref+Math.floor(Math.random() * (100000 - 10000 + 1)) + 10000; 
};

zAction._lock=function(event){
    var t=zAction,p=t.param;
    p.lock[event]++;
    return p.lock[event]===1;
};

zAction._unlock=function(event){
    var t=zAction,p=t.param;
    p.lock[event]--;
    return p.lock[event]===0;
};
zAction._can=function(event){
    var t=zAction,p=t.param;
    return (p.lock[event]===0);
};

