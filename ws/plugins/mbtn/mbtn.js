/*global $,jQuery,JX*/
/**
 * Простой плагин, позволяющий управлять отображением состояния элемента
 * не добавляет и не удаляет доп элементов, только добавляет или удаляет прописанные классы
 * 
 * Ex:
 * $("btn").mbtn({click:function(btn){
 *        btn.active(!btn.active());
 *   }})
 *   
 *   ВАЖНО!
 *   Помещать dcss для плагина после определения css для компонентов, которые будут обернуты в mbtn
 *   ВАЖНО!
 *   При использовании свойства click плагина, реакция от click не будет, если компонент в состоянии disable
 * 
*/

(function( $ ){

var m={
name:"mbtn",    
init:function(param){
    var o = m.obj(this);
    if (o===undefined){
        if (param===undefined) param = {};
        param.own = this;
        m.obj(this,new mbtn(param));
    }else
        o.put(param);                
    return this;     
},
destroy:function(){
    var o = m.obj(this);
    m.obj(this,undefined);    
    return this;
},
obj:function(t/*set*/){
    if (arguments.length>1){ 
        if (arguments[1]===undefined)
            $.removeData(t[0],m.name);
        else    
            $.data(t[0],m.name,arguments[1]);
    }    
    return $.data(t[0],m.name);
},
put:function(param){
    m.obj(this).put(param);
    return this;
},
get:function(name){
    return m.obj(this).attr(name);
},
active:function(/*param*/){
    var o = m.obj(this);
    if (arguments.length>0){
        o.active(arguments[0]);
        return this;
    }else
        return o.active();
},
disable:function(/*param*/){
    var o = m.obj(this);
    if (arguments.length>0){
        o.disable(arguments[0]);
        return this;
    }else
        return o.disable();
}

};

$.fn.mbtn = function(n){
    if(m[n])
        return m[n].apply(this,Array.prototype.slice.call( arguments, 1 ));
    else 
    if ((typeof n==='object')||(!n)){
        return m.init.apply(this,arguments);
    }else
        $.error( 'Not exists method ' +  n + ' for jQuery.mbtn');
};
})(jQuery);


function mbtn(o){
    var t=this,p;
    t.param = $.extend(true,{
        own:undefined,
        click:undefined,
        _active:false,
        _disable:false,
        caption:false,
        captions:{
            common:undefined,
            active:undefined
        },
        cssPref:'',
        css:{
            hover:"mb_hover",
            active:"mb_active",
            disable:"mb_disable"
        }        
    },o);
    
    if (t.param.cssPref!=='')
        ut.addPref(t.param.css,t.param.cssPref);
        
    t.put(t.param);
    t._event();
    
}

mbtn.prototype._event = function(o){
    var t=this,p=t.param,css=p.css,b=p.own;
    
    b.hover(function(){
        if (!t.disable())
            b.addClass(css.hover);            
    },function(){
        b.removeClass(css.hover);
    });
    
    b.on('click',function(){
        if (!t.disable()&&(p.click!==undefined)){
            p.click(t);
        }
    });
    
};

mbtn.prototype._css = function(css){
    var t = this,p=t.param;
    if (css===undefined)
        return p.css;
        
    for (var key in css) {    
        if (key in p.css){
            if (p.own.hasClass(p.css[key]))
                p.own.removeClass(p.css[key]).addClass(css[key]);
        }    
    }
    
    p.css=$.extend(true,p.css,css);
};

mbtn.prototype.put = function(o){
    var t=this;

    $.each(o,function(n,v){
        t.attr(n,v);
    });
    
};

mbtn.prototype.attr = function(n/*v*/){
    var t=this,p=t.param,v,r=(arguments.length===1),css=p.css;
    
    if (arguments.length===0) return;
    if (!r) v=arguments[1];
    /*-----------------------------------*/
    if (n==='visible'){
        if (r) 
            return JX.visible(p.own);
        else    
            JX.visible(p.own);
    }
    /*-----------------------------------*/
    if (n==='active'){
        if (r) 
            return t.active();
        else    
            t.active(v);
    }
    /*-----------------------------------*/
    if (n==='disable'){
        if (r) 
            return t.disable();
        else    
            t.disable(v);
    }
    /*-----------------------------------*/
    if (n==='click'){
        if (r) 
            return p.click;
        else    
            p.click=v;
    }
    /*-----------------------------------*/
    if (n==='cssPref'){
        if (r) 
            return p.cssPref;
        else if (v!==p.cssPref){
            
            ut.delPref(css,p.cssPref);
            
            p.cssPref = v;
            var newCSS = $.extend(true,{},css);
            ut.addPref(newCSS,v);
            
            t._css(newCSS);
           
        }    
    }
    /*-----------------------------------*/
    if (n==='css'){
        if (r) 
            return p.css;
        else    
           t._css(v);
    };
    /*-----------------------------------*/
    if (n==='caption'){
        if (r) 
            return p.own.text();
        else{    
            if (typeof v==='string')
                p.own.text(v);
        }    
    }
    /*-----------------------------------*/
    if (n==='captions'){
        if (r) 
            return p.captions;
        else{
            p.captions = $.extend(true,v,p.captions);
            p.own.text(t.active()?p.captions.active:p.captions.common);     
        }    
    }
};

mbtn.prototype.active = function(bool){
    var t=this,p=t.param,css=p.css;
    
    if (bool!==undefined){
        bool?p.own.addClass(css.active):p.own.removeClass(css.active);
        if ((bool)&&(p.captions.active))
            p.own.text(p.captions.active);   
        if ((!bool)&&(p.captions.common))
            p.own.text(p.captions.common);   
        p._active = bool;
    }else
        return p._active;
};    

mbtn.prototype.disable = function(bool){
    var t=this,p=t.param,css=p.css;
    
    if (bool!==undefined){
        bool?p.own.addClass(css.disable):p.own.removeClass(css.disable);
        p._disable = bool;
    }else
        return p._disable;
}; 