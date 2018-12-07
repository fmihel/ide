/*global ut,$,jQuery,JX,Qs,Ws*/
(function( $ ){
var m={
name:"hint",
init:function(param){
    var o = m.obj(this),p;
    if (typeof param ==='string')
        param = {msg:param};
    
    if (o===undefined){
        p=$.extend(true,{},param);
        p.plugin = this;
        m.obj(this,new Thint(p));
    }else
        o.put(param);                
        
    return this;     
},
destroy:function(){
    var o = m.obj(this);
    o.done();
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
}
};

$.fn.hint = function(n){
    if(m[n])
        return m[n].apply(this,Array.prototype.slice.call( arguments, 1 ));
    else 
    if ((typeof n==='object')||(!n)){
        return m.init.apply(this,arguments);
    }else{
        if (typeof n==='string')
            return m.init.apply(this,arguments);
        else    
            $.error( 'Not exists method ' +  n + ' for jQuery.hint');
    }    
};
})(jQuery);

function Thint(o){
    var t = this;
    t.init(o);
}

Thint.prototype.init = function(o){
    var t=this,ext;
    

    t.param = $.extend(true,{
        plugin:null,
        id:ut.id('hint'),

        jq:{
            frame:undefined,
        }, 
        msg     :"",         /** отображаемый текст */
        place   :"auto",   /** top, bottom,auto,left,right */
        delay   :7000,
        timeIn  :500,
        timeOut :500,
        animate:"slide",
        size:"auto", /** auto , {w:int,h:int} */
        off:{x:1,y:1},
        cssPref:'',
        margin:5,
        css:{
            frame:'hint_frame',
            text:'hint_text',
        }
        
    }, 
        (typeof gHintParam!=='undefined')?$.extend(true,gHintParam,o):o
    );
    
    if (t.param.cssPref!=='')
        ut.addPref(t.param.css,t.param.cssPref);    
    
    t.put(t.param);
    t.create();
    t.run();
};
  

Thint.prototype.done=function(){
    var t=this,p=t.param;
    p.jq.frame.remove();
    p.jq = {frame:undefined};
};

/** создание объекта */
Thint.prototype.create=function(){
    var t=this,p=t.param;
    
    Qs.modal.append(
        framet({id:p.id,css:p.css.frame,child:[
            {css:p.css.text,value:p.msg}
        ]})
    );
    p.jq.frame = Qs.modal.find('#'+p.id);
    p.jq.text  = p.jq.frame.find('.'+p.css.text);
    JX.visible(p.jq.frame,false);
    
};

/** запуск анимации */
Thint.prototype.run=function(){
    var t=this,p=t.param,jq = p.jq.frame,pos,
    move = {},stop = {},own = JX.abs(p.plugin),
    brdr = JX._border(jq[0]);
    end_func = ()=>{
        p.plugin.hint('destroy');
    };
    
    JX.visible(jq,true);
    pos = t._align();
    
    if (p.animate === 'fade'){
        
        jq.fadeOut(0).fadeIn(p.timeIn,()=>{
            setTimeout(()=>jq.fadeOut(p.timeOut,end_func),p.delay);
        });
        
    }else if (p.animate === 'slide'){
        
        /** данный расчет годится только для случая если size = auto 
         *  для случая задания другого нужно переделать
        */
        if (['auto','top','bottom'].indexOf(p.place)!=-1){
            
            if (pos.y<own.y){
                
                JX.abs(jq,{h:0,y:pos.y+pos.h-(brdr.t+brdr.b)});
                move = {
                    top:    "-="+pos.h,
                    height: "+="+pos.h,
                };
                stop = {
                    top:    "+="+(pos.h-(brdr.t+brdr.b)),
                    height: 0,
                };
            
            }else if (pos.y>own.y){
                JX.abs(jq,{h:0});
                move = {
                    height: "+="+pos.h,
                };
                stop = {
                    height: 0,
                };
            }
        }
        if (['left','right'].indexOf(p.place)!=-1){
            
            if (pos.x<own.x){
                
                JX.abs(jq,{w:0,x:pos.x+pos.w-(brdr.l+brdr.r)});
                move = {
                    left:  "-="+pos.w,
                    width: "+="+pos.w,
                };
                stop = {
                    left:    "+="+(pos.w-(brdr.l+brdr.r)),
                    width: 0,
                };
            }else if (pos.x>own.x){
                JX.abs(jq,{w:0});
                move = {
                    width: "+="+pos.w,
                };
                stop = {
                    width: 0,
                };
            }
        }
        
        
        jq.animate(move,p.timeIn,()=>{
            JX.abs(jq,pos);
            setTimeout(()=>jq.animate(stop,p.timeOut,end_func),p.delay);
        });        
        
    }
};

Thint.prototype._css = function(css){
    var t = this,p=t.param;
    if (css===undefined)
        return p.css;
        
    for (var key in css) {    
        if (key in p.css)
            p.plugin.find('.'+p.css[key]).removeClass(p.css[key]).addClass(css[key]);
    }
    
    p.css=$.extend(true,p.css,css);
};

Thint.prototype.put = function(o){
    var t=this,p=t.param,l=p.lock;
    
    $.each(o,function(n,v){
        t.attr(n,v);
    });
    
};    

Thint.prototype.attr = function(n/*v*/){
    var t=this,p=t.param,v,r=(arguments.length===1),css=p.css;
    /*-----------------------------------*/
    if (arguments.length===0) return;
    /*-----------------------------------*/
    if (!r) v=arguments[1];
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
    }
    /*-----------------------------------*/
    if (n==='msg'){
        if (r) 
            return p.msg;
        else    
            p.msg = v;
    }
    /*-----------------------------------*/
    if (n==='delay'){
        if (r) 
            return p.delay;
        else    
            p.delay = v;
    }
    /*-----------------------------------*/
    if (n==='timeIn'){
        if (r) 
            return p.timeIn;
        else    
            p.timeIn = v;
    }
    /*-----------------------------------*/
    if (n==='timeOut'){
        if (r) 
            return p.timeOut;
        else    
            p.timeOut = v;
    }
    /*-----------------------------------*/
    if (n==='margin'){
        if (r) 
            return p.margin;
        else    
            p.margin = v;
    }
    /*-----------------------------------*/
};
/** конечное расположение объекта */
Thint.prototype._align=function(){
    var t=this,p=t.param,
    size = {},side,pivot,own = JX.pos(p.plugin),
        off={x:0,y:0};
    
    if ((p.size === 'auto')||(p.size.w===undefined))
        size.w = own.w;
    else
        size.w = p.size.w;
        
    if ((p.size === 'auto')||(p.size.h===undefined))
        size.h = own.h;
    else
        size.h = p.size.h;
    
    if ((p.place==='auto')||(p.place==='bottom')){
        side    = {a:"center",b:"center"};
        pivot   = {a:"bottom",b:"top"};
        off.y = p.off.y;    
    }else if (p.place==='top'){
        side    = {a:"center",b:"center"};
        pivot   = {a:"top",b:"bottom"};
        off.y = -p.off.y;    
    }else  if (p.place==='right'){   
        side    = {a:"right",b:"left"};
        pivot   = {a:"center",b:"center"};
        off.x = p.off.x;    
    }else  if (p.place==='left'){   
        side    = {a:"left",b:"right"};
        pivot   = {a:"center",b:"center"};
        off.x = -p.off.x;
    }
    
    JX.pos(p.jq.frame,size);
    JX.cling(p.plugin,p.jq.frame,{side,pivot,off,abs:true,area:"screen",cross:true,strong:true});
    
    JX.arrange([p.jq.text],{direct:"horiz",type:"stretch",align:"stretch",stretch:p.jq.text,margin:p.margin});
    
    return JX.abs(p.jq.frame);
};
    
    

    