/*global ut,$,jQuery,JX*/
(function( $ ){
var m={
name:"jshadow",    
init:function(param){
    var o = m.obj(this);
    if (nil(o)) 
        m.obj(this,new JSHADOW(this,param));
    else
        o.put(param);                
    return this;     
},
destroy:function(){
    var o = m.obj(this);
    if (o) o._destroy();
    m.obj(this,false);    
    return this;
},
obj:function(t/*set*/){
    if (arguments.length>1) 
        if (arguments[1])
            $.data(t[0],m.name,arguments[1]);
        else
            $.data(t[0],m.name,null);
    return $.data(t[0],m.name);
},
put:function(param){
    m.obj(this).put(param);
    return this;
},
get:function(name){
    return m.obj(this).param[name];
},
show:function(/*param*/){
    var o = m.obj(this);
    if (arguments.length>0)
        o.put(arguments[0]);
    o.show();    
    return this;
},
visible:function(){
    var o = m.obj(this);
    return o.visible();
},
hide:function(/*param*/){
    var o = m.obj(this);
    if (arguments.length>0)
        o.put(arguments[0]);
    o.hide();    
    return this;
}

};

$.fn.jshadow = function(n){
    if(m[n])
        return m[n].apply(this,Array.prototype.slice.call( arguments, 1 ));
    else 
    if ((typeof n==='object')||(!n)){
        return m.init.apply(this,arguments);
    }else
        $.error( 'Not exists method ' +  n + ' for jQuery.jshadow');
};
})(jQuery);

function JSHADOW(own,param){
    var t = this;
    t.param = $.extend(true,{
        own:own,
        id:ut.id('shad'),
        opacity:0.1,
        speed:100,
        toBack:false,
        click:undefined,/*if not defined click on shadow hide the shadow, other to hide shadow need call hide() */
        onhide:undefined,/*do after hide*/
        onshow:undefined/*do after show*/
    },param);
    
    t._create();
    t._event();
    t._align();
    t.put(t.param);
    
}
JSHADOW.prototype.put=function(param){
    var t=this,p=t.param;
    $.extend(true,p,param);
    if((p.show!==undefined))
        (p.show?t.show():t.hide());
};

JSHADOW.prototype.toBack=function(){
    var t=this,p=t.param,s=p.shadow;
    s.detach().insertBefore(p.own);
    
};
JSHADOW.prototype.show=function(){
    var t=this,p=t.param,s=p.shadow;
    if (!t.visible()){
        if (p.toBack) t.toBack();
        s.css('opacity',p.opacity).fadeIn(p.speed,function(){t._align();(p.onshow?p.onshow(p.own):0);});
    }
};
JSHADOW.prototype.visible=function(){
    return JX.visible(this.param.shadow);
};

JSHADOW.prototype.hide=function(){
    var t=this,p=t.param,s=p.shadow;
    if (t.visible()){
        s.fadeOut(0);
        (p.onhide?p.onhide(p.own):0);
    }
};

JSHADOW.prototype._create=function(){ 
    var t=this,p=t.param,
    c=ut.tag({id:p.id,style:"display:none;position:absolute;background-color:black"});
    p.own.before(c);
    p.shadow = p.own.parent().find("#"+p.id);
};
JSHADOW.prototype._destroy=function(){
    var t=this,p=t.param;
    var w = JX.window();
    w.off("resize",p.wr_align);
    w.off("scroll",p.wr_align);
    
    if (p.shadow) p.shadow.remove();
};

JSHADOW.prototype._align=function(){
    JX.stretch_scr(this.param.shadow);
};


JSHADOW.prototype._event=function(){
    var t=this,p=t.param;
    
    p.shadow.on("click",function(e){
        p.click?p.click(p.own):t.hide();
        return false;
    }).on("contextmenu",false);

    var w = JX.window();
    /*bind need for save context of run function _align, this standart method in js< create wrapper for func*/
    p.wr_align = t._align.bind(t);
    w.on("resize",p.wr_align);
    w.on("scroll",p.wr_align);
};