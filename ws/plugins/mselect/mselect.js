/*global ut,$,jQuery,JX,Qs,Ws,jlock,jhandler*/
/** @module trial/combobox/mselect*/
/**
 * plugin: mselect<br>    
 * file  : mselect.js<br>
 * path  : trial/combobox/<br>
 * @class mselect
 *
*/
(function( $ ){
/** @lends module:trial/combobox/mselect~mselect# */
var m={
name:"mselect",
/**
 * Create plugin and handler object {@link module:trial/combobox/mselect~Tmselect Tmselect}
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.mselect(param)
 * </div>
 * @param {json} param full list of param see in {@link module:trial/combobox/mselect~Tmselect.param Tmselect.param}
 * @return this
 * @example 
 * $('#div').mselect({
 * ...
 * });
 * @example get attr (1)
 * var t = $('#div').mselect("multiSelect");
 * @example get attr (2)
 * var t = $('#div').mselect("get","multiSelect");
 * @example set attr (1)
 * $('#div').mselect("put",{multiSelect:true});
 * @example set attr (2)
 * $('#div').mselect({multiSelect:true});
 * 
 *   
*/
init:function(param){
    var o = m.obj(this),p;

    if (o===undefined){
        p=$.extend(true,{},param);
        p.plugin = this;
        m.obj(this,new Tmselect(p));
    }else
        o.put(param);                
        
    return this;     
},
/**
 * Deleted plugin and related objects
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.mselect("destroy")
 * @return this
 * </div>
*/
destroy:function(){
    var o = m.obj(this);
    o.done();
    m.obj(this,undefined);    
    return this;
},
/** 
 * return true if plugin is initializing!!! 
 * @return boolean
*/
assigned:function(){
    return (m.obj(this)!==undefined);
},
/**
 * Story the handler object Tmselect in jquery 
 * @private
 * @return Tmselect
*/
obj:function(t/*set*/){
    if (arguments.length>1){ 
        if (arguments[1]===undefined)
            $.removeData(t[0],m.name);
        else    
            $.data(t[0],m.name,arguments[1]);
    }    
    return $.data(t[0],m.name);
},
/**
 * Sets the parameters of the object Tmselect, see {@link module:trial/combobox/mselect~Tmselect.param }
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.mselect('put',param)
 * </div>
 * @param {json} param full list of param see in {@link module:trial/combobox/mselect~Tmselect.param}
 * @return this
 * @example 
 * $('#div').mselect('put',{ 
 *     param1:value1,
 *     param2:value2 
 * });
*/
put:function(param){
    m.obj(this).put(param);
    return this;
},
/**
 * Gets the value of the object's parameters (see {@link module:trial/combobox/mselect~Tmselect.param})
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.mselect("get",name)
 * </div>
 * @param {string} name name of param in mselect  {@link module:trial/combobox/mselect~Tmselect.param}
 * @return mixed type
 * @example 
 * var v = $('#div').mselect('get',"deepSelect");
 *   
*/
get:function(name){
    return m.obj(this).attr(name);
},
/** устанавливает или получает данные в список 
 * если указать param === 'selected' то вернет данные свзанные с выделенным пунктом
 * 
*/
data:function(param){
    var o = m.obj(this),data = arguments[0];
    
    if (data === 'selected'){
        return o.toData(o.selected());    
    }else{
        if (data)
            o.setData(data);
        else
            return o.getData();
    }        
    return this;
},
/** возвращает массив значений (только)*/
values:function(){
    var o = m.obj(this),data = o.getData(),res=[];
    $.each(data,function(i,o){
        res.push(o.value);   
    });
    return res;
},
/** возвращает текущее выбранное значение, или default*/
value:function(val){
    var o = m.obj(this);
    return o.value(val);    
},

/** возвращает элемент как элемент входной структуры данных  
 *  @return object {id:int,value:"text",data:object}
*/
toData:function(param){
    var o = m.obj(this);
    return o.toData(param);
},
next:function(need_change){
    var o = m.obj(this);
    return o.step("next",need_change);
},
prev:function(need_change){
    var o = m.obj(this);
    return o.step("prev",need_change);
},

selected:function(/*param*/){
    var o = m.obj(this),p = arguments[0];
    if  (p) 
        o.selected(p);
    else
        return o.selected();
},
disabled:function(obj,bool){
    var o = m.obj(this);
    return  o.disabled(obj,bool);
    
},
clear:function(/*param*/){
    var o = m.obj(this);
    o.clear();
},
focus:function(){
    var o = m.obj(this);
    o.focus();
},
delete:function(param){
    var o = m.obj(this);
    o.delete(param);
},
open:function(bool){
    var o = m.obj(this);
    o.open(bool);
}
};

$.fn.mselect = function(n){
    if(m[n])
        return m[n].apply(this,Array.prototype.slice.call( arguments, 1 ));
    else 
    if ((typeof n==='object')||(!n)){
        return m.init.apply(this,arguments);
    }else{
        if (typeof n==='string')
            return  m.get.apply(this,[n]);
        else    
            $.error( 'Not exists method ' +  n + ' for jQuery.mselect');
    }    
};
})(jQuery);

/**
 * Handler for mselect plugin. Propertys this class story in param,
 * see {@link module:trial/combobox/mselect~Tmselect.param}
 * @class Tmselect
 * @property {json} param see {@link module:trial/combobox/mselect~Tmselect:param param}
 *  
*/
function Tmselect(o){
    var t = this;
    t.init(o);
}
/** Constructor of Tmselect. Initial creation and initialization of the object.*/
Tmselect.prototype.init = function(o){
    var t=this;
    if ($.isArray(o)) o = {data:o};
    /** @namespace module:trial/combobox/mselect~Tmselect:param */
    t.param = $.extend(true,
    /** @lends module:trial/combobox/mselect~Tmselect:param#*/
    {
        /** 
         * ref on plugin (jQuery) wrapper, initializing in {@link module:trial/combobox/mselect~mselect.init}
         * @type {jQuery}
        */
        plugin:null,
        select:null,
        /** 
         * unique identificator 
         * @type {string}
        */
        id:ut.id('mselect'),
        id_input:ut.id('mselinp'),
        maxid:0,
        /** включает режим редактирования */
        editable:false,
        /** событие onSelect генерируется если нажат enter  */
        changeOnKeyEnter:true,
        /** событие onSelect генерируется если  произощла смена фокуса */
        changeOnFocusOut:false,
        size:100,
        lock:new jlock(),
        handler:new jhandler(),
        
        /** вызывается при выборе пункта из раскрывающегося списка*/
        onSelect:undefined,        
        /** array of css classes uses in Tmselect */
        css:{
            select:'mselect',
            input:'minput',
            
        }
        
    },o);

    var c='',p=t.param;
    //---------------------------
    c=ut.tag({id:p.id,tag:'select',css:p.css.select,style:"width:100%;height:100%"});    
    c+=ut.tag({id:p.id_input,tag:'input',attr:{type:'text',autocomplete:'off'},css:p.css.input,
        style:"outline:none;display:none;position:absolute;left:0px;top:0px;border:1px solid rgba(0,0,0,0);text-indent:3px"});    
    //---------------------------
    
    $.each(p.plugin.children(),function(i,dd){
        let d=$(dd),at = ut.attrs(d,{exclude:"id"});
        if (Object.keys(at).length===0) at = undefined;
        if (p.data===undefined) p.data=[];
        if (dd.id)
            p.data.push({id:dd.id,value:d.text(),data:at});
        else
            p.data.push({value:dd.text(),data:at});
           
    });
    //---------------------------
    p.plugin.html(c);
    //---------------------------
    p.select = p.plugin.find('#'+p.id);
    p.input  = p.plugin.find('#'+p.id_input);
    //---------------------------
    t._event();
    t.put(t.param);
    
};
Tmselect.prototype._event=function(){
     var t=this,p=t.param;
    //-------------------------
    p.select.on('change',()=>{
        p.input.val(p.select.val());
        if (p.onSelect)
            p.onSelect({sender:t,item:t.selected(),value:p.select.val()});
    });
    //-------------------------
    let sel=()=>{
        let item,value = p.input.val();
        t.each((a)=>{
            if (a.value===value){
                item = a.item;
                return false;
            }
        });
            
        if (item)
            t.selected(item);
                
        if (p.onSelect)
            p.onSelect({sender:t,item,value});
    };


    p.input.on('keydown',(e)=>{
        if((e.which == 13)&&(p.changeOnKeyEnter))
            sel();
        
    });    
    p.input.on('focusout',()=>{
        if(p.changeOnFocusOut)
            sel();
    });
    p.input.on('input',()=>{
        if (!p.changeOnKeyEnter)
            sel();
    });
    
    //---------------------------    
};    

/** Destructor of Tmselect. Call on plugin is deleted.*/
Tmselect.prototype.done=function(){
    
};


/**
 * A handler for various events. Performs two functions: 
 * it collects handlers and binds to a certain event and 
 * launches all handlers for a specific event
 * @param {string} group name of event Ex. "click"
 * @param {string} ev  'do'|'add' , if ev = 'add' then story func (arg) to in handler collection, else ev='do' then run all handler for group
 * @param {function|json} arg, if ev=='add' then arg is user function, if ev='do' then arg is param transmitted to handler
 * @example 
 *   // story handler in collection
 *   this.onEvent('click','add',function(){ 
 *          //TO DO...
 *   });
 * @example 
 *   // call handlers for event ='click'
 *   this.onEvent('click','do',{name:'mike'});
*/
Tmselect.prototype.onEvent=function(group,ev,arg){ 
    var t=this,p=t.param,h=p.handler;
    
    if (ev === 'do')
        h.do({group:group,param:arg});
    else
        h.add({group:group,func:arg});    
};

Tmselect.prototype._id=function(){
    var t=this,p=t.param;
    p.maxid++;
    while (p.plugin.find('#'+p.maxid).length>0)
        p.maxid++;
    return p.maxid;    
};
/**
 * добавление или замена данных
 * общая структура жанных
 * data = [{id:integer,value:string,data:object}]
 * но на вход можно подавать и упрощенные структуры
 * data = [string,strng,string,string...]
 * data = [{value:string},{value:string},...]
*/ 
Tmselect.prototype.setData=function(data){
    var t=this,p=t.param,css=p.css,i,c='',item,_item,have;
    
    for(i=0;i<data.length;i++){
        
        _item = data[i];
        if ((typeof _item === 'string')||(typeof _item === 'number'))
            item = {id:t._id(),value:_item,data:undefined};
        else if (typeof _item === 'object'){
            if ( _item.id===undefined)
                _item.id = t._id();
            item = {id:_item.id,value:_item.value,data:_item.data};
        }else
            item = {id:t._id(),value:"error",data:_item};
        
        
        have = p.select.find('#'+item.id);
        if (have.length===0){
            p.select.append(ut.tag({id:item.id,tag:"option"}));
            have = p.select.find('#'+item.id);
        }    

        if (item.value!==undefined)
            have.text(item.value);
                
        if (item.data!==undefined)    
            $.data(have[0],'data',item.data);
    }
    
    p.input.val(p.select.val());
    
};
Tmselect.prototype.each=function(f){
    var t=this,p=t.param;
    $.each(p.select.children(),(i,o)=>{
        let item = $(o);
        if (f) 
            if (f({i,id:o.id,item,value:item.val(),data:$.data(o,'data')}) === false) return false;
            
    });
};

Tmselect.prototype.getData=function(){
    var t=this,p=t.param,css=p.css,out=[];
    $.each(p.select.children(),function(i,o){
        out.push({id:o.id,value:$(o).val(),data:$.data(o,'data')});
    });
    
    return out;
};

Tmselect.prototype._paramToOpt=function(o){
    var t=this,p=t.param,dt,find =false;
    if (JX.is_jquery(o))
        return o;
    else if (o.id!==undefined)
        return p.select.find('#'+o.id);
    else if (o.index!==undefined)
        return p.select.children().eq(o.index);
    else if (o.value!==undefined){
        dt = t.getData()
        
        $.each(dt,function(i,a){
            if (a.value==o.value){
                find = a;
                return false;
            }    
        });
         
        return find?p.select.find('#'+find.id):undefined;
            
    }    
    return false;    
};
Tmselect.prototype.value=function(val){
    var t=this,p=t.param,sel;
    if (p.editable){
        
        if (val!==undefined)
            t.selected({value:val});
        return p.input.val();
    }else{
        
        if (val !== undefined)
                t.selected({value:val});
        else{
            sel = t.selected();
            if (sel){
                sel = t.toData(sel);
                return sel.value;
            }
        }
        
    }
    
};    
Tmselect.prototype.toData=function(o){
    var t=this,
    opt = t._paramToOpt(o);
    if (opt)
        return {id:opt[0].id,value:opt.val(),data:$.data(opt[0],'data')};

    return null;
};
Tmselect.prototype.step=function(step,need_change){
    var t=this,p=t.param;
    var current = t.selected();

    var obj = (step==='prev'?current.prev():current.next());
    
    if (obj.length>0){
        t.selected(obj);
        if((need_change===undefined)||(need_change === true)){
            p.select.trigger('change');
        }
    }    
        
};
/** возвращает jquery объект выделенного елемента ( или устанавливает) */ 
Tmselect.prototype.selected = function(o){
    var t=this,p=t.param,opt,d;
    
    if (o){
        opt = t._paramToOpt(o);
        
        if (opt){
            p.select.children().not(opt).prop('selected',false);
            opt.prop('selected',true);
            d = t.toData(opt);
        };
        
        if (('value' in o)||((d) && ('value' in d)))
            p.input.val(('value' in o)?o.value:d.value);

    }else
        return p.select.find("option:selected");
    
};

Tmselect.prototype.focus=function(){
    var t=this,p=t.param;
    
    p.editable?p.input.focus():p.select.focus();

};

Tmselect.prototype.open=function(bool){
    var t=this,p=t.param;
    /* not realized :(*/

};
/** устанавливает элемент невозможным к редактированию */ 
Tmselect.prototype.disabled=function(o,bool){
    var t=this,p=t.param,css=p.css,opt,i;
    if(o===undefined){
        // вернем все отключенные элементы
        return p.select.find("option:disabled");
    }else if (bool === undefined){
        // вернем состояние o
        opt = t._paramToOpt(o);
        if (opt)
            return opt.attr('disabled')==='disabled';
        return true;    
    }else{
        if (jQuery.isArray(o)){
            for(i=0;i<o.length;i++) t.disabled(o[i],bool);
        }else{    
        
            opt = t._paramToOpt(o);
            if (opt){
                if (!bool)
                    opt.removeAttr('disabled');
                else    
                    opt.attr('disabled','disabled');    
            }    
        }
    }

};

Tmselect.prototype.clear=function(o){
    var t=this,p=t.param;
    p.select.html("");
    p.maxid = 0;
};    

/** удаляет элемент  */ 
Tmselect.prototype.delete=function(o){
    var t=this;
    if (!o) return;
    var opt = t._paramToOpt(o);

    if (opt)
        opt.remove();

};

/**
 * Set parameters to {@link Tmselect.param}
 * @param {json} o
 * @example xxx.put({name:"value"});
*/ 
Tmselect.prototype.put = function(o){
    var t=this,p=t.param,l=p.lock;
    
    l.lock('align');
    
    if ($.isArray(o)) o={data:o};
    
    $.each(o,function(n,v){
        t.attr(n,v);
    });
    
    if (o.data!==undefined)
        t.setData(o.data);
    
    if (l.unlock('align'))
        t.align();
};    
/**
 * Set or get parameters from {@link Tmselect.param}
 * @param {string} n name of parameter
 * @param {undefined | mixed} v undefined or mixed data 
 * @example xxx.attr("select",true);
 * @example var v = xxx.attr("select");
*/ 
Tmselect.prototype.attr = function(n/*v*/){
    /*-----------------------------------*/
    if (arguments.length===0) 
        return;
    var t=this,p=t.param,v,r=(arguments.length===1);
    if (!r) 
        v=arguments[1];
    /*-----------------------------------*/

    if (n==='onSelect'){
        if (r) 
            return p.onSelect;
        else   
            p.onSelect = v;
         
    }
    /*-----------------------------------*/
    if (n==='css'){
        if (r) 
            return p.css;
        else    
            p.css=$.extend(true,p.css,v);
    }
    

    /*-----------------------------------*/
    if (n==='editable'){
        if (r) 
            return p.editable;
        else{    
            p.editable=v?true:false;
            JX.visible(p.input,p.editable);
        }    
    }
    /*-----------------------------------*/
    if (n==='changeOnKeyEnter'){
        if (r) 
            return p.changeOnKeyEnter;
        else
            p.changeOnKeyEnter=v?true:false;
    }
    /*-----------------------------------*/
    if (n==='changeOnFocusOut'){
        if (r) 
            return p.changeOnFocusOut;
        else
            p.changeOnFocusOut=v?true:false;
    }

    /*-----------------------------------*/
    /** example
    
    if (n==='visible'){
        if (r) 
            return JX.visible(p.plugin);
        else    
            JX.visible(p.plugin,(v?true:false));
    }
    */
    /*-----------------------------------*/
    t.align();
};

/** Call this method to redraw all DOM elements*/ 
Tmselect.prototype.align=function(){
    var t=this,p=t.param,l=p.lock;
    
    if (l.lock('align'))
        t._align();
    l.unlock('align');   
    
};

/** Method calling from Tmselect.align(). Custom call not needed*/ 
Tmselect.prototype._align=function(){
    var t=this,p=t.param;
    let pos = JX.pos(p.select);
    JX.pos(p.input,{y:1,x:1,w:pos.w-22,h:pos.h-4});
};


    