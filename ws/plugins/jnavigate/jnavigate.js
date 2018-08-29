/*global ut,$,jQuery,JX,Qs,Ws*/
/** @module trial/navigate/jnavigate*/
/**
 * plugin: jnavigate<br>    
 * file  : jnavigate.js<br>
 * path  : trial/navigate/<br>
 * @class jnavigate
 *
*/
(function( $ ){
var m={
name:"jnavigate",
init:function(param){
    var o = m.obj(this),p;

    if (o===undefined){
        if ((JX.is_jquery(param))||($.isArray(param)))
            p={nodes:param};
        else
            p=$.extend(true,{},param);
        
        p.plugin = this;
        m.obj(this,new Tjnavigate(p));
        
    }else{
        if ((JX.is_jquery(param))||($.isArray(param)))
            o.put({nodes:param});
        else
            o.put(param);                
    }    
    return this;     
},
destroy:function(){
    var o = m.obj(this);
    o.done();
    m.obj(this,undefined);    
    return this;
},
assigned:function(){
    return (m.obj(this)!==undefined)
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
next:function(){
    var o = m.obj(this);
    o.next();    
    return this;
},
prev:function(){
    var o = m.obj(this);
    o.prev();    
    return this;
},
align:function(){
    var o = m.obj(this);
    o.align();    
    return this;
},
clear:function(){
    var o = m.obj(this);
    o.clear();    
    return this;
}

};

$.fn.jnavigate = function(n){
    if(m[n])
        return m[n].apply(this,Array.prototype.slice.call( arguments, 1 ));
    else 
    if ((typeof n==='object')||(!n)){
        return m.init.apply(this,arguments);
    }else{
        if (typeof n==='string')
            return  m.get.apply(this,[n]);
        else    
            $.error( 'Not exists method ' +  n + ' for jQuery.jnavigate');
    }    
};
})(jQuery);

function Tjnavigate(o){
    var t = this;
    t.init(o);
}
/** Constructor of Tjnavigate. Initial creation and initialization of the object.*/
Tjnavigate.prototype.init = function(o){
    var t=this;
    t.param = $.extend(true,
    {
        plugin:null,
        _alignFunc:ut.id('jnalf'),
        id:{
            up:ut.id('jnup'),
            down:ut.id('jndn'),
            panel:ut.id('jnpnl'),
        },
        /** сроллируемый объект */
        scrollObject:null,
        
        /** коллекция объектов, из которых состоит наиатор */
        jq:[],
        /**
         * узловые точки на которые осуществляется скролинг
         * Ex:
         * nodes:[
         *    {to:10,caption:"1"},
         *    {to:500,caption:"2"},
         *    {to:1050,caption:"3"}
         * ]
         * Ex:
         * nodes:[
         *    {to:$("#div1"),caption:"1"},
         *    {to:$("#div2"),caption:"2"},
         *    ....
         * ]
         */ 
        nodes:[],
        _nodes:[],
        lock:new jlock(),
        
        /** center,left,right*/
        stick:"center",
        /** варианты расположния кнопок 
         * bpb bbp pbb
        */
        layout:"pbb",
        gap:0,
        gapNode:2,
        margin:0,
        cssScrollBar:'ws_scrollbar',
        animate:500,
        scrollAdd:10,
        onClick:undefined,
        onAfterScroll:undefined,
        css:{
            panel:  'jnavigate_panel',
            up:     'jnavigate_up',
            down:   'jnavigate_down',
            node:   'jnavigate_node',
            hover:  'jnavigate_hover',
            active: 'jnavigate_active',
            disable: 'jnavigate_disable',
        }
        
    },o);
    
    
    t.param.margin = JX.margin(t.param.margin);
    
    t._create();
    t._event();
    t.put(o);
    
    Ws.align({
        id:t.param._alignFunc,
        recall:true,
        func:
        function(){ 
            
            t.align();
            
            
        }
    });
};
  

/** Destructor of Tjnavigate. Call on plugin is deleted.*/
Tjnavigate.prototype.done=function(){
    Ws.removeAlign(this.param._alignFunc);
    this.clear();
    this.param.plugin.html('');
};

Tjnavigate.prototype.clear=function(){
    var t=this,p=t.param;
    p.nodes=[];
    p._nodes=[];
    p.jq.panel.html("");
    t.attr('scrollObject',null);
    
};

/** возвращает код узла к отображению*/
Tjnavigate.prototype._create=function(){
    var t=this,p=t.param,css=p.css,c,jq=p.jq,id=p.id;
    
    c = t._codeFrame();    
    p.plugin.append(c);
    
    jq.up = p.plugin.find('#'+id.up);
    jq.down = p.plugin.find('#'+id.down);
    jq.panel = p.plugin.find('#'+id.panel);
    
};

/** возвращает код узла к отображению*/
Tjnavigate.prototype._codeFrame=function(node){
    var t=this,p=t.param,css=p.css,c='',
    id=p.id;
    
    c=ut.tag({id:id.up,css:css.up,style:"position:absolute"});    
    c+=ut.tag({id:id.panel,css:css.panel+' '+p.cssScrollBar,style:"position:absolute;overflow-y:auto;overflow-x:hidden"});
    c+=ut.tag({id:id.down,css:css.down,style:"position:absolute"});
    
    return c;
};

/** возвращает код узла к отображению*/
Tjnavigate.prototype._codeNode=function(node){
    var t=this,p=t.param,css=p.css,c;
    c=ut.tag({id:node.id,value:node.caption,css:css.node,style:"position:absolute",attr:{title:node.title}});
    return c;
};
Tjnavigate.prototype._event=function(){
    var t=this,p=t.param,css=p.css,l=p.lock;
    
    p.jq.panel.on('click',function(e){
        var et = $(e.target);
        if (et.hasClass(css.node)){
                
                var a = t._nodeToIndex($.data(t.current()[0],'data'));
                var b = t._nodeToIndex($.data(et[0],'data'));
                var lockAnimate = Math.abs(a-b)>1;
                
                if (lockAnimate)
                    l.lock('animate');
                    
                t.current(et);
                if (p.onClick)
                    p.onClick({sender:t,node:t.current()});
                
                if (lockAnimate)
                    l.unlock('animate');
        }
    });
    
    p.jq.up.on('click',function(){
        t.prev();
    });

    p.jq.down.on('click',function(){
        t.next();
    });
    
};    
Tjnavigate.prototype._doScroll=function(){
    var t=this,p=t.param,l=p.lock;
    if (l.lock('scroll')){
        
        var node = t.nodeInViewPort();
        if (node) t.current(node);
    }
    l.unlock('scroll');        
};

Tjnavigate.prototype._scroll=function(node){
    var t=this,p=t.param,css=p.css,scroll=node.to,l=p.lock;
    
    var animate = l.can('animate')?p.animate:0;
    
    if (!l.can('scroll')) return;

    if (JX.is_jquery(node.to))
        scroll = JX.pos(node.to).y-p.scrollAdd;
    
    if (p.scrollObject){
        if (animate>0){
                    p.scrollObject.stop(true).animate({scrollTop:scroll}, {
                        duration:animate,
                        start:
                        function(){
                            l.lock('scroll');
                        },
                        done:
                        function(){
                            setTimeout(function(){
                                l.unlock('scroll');
                                if (p.onAfterScroll) p.onAfterScroll({sender:t,node:t.current()});
                            },100);
                        }
                        
                    });
                
        }else{
            if (l.lock('scroll'))
                p.scrollObject.scrollTop(scroll);
            setTimeout(function(){
                l.unlock('scroll');
                if (p.onAfterScroll) p.onAfterScroll({sender:t,node:t.current()});

            },100);
        }    
    }    
};

Tjnavigate.prototype._inViewPort = function(obj,parent,move){
    var pos = JX.pos(obj),own = JX.pos(parent),scroll = parent.scrollTop();
    var out = (pos.y>=scroll)&&(pos.y<scroll+own.h);
    
    if ((!out)&&(move===true)){
        scroll =pos.y<scroll? pos.y:pos.y-own.h+pos.h;

        parent.scrollTop(scroll);  
        
        
        out = true;    
    };
    return out;
    
};
/** возвращает node соотвествующий текущему видимому обекту на scrollObject */

Tjnavigate.prototype.nodeInViewPort = function(){
    var t=this,p=t.param,i,node,pos,cs,sq=0,snode=false,
        bound=JX.pos(p.scrollObject),scroll=p.scrollObject.scrollTop();
    bound.y=scroll;
    
    for(i=0;i<p.nodes.length;i++){
        node = p.nodes[i];
        if (typeof node.to ==='number'){
            if ((bound.y<=node.to)&&(bound.y+bound.h>=node.to)){
                snode =node;
                break;
            }    
        }else{
            
            pos =JX.pos(node.to)
            cs = JX.square(pos,bound);
            if (cs>sq){
                snode =node;
                sq=cs;
            }    
        }
    }
    return snode;    
};
Tjnavigate.prototype.next = function(){
    var t=this,p=t.param,c=t.current(),node=false,i;
    
    if (c.length===0)
        node = p.nodes[0];
    else{
        node = $.data(c[0],'data');
        i = t._nodeToIndex(node)+1;
        if ((i>0)&&(i<p.nodes.length))
            node = p.nodes[i];
    }
    
    if (node)
        t.current(node);
};

Tjnavigate.prototype.prev = function(){
    
    var t=this,p=t.param,c=t.current(),node=false,i;
    
    if (c.length===0)
        node = p.nodes[0];
    else{
        node = $.data(c[0],'data');
        i = t._nodeToIndex(node)-1;
        if (i>-1)
            node = p.nodes[i];
    }
    
    if (node)
        t.current(node);

};

Tjnavigate.prototype._nodeToIndex = function(node){
    var t=this,p=t.param,i;
    
    for(i=0;i<p.nodes.length;i++){
        if (p.nodes[i].id===node.id)
            return i;
    }
    
    return -1;
};

Tjnavigate.prototype.current = function(node){
    var t=this,p=t.param,jq=p.jq,css=p.css,i,l=p.lock;
    
    var activate=function(data){
        data.jq.addClass(css.active);
        
        t._scroll(data);
        
        t._inViewPort(data.jq,jq.panel,true);
        
    }
    
    
    if (node===undefined){
        return jq.panel.find('.'+css.active);
        
    }else{
        
        jq.panel.find("."+css.node).removeClass(css.active);
        
        if (typeof node==='number'){
            activate(p.nodes[node]);
            return;
        }
        
        if (JX.is_jquery(node)){
            if (node.hasClass(css.node))
                activate($.data(node[0],'data'));
            else{
                for(i=0;i<p.nodes.length;i++){
                    if (p.nodes[i].jq[0].id === node[0].id){
                        activate(p.noes[i]);
                        break;
                    }
                }
            }
            return;
        }else if (typeof node==='object'){
            activate(node);
            return;
        }
    }
};    
    

Tjnavigate.prototype.setNodes = function(o){
    var t=this,p=t.param,css=p.css,jq=p.jq,nodes=[],n,i,c='',l=p.lock;
    
    l.lock('align');
    
    if (JX.is_jquery(o)){
        $.each(o.children(),function(i){
            nodes.push({to:$(this),caption:(i+1),id:"node"+i,title:''});
        });
    }else if ($.isArray(o)){
        for(i=0;i<o.length;i++){
            n = o[i];
            if (JX.is_jquery(n))
                nodes.push({to:n,caption:(i+1),id:"node"+i,title:''});
            else if (typeof n==='object')
                nodes.push($.extend(true,{to:undefined,caption:(i+1),id:"node"+i,title:''},n));
            else if (typeof n==='number')
                nodes.push({to:n,caption:(i+1),id:"node"+i,title:''});
        };   
    };
    
    for(i=0;i<nodes.length;i++)
        c+=t._codeNode(nodes[i]);
    
    jq.panel.append(c);
    
    p.jq.nodes = jq.panel.find('.'+css.node);
    
    for(i=0;i<nodes.length;i++){
        q = jq.panel.find('#'+nodes[i].id);
        nodes[i].jq = q;
        p._nodes.push(q);
        $.data(nodes[i].jq[0],'data',nodes[i]);
        
        q.hover(function(){
            
            p.jq.nodes.removeClass(css.hover);
            var _t=$(this);
            if (!_t.hasClass(css.active))
                _t.addClass(css.hover);
            
        },
        function(){
            $(this).removeClass(css.hover);
        });
    }

    
    if ((p.scrollObject===null)&&(nodes.length>0)&&(JX.is_jquery(nodes[0].to) ))
        t.attr('scrollObject',nodes[0].to.parent());

    p.nodes = nodes;
    
    if (p.nodes.length>0)
        p.nodes[0].jq.addClass(css.active);
    
    
    
    if (l.unlock('align'))
        t.align();
};

Tjnavigate.prototype._css = function(css){
    var t = this,p=t.param;
    if (css===undefined)
        return p.css;
        
    for (var key in css) {    
        if (key in p.css)
            p.plugin.find('.'+p.css[key]).removeClass(p.css[key]).addClass(css[key]);
    }
    
    p.css=$.extend(true,p.css,css);
};


Tjnavigate.prototype.put = function(o){
    var t=this,p=t.param,l=p.lock;
    
    l.lock('align');


    if ('nodes' in o){
        t.clear();
        t.setNodes(o.nodes);
    }
    
    
    $.each(o,function(n,v){
        t.attr(n,v);
    });
    
    if (l.unlock('align'))
        t.align();
};    

Tjnavigate.prototype.attr = function(n/*v*/){
    /*-----------------------------------*/
    if (arguments.length===0) 
        return;
    var t=this,p=t.param,v,r=(arguments.length===1);
    if (!r) 
        v=arguments[1];

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
    if (n==='scrollObject'){
        if (r) 
            return p.scrollObject;
        else{
            if (p.scrollObject!==null)
                p.scrollObject.off('scroll')
            
            p.scrollObject = v;

            if (p.scrollObject!==null)
                p.scrollObject.on('scroll',function(){ t._doScroll(); });
        }    
    }
    /*-----------------------------------*/
    if (n==='onClick'){
        if (r) 
            return p.onClick;
        else    
            p.onClick = v;
    }
    /*-----------------------------------*/
    if (n==='onAfterScroll'){
        if (r) 
            return p.onAfterScroll;
        else    
            p.onAfterScroll = v;
    }
    /*-----------------------------------*/    
    if (n==='animate'){
        if (r) 
            return p.animate;
        else    
            p.animate = v;
    }
    /*-----------------------------------*/
    if (n==='margin'){
        if (r) 
            return p.margin;
        else    
            p.margin = $.extend(true,p.margin,JX.margin(v));
    }
    /*-----------------------------------*/
    if (n==='stick'){
        if (r) 
            return p.stick;
        else    
            p.stick = v;
    }
    /*-----------------------------------*/
    if (n==='layout'){
        if (r) 
            return p.layout;
        else    
            p.layout = v;
    }
    /*-----------------------------------*/
    if (n==='gap'){
        if (r) 
            return p.gap;
        else    
            p.gap = v;
    }
    /*-----------------------------------*/
    if (n==='gapNode'){
        if (r) 
            return p.gapNode;
        else    
            p.gapNode = v;
    }
    /*-----------------------------------*/
    if (n==='cssScrollBar'){
        if (r) 
            return p.cssScrollBar;
        else    
            p.cssScrollBar = v;
    }
    /*-----------------------------------*/
    if (n==='css'){
        if (r) 
            return p.css
        else{    
           t._css(v);
        }    
    }
    /*-----------------------------------*/
    t.align();
};

/** Call this method to redraw all DOM elements*/ 
Tjnavigate.prototype.align=function(){
    var t=this,p=t.param,l=p.lock;
    
    if (l.lock('align'))
        t._align();
    l.unlock('align');   
    
};

/** Method calling from Tjnavigate.align(). Custom call not needed*/ 
Tjnavigate.prototype._align=function(){
    var t=this,p=t.param,jq=p.jq,arr;
    
    if (p.layout==='bpb')
        arr=[jq.up,jq.panel,jq.down];
    else if (p.layout==='bbp')
        arr=[jq.up,jq.down,jq.panel];
    else 
        arr=[jq.panel,jq.up,jq.down];
        
    JX.arrange(arr,{direct:"vert",type:"stretch",align:p.stick,stretch:jq.panel,gap:p.gap,margin:p.margin});
    
    JX.arrange(p._nodes,{direct:"vert",type:"top",align:"center",gap:p.gapNode});
    
};