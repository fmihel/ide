/**
коллеция обработчиков событий (триггеры) 
Принцип создания:

a. Создание  и регистрация обработчика ( ф-ция которая соджержит необходимы действия, при возникновении события )

events = getEventCollector({
    event1:func1,
    event2:func2,
    ...
});


var Object={

    ev:undefined,
    //------------------------------------------
    // регистрация внутренненго обработчика
    init:functon(){
        Object.ev = getEventCollector({
            change  :Object.change,
            align   :Object.align
        });
        
        Object.ev.bind("change",func1) - добавление внешних обработчиков (они сработают после того, как сработает основной )
        
    },
    //------------------------------------------
    // внутренний обработчик
    change:function(){
        if (Object.ev.need("change"){
            try{
                //TO DO
            
            }catch(e){}
            
            Object.ev.complete("change");
        }
    },
    //------------------------------------------
    // внутренний обработчик
    align:function(){
        if (Object.ev.need("align"){
            try{
            
                //TO DO
            
            }catch(e){}
            Object.ev.complete("align");
        }
    },
    //------------------------------------------
    // метод с блокирование change
    proc1:function(){
        Object.ev.begin('change');
            // внутри этого блока можно вызывать Object.change(), но заупщен он будет только после end('change');
            
        Object.ev.end('change'); 
    },
    //------------------------------------------
    // метод с мягким блокирование (!-перед названием события)
    // Object.change() не будет вызыван после вызова end('change') если внутри блока не был запущен Object.change();    
    proc2:function(){
        Object.ev.begin('change');
        //         
        Object.ev.end('!change');
    },
    

}
    
*/

function getEventCollector(evc){
    return new Evc(evc);
}

function Evc(evc){
    var t=this;
    t.events=[];
    if (evc) for(var name in evc)
        t.define(name,evc[name]);
}

Evc.prototype._item=function(name,create){
    var t=this,ev=t.events;
    if (!(name in ev)){
        ev[name] = {
            name:name,
            func:undefined,
            bind:[],
            lock:0,
            need:false,
            story:false,
        };
        if (create) create({event:ev[name],name:name});
        
    }    
    return ev[name];
};
Evc.prototype._each=function(f){
    var t=this,ev=t.events,i=0,o,res;
    
    while(i<ev.length){
        o = {
            item:ev[i],
            i:i,
            events:ev,
        };
        
        if (f(o)===true)
            break;
            
        i=o.i+1;
    }
    
    
};
Evc.prototype.define=function(name,func){
    this._item(name).func= func;
};

Evc.prototype.begin=function(/*name,name,name*/){
    var t=this,i,e,res;
    
    for(i=0;i<arguments.length;i++){
        e = t._item(arguments[i]);
        if (res===undefined) res = (e.lock===0);
        e.lock++;
        e.story = true;        
    }
    
    return res;
};
Evc.prototype.end=function(/*name,name,name,param*/){
    var t=this,i,p,e,param ;
    // поиск параметров для передачи в ф-цию
    for(i=0;i<arguments.length;i++){
        if (typeof arguments[i] === 'object'){
            param = arguments[i];
            break;
        }    
    }
    
    for(i=0;i<arguments.length;i++){
        if (typeof arguments[i] === 'string'){
            p   = t._ext(arguments[i]);
            e  = t._item(p.name);
        
            e.lock--;
    
            if (p.soft==='')
                e.need = true;
            else if (p.soft==='-')
                e.need = false;
        
            if (e.lock===0){
                if ((e.need)&&(e.func))
                    e.func(param);
                e.story = false;
                e.need = false;
            }    
        }    
    }    
    
};
Evc.prototype._ext=function(name){
    
    var out = 
    {
        name:name,
        soft:'',
    };
    if (name[0]==='!'){
        out.soft = '!';
        out.name = name.slice(1);
    }else if (name[0]==='-'){
        out.soft = '-';
        out.name = name.slice(1);
    }
    
    return out;

};    

Evc.prototype.can=function(name){
    var t=this,e = t._item(name);
    return (e.lock===0);
};

Evc.prototype.wrap=function(name,func,param){
    var t=this,e = t._item(name),i;
    
    if (e.lock===0){
        if ((!e.story)||(e.need)){
            e.need = false;
            try{
                func(param);
            }catch(err){
                console.error(err);
            }    
            
            for(i=0;i<e.bind.length;i++)
                e.bind[i](param);            
        }
    }else
        e.need = true;

};

Evc.prototype.need=function(name){
    var t=this,e = t._item(name);
    if (e.lock===0){
        if ((!e.story)||(e.need)){
            e.need = false;
            return true;
        }
    }else
        e.need = true;
        
    return false;
    
};

Evc.prototype.complete=function(name,param){
    var t=this,e = t._item(name),i;
    for(i=0;i<e.bind.length;i++)
        e.bind[i](param);
};

Evc.prototype.do=function(/*name,name,name,param*/){
    var t=this,i,param;
    // поиск параметров для передачи в ф-цию
    for(i=0;i<arguments.length;i++){
        if (typeof arguments[i] === 'object'){
            param = arguments[i];
            break;
        }    
    }
    
    for(i=0;i<arguments.length;i++){
        if (typeof arguments[i]==='string'){
            e = t._item(arguments[i]);    
            if (e.func) 
                e.func(param);
            else{
                for(j=0;j<e.bind.length;j++){
                    
                }
            }
        }
    }
    
};

Evc.prototype.bind=function(name,func){
    var t=this,e = t._item(name);
    e.bind.push(func);
    return func;
};
/**
 * unBind - отсоединение обработчиков
 * unBind() - отсоединение всех 
 * unBind('change') - отсоединение всех от тригера change
 * unBind('change',func) - тсоединение func от тригера change
 * unBind(func) - поиск во всех тригерах обработчика func и отсоединение
 */ 
Evc.prototype.unBind=function(name,func){
    var t=this,e,idx;
    // отсоединение всех
    if (name === undefined){
        t._each(function(o){
            o.item.bind = [];
            o.i--;
        });
        return;
    }
    
    if (typeof name === 'function'){
        func = name;
        name = '';
    }
    
    // если имя тригера определено
    if (name!==''){
        e = t._item(name);
        if (func!==undefined){
            idx =e.bind.indexOf(func);
            if (idx!==-1)
                e.bind.slice(idx,1);
        }else
            e.bind = []
        return;
    }    
    // поиск во всех тригерах ф-ции func
    t._each(function(o){
        
        idx = o.item.bind.indexOf(func);
        if (idx!==-1){
            o.item.bind.slice(idx,1);
            o.i--;
        }

    });
    
};