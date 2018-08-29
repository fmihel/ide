/*global ut,$,jQuery,JX,Qs,Ws*/
/**
 *  групповая работа с компонентами 
 *  в основном предполагается работать с плагином jedit
 *  однако объект достаточно универсален и должен работать и с другими компонентами требующими группировки
 *  основной задачей объекта - вызыввать одноименные методы у объектов находящихся в одной группе
 *  о умолчанию все объекты помещаются в группу common
 *  одни и теже компоенты могут над\ходится в различных группах
 * 
 *  Ex. делает все компоненты входящие в группу "my" неактивными
 * 
 *  jgroup.put({disable:true},"my");
 *  или более универсально
 *  jgroup.call("put",[{disable:true}],"my")
 *  
 * 
*/ 

var jgroup={

_groups:{common:[]},
/** добавляет объект в группу 
 *  
 * Ex: 
 *      jgroup.add(obj) 
 *      eq 
 *      jgroup.add(obj,'common') 
 *      or 
 *      jgroup.add(obj,['common'])
 * 
 * Ex:
 *      jgroup.add([obj1,obj2])
 * 
*/
add:function(obj,group){
    var t=jgroup,g=t._groups,i,j,
    p=t._normal(obj,group);
    

    for(i=0;i<p.group.length;i++)
        for(j=0;j<p.obj.length;j++){
            if (g[p.group[i]]===undefined)
                g[p.group[i]]=[];
                
            if (!t.exist(p.obj[j],p.group[i]))
                g[p.group[i]].push(p.obj[j]);
        }        
            

    return true;
},
/** нормализует входные параметры 
 * @param {object|[object,object]|undefined} obj
 * @param {string|[string,string]|-1|undefined}  ubdefined eq "common" -1 eq all groups
 * @return {obj:[obj1,obj2,..],group:[group1,group2,...]}
*/
_normal:function(obj,group){
    
    if (group === -1){
        group = [];
        for(var g in jgroup._groups)
            group.push(g);
    }
    
    if (group===undefined)
        group=['common'];
        
    if (typeof group==='string')
        group = [group];
        
    if (!$.isArray(group)) return false;
    
    if (!$.isArray(obj))
        obj = [obj];
        
    return {obj:obj,group:group};    
},
/** проверка существования объекта obj в группе group 
* @param {object} obj
* @param {string} group
* @return boolean
*/
exist:function(obj,group){
    var t = jgroup,res = false;
    
    t.each(function(o){
         res = ut.oEq(o.obj,obj);
         return res;
    },group);
    
    return res;
},
/** удаляем объект(ы) из группы (групп)
 * @param {object|[object,object]|undefined} obj
 * @param {string|[string,string]|-1|undefined} group undefined eq "common" -1 eq all groups
*/
remove:function(obj,group){
    var t=jgroup,i,j,idx,g=t._groups,
    p=t._normal(obj,group);

    for(i=0;i<p.group.length;i++){
        for(j=0;j<p.obj.length;j++){
            idx = t.indexOf(p.obj[j],p.group[i]);
            if(idx!==-1)          
                t._groups[p.group[i]].splice(idx,1);
        }    
    }    
},
/** индекс obj в group
* @param {object} obj
* @param {string} group
* @return number (if not exists return -1)
*/
indexOf:function(obj,group){
    var t=jgroup,i,g=t._groups[group];
    for(i=0;i<g.length;i++)
        if (ut.oEq(g[i],obj))
            return i;
    
    return -1;
},
/** возвращает все группы где присутствует объект obj
* @param {object} obj
* @return [string,string,...]
*/
groups:function(obj){
    var t=this,group,res = [];
    for(group in t._groups)
        if (t.exist(obj,group)) res.push(group);
    
    return res;
},
/** цикл по всем объектам 
* @param {function} func is callback  with params ({obj:object,group:string,plugin:{own:jquery,name:string}})
* @param {string|[string,string]|-1|undefined} group undefined eq "common" -1 eq all groups
*
*/
each:function(func,group){
    var t=jgroup,g=t._groups,i,j,
    
    p=t._normal(null,group),obj;
    if (!func) return;
    
    for(i=0;i<p.group.length;i++){
        obj = g[p.group[i]];
        if (obj!==undefined)
        for(j=0;j<obj.length;j++)
            if (func({obj:obj[j],group:p.group[i],plugin:t._plugin(obj[j])})===true)
                return;
        
    }    
},
/**
 * универсальный метод, для группового вызова метода объекта группы
 * @param {string} name имя метода объекта
 * @param {array} arrayArgs  массив параметров метода name
 * @param {string|[string,string]|-1|undefined} group undefined eq "common" -1 eq all groups
*/ 
call:function(name,arrayArgs,group){
    var t=jgroup,fn;
    
    t.each(function(o){
        fn = o.obj[name];
        if (fn)
            fn.apply(o.obj,Array.prototype.slice.call( arrayArgs, 0 ));            
            
    },group);
    
},
/** цикл по плагинам объекта 
* @param {function} func is callback  with params (plugin,name,obj)
* @param {string} pluginName имя плагина ( анализ принадлежности плагину определяется в jgroup._plugin())
* @param {string|[string,string]|-1|undefined} group undefined eq "common" -1 eq all groups
*/
plugin:function(func,pluginName,group){
    var t=jgroup,pn=[];
    if (!func) return false;
    
    if (typeof pluginName==='string')
        pn=[pluginName];
        
    if ($.isArray(pluginName)) 
        pn = pluginName;
        
    t.each(function(o){
        if ((o.plugin!==undefined)&&((pn.length===0)||(pn.indexOf(o.name)>-1)))
            func(o.plugin.own,o.obj,o.name);
    },group);
    
},
/** 
 * интерфейсные ф-ии 
 * 
*/
begin:function(event,group){
    var t=jgroup;
    t.each(function(o){
        if (o.obj.begin) o.obj.begin(event);
    },group);
},
end:function(event,group){
    var t=jgroup;
    t.each(function(o){
        if (o.obj.end) o.obj.end(event);
    },group);
},
put:function(params,group){
    var t=jgroup;
    t.each(function(o){
        if (o.obj.put) o.obj.put(params);
    },group);
},
align:function(group){
    var t=jgroup;
    t.each(function(o){
        if (o.obj.align) o.obj.align();
    },group);
},
changed:function(params,group){
    var t=jgroup,res=false;
    
    t.each(function(o){
        if (o.obj.changed){ 
            if (params===undefined){
                res = o.obj.changed();
                return res;
            }else
                o.obj.changed(params);
        }            
    },group);
    
    return res;
},
value:function(value,group){
    var t=jgroup;
    t.each(function(o){
        o.obj.attr('value',value);
    },group);
},
setData:function(data,group){
    var t=jgroup;
    t.each(function(o){
        if (o.obj.setData) o.obj.setData(data);
    },group);
},
getData:function(data,group){
    var t=jgroup;
    
    if (typeof data==='string'){
        group = data;
        data = {};
    }
    
    t.each(function(o){
        if (o.obj.getData) o.obj.getData(data);
    },group);
    
    return data;
},
on:function(event,func,group){
    var t=jgroup;
    t.each(function(o){
        if (o.obj.on) o.obj.on(event,func);  
    },group);
},
/** возращает плагин по его объекту */
_plugin:function(obj){
    if (obj instanceof Tjedit) return {
            own:obj.param.plugin,
            name:"jedit"
    };
    return undefined;
}

};