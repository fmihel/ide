/*global ut,$,jQuery,JX,Qs,Ws*/
/** @module trial/gallery/jgallery*/
/**
 * plugin: jgallery<br>    
 * file  : jgallery.js<br>
 * path  : trial/gallery/<br>
 * @class jgallery
 *
*/
(function( $ ){
/** @lends module:trial/gallery/jgallery~jgallery# */
var m={
name:"jgallery",
/**
 * Create plugin and handler object {@link module:trial/gallery/jgallery~Tjgallery Tjgallery}
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.jgallery(param)
 * </div>
 * @param {json} param full list of param see in {@link module:trial/gallery/jgallery~Tjgallery.param Tjgallery.param}
 * @return this
 * @example 
 * $('#div').jgallery({
 * ...
 * });
 * @example get attr (1)
 * var t = $('#div').jgallery("multiSelect");
 * @example get attr (2)
 * var t = $('#div').jgallery("get","multiSelect");
 * @example set attr (1)
 * $('#div').jgallery("put",{multiSelect:true});
 * @example set attr (2)
 * $('#div').jgallery({multiSelect:true});
 * 
 *   
*/
init:function(param){
    var o = m.obj(this),p;
    
    if ($.isArray(param))
        param={data:param};

    if (o===undefined){
            
        p=$.extend(true,{},param);
        p.plugin = this;
        m.obj(this,new Tjgallery(p));
    }else
        o.put(param);                
        
    return this;     
},
/**
 * Deleted plugin and related objects
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.jgallery("destroy")
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
    return (m.obj(this)!==undefined)
},
/**
 * Story the handler object Tjgallery in jquery 
 * @private
 * @return Tjgallery
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
 * Sets the parameters of the object Tjgallery, see {@link module:trial/gallery/jgallery~Tjgallery.param }
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.jgallery('put',param)
 * </div>
 * @param {json} param full list of param see in {@link module:trial/gallery/jgallery~Tjgallery.param}
 * @return this
 * @example 
 * $('#div').jgallery('put',{ 
 *     param1:value1,
 *     param2:value2 
 * });
*/
put:function(param){
    m.obj(this).put(param);
    return this;
},
/**
 * Gets the value of the object's parameters (see {@link module:trial/gallery/jgallery~Tjgallery.param})
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.jgallery("get",name)
 * </div>
 * @param {string} name name of param in jgallery  {@link module:trial/gallery/jgallery~Tjgallery.param}
 * @return mixed type
 * @example 
 * var v = $('#div').jgallery('get',"deepSelect");
 *   
*/
get:function(name){
    return m.obj(this).attr(name);
},
/** делает текущим картинку
* @param {int|string|jQuery} int - индекс, string - id 
*/
current:function(item){
    var o = m.obj(this);
    if (item)
        o.current(item);
    else
        return o.current(item);
},
story:function(name){
    var o = m.obj(this);
    return o.story(name);
},
reStory:function(name){
    var o = m.obj(this);
    return o.reStory(name);
},
next:function(){
    var o = m.obj(this);
    o.step(1);

},
prev:function(){
    var o = m.obj(this);
    o.step(-1);
},
addPlugin:function(param){
    var o = m.obj(this);
    o.pluginsAdd(param);
},
clear:function(){
    var o = m.obj(this);
    o.clear();
},


};

$.fn.jgallery = function(n){
    if(m[n])
        return m[n].apply(this,Array.prototype.slice.call( arguments, 1 ));
    else 
    if ((typeof n==='object')||(!n)){
        return m.init.apply(this,arguments);
    }else{
        if (typeof n==='string')
            return  m.get.apply(this,[n]);
        else    
            $.error( 'Not exists method ' +  n + ' for jQuery.jgallery');
    }    
};
})(jQuery);

/**
 * Handler for jgallery plugin. Propertys this class story in param,
 * see {@link module:trial/gallery/jgallery~Tjgallery.param}
 * @class Tjgallery
 * @property {json} param see {@link module:trial/gallery/jgallery~Tjgallery:param param}
 * 
*/
function Tjgallery(o){
    var t = this;
    t.init(o);
}
/** Constructor of Tjgallery. Initial creation and initialization of the object.*/
Tjgallery.prototype.init = function(o){
    var t=this;
    /** @namespace module:trial/gallery/jgallery~Tjgallery:param */
    t.param = $.extend(true,
    /** @lends module:trial/gallery/jgallery~Tjgallery:param#*/
    {
        plugin:null,
        id:ut.id('jgallery'),
        lock:new jlock(),
        data:[],
        
        name:undefined,
        storyObjectName:"jgallery",
        /** список подключенных плагинов */
        plugins:[],
        /** width,height,tile,number*/ 
        placeBy:"height",
        /** center,left,right*/
        align:"center",
        margin:0,
        /** кол во кусков для paceBy = "tile" */
        tile:2,
        direct:"vert",
        onChangeCurrent:undefined,
        onChangeView:undefined,
        onLoading:undefined,
        onLoad:undefined,
        onClear:undefined,
        /** отступ между изображениями */
        gap:10,
        /** отступ при вписывании в видимую область */
        indent:20,
        /** скорость анимации */
        animation:500,
        /** изображение, которое грузится на в случае ошибки загрузки */
        //imgOnError:'http://windeco.su/pdfcatalog/JPG/Drapirov_kolca_foto/Drapirov_kolca_foto-000001.jpg',
        imgOnError:'',
        /** включает режим при котором видимая картинка автоматически становится текущей */
        viewIsCurrent:false,
        css:{
            current :'jgallery_current',
            common  :'jgallery_common'
        }
        
    },o);
    
    t.param.plugin.css({'overflow-x':"hidden",'overflow-y':"auto"});
    t.param.margin = JX.margin(t.param.margin);
    
    
    t.param.plugin.on("scroll",function(){
        t._onScroll();
    });
    t.put(t.param);
    
    Ws.align(function(){t.align();});
};
  

/** Destructor of Tjgallery. Call on plugin is deleted.*/
Tjgallery.prototype.done=function(){
    this.clear();
};
/** полная очистка галлереи */
Tjgallery.prototype.clear=function(o){
    var t=this,p=t.param,
    a=$.extend(true,{
        callOnClear:true,
    },o);
    
    p.data = [];
    p._images=[];
    p.plugin.html("");
    p.plugin.scrollTop(0);
    p._view = undefined;
    p._current     = undefined;
    if ((a.callOnClear)&&(p.onClear))
        p.onClear({sender:t});
    t.pluginsDo('clear');
    
};    

/** перемещение на off рисунков относительно текущего */
Tjgallery.prototype.step=function(off){
    
    var t=this,p=t.param,cnt = t.count(),l=p.lock,c;
    if ((!off)||(cnt===0)) return;

    c=t.current();
    
    if (c.length === 0){
        c=t.view();
        l.lock('animate');
        t.current(c);
        l.unlock('animate');
    }
    
    var nxt = t.indexOf(c)+off;
    
    if ((nxt<0)||(nxt>=t.count())) return;
        
    nxt=t.item(nxt);
    if (Math.abs(off)>1)
        l.lock('animate');
    
    t.current(nxt);

    if (Math.abs(off)>1)
        l.unlock('animate');
    
    
    
};

/** добавляет класс к текущему изображению */
Tjgallery.prototype._current=function(it){
    var t=this,p=t.param,css=p.css,i,pi,idx=-1;

    for(i=0;i<p._images.length;i++){
        pi = p._images[i];
        if (pi[0].id!==it[0].id) 
            pi.removeClass(css.current);
        else{
            pi.addClass(css.current);
            idx=i;
        }    
    }
    
    
    if (p.onChangeCurrent) 
        p.onChangeCurrent({current:it});
    
    t.pluginsDo('change',{current:it,idx:idx});    
};

/** возвращает или устанавливает текущее изображение, с переносом его в область просмотра */
Tjgallery.prototype.current=function(o){
    var t=this,p=t.param,css=p.css,i,it,pi,l=p.lock;
      
    if ((o===undefined)||(o===null))
        return p.plugin.find('.'+css.current);

    it = t.item(o);

    l.lock('scroll');
    l.lock('viewIsCurrent');
    t.view(it,function(){
        l.unlock('scroll');
        t._onScroll();
        l.unlock('viewIsCurrent');
    });
    t._current(it);    

};

/** определяет или устанавливает текущий видимый рисунок */
Tjgallery.prototype.view=function(o,done){
    var t=this,p=t.param,i,it=false,rect,l=p.lock,
        bound = {x:p.plugin.scrollLeft(),y:p.plugin.scrollTop(),w:JX.pos(p.plugin).w,h:JX.pos(p.plugin).h},out;
    
    if (o===undefined){
        if (p.direct==="vert"){
            var sq=null,smax,s=0;
            for(i=0;i<p.data.length;i++){
                it = p.data[i];
                rect = JX.pos(it.jq);
                //if (JX.inline(rect.y,bound.y,bound.y+bound.h/2))
                //    return it.jq;
                s = JX.square(rect,{x:bound.x,y:bound.y,w:bound.w,h:bound.h/2});
                if ((sq===null)||(s>smax)){
                    smax = s;
                    sq = it.jq;
                }
            }
            
            return sq;
        }
    }else{
        
        if (typeof o === "number")
            it = p._images[o];

        if (typeof o === "string")
            it = t.item(o);

        if (typeof o === "object")
            it = o;

        if ((it)&&(it.length>0)&&(p.direct==="vert")){
            var to = JX.pos(it).y-(p.placeBy==='width'?0:p.indent);
            
            if ((p.animation===0) || (!l.can("animate")) ){
                p.plugin.scrollTop(to);
                if (done) done();
                
            }else{
                p.lock.lock('scroll');
                
                p.plugin.stop(true).animate({scrollTop:to},p.animation,function(){
                    p.lock.unlock('scroll');
                    t._onScroll();
                    if (done) done();    
                });
            }    
        }    
        
    }
    
    
};    

/** кол-во изображений */
Tjgallery.prototype.count=function(){
    return this.param.data.length;    
};

/** получить изображение по индексу или по id */
Tjgallery.prototype.item=function(idxOrId){
    var t=this,p=t.param,it,i;
    try{
        if (typeof idxOrId==="object")
            return idxOrId;
        
        if (typeof idxOrId==="number")
            return p.data[idxOrId].jq;
    
        if (typeof idxOrId==="string"){
            for(i=0;i<p.data.length;i++){
                it = p.data[i].jq;
                if (it[0].id===idxOrId)
                    return it;
            }
        }
    }catch(e){
        
    }    
    return null;
};

/** порядковый номер изображения */
Tjgallery.prototype.indexOf=function(item){
    var t=this,p=t.param,it,i;
    
    if ((item)&&(item.length))
    for(i=0;i<p.data.length;i++){
        it = p.data[i].jq;
        if (it[0].id===item[0].id)
            return i;
    }
    return -1;

    
};

Tjgallery.prototype.story=function(name){
    var t=this,p=t.param;
    
    
    var obj = bdata.json_get(p.storyObjectName);
    
    obj.placeBy = p.placeBy;
    if (!obj.states) obj.states = {};
    
    name = (name!==undefined?name:p.name);
    if (name !== undefined){ 
        obj.states[name] = {
            st:p.plugin.scrollTop(),
            sl:p.plugin.scrollLeft(),
            c:t.indexOf(t.current())
        };
    }
    
    bdata.json_set({key:p.storyObjectName,val:obj});
    
    return true;
    
};

Tjgallery.prototype.reStory=function(name){
    var t=this,p=t.param,obj,item,state,l=p.lock;
    
    if (bdata.exist(p.storyObjectName)){
        
        l.lock('animate');
        l.lock('scroll');  
        
        p._view         = undefined;
        p._current      = undefined;
        
        
        obj = bdata.json_get(p.storyObjectName);
        t.put(obj);
        
        name = (name!==undefined?name:p.name);
        if (name !== undefined){ 
            states = obj.states;
            
            if ((states)&&(states[name])){
                state = states[name];
                item = t.item(parseInt(state.c));
            
                t.current(item);
            
                p.plugin.scrollTop(state.st);
                p.plugin.scrollLeft(state.sl);
        
            }
        
        }
        l.unlock('scroll');
        l.unlock('animate');
        t._onScroll();
    }
    return true;
};    

Tjgallery.prototype.pluginsDo=function(event,data){
    var t=this,p=t.param,i,pl;
    for(i=0;i<p.plugins.length;i++){
        pl = p.plugins[i];
        if (pl.obj.onGallery)
            pl.obj.onGallery({event:event,sender:t,data:data});
            
    }
};
Tjgallery.prototype.pluginsAdd=function(o){
    var t=this,p=t.param,
    a=$.extend(true,{
        name:ut.id("plugins"),
        obj:undefined
    },o);
    
    if (!a.obj) return;
    
    p.plugins.push(a);
    
    if (a.obj.onGallery)
        a.obj.onGallery({event:'init',sender:t});
    
};    

/** обработчик скролирования, пороверяет не изменился ли отображаемый рисунок, и при смене вызывает onChangeView */
Tjgallery.prototype._onScroll=function(){
    var t=this,p=t.param,pos,l=p.lock,c,need=false;
    if (!l.can("scroll")) return;
    
    c=t.view();
    if ((!p._view)&&(c)){
        p._view = c;
        need = true;
    }else if ((p._view)&&(c)&&(p._view[0].id!==c[0].id)){
        p._view = c;
        need = true;
    }
    
    if (need){
        var data = {view:c,idx:t.indexOf(c)};

        if ((p.viewIsCurrent)&&(l.can('viewIsCurrent')))
            t._current(c);
        
        if (p.onChangeView)
            p.onChangeView(data);
        t.pluginsDo('scroll',data);
    }    

};

/** обработчик загрузки изображения */
Tjgallery.prototype._onLoad=function(e){
    var t=this,p=t.param,img = e.currentTarget,need = true;
    
    if( e.type==='error'){
        if (p.imgOnError!==''){
            need = false;
            img.src = p.imgOnError;
        }    
    }    

    if (need){
        p._loadingCount++;
    

        $(img).show(0);
        t.align();

        if (p.onLoading)
            p.onLoading({all:p._images.length,current:p._loadingCount});
        t.pluginsDo('loading');
    
    
        if (p._loadingCount==p._images.length){
            if (p.onLoad) p.onLoad();
            t.pluginsDo('load');
        }    
    }    
    
};

/** создаем объект */
Tjgallery.prototype._create=function(){
    var t=this,p=t.param,css=p.css,img,
    onload = function(e){
        t._onLoad(e);
    };
    p._loadingCount = 0;
    
    p._images = [];
    for(var i=0;i<p.data.length;i++){
        
        img=new Image();    
        p.data[i].img = img;
        p.data[i].jq = $(img);
        p.data[i].jq[0].id = p.data[i].id;
        
        $.data(p.data[i].jq[0],'data',p.data[i].data);
        
        p._images.push(p.data[i].jq);
        img.onload=onload;
        $(img).on('error',onload);
        
        p.plugin.append(img);
        
        
        p.data[i].jq.css({"position":"absolute"}).addClass(css.common).hide(0);
        

        img.src = p.data[i].url;

    }
    
    t.pluginsDo('create');
    t._onScroll();

};

/** нормализация входных данных от пользователя 
* 
*/
Tjgallery.prototype._dataNormal=function(data){
    var i,out=[],d;
    
    if (data===undefined) 
        data = [];
    
    for(i=0;i<data.length;i++){
        d = data[i];
        if (typeof d === "string")
            out.push({url:d,data:undefined,id:ut.id('img')});
        else if (typeof d === "object")
            out.push($.extend(true,{url:'',data:undefined,id:ut.id('img')},d));
        else
            out.push({url:'',data:undefined,id:ut.id('img')});
    }        
    
    return out;    

};

/**
 * Set parameters to {@link Tjgallery.param}
 * @param {json} o
 * @example xxx.put({name:"value"});
*/ 
Tjgallery.prototype.put = function(o){
    var t=this,p=t.param,l=p.lock,placeBy=p.placeBy;
    
    l.lock('align');
    
    if (o.data!==undefined){
        var data = $.extend(true,[],o.data);
        t.clear({callOnClear:false});
        p.data = t._dataNormal(data);
        t._create();
    }
    
    
    
    
    $.each(o,function(n,v){
        t.attr(n,v);
    });
    
    
    
    if (l.unlock('align')){
        t.align();
        
        if (placeBy!==p.placeBy)
            t.view(t.current())  

    }    
    
        
};    
/**
 * Set or get parameters from {@link Tjgallery.param}
 * @param {string} n name of parameter
 * @param {undefined | mixed} v undefined or mixed data 
 * @example xxx.attr("select",true);
 * @example var v = xxx.attr("select");
*/ 
Tjgallery.prototype.attr = function(n/*v*/){
    /*-----------------------------------*/
    if (arguments.length===0) 
        return;
    var t=this,p=t.param,v,r=(arguments.length===1);
    if (!r) 
        v=arguments[1];

    /*-----------------------------------*/
    if (n==='placeBy'){
        if (r) 
            return p.placeBy;
        else    
            p.placeBy = v;
    }
    /*-----------------------------------*/
    if (n==='align'){
        if (r) 
            return p.align;
        else    
            p.align = v;
    }
    /*-----------------------------------*/
    if (n==='margin'){
        if (r) 
            return p.margin;
        else    
            p.margin = $.extend(true,p.margin,JX.margin(v));
    }
    /*-----------------------------------*/
    if (n==='css'){
        if (r) 
            return p.css;
        else    
            p.css = $.extend(true,p.css,v);
    }
    /*-----------------------------------*/
    if (n==='tile'){
        if (r) 
            return p.tile;
        else    
            p.tile = v>2?v:2;
    }
    /*-----------------------------------*/
    if (n==='onChangeCurrent'){
        if (r) 
            return p.onChangeCurrent;
        else    
            p.onChangeCurrent = v;
    }
    /*-----------------------------------*/
    if (n==='onChangeView'){
        if (r) 
            return p.onChangeView;
        else    
            p.onChangeView = v;
    }
    /*-----------------------------------*/
    if (n==='onLoading'){
        if (r) 
            return p.onLoading;
        else    
            p.onLoading = v;
    }
    /*-----------------------------------*/
    if (n==='onLoad'){
        if (r) 
            return p.onLoad;
        else    
            p.onLoad = v;
    }
    /*-----------------------------------*/
    if (n==='onClear'){
        if (r) 
            return p.onClear;
        else    
            p.onClear = v;
    }
    /*-----------------------------------*/
    if (n==='gap'){
        if (r) 
            return p.gap;
        else    
            p.gap = v;
    }
    /*-----------------------------------*/
    if (n==='animation'){
        if (r) 
            return p.animation;
        else    
            p.animation = v;
    }
    /*-----------------------------------*/
    
    
    t.align();
};

/** Call this method to redraw all DOM elements*/ 
Tjgallery.prototype.align=function(){
    var t=this,p=t.param,l=p.lock;
    
    if (l.lock('align'))
        t._align();
    l.unlock('align');   
    
};

Tjgallery.prototype._maxArea=function(){
    var t=this,p=t.param,out={w:0,h:0},pos;
    
    if (p._images.length>0){
        pos = JX.pos(p._images[p._images.length-1]);
        out = {h:pos.y+pos.h,w:pos.x+pos.w};
    }
    return out;
}    

Tjgallery.prototype._getScroll=function(){
    var t=this,p=t.param,out={scroll:{x:0,y:0},max:{w:0,h:0}},pos;
    
    out.scroll = {y:p.plugin.scrollTop(),x:p.plugin.scrollLeft()};
    out.max    = t._maxArea();    

    return out;
}

/** Method calling from Tjgallery.align(). Custom call not needed*/ 
Tjgallery.prototype._align=function(){
    var t=this,p=t.param,fr=p.plugin,i,it,newW,newH,k,l=p.lock,pos;
    var bound= JX.pos(fr),scroll,area;
    l.lock("scroll");
    scroll = t._getScroll();
    
    
    if (p.direct==='vert'){

        if ((p.placeBy==='height')||(p.placeBy==='tile')){
                bound.h = bound.h-p.indent*2;
                bound.w = bound.w-p.indent*2;
        }
        
        for(i=0;i<p.data.length;i++){
            it=p.data[i].img;
            if (it.naturalWidth === 0){
                k = 497/352;
            }else
                k = it.naturalHeight/it.naturalWidth;
                
            if (p.placeBy==='width'){
                
                it.width = bound.w-(p.margin.left+p.margin.right);
                it.height = it.width*k;
                
            }else if (p.placeBy==='height'){
                //bound.h = bound.h-5;
                //bound.w = bound.w-5;
                
                newW = bound.h/k;
                if (newW<bound.w){
                    it.height = bound.h;
                    it.width  = newW;
                }else{    
                    it.width=bound.w;
                    it.height = bound.w*k;
                }    
            }else if (typeof p.placeBy === 'number'){
                
                it.width = p.placeBy;
                it.height = p.placeBy*k;
            
            }else{
                var bound2 = {w:bound.w/p.tile,h:bound.h};
                
                newW = bound2.h/k;
                if (newW<bound2.w){
                    it.height = bound2.h;
                    it.width  = newW;
                }else{    
                    it.width=bound2.w;
                    it.height = bound2.w*k;
                }    

            }    
        }
        
        if (p.placeBy!=='tile')
            JX.arrange(p._images,{direct:"vert",type:"top",align:p.align,gap:p.gap,margin:p.placeBy==="height"?p.indent:p.margin});
        else
            JX.tile(p._images,{count:p.tile,align:{horiz:"center"},inside:{horiz:"center"},tail:"inherit",gap:{vert:p.gap,horiz:p.gap*2}});
            

        area = t._maxArea();
        p.plugin.scrollTop(ut.translate(scroll.scroll.y,0,scroll.max.h,0,area.h));
        
        
    }
    l.unlock("scroll");
};


    