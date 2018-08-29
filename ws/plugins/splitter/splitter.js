(function( $ ){

var m={
name:"splitter",    
init:function(param){
    var o = m.obj(this);
    if (o===undefined) 
        m.obj(this,new ws_splitter(this,param));
    return this;     
},
destroy:function(){
    m.obj(this,undefined);    
    return this;
},
obj:function(t/*set*/){
    if (arguments.length>1) 
        $.data(t[0],m.name,arguments[1]);
    return $.data(t[0],m.name);
}

};

$.fn.splitter = function(n){
    if(m[n])
        return m[n].apply(this,Array.prototype.slice.call( arguments, 1 ));
    else 
    if ((typeof n==='object')||(!n)){
        return m.init.apply(this,arguments);
    }else
        $.error( 'Not exists method ' +  n + ' for jQuery.splitter');
};
})(jQuery);

function ws_splitter(own,o){
    
    var a=$.extend({
        own:own,
        jinc:own.prev(),
        jdec:own.next(),
        horiz:false,
        
        limit:{by:'inc',min:0},
        onsplit:undefined        
    },o);
    
    var sp = {enable:false,mouseX:0,mouseY:0,x:0,y:0};
    $.data(own[0],'splitter',sp);
    
    (Qs?Qs.body:$("body")).on("mousemove",function(e){

        sp.mouseX = e.pageX;
        sp.mouseY = e.pageY;
        if (sp.enable)
        {
            var dx = (sp.mouseX - sp.x);
            var dy = (sp.mouseY - sp.y);
            var new_inc,new_dec,need = true;
            if (a.horiz){
                new_inc = a.jinc.width()+dx;
                new_dec = a.jdec.width()-dx;
                
                if (a.limit.by=='inc')
                    need = (new_inc>=a.limit.min);
                else
                    need = (new_dec>=a.limit.min);
                
                
                if (need){
                    a.jinc.width(new_inc);
                    a.jdec.width(new_dec);
                }else
                    sp.enable = false
                
            }else{
                new_inc = a.jinc.height()+dy;
                new_dec = a.jdec.height()-dy;
                
                if (a.limit.by=='inc')
                    need = (new_inc>=a.limit.min);
                else
                    need = (new_dec>=a.limit.min);
                
                
                if (need){
                    a.jinc.height(new_inc);
                    a.jdec.height(new_dec);
                }else
                    sp.enable = false    
            }
            
            sp.x = sp.mouseX;
            sp.y = sp.mouseY;
            
            if(a.onsplit)
                a.onsplit();
            else if((Ws)&&(Ws.align))
                Ws.align();
                
        }
        
    });
            
    own.on("mousedown",function(){
        sp.x = sp.mouseX;
        sp.y = sp.mouseY;
        sp.enable = true;
    });
    
    own.on("mouseup",function(){
        sp.enable = false;
    });
}