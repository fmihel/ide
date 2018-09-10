/*global ut,$,jQuery,JX,Qs,Ws*/
/** @module trial/markDialog/popup*/

/**
 * simple popup (using popup plugin)
 * create pupup DOM in modal DOM of Ws framework
 * @example 
 *  popup("wait a momment...");
 *  popup({caption:"wait a mommen.."});
 * @example 
 * popup({
 *  caption:"wait a momment..",
 *  btnCaption:"stop",
 *  delay:10000,
 *  context:{....}
 *  onButton:function(context){
 *     ....
 *  },
 *  onDelayClose:function(context){
 *     ....
 *  },
 * });
 * @example set default params ( to alll next popup messages) 
 * not use a caption property
 * popup({
 *      align:{margin:{right:200}},
 *      css:{frame:"my_frame"},
 *      delay:1000,
 * });
 * 
*/ 
function popup(o){
    if (!Qs || !Qs.modal) return; 
    if (typeof o==='string') o = {caption:o};
    
    if (!Qs.popup){ 
        Qs.popup = $('<div id="popup" style="position:absolute"></div>');
        Qs.modal.append(Qs.popup);
    }
    
    
    if (o.caption!==undefined){
        if ((o.enableButton===undefined)&&(o.onButton!==undefined))
            o.enableButton = true;
        Qs.popup.popup("show",o);
    }else    
        Qs.popup.popup(o);
}

/**
 * plugin: popup<br>    
 * file  : popup.js<br>
 * path  : trial/markDialog/<br>
 * @class popup
 *
*/
(function( $ ){
/** @lends module:trial/markDialog/popup~popup# */
var m={
name:"popup",
/**
 * Create plugin and handler object {@link module:trial/markDialog/popup~Tpopup Tpopup}
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.popup(param)
 * </div>
 * @param {json} param full list of param see in {@link module:trial/markDialog/popup~Tpopup.param Tpopup.param}
 * @return this
 * @example show popup message
 * $('#div').popup("show",{
 *      caption:"my message",
 *      btnCaption:"revert",
 *      context:{"file1.php"},
 *      delay:3000,
 *      onButton:function(context){
 *           
 *      },
 *      onDelayClose:function(context){
 *     
 *      }
 * });
 * @example get attr (1)
 * var t = $('#div').popup("reverse");
 * @example get attr (2)
 * var t = $('#div').popup("get","reverse");
 * @example set attr (1)
 * $('#div').popup("put",{reverse:true});
 * @example set attr (2)
 * $('#div').popup({reverse:true});
 * 
 *   
*/
init:function(param){
    var o = m.obj(this);

    if (o===undefined){
        var p=$.extend(true,{},param);
        p.plugin = this;
        m.obj(this,new Tpopup(p));
    }else{
        
        o.put(param);                
    }    
        
    return this;     
},
/**
 * Deleted plugin and related objects
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.popup("destroy")
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
 * Story the handler object Tpopup in jquery 
 * @private
 * @return Tpopup
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
show:function(param){
    var o = m.obj(this);
    o.show(param);
},
/**
 * Sets the parameters of the object Tpopup, see {@link module:trial/markDialog/popup~Tpopup.param }
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.popup('put',param)
 * </div>
 * @param {json} param full list of param see in {@link module:trial/markDialog/popup~Tpopup.param}
 * @return this
 * @example 
 * $('#div').popup('put',{ 
 *     param1:value1,
 *     param2:value2 
 * });
*/
put:function(param){
    m.obj(this).put(param);
    return this;
},
/**
 * Gets the value of the object's parameters (see {@link module:trial/markDialog/popup~Tpopup.param})
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.popup("get",name)
 * </div>
 * @param {string} name name of param in popup  {@link module:trial/markDialog/popup~Tpopup.param}
 * @return mixed type
 * @example 
 * var v = $('#div').popup('get',"deepSelect");
 *   
*/
get:function(name){
    return m.obj(this).attr(name);
},

};

$.fn.popup = function(n){
    
    if ((typeof n === 'object')||(!n))
        return m.init.apply(this,arguments);
    if(m[n]){
        if (!m.assigned.apply(this))
            m.init.apply(this,arguments[1]);
            
        return m[n].apply(this,Array.prototype.slice.call( arguments, 1 ));
    }else{
        if (typeof n==='string')
            return  m.get.apply(this,[n]);
        else    
            $.error( 'Not exists method ' +  n + ' for jQuery.popup');
    }    
};
})(jQuery);

/**
 * Handler for popup plugin. Propertys this class story in param,
 * see {@link module:trial/markDialog/popup~Tpopup.param}
 * @class Tpopup
 * @property {json} param see {@link module:trial/markDialog/popup~Tpopup:param param}
 * 
*/
function Tpopup(o){
    var t = this;
    t.init(o);
}
/** Constructor of Tpopup. Initial creation and initialization of the object.*/
Tpopup.prototype.init = function(o){
    var t=this;
    /** @namespace module:trial/markDialog/popup~Tpopup:param */
    t.param = $.extend(true,
    /** @lends module:trial/markDialog/popup~Tpopup:param#*/
    {
        /** 
         * ref on plugin (jQuery) wrapper, initializing in {@link module:trial/markDialog/popup~popup.init}
         * @type {jQuery}
        */
        plugin:null,
        /** 
         * unique identificator 
         * @type {string}
        */
        id:ut.id('popup'),
        /** 
         * Trigger for  check  the  last call. Use if you need to make sure that this is the last function call 
         * @type {jlock}
         * @example 
         * this.param.lock.lock("my");
         * ...
         *   this.param.lock.lock("my");
         *   ...
         *   ...
         *   if (this.param.lock.unlock("my"))
         *           func(); // not be calling        
         * ...
         * ...
         * if (this.param.lock.unlock("my"))
         *        func(); // be calling        
         * 
        */
        lock:new jlock(),

        caption:undefined,
        btnCaption:"cancel",
        enableButton:false,
        onButton:undefined,
        onDelayClose:undefined,
        context:undefined,
        align:{direct:"vert",type:"bottom",align:"right",gap:5,margin:{right:5,bottom:5}},
        alignChild:{direct:"horiz",type:"stretch",align:"center",stretch:[{idx:0}],margin:{left:5,right:5} },
        delay:5000,        
        reverse:true,
        
        /** array of css classes uses in Tpopup */
        css:{
            frame:'popup_frame',
            msg:"popup_msg",
            btn:"popup_btn"
        }
        
    },o);

    t.align();
    
};

/** Destructor of Tpopup. Call on plugin is deleted.*/
Tpopup.prototype.done=function(){
    
};

Tpopup.prototype.show=function(o){  
    if (o===undefined) return;
    var t=this,p=t.param;
    
    var pp = $.extend(true,{},p);
    var a = $.extend(true,pp,o);
    
    
    p.lock.lock("align");        

    if (typeof(o) === 'string') 
        o = {caption:o};
    
    t._show(a);
    
    if (p.lock.unlock("align")) 
        t.align();
};



Tpopup.prototype._show=function(o){
    var t=this,p=o,css=p.css,c='',id=ut.id('pp');
    var cancel = o.onButton,ok = o.onDelayClose,context  = o.context;
    
    c = ut.tag('<',{id:id,css:css.frame,style:'position:absolute'});
    c+= ut.tag({css:css.msg,style:'position:absolute',value:o.caption});
    if (o.enableButton)
        c+=ut.tag({css:css.btn,style:'position:absolute',value:o.btnCaption});
    c+= ut.tag('>');

    o.plugin.append(c);
    c = o.plugin.find('#'+id);
    
    var par = o.plugin.parent();
    o.plugin.detach().appendTo(par);

    JX.arrange(c.children(),o.alignChild);
    
    var handler = setTimeout(function(){
        c.fadeOut(500,function(){
            cancel = undefined;
            if (ok) 
                ok(context);
            $(this).remove();
        });
    },o.delay);    

    c.find('.'+css.btn).on("click",function(){
        clearTimeout(handler);
        ok = undefined;
        if (cancel) 
            cancel(context);                   
        $(this).parent().remove();
    });
    

};

/**
 * Set parameters to {@link Tpopup.param}
 * @param {json} o
 * @example xxx.put({name:"value"});
*/ 
Tpopup.prototype.put = function(o){
    var t=this,p=t.param,l=p.lock;
    
    l.lock('align');
    
    $.each(o,function(n,v){
        t.attr(n,v);
    });
    
    if (l.unlock('align'))
        t.align();
};    
/**
 * Set or get parameters from {@link Tpopup.param}
 * @param {string} n name of parameter
 * @param {undefined | mixed} v undefined or mixed data 
 * @example xxx.attr("select",true);
 * @example var v = xxx.attr("select");
*/ 
Tpopup.prototype.attr = function(n/*v*/){
    /*-----------------------------------*/
    if (arguments.length===0) 
        return;
    
    var t=this,p=t.param,v,r=(arguments.length===1);
    
    if (!r) 
        v=arguments[1];

    /*-----------------------------------*/
    if (n==='align'){
        if (r) 
            return p.align;
        else    
            p.align = $.extend(true,p.align,v);
    }
    /*-----------------------------------*/
    if (n==='alignChild'){
        if (r) 
            return p.alignChild;
        else    
            p.alignChild = $.extend(true,p.alignChild,v);
    }
    /*-----------------------------------*/
    if (n==='reverse'){
        if (r) 
            return p.reverse;
        else    
            p.reverse = p.reverse?true:false;
    }
    /*-----------------------------------*/
    if (n==='enableButton'){
        if (r) 
            return p.enableButton;
        else    
            p.enableButton = p.enableButton?true:false;
    }
    /*-----------------------------------*/
    if (n==='css'){
        if (r) 
            return p.css;
        else    
            p.css = $.extend(true,p.css,v);
    }
    /*-----------------------------------*/
    if (n==='delay'){
        if (r) 
            return p.delay;
        else    
            p.delay = v;
    }
    
    /*-----------------------------------*/
    if (n==='btnCaption'){
        if (!r) 
            p.btn = v;
    }
    /*-----------------------------------*/

    t.align();
};

/** Call this method to redraw all DOM elements*/ 
Tpopup.prototype.align=function(){
    var t=this,p=t.param,l=p.lock;
    
    if (l.lock('align'))
        t._align();
    l.unlock('align');   
    
};

/** Method calling from Tpopup.align(). Custom call not needed*/ 
Tpopup.prototype._align=function(){
    var t=this,p=t.param,obj=[];
    
    JX.stretch_scr(p.plugin);
    
    $.each(p.plugin.children(),function(e){
        obj.push(this);
    });
    
    if (p.reverse)
        obj.reverse();
    
    JX.arrange(obj,p.align);
    
    JX.pos(p.plugin,{w:0,h:0});
    
};


    