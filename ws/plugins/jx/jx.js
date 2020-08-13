/*global $,navigator*/
/** @module ws/plugins/jx*/

/**
 * Библиотека предназначена для работы с координатами и размерами DOM объектов. Для передачи координат и габаритов используется
 * структура {@link ws/plugins/jx~JX:rect rect} 
 * <br><br><b>Внимание!</b> Объекты DOM должны иметь стиль <b>position:absolute</b>.
 * <br><b>Внимание!</b> Все ф-ции имеющие в имени префикс _(подчеркивание) работают только с DOM и имеют статус private
 * @summary Низкоуровневая библиотека для работы с позицией, габаритами и относительным позиционированием объектов DOM
 * @class
 * 
*/ 
var JX = {
/** 
 * кеш библиотеки 
 * @private
*/
_dx_:{mouse:{x:0,y:0}},
_lh:[],
/** 
 * конструктор библиотеки, выполняется один раз при загрузке 
 * @private
*/
_init:function(){
    var t = JX;
    t.window().on("mousemove",function(e){
        t._dx_.mouse = {x:e.originalEvent.clientX,y:e.originalEvent.clientY};
    })

},
/** 
 * Если задан второй параметр, то возвращает относительные координаты и габариты объекта.
 * Если второй параметр задан как json = {x:float,y:float,w:float,h:float}, то будут установлены относительные координаты объекта
 * @param {jquery|DOM} o целевой объект
 * @param {undefined|rect} rect объект {@link ws/plugins/jx~JX:rect rect}
 * @example 
 * JX.pos($("#div"),{h:100,w:200});
 * @example 
 * var v = JX.pos($("#div"));
 * 
*/
pos:function(o,bound){
    var d=JX._dom(o);
    if (bound!==undefined) 
        JX._pos(d,bound);
    else
        return JX._pos(d);
},
size:function(o/*rect*/){
    var d=JX._dom(o);

    if (arguments.length>1){ 
        var a = arguments[1],n={};
        if (a.w!==undefined) n.w=a.w;
        if (a.h!==undefined) n.h=a.h;
        
        JX._pos(d,n);
    }else{
        var r = JX._pos(d);
        return {w:r.w,h:r.h};
    }    
},
/*сбрасываются сохраненные значения рамок, использовать перед расчетом габаритов, если рамка меняется динамически*/
reset:function(o){
    if (JX.is_jquery(o))
    $.each(o,function(){
        delete this._brdrjx_;
    });
    else
        delete o._brdrjx_;
},
animate:function(o,to/*param*/){
    var prm=arguments.length>2 ? arguments[2] : {};
    var p = 
    {
        onmove:null,
        speed:200
    };
    $.extend(true,p,prm);
    
    var d=JX._dom(o);
    var r = JX._pos(d);
    var a = {};
    if (to.x!==r.x) a.left   = to.x;
    if (to.y!==r.y) a.top    = to.y;
    if (to.w!==r.w) a.width  = to.w;
    if (to.h!==r.h) a.height = to.h;
    
    $(d).animate(a,{
        duration:p.speed,
        step:
        function(){
            JX._pos(d,to);
            if (p.onmove)p.onmove("step");
        },
        complete:
        function(){
            JX._pos(d,to);
            if (p.onmove)p.onmove("stop");
        }
    });
},

/** 
 * Если задан второй параметр, то возвращает абсолютные координаты и габариты объекта.
 * Если второй параметр задан как json = {x:float,y:float,w:float,h:float}, то будут установлены абсолютные координаты объекта
 * @param {jquery|DOM} o целевой объект
 * @param {undefined|rect} rect объект {@link ws/plugins/jx~JX:rect rect}
 * @example 
 * JX.pos($("#div"),{h:100,w:200});
 * @example 
 * var v = JX.pos($("#div"));
 * 
*/
abs:function(o/*rect*/){
    var d=JX._dom(o);
    
    if ((arguments.length>1)&&(arguments[1]!==undefined)) 
    {
        var a = JX._abs(d);
        var c = arguments[1];
        var p = JX._parent(d);
        var pa=(p.tagName=='BODY'?JX.screen():JX._abs(p));
        
        var otn = {x:a.x-pa.x,y:a.y-pa.y};
        if(c.x!==undefined) otn.x = c.x-pa.x+p.scrollLeft;          
        if(c.y!==undefined) otn.y = c.y-pa.y+p.scrollTop;          
        if(c.w!==undefined) otn.w = c.w;          
        if(c.h!==undefined) otn.h = c.h;
        JX._pos(d,otn);
        return;
    }else
        return JX._abs(d);
       
},

parent_pos:function(o/*rect*/){
    var d=JX._parent(JX._dom(o));
    var c=arguments.length>1 ? arguments[1] : undefined;
    return (d?JX._pos(d,c):{x:0,y:0,w:0,h:0});
},

childs:function(o){
    return JX._dom(o).children;     
},

arrange:function(childs/*p*/){
    var p=arguments.length>1 ? arguments[1] : {};
    var d = [],i;
    for(i=0;i<childs.length;i++) d.push(JX._dom(childs[i]));

    JX._arrange(d,p);
},

placer:function(o,i/*param*/){
    /*возвращает координаты i по отношению к внешнему 
    o,i : rect
    */
    var prm=arguments.length>2 ? arguments[2] : {};
    var p = 
    {
        vert:"center",
        horiz:"center",
        margin:{left:0,top:0,right:0,bottom:0}
    };
    $.extend(true,p,prm);
    var r = JX.copy(i);
    if(p.vert=="top")
        r.y=p.margin.top;
    else if(p.vert=="bottom")
        r.y=o.h-p.margin.bottom-r.h;
    else if(p.vert=="center")
        r.y = (o.h-r.h)/2+p.margin.top;
    
    if(p.horiz=="left")
        r.x=p.margin.left;
    else if(p.horiz=="right")
        r.x=o.w-p.margin.right-r.w;
    else if(p.horiz=="center")
        r.x = (o.w-r.w)/2+p.margin.left;
    return r;    
    
},

place:function(jq,o){
    var 
    p =$.extend(true,{
        by:null,/*rect */
        vert:"center",
        horiz:"center",
    },o),
    d = JX._dom(jq),
    pr=null,
    pd=null;
    p.margin = JX.margin(p.margin);
    
    if (p.by===null){
        pr = JX._pos(JX._parent(d));
        pd = JX._pos(d);
    }else{
        pr = p.by;
        pd = JX._abs(d);
    };
    pd = JX.placer(pr,pd,p);
    delete pd.w;
    delete pd.h;
    
    (p.by===null?JX._pos(d,pd):JX.abs(d,pd));
},
/**
 * располагает модифицирует координаты b так, чтобы они b липнул к a 
 * с учетом указанных в p
*/
clinger:function(a,b,prm){
    var p=$.extend(true,{
        side:{a:"right",b:"left"},
        pivot:{a:"center",b:"center"},
        off:{x:0,y:0}
    },prm),c;
    
    
    if ((p.side.a==='right')||(p.side.a==='left')||(p.side.a==="center")){
        
        if (p.side.a==="right"){
            
            if (p.side.b==='left')
                b.x = a.x+a.w;
            else if (p.side.b==='center')
                b.x = a.x+a.w-b.w/2;
            else
                b.x = a.x + a.w-b.w;
                
        }else if (p.side.a==="center"){ 
            c=a.x + a.w/2;             
            if (p.side.b==='left')
                b.x = c;
            else if (p.side.b==='center')
                b.x = c - b.w/2;
            else
                b.x = c-b.w;
                
        }else{
            
            if (p.side.b==='left')
                b.x = a.x;
            else if (p.side.b==='center')
                b.x = a.x-b.w/2;
            else
                b.x = a.x-b.w;;
        };    
         
        if (p.pivot.a==="top"){
            
            if (p.pivot.b==="top")
                b.y = a.y;    
            else if (p.pivot.b==="center")
                b.y = a.y-b.h/2;
            else
                b.y = a.y-b.h;
            
        }else if (p.pivot.a==="center"){    
            c=a.y + a.h/2;             
            if (p.pivot.b==="top")
                b.y = c;
            else if (p.pivot.b==="center")
                b.y = c-b.h/2;
            else    
                b.y = c-b.h;
        }else{
            if (p.pivot.b==="top")
                b.y = a.y+a.h;
            else if (p.pivot.b==="center")
                b.y = a.y+a.h-b.h/2;
            else    
                b.y = a.y+a.h-b.h;
        }    
    };
    
    
    if ((p.side.a==='top')||(p.side.a==='bottom')){
        
        if (p.side.a==='top'){
            
            if (p.side.b==="top")
                b.y = a.y;
            else if (p.side.b==="center") 
                b.y = a.y-b.h/2;
            else
                b.y = a.y-b.h;
                
        }else{
            
            if (p.side.b==="top")
                b.y = a.y+a.h;
            else if (p.side.b==="center") 
                b.y = a.y+a.h-b.h/2;
            else
                b.y = a.y+a.h-b.h;

        };
        
        if (p.pivot.a==="left"){
            if (p.pivot.b==='left')
                b.x = a.x;
            else if (p.pivot.b==='center')
                b.x = a.x-b.w/2;
            else
                b.x = a.x-b.w;
                
        }else if (p.pivot.a==="center"){
            c=a.x + a.w/2;             
            if (p.pivot.b==='left')
                b.x = c;
            else if (p.pivot.b==='center')
                b.x = c-b.w/2;
            else
                b.x = c-b.w;
        }else{
            if (p.pivot.b==='left')
                b.x = a.x+a.w;
            else if (p.pivot.b==='center')
                b.x = a.x+a.w-b.w/2;
            else
                b.x = a.x+a.w-b.w;
        }    
        
        
        
    }    
    
    
    b.x+=p.off.x;
    b.y+=p.off.y;
    return b;
    
},
/** прилипание объекта b к объекту a
 *  по умолчанию объект b прилипает своей левой гранью к правой грани a,
 *  рспологая свой вертикальны центр по центру a
 *  для задания других вариантов используется парметр prm
 *  
 * // прилипание горизонтально 
 * prm:{
 *      side:{ // грани, котроыми будут прилипать объекты                      
 *          a:"left" | "right" | "center",
 *          b:"left" | "right" | "center",
 *      },
 *      pivot:{ // как расположить объекты по вертикали
 *          a:"top" | "bottom" | "center",          
 *          b:"top" | "bottom" | "center",          
 *      }
 * }
 * // прилипание вертикально
 * prm:{
 *      side:{ // грани, котроыми будут прилипать объекты                      
 *          a:"top" | "bottom" | "center",          
 *          b:"top" | "bottom" | "center",          
 *      },
 *      pivot:{ // как расположить объекты по горизонтали
 *          a:"left" | "right" | "center",
 *          b:"left" | "right" | "center",
 *      }
 * }
 * 
 * // дополнительные параметры
 * prm:{
 *      abs:bool  - какой алгоритм расчета позиции 
 *      area: "screen" | jquery | false - область или объект за который не должен выйти b
 *      cross:bool  если после расчета позиции b, объекты a и b пересекаются, то будет проведено симметричное перерасположение
 *      strong:bool - тип а
 * }
 * 
 */  
cling:function(a,b,prm){
    var p=$.extend(true,{
        side:{a:"left",b:"left"},
        pivot:{a:"bottom",b:"top"},
        off:{x:0,y:0},
        abs:false,
        area:false,
        cross:false,
        strong:false,
    },prm),
    ad = JX._dom(a),
    bd = JX._dom(b),
    ap,bp;

    if (p.abs){
        ap = JX._abs(ad);
        bp = JX._abs(bd);
        
    }else{
        ap = JX._pos(ad);
        bp = JX._pos(bd);
    }
    
    bp = JX.clinger(ap,bp,p);
    
    delete bp.w;
    delete bp.h;
    
    if (p.abs)
        JX.abs(bd,bp);
    else
        JX._pos(bd,bp);
    
    if (p.area==='screen')
        JX.stick_scr(b);    
    else if (JX.is_jquery(p.area)){
        JX.stick(p.area,b);
    }
    
    if (p.cross){
        if (p.abs){
            ap = JX._abs(ad);
            bp = JX._abs(bd);
        }else{
            ap = JX._pos(ad);
            bp = JX._pos(bd);
        }
    
        if (JX.iscrossr(ap,bp,p.strong)){
            let isx = true,isy = true,
            fx=()=>{
                if (isx)p.off.x = -p.off.x;
                isx = false;
            },fy=()=>{
                if (isy)p.off.y = -p.off.y;
                isy = false;
            };
            
            if (p.side.a==="left") {p.side.a = "right";fx();}
            else if (p.side.a==="right"){ p.side.a = "left";fx();}
            else if (p.side.a==="top") {p.side.a = "bottom";fy();}
            else if (p.side.a==="bottom") {p.side.a = "top";fy();}

            if (p.side.b==="left") {p.side.b = "right";fx();}
            else if (p.side.b==="right") {p.side.b = "left";fx();}
            else if (p.side.b==="top") {p.side.b = "bottom";fy();}
            else if (p.side.b==="bottom") {p.side.b = "top";fy();}
            
            if (p.side.a==="center"){
                if (p.pivot.a==="left")         {p.pivot.a = "right";fx();}
                else if (p.pivot.a==="right")   {p.pivot.a = "left";fx();}
                else if (p.pivot.a==="top")     {p.pivot.a = "bottom";fy();}
                else if (p.pivot.a==="bottom")  {p.pivot.a = "top";fy();}
            }
            
            if (p.side.b==="center"){
                if (p.pivot.b==="left")         {p.pivot.b = "right";fx();}
                else if (p.pivot.b==="right")   {p.pivot.b = "left";fx();}
                else if (p.pivot.b==="top")     {p.pivot.b = "bottom";fy();}
                else if (p.pivot.b==="bottom")  {p.pivot.b = "top";fy();}
                
            }
            
            
            p.cross = false;
            JX.cling(a,b,p);
        }
    }    
        

},
stretch_scr:function(o){
    var prm=arguments.length>1 ? arguments[1] : {};
    var p = {margin:{left:0,top:0,right:0,bottom:0,by:null}};
    $.extend(true,p,prm);
    
    var d = JX._dom(o);
    var r = JX.screen();
    r.x=p.margin.left;
    r.y=p.margin.top;

    r.w-=(p.margin.left+p.margin.right);
    r.h-=(p.margin.top+p.margin.bottom);
    
    JX.abs(d,r);
},

stretch:function(o,a){
    var p = $.extend(true,{by:null},a);
        p.margin = JX.margin(p.margin);
    
    
    var d = JX._dom(o);
    if (p.by==null){
        var r = JX._pos(JX._parent(d));
        r.x=p.margin.left;
        r.y=p.margin.top;
    
        r.w-=(p.margin.left+p.margin.right);
        r.h-=(p.margin.top+p.margin.bottom);
    
        JX._pos(d,r);
    }else{
        var r = JX._abs(p.by);
        r.x=p.margin.left;
        r.y=p.margin.top;
    
        r.w-=(p.margin.left+p.margin.right);
        r.h-=(p.margin.top+p.margin.bottom);
    
        JX.abs(d,r);
    }
},

window:function(){
    
    if (JX._dx_.window===undefined)
        JX._dx_.window = $(window);
    return JX._dx_.window;
},

screen:function(){
    var w = JX.window();
    return {x:0,y:0,w:w.width(),h:w.height()};
},

mouse:function(){
    if (arguments.length>0) 
        return JX._mouse(JX._dom(arguments[0]));
    return JX._mouse();
},

add:function(r,m){
    var out = {};
    if (r.x!==undefined) out.x = r.x+(m.x===undefined?0:m.x);
    if (r.y!==undefined) out.y = r.y+(m.y===undefined?0:m.y);
    if (r.h!==undefined) out.h = r.h+(m.h===undefined?0:m.h);
    if (r.w!==undefined) out.w = r.w+(m.w===undefined?0:m.w);
    
    return out;    
},
/**
 * изменяет значение to = {[x],[y],[h],[w]} на значения из from ={[x],[y],[h],[w],[dx],[dy],[dh],[dw]}
 */ 
set:function(to,from){
    if (from.x!==undefined) to.x = from.x;
    if (from.dx!==undefined) to.x = (to.x!==undefined?to.x:0)+from.dx;
    
    if (from.y!==undefined) to.y = from.y;
    if (from.dy!==undefined) to.y = (to.y!==undefined?to.y:0)+from.dy;
    
    if (from.h!==undefined) to.h = from.h; 
    if (from.dh!==undefined) to.h = (to.h!==undefined?to.h:0)+from.dh;
    
    if (from.w!==undefined) to.w = from.w;
    if (from.dw!==undefined) to.w = (to.w!==undefined?to.w:0)+from.dw;
    return to;
},
/** проверяет, что объект или его родня не привязаны к странице */
inAir:function(o){
    
    while(o[0].tagName!=='BODY'){
        o=o.parent();
        if (o.length===0)
            return true;
    }
    return false;
    
},
workplace:function(wp,page){
/*для JSCENE начальная разметка рабочей области*/    
    var r=JX.screen();
    JX._pos(wp[0],JX.screen());    
    r.x=0;r.y=0;
    JX._pos(page[0],r);
},

sideLenSum:function(o,wrap){
    /* суммма длин по ширине и высоте  */
    var p = {
        inc:wrap?{w:0,h:0,...wrap.inc}:{w:0,h:0},
        ...wrap
    };
    var d   = JX._dom(o),i,
        c   = JX._childs(d),
        a   = {w:0,h:0},b;
    
    for(i=0;i<c.length;i++){
        if (JX._visible(c[i])){
            b = JX._pos(c[i]);
            
            a.w+=b.w+p.inc.w;
            a.h+=b.h+p.inc.h;
            
        }
    }


    if (wrap === undefined){
        return a;
    }else{
        if (wrap.width!==true) delete a.w;
        else if (wrap.dw) a.w+=wrap.dw;
        if (wrap.height!==true) delete a.h;
        else if (wrap.dh) a.h+=wrap.dh;
        
        JX.pos(o,a);
    }    
},

wrap:function(o/*{width:boolean=true,height:true,off}*/){
    /* растягивает объект, чтобы уместилось все что внутри*/
    var w=JX.wrapper(o);
    var s=arguments.length>1?arguments[1]:{width:true,height:true};
    var r = {};
    if ((s.width===undefined)||(s.width===true)) r.w = w.w;
    if ((s.height===undefined)||(s.height===true))r.h = w.h;
    JX.pos(o,r);
},

eq:function(a,b){
    /*стравнение двух rect по существующим полям*/
    
    if ((a.x!==undefined)&&(b.x!==undefined)&&(a.x!=b.x)) return false;
    if ((a.y!==undefined)&&(b.y!==undefined)&&(a.y!=b.y)) return false;
    if ((a.w!==undefined)&&(b.w!==undefined)&&(a.w!=b.w)) return false;
    if ((a.h!==undefined)&&(b.h!==undefined)&&(a.h!=b.h)) return false;
    
    return true;
    
},

margin:function(m){
    if (m===undefined) return {left:0,right:0,top:0,bottom:0};
    if (typeof m ==='number') return {left:m,right:m,top:m,bottom:m};

    return {
        left:(m.left===undefined?0:m.left),
        right:(m.right===undefined?0:m.right),
        top:(m.top===undefined?0:m.top),
        bottom:(m.bottom===undefined?0:m.bottom),
    };
},

fit:function(outer,inner){
    /*возвращает размеры inner, (уменьшенного/увеличенного)  так, чтобы пропорционально вместиться в outer*/
    var o=(JX.is_jquery(outer)?JX.pos(outer):outer);
    var i=(JX.is_jquery(inner)?JX.pos(inner):inner);

    var k = i.w/i.h;
    var h = o.w/k;
    if (h<=o.h)
        return {w:o.w,h:h};
    else
        return {w:o.h*k,h:o.h};
},

isfit:function(outer,inside/*by all||w||h*/){
/*проверка вмещается ли inside внутрь outer */        
    var by=arguments.length>2 ? arguments[2] : 'all';
    var o = JX.pos(outer),i = JX.pos(inside);
    var w = (o.w>=i.w),h=(o.h>=i.h);
    if (by=='w') return w;
    if (by=='h') return h;
    return ((w)&&(h));
},

stick_scr:function(i/*margin*/){
    var m = {left:0,top:0,bottom:0,right:0};
    $.extend(true,m,arguments.length>1 ? arguments[1] : {});
    var a = JX._dom(i);
    var c = JX._abs(a);
    var d = JX.screen();
    
    d.x+=m.left;
    d.y+=m.top;
    d.w-=m.left+m.right;
    d.h-=m.top+m.bottom;
    
    var s = JX.sticker(d,c);
    delete s.w;
    delete s.h;
    JX.abs(a,s);
},

stick:function(o,i/*margin*/){
    var m = {left:0,top:0,bottom:0,right:0};
    $.extend(true,m,arguments.length>1 ? arguments[1] : {});

    var a = JX._dom(o);
    var b = JX._dom(i);
    var c = JX._abs(a);
    
    c.x+=m.left;
    c.y+=m.top;
    c.w-=m.left+m.right;
    c.h-=m.top+m.bottom;

    var d = JX._abs(b);
    var s = JX.sticker(c,d);
    JX.abs(b,s);
},

sticker:function(o,i/*size_modif=false*/){
    /*возвращает координаты i, так чтобы не выходить за пределы o*/
    var r = JX.copy(i);
    var sm=arguments.length>2 ? arguments[2] : false;/*если true то и размеры будут вписываться*/
    if((r.x+r.w)>(o.x+o.w))r.x=o.x+o.w-r.w-1;
    if((r.y+r.h)>(o.y+o.h))r.y=o.y+o.h-r.h-1;
    if(i.x<o.x)r.x=o.x+1;
    if(i.y<o.y)r.y=o.y+1;
    
    if (sm){
        if((r.x+r.w)>(o.x+o.w))r.w=o.w-2;
        if((r.y+r.h)>(o.y+o.h))r.h=o.h-2;
    };
    return r;
},

insider:function(o,i){
    /*проверяет лежит ли i внутри o*/
    if (o.x>i.x) return false;
    if (o.y>i.y) return false;
    if (o.x+o.w<i.x+i.w) return false;
    if (o.y+o.h<i.y+i.h) return false;
    
    return true;
},

inside_screen:function(o){
    if (JX._isrect(o))
        return JX.insider(JX.screen(),o);
    else
        return JX.insider(JX.screen(),JX.abs(o));
},

wrapper:function(o){
    /* квадрат в который можно вписать все дочерние э-ты (по видимой области)*/
    var d   = JX._dom(o),i,
        r   = JX._pos(d),
        c   = JX._childs(d),
        s   = {x:0,y:0,h:0,w:0};
    
    for(i=0;i<c.length;i++){
        if (JX._visible(c[i])){
            var b = JX._pos(c[i]);
            
            s.x=Math.min(s.x,b.x);
            s.y=Math.min(s.y,b.y);
            s.w=Math.max(s.w,b.x+b.w); 
            s.h=Math.max(s.h,b.y+b.h); 
        }
    }
    return {x:s.x,y:s.y,w:s.w-s.x,h:s.h-s.y};
},

copy:function(s){
    return {x:(s.x?s.x:0),y:(s.y?s.y:0),w:(s.w?s.w:0),h:(s.h?s.h:0)};
},

in_view:function(){
    /*провекра что obj находится в области видимости, if cross=true то используется пересечение*/
    var a=$.extend(true,{
        obj:null,
        cross:false,
        by:undefined
    },arguments[0]);
    
    var m=JX.abs(a.obj);
    var s = (a.by?JX.abs(a.by):JX.screen());
    return (a.cross?JX.iscrossr(m,s):((m.x>=s.x)&&((m.x+m.w)<(s.x+s.w))) && ((m.y>=s.y)&&((m.y+m.h)<(s.y+s.h))));
},   

visible:function(o/*set*/){
    var d = JX._dom(o),disp='',css='_jxcssv_',a=arguments,bool;
    
    if (a.length>1){
        
        if (d[css]===undefined){
            disp = $(d).css('display');
            if ((disp==='none')||(disp===''))
                disp=(o.tagName==='TABLE'?'table':'block');
            d[css]=disp;    
        };
        
        disp = d[css];
        if (a[1]==='hover'){
            bool = !((d.style===undefined)||(d.style.display===undefined)||(d.style.display!=="none"));
            d.style.display = (bool?disp:"none");
            return bool;
        }

        d.style.display = (a[1]?disp:"none");

    }else
        return ((d.style===undefined)||(d.style.display===undefined)||(d.style.display!=="none"));
        
},

visiblex:function(jq){
    /*проверка видимоти по родительским элементам*/
    if (!JX.visible(jq)) return false;
    if (jq[0].tagName==='BODY') return true;
    else
        return JX.visiblex(jq.parent());
},

iscross:function(a1,a2,b1,b2,strong){
    /*проверка пересечения двух отрезков*/
    return (JX.inline(b1,a1,a2,strong)||JX.inline(b2,a1,a2,strong)||JX.inline(a1,b1,b2,strong)||JX.inline(a2,b1,b2,strong));
},

crossLine:function(a1,a2,b1,b2){
  /*результат пересечения двух отрезков*/
    
    var r = {r1:0,r2:0};
    if (!JX.iscross(a1,a2,b1,b2) )
        return r;
    
    if (a1===b1)
        r.r1=a1
    else
    if (JX.inline(a1,b1,b2))
        r.r1=a1
    else
    if (JX.inline(b1,a1,a2))
        r.r1=b1;

    if (a2===b2) 
        r.r2=a2
    else
    if (JX.inline(a2,b1,b2))
        r.r2=a2
    else
    if (JX.inline(b2,a1,a2))
        r.r2=b2;
    
    return r;        
  
},
square:function(r1,r2){
  /* площадь пересеяени двух прямоугольнико*/
  if (!JX.iscrossr(r1,r2)) return 0;
  var s = JX.crossRect(r1,r2);
  return s.w*s.h;
},
crossRect:function(r1,r2){
  /*результат пересечения двух прямоугольников*/
  var x = JX.crossLine(r1.x,r1.x+r1.w,r2.x,r2.x+r2.w);
  var y = JX.crossLine(r1.y,r1.y+r1.h,r2.y,r2.y+r2.h);
  return {
        x:x.r1,
        y:y.r1,
        w:x.r2-x.r1,
        h:y.r2-y.r1
  }
},

translate:function(y,y1,y2,x1,x2){
  if ((y2-y1) === 0)
      return 0;
   else
     return (x2*(y-y1)+x1*(y2-y))/(y2-y1);
},

iscrossr:function(a,b,strong){
    /*проверка пересечения двух box*/
    return JX.iscross(a.x,a.x+a.w,b.x,b.x+b.w,strong)&&JX.iscross(a.y,a.y+a.h,b.y,b.y+b.h,strong);
},

inline:function(x,a,b,strong){
    
    /*x inside a b*/
    if ((strong===undefined)||(strong===true)){
        if ((a<=b) && ((x>=a) && (x<=b))) return true;
        if ((a>=b) && ((x>=b) && (x<=a))) return true;
    }else{
        if ((a<b) && ((x>a) && (x<b))) return true;
        if ((a>b) && ((x>b) && (x<a))) return true;
    }    
    return false;
},

inrect:function(x,y,rect,strong){
    return JX.inline(x,rect.x,rect.x+rect.w,strong)&&JX.inline(y,rect.y,rect.y+rect.h,strong);
},

int:function(v){
    v = parseInt(v);
    return (!v?v:0);
},

float:function(v){
    if (typeof v === "string") v = v.replace(new RegExp(",", "g"),".");
    v = parseFloat(v);
    return (!v?0:v);
},

invert : function(childs/*p*/){
    var p=arguments.length>1 ? arguments[1] : {};
    var d = [];
    for(var i=0;i<childs.length;i++) d.push(JX._dom(childs[i]));

    JX._invert(d,p);
},
/** устанавливает свойство line-height*/
lh:function(o){
    var t=JX,i,L,H;
    
    if (o==='update'){
        i = 0;
        while(i<t._lh.length){
            try{
                L = parseInt(t._lh[i].css('line-height'),10);
                H = t.pos(t._lh[i]).h;
                if (L!==H) t._lh[i].css({'line-height': H+'px' });
            }catch(e){
                console.error(e);
                t._lh.splice(i,1);
                i--;
            }
            i++;
        }
    }else if (o==='remove'){
        
    }else{
        if (JX.is_jquery(o)) t._lh.push(o);
        else if (o.length) for(i=0;i<o.length;i++){
            t._lh.push(o[i]);
        }
    }
    
},
/** располагает набор o (массив объектов jquery) по области p.own, 
 * так чтобы они умещались по ширине (если не умещается, то перенос на след строку)
 * p = {
 *     own:undefined|jquery - объект по которому будут размещаться 
 *     gap:{vert:int,horis:int} - отступы между объектами
 *     margin: - отступы внутри own
 *     align: string (left,top,center) - к какой границе own будут липнуть объекты o
 *     inside:string (top,bottom,center ) - как расположены объекты на строке по вертикали
 * }
 * Ex:
 * JX.accom(Qs.frame.children());
 */
accom:function(o,p){
        var 
        x=0,y=0,w=0,h=0,box=[],row=[],i,pos,r=0,
        a = $.extend(true,{
            own:o.parent(),
            gap:{vert:0,horiz:0},
            margin:{left:0,top:0,right:0,bottom:0},
            inside:"top",// top,bottom,center
            align:"left",// left,right,center
        },p),
        area = JX.pos(a.own);
        
        if (typeof(a.margin)==='number')    a.margin={left:a.margin,right:a.margin,top:a.margin,bottom:a.margin};
        if (typeof(a.gap)==='number')       a.gap={vert:a.gap,horiz:a.gap};
        
        area.w-= a.margin.right;
        x = a.margin.left;
        y = a.margin.top;
        h = a.margin.top;
        
        row.push({x:x,y:y,w:area.w,h:0,wmax:0})
        
        for(i=0;i<o.length;i++){
            pos = JX.pos(o[i]);

            pos.x = x;
            pos.y = y;
            pos.row = r;
            
            if ((pos.x+pos.w>area.w)){
                r++;
                y = h;
                x = a.margin.left;
                
                pos.x = x;
                pos.y = h;
                pos.row=r;
                
                h=h+pos.h+a.gap.vert;
                
                row.push({x:x,y:y,h:pos.h,w:area.w,wmax:0});
            
                
                
            }else{
                h = Math.max(h,pos.y+pos.h+a.gap.vert);
                row[row.length-1].h = Math.max(row[row.length-1].h,pos.h);
                
            }    
            row[row.length-1].wmax = pos.x+pos.w+a.gap.horiz;
            
            x+=pos.w+a.gap.horiz;
            
            box.push(pos);
            
        }
        
        
        for(i=0;i<o.length;i++){
            r = row[box[i].row];
            if (a.inside==="bottom")
                box[i].y = r.y+r.h-box[i].h;
            else if (a.inside==="center")                 
                box[i].y = r.y+(r.h-box[i].h)/2;
            
            if (a.align === "center")
                box[i].x+=(r.w-r.wmax)/2;
            else if (a.align === "right")
                box[i].x+=r.w-r.wmax;
            
            if (JX.visible(o[i]))
                JX.pos(o[i],{x:box[i].x,y:box[i].y});
            
        }
        
        
},
    
_mouse : function(d){
    /*var e = e || window.event

	if (e.pageX == null && e.clientX != null ) { 
		var html = document.documentElement
		var body = document.body
	
		e.pageX = e.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0)
		e.pageY = e.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0)
	}
	
	return {x:event.pageX,y:event.pageY};
*/
    
    var dot, eventDoc, doc, body, pageX, pageY,ret={};
    
    if (d==undefined){
        return JX._dx_.mouse;
    }else{
        event = d 
        if (!event){
            var s = JX.screen();
            event = {pageX:s.x+s.w/2,pageY:s.y+s.h/2};
        
        }else
        if ((event.pageX == null) && (event.clientX != null)) {
            eventDoc = (event.target && event.target.ownerDocument) || document;
            doc = eventDoc.documentElement;
            body = eventDoc.body;

            event.pageX = event.clientX +
                (doc && doc.scrollLeft || body && body.scrollLeft || 0) -
                (doc && doc.clientLeft || body && body.clientLeft || 0);
            
            event.pageY = event.clientY +
                (doc && doc.scrollTop  || body && body.scrollTop  || 0) -
                (doc && doc.clientTop  || body && body.clientTop  || 0 );
        };        
        return {x:event.pageX,y:event.pageY};
    }    
    
    
},

toRect : function(o){
    var t = JX.typeof(o),abs=(arguments.length>1?arguments[1]:false);
    if (t=="rect") return o;
    if ((t=="jquery")||(t=="dom")) return (abs?JX.abs(o):JX.pos(o));
    return {x:0,y:0,w:0,h:0};
    
},

typeof : function(o){
    if ((o==undefined)||(o==null)) return "null";
    
    if (typeof o === "object"){
        if (o.jquery) return "jquery";
        if (o.tagName) return "dom";
        if ((o.x)||(o.y)||(o.w)||(o.h)) return "rect";
        return "object";
        
    }else return typeof o;
},

is_jquery:function(o){
    /*return ((o)&&(o.jquery!==undefined));*/
    return o instanceof jQuery;
},

_tile_mx : function(jq,cnt){
    /*создаем матрицу*/
    
    var out = [],c=0,o=[];
    for(var i=0;i<jq.length;i++){
        o.push({jq:jq[i],pos:JX.pos(jq[i])});
        c++;
        if ((c==cnt)||(i==jq.length-1)){
            out.push(o);
            c=0;
            o=[];
        }
    }
    return out;
},    

_tile_param:function(arr,tail){
    /* вычисляем максимальное значение */
    var cols = [],rows = [],r,c;
    
    for(r=0;r<arr.length;r++){
        var hmax = 0;
        for(c=0;c<arr[r].length;c++){
            hmax = Math.max(arr[r][c].pos.h,hmax);
            
            if (c>=cols.length) cols.push(0);
        }
        rows.push(hmax);
    }
    var tl = (tail=='inherit'?0:1);
    
    for(c=0;c<cols.length;c++){
        
        for(r=0;r<arr.length-tl;r++){/*не учитываем ширину по последней строке*/
            if (arr[r][c]!==undefined)
                cols[c]=Math.max(cols[c],arr[r][c].pos.w);
        }
    }
    return {col:cols,row:rows};
},

tile :function(jq,param){
    var t = JX,mx,size,r,c,x,y,m,area,stretch,off={x:0,y:0},rect,tl,top,tailW;
    var a=$.extend(true,{
        count:4,        /*кол-во областей*/
        own:$(jq[0]).parent(),/*родительская область */
        off:{x:0,y:0},/*дополнительное смещение всего после полного расчета*/
        align:{   /*расположение блоков относительно родительской области*/
                vert:"top", /*top,bottom,center,stretch*/
                horiz:"left",/*right,left,center,stretch*/
        },
        inside:{ /*расположение блоков внутри получившейся разбивки*/
            vert:"top",
            horiz:"left"
        },
        tail:"inherit",/*left, center, right  tail - если указать не inherit то нижний ряд блоков будет обрабатываться отдельно, по собственному заданному правилу*/ 
        stretch:{row:0,col:0},/*индекс растягиваемого блока*/
        margin:{left:0,top:0,right:0,bottom:0},    
        gap:{vert:0,horiz:0},    /*отступы между рядами*/
        
    },param);
    
    
    tl = (a.tail=='inherit'?0:1);
    
    if (typeof(a.margin)==='number') a.margin={left:a.margin,right:a.margin,top:a.margin,bottom:a.margin};
    if (typeof(a.gap)==='number') a.gap={vert:a.gap,horiz:a.gap};

    mx  = t._tile_mx(jq,a.count);
    size = t._tile_param(mx,a.tail);
    
    area = JX.pos(a.own);

    for(r=0;r<size.row.length-1;r++)
        size.row[r]+=a.gap.vert;
        
    for(c=0;c<size.col.length-1;c++)
        size.col[c]+=a.gap.horiz;
    

    /*расчет общей суммы длин и растянутого блока*/    
    if (a.align.horiz=="stretch")
        size.col[a.stretch.col]=0;
    size.w = 0;
    for(c=0;c<size.col.length;c++)
        size.w+=size.col[c];
    if (a.align.horiz=="stretch"){    
        area.w-=a.margin.left+a.margin.right;
        size.col[a.stretch.col]=area.w-size.w;
        size.w=area.w;
    }

    if (a.align.vert=="stretch")
        size.row[a.stretch.row]=0;
    size.h = 0;
    for(r=0;r<size.row.length;r++)
        size.h+=size.row[r];
    if (a.align.vert=="stretch"){    
        area.h-=a.margin.top+a.margin.bottom;
        size.row[a.stretch.row]=area.h-size.h;
        size.h=area.h;
    }
    
    if (a.align.horiz=="right")
        off.x=area.w-size.w-a.margin.right;    
    else if (a.align.horiz=="center")
        off.x=(area.w-size.w)/2;    
    else
        off.x=a.margin.left;

    
    if (a.align.vert=="bottom")
        off.y=area.h-size.h-a.margin.bottom;    
    else if (a.align.vert=="center")
        off.y=(area.h-size.h)/2;    
    else
        off.y=a.margin.top;

    y=off.y;
    
    top = false;
    tailW = 0;
    for(r=0;r<size.row.length;r++){
        x=off.x;
        for(c=0;c<size.col.length;c++){
            
            m=mx[r][c];
            if (m!==undefined){
                            
                rect = {x:x,y:y,w:size.col[c],h:size.row[r]};
                if (top===false) top=rect.x;

                m.pos.x=rect.x;
                m.pos.y=rect.y;
                
                if (r==size.row.length-1) tailW+=m.pos.w;
                
                if (a.inside.horiz=='right')
                    m.pos.x=rect.x+rect.w-m.pos.w-(c<size.col.length-1?a.gap.horiz:0);
                else if (a.inside.horiz=='center')
                    m.pos.x=rect.x+(rect.w-m.pos.w)/2;

                if (a.inside.vert=='bottom')
                    m.pos.y=rect.y+rect.h-m.pos.h-(r<size.row.length-1?a.gap.vert:0);
                else if (a.inside.vert=='center')
                    m.pos.y=rect.y+(rect.h-m.pos.h)/2;
                
                m.pos.x+=a.off.x;
                m.pos.y+=a.off.y;
                
                JX.pos(m.jq,{x:m.pos.x,y:m.pos.y});
            }
            x+=size.col[c];    
        }
        y+=size.row[r];
    }
    
    
    
    tailW+=a.gap.horiz*(mx[mx.length-1].length-1);
    
    if ((a.tail!=="inherit")){ /*если нижний ряд формируем по отдельному правилу*/
        
        r=size.row.length-1;
        if (a.tail=="right")
            top=(top+size.w)-tailW;
        else if (a.tail=="center")
            top=(top+(size.w-tailW)/2);    

        for(c=0;c<size.col.length;c++){
            
            m=mx[r][c];

            if (m!==undefined){
                rect = JX.pos(m.jq);
                rect.x=top;
                top+=rect.w+a.gap.horiz;
                
                rect.x+=a.off.x;

                JX.pos(m.jq,{x:rect.x,y:rect.y});
            }      
        }
    }


},

_dom : function(o){
    if (typeof o === "string") 
        return (JCACHE?JCACHE[o][0]:(Qs?Qs[o][0]:undefined));
    else
        return (o.length===undefined?o:(o.length>0?o[0]:undefined));
},

_isrect:function(o){
    return ((typeof o==="object")&&(
        (o.x)||(o.y)||(o.w)||(o.h)
        ));
},

_parent : function(d){
    return (d.parentNode);
}, 

_childs : function(d){
    return d.children;
},

_int:function(f){
    return Math.round(f);
    /*return f;*/
},
/**
 * координаты относитьельно родительского DOM 
 * d - DOM
 * param2 - {x,y,w,h} - установка нового размера
*/
_pos:function(d,c){ 
    if (c!==undefined){
        //-----------------------------------
        var p = JX._parent(d),
            b  =JX._border(p),
            bs =JX._border(d),
            s = d.style,
            off = {x:0,y:0},
            v;
        //-----------------------------------
        if ((p.tagName!=='BODY')&&((window.getComputedStyle(p).position!=='absolute')))
            off = {x:p.offsetLeft+1,y:p.offsetTop+1};
        //-----------------------------------
        v = (c.x-b.l+off.x)+'px';
        if((c.x!==undefined)&&(s.left!==v))
            s.left=v;
        //-----------------------------------
        v = (c.y-b.t+off.y)+'px';
        if((c.y!==undefined)&&(s.top!==v))
            s.top =v;
        //-----------------------------------
        if(c.w!==undefined){
            v=(c.w-bs.l-bs.r);
            v=(v<0?0:v)+'px';
            if (s.width!==v) 
                s.width = v;
        }
        //-----------------------------------
        if(c.h!==undefined){
            v=(c.h-bs.t-bs.b);
            v=(v<0?0:v)+'px';
            if (s.height!==v)   
                s.height = v;
        }
        //-----------------------------------
    }else
        return {x:d.offsetLeft+1,y:d.offsetTop+1,w:d.offsetWidth,h:d.offsetHeight}; 
},
_abs:function(d){
    var w=d.getBoundingClientRect();
    return {x:JX._int(w.left+pageXOffset),
            y:JX._int(w.top+pageYOffset),
            w:JX._int(w.width),
            h:JX._int(w.height)
    };    
},

_border:function(d){
    if (!d._brdrjx_) {
        var b = getComputedStyle(d,null);
        d._brdrjx_={l:parseInt(b.getPropertyValue('border-left-width')),
                    r:parseInt(b.getPropertyValue('border-right-width')),
                    t:parseInt(b.getPropertyValue('border-top-width')),
                    b:parseInt(b.getPropertyValue('border-bottom-width'))};
    }
    return d._brdrjx_;
},

_idx : function(childs,id){
    for(var i=0;i<childs.length;i++)
        if (childs[i].id==id) return i;
    return -1;
},

_invert : function(childs/*p*/){
/*симметрично меняет координаты и размеры массива childs */
    var p = $.extend(true,{
        x:true,
        y:true,
        w:false,
        h:false,
        xoff:0,
        yoff:0
    },arguments[1]);
    
    var count=childs.length;
    if (count<=1) return;
    
    var i=0,j=count-1;
    while(count>0){
        var p1 = JX._pos(childs[i]);
        var p2 = JX._pos(childs[j]);
        
        var ps = {};
        if (p.x) ps.x = p2.x+p.xoff;
        if (p.y) ps.y = p2.y+p.yoff;
        if (p.w) ps.w = p2.w;
        if (p.h) ps.h = p2.h;
        JX._pos(childs[i],ps);
        
        ps = {};
        if (p.x) ps.x = p1.x+p.xoff;
        if (p.y) ps.y = p1.y+p.yoff;
        if (p.w) ps.w = p1.w;
        if (p.h) ps.h = p1.h;
        JX._pos(childs[j],ps);

        i++;if (i==j) break;
        j--;if (i==j) break;
        count--;
    }
},

_arrange : function(childs/*param*/){
    
    if ((!childs) || (childs.length===0)) return;
    var own = childs[0].parentNode,i;
    if ((!own)||(!JX._visible(own))) return;
    
    var prm=arguments.length>1 ? arguments[1] : {};
    var i,out,p = {
        type:"stretch",
        align:"stretch",
        gap:-1,
        stretch:[{idx:0,rate:1,min:10}],
        margin:{left:0,top:0,right:0,bottom:0}
    };
    
    if (prm.direct===undefined)
        p.direct="horiz";

    $.extend(true,p,prm);
    p.visible = JX._visibles(childs);
    if (typeof p.margin !== "object") p.margin = {left:p.margin,top:p.margin,right:p.margin,bottom:p.margin};
    
    
    if (p.stretch ==='all'){
        var stretch = [];
        
        for(i=0;i<childs.length;i++)
            stretch.push({idx:i,rate:1});
        p.stretch = stretch;
        
    }else
    for(i=0;i<p.stretch.length;i++)
    {
        if (typeof p.stretch[i] === "string")
            p.stretch[i] = {idx:JX._idx(childs,p.stretch[i])};
        else if (JX.is_jquery(p.stretch[i])){
            p.stretch[i] = {idx:JX._idx(childs,p.stretch[i][0].id)};
            
        }else if ("id" in p.stretch[i])
            p.stretch[i].idx = JX._idx(childs,p.stretch[i].id);
            
        if (!("rate" in p.stretch[i])) p.stretch[i].rate = 1;
        if (p.stretch[i].idx<0) p.stretch[i].idx = childs.length+p.stretch[i].idx;
    }
    
    
    var rect = JX._pos(own);
    rect.x = p.margin.left; 
    rect.y= p.margin.top;
    rect.w = rect.w-(p.margin.left+p.margin.right);
    rect.h = rect.h-(p.margin.top+p.margin.bottom);
    
    var rects = []; 

    for(i=0;i<childs.length;i++)
        rects.push(JX._pos(childs[i]));
        
    if (p.direct==='horiz')
        out = JX._inscribeH(rect,rects,p.type,p.gap,p.stretch,p.align,p.visible);
    else
        out = JX._inscribeV(rect,rects,p.type,p.gap,p.stretch,p.align,p.visible);
    
        
    for(i=0;i<childs.length;i++)
        JX._pos(childs[i],out[i]);
        
},

_visibles : function(childs){
    var v = [];
    for(var i=0;i<childs.length;i++)
        v.push(JX._visible(childs[i]));    
    return v;
},

_visible : function(d){
    return ((d.style.display !== "none"));
},

_stretch:function(stretch,idx){
    for(var i=0;i<stretch.length;i++)
        if (stretch[i].idx==idx) return i;
    return -1;    
},

_inscribeH:function(rect,rects,type,gap,stretch,align,visible){
    // вписываем набор прямоугольников в прямоугольник rect   
    // type  = left,right,center,stretch
    // align = top,center,bottom
    // gap расстояние между блоками
    // stretch - массив описания растяжек [{idx:num,rate:num,min:num},...]
    //    idx - индекс блока
    //    rate - доля при растягивании 
    //    min - Минимальная высота
    // visible - массив признаков отображения   
    
    var out=[];
    var i,rate;
    var all = 0;
    var start = 0;
    var first = true;
    var idx;
    // расчитываем сумму длин всех блоков (кроме растянутых)
    for(i=0;i<rects.length;i++)
    {
        if (visible[i])
        {
            // добавляем промежутки (без учета последнего)            
            if (!first)
                all+=gap; 
                
            first = false;          
            /*if ((type!=="stretch") || (!(i in stretch)))*/
            if ((type!=="stretch") || (JX._stretch(stretch,i)==-1))
                all+=rects[i].w;
        }
    }

    if (type === "stretch")
    {
        // расчет длины растягиваемых блоков    
        if (stretch.length>0)
        {
            rate = 0;
            for(i=0;i<stretch.length;i++){
                idx = stretch[i].idx;
                if (visible[idx])
                    rate+=stretch[i].rate;
            }
            for(i=0;i<rects.length;i++){
                idx = JX._stretch(stretch,i);
                if ((idx!==-1) && (visible[i]))
                {
                    rects[i].w = (rect.w-all)*(stretch[idx].rate/rate);
                    if(("min" in stretch[idx])&&(rects[i].w<stretch[idx].min))
                        rects[i].w = stretch[idx].min;    
                };
            };    
        };
    };
    
    if (type==="left")
        start = rect.x;
    else
    if (type==="right")
        start = rect.x+rect.w-all;
    else
    if (type==="center")
        start = rect.x+(rect.w-all)/2;
    else
    if (type==="stretch")
        start = rect.x;
    
    for(i = 0;i<rects.length;i++)
    {
        var crect = {};
        crect.x= rects[i].x;
        crect.y= rects[i].y;
        
        if ((type==="stretch") && (JX._stretch(stretch,i)!==-1))
            crect.w= rects[i].w;

        if (visible[i]) 
        {
            if (align === "top")
                crect.y = rect.y;
            else
            if (align === "bottom")
                crect.y = rect.y+rect.h-rects[i].h;
            else
            if (align === "center")
                crect.y = rect.y+(rect.h-rects[i].h)/2;
            else
            if (align === "stretch")
            {
                crect.y = rect.y;
                crect.h = rect.h;
            };
        
            crect.x = start;
            start+=(rects[i].w+gap);
        };
        out.push(crect);
    }
    return out;
},

_inscribeV:function(rect,rects,type,gap,stretch,align,visible){    
    // вписываем набор прямоугольников в прямоугольник rect   
    // type  = top,bottom,center,stretch
    // align = left,center,right

    var out=[];
    var i,rate;
    var all = 0;
    var start = 0;
    var first = true;
    var idx;
    
    // расчитываем сумму длин всех блоков (кроме растянутых)
    for(i=0;i<rects.length;i++)
    {
        if (visible[i])
        {
            if (!first)
                all+=gap;
            first=false;
            if ((type!=="stretch") || (JX._stretch(stretch,i)==-1))
                all+=rects[i].h;
        };
    };
            
    // добавляем промежутки (без учета последнего)
    
    //all+=(rects.length-1)*gap;
    
    if (type === "stretch")
    {
        // расчет длины растягиваемых блоков    
        if (stretch.length>0)
        {
            rate = 0;
            for(i=0;i<stretch.length;i++){
                idx = stretch[i].idx;
                if (visible[idx])
                    rate+=stretch[i].rate;
            }

            for(i = 0;i<rects.length;i++){
                idx = JX._stretch(stretch,i);
                if ((idx!==-1) && (visible[i]))
                {
                    rects[i].h = (rect.h-all)*(stretch[idx].rate/rate);
                    if(("min" in stretch[idx])&&(rects[i].h<stretch[idx].min))
                        rects[i].h = stretch[idx].min;    
                };
            }    
        };
    };
    
    if (type==="top")
        start = rect.y;
    else
    if (type==="bottom")
        start = rect.y+rect.h-all;
    else
    if (type==="center")
        start = rect.y+(rect.h-all)/2;
    else
    if (type==="stretch")
        start = rect.y;
    
    for(i = 0;i<rects.length;i++)
    {
        var crect = Array();
        crect.x= rects[i].x;
        crect.y= rects[i].y;
        if ((type==="stretch") && (JX._stretch(stretch,i)!==-1))
            crect.h= rects[i].h;
            
        if (visible[i]) 
        {
            if (align === "left")
                crect.x = rect.x;
            else
            if (align === "right")
                crect.x = rect.x+rect.w-rects[i].w;
            else
            if (align === "center")
                crect.x = rect.x+(rect.w-rects[i].w)/2;
            else
            if (align === "stretch")
            {
                crect.x = rect.x;
                crect.w = rect.w;
            };
            crect.y = start;
            start+=(rects[i].h+gap);
        };        
        
        out.push(crect);
    }
    return out;

}
};

$(function(){ JX._init(); });   

/** 
 * Базовая структура в библиотеке JX
 * @namespace ws/plugins/jx~JX:rect
 * @example 
 * var r = {x:100,h:200};
*/ 
/** @member {float} ws/plugins/jx~JX:rect#.x */
/** @member {float} ws/plugins/jx~JX:rect#.y */ 
/** @member {float} ws/plugins/jx~JX:rect#.h */
/** @member {float} ws/plugins/jx~JX:rect#.w */