/*global ut,$,jQuery,JX*/
(function( $ ){
var m={
name:"jtable",    
init:function(param){
    var o = m.obj(this);
    if (o===undefined) 
        m.obj(this,new JTABLE(this,param));
    else
        o.put(param);                
    return this;     
},
destroy:function(){
    var o = m.obj(this);
    o._destroy();
    m.obj(this,undefined);    
    return this;
},
obj:function(t/*set*/){
    if (arguments.length>1) 
        $.data(t[0],m.name,arguments[1]);
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

$.fn.jtable = function(n){
    if(m[n])
        return m[n].apply(this,Array.prototype.slice.call( arguments, 1 ));
    else 
    if ((typeof n==='object')||(!n)){
        return m.init.apply(this,arguments);
    }else
        $.error( 'Not exists method ' +  n + ' for jQuery.jshadow');
};
})(jQuery);

function JTABLE(own,o){
        
};
JTABLE.prototype.put(p){
    
};

