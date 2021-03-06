/*global ut,$,jQuery,JX,Qs,Ws,jlock*/
(function( $ ){

var m={
name:"mform",    
init:function(param){
    var o = m.obj(this);
    if (o===undefined){ 

        if (param===undefined) param = {};
        param.plugin = this;
        m.obj(this,new mform(param));
        
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
    if (arguments.length>1) 
        $.data(t[0],m.name,arguments[1]);
    return $.data(t[0],m.name);
},
put:function(param){
    m.obj(this).put(param);
    return this;
},
get:function(name){
    return m.obj(this).attr(name);
},
open:function(/*param*/){
    var o = m.obj(this);
    if (arguments.length>0)
        o.put(arguments[0]);
    o.open();    
    return this;
},
align:function(){
    var o = m.obj(this);
    o.align();    
    return this;
},

close:function(/*param*/){
    var o = m.obj(this);
    if (arguments.length>0)
        o.put(arguments[0]);
    o.close();    
    return this;
}

};


$.fn.mform = function(n){
    if(m[n])
        return m[n].apply(this,Array.prototype.slice.call( arguments, 1 ));
    else 
    if ((typeof n==='object')||(!n)){
        return m.init.apply(this,arguments);
    }else{
        if (typeof n==='string')
            return m.get.apply(this,[n]);
        else
            $.error( 'Not exists method ' +  n + ' for jQuery.mform');
    }    
};
})(jQuery);

function mform(o){
    var t = this;
    t.init(o);
}

mform.prototype.init = function(o){
    var t=this,p;
    t.param = $.extend(true,{
        jq:{
            own:o.plugin,/*plugin object*/
            parent:((Qs&&Qs.modal)?Qs.modal:undefined),
            workplace:((Qs&&Qs.workplace)?Qs.workplace:undefined),
            frame:undefined,
            ref:ut.id('mform'),
        },    
        align_id:ut.id('alignfrm'),
        
        onOpen:     undefined,/*событие при открытии формы*/
        onClose:    undefined,/*событие при закрытии формы*/
        onAfterClose: undefined,/*событие после полного закрытия формы (можно снова открывать)*/
        canClose:   undefined,/*если определено, то должен венрнуть true чтобы форма закрылась*/
        onAlign:    undefined,/* после перерисовки */
        onClickResize:    undefined,/* клик на кнопке изменения размера */
        onClickPin:    undefined,/* клик на кнопке прикрепления */
        
        visible:false,/*признак видимости (НЕ вызывает onOpen)*/
        animate:0,
        animateType:'slide',/** slide | fade */
        caption:'',/*заголовок*/
        
        needCloseBtn:true, /*отображаете кнопку закрытия*/
        shadowAsClose:true,/*нажатие вне формы аналогичное вызову close*/
        shadowOpacity:0.5,/** прозрачность подложки */
        shadowSpeed:0, /* скорость анимации тени */
        placeCloseOnTopRight:false, /*отображает кнопку закрытия вынесенной на угол*/
        bumpClose:3,/*max or [2..8]  коэф - на сколько вынесена кнопка закрытия */ 
        showHeader:false,/*отображать или нет заголовок*/
        showFooter:false,/*отображать или нет подвал с кнопками*/
        padding:10,/*внутренние отсутпы */
        modal:true,/*форма будет модальной */
        draggable:true,/*форму можно перемещать*/
        dragOnAllForm:false,/* если true - то для перемещения формы используется вся форма, false- только заголовок */
        resizable:true,/*можно изменять размеры формы */
        showPin:false,/** показывать или нет кноку фиксации формы */
        autoPin:true, /** если showPin, и пользователь сместил форму, то pin включается */
        headerButtons:{ /** дополнительные кнопки в заголовке */
            /*Ex:
            id:{
                visible:true, 
                disabled:false,
                caption:"",
                css:"",
                title:"",
                click(){
                    
                }
            }
            */ 
        },
        stretch:"custom",/* указывает как будет отрисовываться окно "custom" "horiz"  "fullscreen"  */
        fullscreen:false, /* deprecated, use  stretch=="fullscreen" */
        margin:0,
        buttonPlace:"right",
        position:{
            type:'align',/*custom,cling*/
            align:{vert:"center",horiz:"center"},/*for align*/
            cling:{side:{a:"bottom",b:"top"},pivot:{a:"center",b:"center"},abs:true,area:"screen"},
            сlinger:undefined,
            /*x,y  for custom*/
            keep:false, /*стремится сохранить позицию после каждой перерисовки*/
        },
        /*управление кнопками через plugin
            $("my").mform({button:{id:"ok",disable:true}});
            $("my").mform({button:{id:"ok",caption:"n`ok"}});
        */
        buttons:{ 
        /*кнопки в подвале 
        ok:{caption:"ok",
            click:function(){
                ...
            },
            css:{
                btn:"",
                btn_disable:""
            }
        }
        */
        },
        
        
        /*lock:{align:0},*/
        lock:new jlock(), 
        cssPref:'',
        css:{
            frame:'mfr_frame',
            header:'mfr_header',
            footer:'mfr_footer',
            content:'mfr_content',
            caption:'mfr_caption',
            headerButtons:'mfr_header_buttons',
            
            close_frame:'mfr_close_frame',
            close:'mfr_close',
            pinUp:'mfr_pin_up',
            pinDown:'mfr_pin_down',
            
            btn_common:'mfr_btn_common',
            btn:'mfr_btn',
            btn_disable:'mfr_btn_disable',
            resize:'mfr_resize'
        }
        
    },o);
    
    
    if (t.param.cssPref!=='')
        ut.addPref(t.param.css,t.param.cssPref);    
    
    t._create();
    
    p=t.param;
    
    p.origin = JX.size(p.jq.own);
    
    p.padding = JX.margin(p.padding);
    var area = t._area_culc();
    if (p.width===undefined) p.width=area.w;
    if (p.height===undefined) p.height=area.h;
    
    p.margin = JX.margin(p.margin);
    
    t._event();
    t.put(p);
    

};

mform.prototype.done=function(){
    
};

mform.prototype.open=function(){
    var t=this,p=t.param;
    if (!t.attr('visible')){
        t.attr('visible',true);
        if (p.onOpen) p.onOpen({sender:t});
    }
};

mform.prototype.close=function(){
    var t=this,p=t.param;
    if (t.attr('visible')){
        if ((p.canClose===undefined)||(p.canClose()===true)){
            if ((!p.onClose)||(p.onClose({sender:t})!==false))
                t.attr('visible',false);
                if (p.onAfterClose) p.onAfterClose();
        }    
    }
};

mform.prototype._create=function(){
    var t=this,p=t.param,css=p.css,c='';
    
    p.id={
        frame:ut.id('frame'),
        header:ut.id('hdr'),
        footer:ut.id('ftr'),
        content:ut.id('cnt'),
        caption:ut.id('cpt'),
        close:ut.id('cls'),
        pin:ut.id('pin'),
        close_frame:ut.id('clf'),
        resize:ut.id('rs'),
    };
    
    c+=ut.tag('<',{id:p.id.frame,css:css.frame,style:'position:absolute'});

    c+=ut.tag({id:p.id.close_frame,   css:css.close_frame,      style:'position:absolute'});

    c+=ut.tag({id:p.id.content,css:css.content,style:'position:absolute'});

    c+=ut.tag('<',{id:p.id.header,css:css.header,style:'position:absolute'});
        c+=ut.tag({id:p.id.caption, css:css.caption,    style:'position:absolute'});
        c+=ut.tag({id:p.id.pin,   css:css.pinUp,        style:'position:absolute'});
        c+=ut.tag({id:p.id.close,   css:css.close,      style:'position:absolute'});
    c+=ut.tag('>');

    c+=ut.tag({id:p.id.footer,css:css.footer,style:'position:absolute'});


    c+=ut.tag({id:p.id.resize,css:css.resize,style:'position:absolute'});
    c+=ut.tag('>');
    
    p.jq.parent.append(c);
    
    p.jq.frame     = p.jq.parent.find('#'+p.id.frame);
    p.jq.header    = p.jq.parent.find('#'+p.id.header);
    p.jq.content   = p.jq.parent.find('#'+p.id.content);
    p.jq.footer    = p.jq.parent.find('#'+p.id.footer);

    p.jq.caption        = p.jq.parent.find('#'+p.id.caption);
    p.jq.headerButtons = [];
    p.jq.close          = p.jq.parent.find('#'+p.id.close);
    p.jq.pin            = p.jq.parent.find('#'+p.id.pin);
    p.jq.close_frame    = p.jq.parent.find('#'+p.id.close_frame);

    p.jq.resize    = p.jq.parent.find('#'+p.id.resize);
    
    p.jq.own.appendTo(p.jq.content);
    p.jq.own.css('position','absolute');
    
    $.data(p.jq.own[0],p.jq.ref,this);

};

mform.prototype._event=function(){
    var t=this,p=t.param,css=p.css;
    
    p.jq.footer.on('click',function(e){
        var tar=$(e.target);
        if (tar.hasClass(css.btn_common)&&(!tar.hasClass(css.btn_disable))){
            var data = $.data(tar[0],'data');
            if (data.click) data.click({sender:t});
        }
            
    });
    
    p.jq.close.on('click',function(){
        t.close();
    });

    p.jq.pin.on('click',function(){
        t.pinDown('toggle');
        if (p.onClickPin) p.onClickPin({sender:t});    
    });
 
    p.jq.header.on('mousedown',function(){
        if ((p.draggable)&&(p.stretch==='custom'))        
            p._draggable = {
                mouse:JX.mouse()
            };
    });
    
    p.jq.content.on('mousedown',function(){
        if ((p.draggable)&&(p.stretch==='custom')&&(p.dragOnAllForm))        
            p._draggable = {
                mouse:JX.mouse()
            };
    });

    p.jq.resize.on('mousedown',function(){
        if ((p.resizable)&&(p.stretch==='custom'))        
            p._resizable = {
                mouse:JX.mouse()
            };
        if (p.onClickResize) p.onClickResize({sender:t});    
    });
    
    JX.window().on('mousemove',function(){
        if ((p.draggable)&&(p._draggable)&&(p.stretch==='custom')){
            var m = JX.mouse();
            var d = {x:m.x-p._draggable.mouse.x,y:m.y-p._draggable.mouse.y};
            var f=JX.abs(p.jq.frame);
            
            JX.abs(p.jq.frame,{x:f.x+d.x,y:f.y+d.y});
            p._draggable.mouse = m;

            if ((p.showPin)&&(p.autoPin))
                t.pinDown(true);
            if (t.pinDown())
                t.pinDown('reculc');
            
        }
        if ((p.resizable)&&(p._resizable)&&(p.stretch==='custom')){
            var m = JX.mouse();
            var d = {x:m.x-p._resizable.mouse.x,y:m.y-p._resizable.mouse.y};
            var f=JX.pos(p.jq.frame);
            
            JX.pos(p.jq.frame,{w:f.w+d.x,h:f.h+d.y});
            p._resizable.mouse = m;

            if ((p.showPin)&&(p.autoPin))
                t.pinDown(true);
            if (t.pinDown())
                t.pinDown('reculc');

            t.align();
        }
        

    });
    
    JX.window().on('mouseup',function(){
            p._draggable = undefined;
            p._resizable = undefined;
    });
    
    if (Ws) 
        Ws.align({
            id:p.align_id,
            func(){ 
                t.align();
            
            }}
        );

};

mform.prototype.pinDown=function(bool){
    var t=this,p=t.param,css=p.css;
    
    if (bool === undefined){
        
        return (p._pinFixed !== undefined);

    }else{
        if (bool==='toggle'){
            
            t.pinDown( !t.pinDown());
            
        }else if (bool ==='reculc'){
            let s = JX.screen();
            let pos = JX.pos(p.jq.frame);
            let left = pos.x-s.x;
            let right = s.x+s.w-pos.x;
            
            p._pinFixed = {
                pos,
                cling:Math.abs(left)<Math.abs(right)?'left':'right',
                left,
                right
            }
        }else if (bool){
            p.jq.pin.removeClass(css.pinUp).addClass(css.pinDown);
            t.pinDown('reculc');
        }else{
            p.jq.pin.removeClass(css.pinDown).addClass(css.pinUp);
            p._pinFixed = undefined;
        }
    }
    
};

mform.prototype._headerButtonUpdate=function(){
    var t=this,p=t.param;
    
    p.jq.headerButtons.map($item=>$item.remove());
    p.jq.headerButtons = [];
    $.each(p.headerButtons,((id,item)=>{
        
        const code = ut.tag({
            id:id, 
            css:item.css?item.css:'',
            value:item.caption?item.caption:'',  
            style:'position:absolute'+( item.visible===false ? ';display:none' : '' ),
            attr:{title:item.title?item.title:''},
        });
        
        p.jq.header.append(code);
        const $q = p.jq.header.find('#'+id);
        p.jq.headerButtons.push($q);
        $q.on('click',()=>{
            if ((item.click)&&(item.disabled!==true))
                item.click({id:id});
        });
    }));
    
    
}


mform.prototype._css = function(css){
    var t = this,p=t.param;
    if (css===undefined)
        return p.css;
        
    for (var key in css){    
        if (key in p.css)
            p.jq.frame.find('.'+p.css[key]).removeClass(p.css[key]).addClass(css[key]);
    }
    
    p.css=$.extend(true,p.css,css);
};

mform.prototype.put = function(o){
    let t=this,p=t.param,l=p.lock,visible;
    /*t.lock('align','begin');*/
    l.lock('align');
    if ('visible' in o){
        visible = o.visible;
        delete o.visible;
    }
    
    $.each(o,function(n,v){
        t.attr(n,v);
    });
    l.unlock('align');
    
    if (visible!==undefined)
        t.attr('visible',visible);
    else
        t.align();
};    

mform.prototype.attr = function(n/*v*/){
    var t=this,p=t.param,v,r=(arguments.length===1),css=p.css;
    
    if (arguments.length===0) return;
    if (!r) v=arguments[1];
    /*-----------------------------------*/
    if (n==='onClose'){
        if (r) 
            return p.onClose;
        else    
            p.onClose = v;
    }
    /*-----------------------------------*/
    if (n==='onAfterClose'){
        if (r) 
            return p.onAfterClose;
        else    
            p.onAfterClose = v;
    }
    /*-----------------------------------*/
    if (n==='onOpen'){
        if (r) 
            return p.onOpen;
        else    
            p.onOpen = v;
    }
    /*-----------------------------------*/
    if (n==='animate'){
        if (r) 
            return p.animate;
        else    
            p.animate = v;
    }
    /*-----------------------------------*/
    if (n==='animateType'){
        if (r) 
            return p.animateType;
        else    
            p.animateType = v;
    }
    /*-----------------------------------*/     
    if (n==='onAlign'){
        if (r) 
            return p.onAlign;
        else    
            p.onAlign = v;
    }
    /*-----------------------------------*/
    
    if (n==='shadowOpacity'){
        if (r) 
            return p.shadowOpacity;
        else    
            p.shadowOpacity = v;
    }
    /*-----------------------------------*/
    if (n==='shadowSpeed'){
        if (r) 
            return p.shadowSpeed;
        else    
            p.shadowSpeed = v;
    }
    /*-----------------------------------*/
    if (n==='canClose'){
        if (r) 
            return p.canClose;
        else    
            p.canClose = v;
    }
    /*-----------------------------------*/

    if (n==='visible'){
        if (r){ 
            if (p._visible===undefined)
                p._visible = JX.visible(p.jq.frame);
            return p._visible;
        }else    
            t._visible(v);
    }
    /*-----------------------------------*/
    if (n==='width'){
        if (r) 
            return JX.pos(p.jq.frame).w;
        else    
            JX.pos(p.jq.frame,{w:v});
    }
    /*-----------------------------------*/
    if (n==='height'){
        if (r) 
            return JX.pos(p.jq.frame).h;
        else    
            JX.pos(p.jq.frame,{h:v});
    }
    /*-----------------------------------*/
    if (n==='caption'){
        if (r) 
            return p.jq.caption.text();
        else    
            p.jq.caption.html(v);
    }
    /*-----------------------------------*/
    if (n==='modal'){
        if (r) 
            return p.modal;
        else    
            p.modal=v;
    }
    /*-----------------------------------*/
    /*
    if (n==='fullscreen'){
        if (r) 
            return (p.stretch==='fullscreen');
        else   
            p.stretch=(v?"fullscreen":"custom");
    }
    */
    /*-----------------------------------*/
    if (n==='stretch'){
        if (r) 
            return p.stretch;
        else    
            p.stretch=v;
    }
    if (n==='margin'){
        if (r) 
            return p.margin;
        else    
            p.margin = JX.margin(v);
    }
    
    /*-----------------------------------*/
    
    if (n==='dragOnAllForm'){
        if (r) 
            return p.dragOnAllForm;
        else    
            p.dragOnAllForm=v;
    }
    /*-----------------------------------*/
    if (n==='showHeader'){
        if (r) 
            return p.showHeader;
        else    
            p.showHeader=v;
    }
    /*-----------------------------------*/
    if (n==='showFooter'){
        if (r) 
            return p.showFooter;
        else    
            p.showFooter=v;
    }
    /*-----------------------------------*/
    if (n==='showPin'){
        if (r) 
            return p.showPin;
        else    
            p.showPin=v?true:false;
    }
    if (n==='autoPin'){
        if (r) 
            return p.autoPin;
        else    
            p.autoPin=v?true:false;
    }
    if (n==='pinDown'){
        if (r) 
            return t.pinDown();
        else    
            t.pinDown(v?true:false);
    }
    
    /*-----------------------------------*/
    if (n==='buttons'){
        if (r) 
            return p.buttons;
        else{
            var btns = $.extend(true,{},v);
            p.buttons = {};
            
            $.each(btns,function(id,o){
                if (typeof o === 'function')
                    p.buttons[id] = {click:o};
                else
                    p.buttons[id] = o;
                if (!('caption' in p.buttons[id]))
                    p.buttons[id].caption = id;
            });
            
            
            
            t._recreate_buttons();
        }    
    }
    /*-----------------------------------*/
    if (n==='buttonPlace'){
        if (r) 
            return p.buttonPlace;
        else{
            p.buttonPlace = v;
        }    
    }
    /*-----------------------------------*/
    if (n==='button'){
        var b=p.buttons[v.id];
        if (b){
            
            if (v.caption!==undefined)
                b.jq.text(v.caption);  
            
            if (v.disable!==undefined){
                var bcss = '';
                if ((b.css)&&(b.css.btn_disable)) bcss+=' '+b.css.btn_disable; else bcss+=' '+css.btn_disable;
                if (v.disable)
                    b.jq.addClass(bcss);
                else    
                    b.jq.removeClass(bcss);
                    
                
            }    
            p.buttons[v.id]=$.extend(true,p.buttons[v.id],v);
            
        }    
    }
    /*-----------------------------------*/
    if (n==='needCloseBtn'){
        if (r) 
            return p.needCloseBtn
        else    
            p.needCloseBtn=v;
    }
    /*-----------------------------------*/
    if (n==='placeCloseOnTopRight'){
        if (r) 
            return p.placeCloseOnTopRight
        else    
            p.placeCloseOnTopRight=v;
    }
    /*-----------------------------------*/
    if (n==='shadowAsClose'){
        if (r) 
            return p.shadowAsClose;
        else    
            p.shadowAsClose=v;
    }
    /*-----------------------------------*/
    if (n==='draggable'){
        
        if (r) 
            return p.draggable;
        else{    
             p.draggable=(v?true:false);
        }    
            
    }
    /*-----------------------------------*/
    if (n==='resizable'){
        
        if (r) 
            return p.resizable;
        else{
             p.resizable=(v?true:false);
        }
        
    }
    /*-----------------------------------*/
    if (n==='position'){
        
        if (r) 
            return p.position;
        else{
             p.position._keeping = undefined;
             p.position = $.extend(true,p.position,v);
        }
        
    }
    /*-----------------------------------*/
    if (n==='bumpClose'){
        
        if (r) 
            return p.bumpClose;
        else{
                        
            p.bumpClose = (typeof v ==='number'?(v<2?2:(v>8?8:v)):'max');
        }
        
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
            return p.css
        else{    
           t._css(v);
        }    
    }
    /*-----------------------------------*/    
    if (n==='headerButtons'){
        if (r) 
            return p.headerButtons;
        else {
            p.headerButtons = $.extend(true,p.headerButtons,v);
            t._headerButtonUpdate();
        }    
    }
    /*-----------------------------------*/
    
    
    t.align();
};

mform.prototype._visible=function(bool){
    
    var t=this,p=t.param,f=p.jq.frame,own = p.jq.own;
    if (bool===p._visible) return;
    p._visible = bool;
    
    if (bool){

        f.detach().appendTo(p.jq.parent);
        
        if (p.modal) f.jshadow({
            show:true,
            opacity:p.shadowOpacity,
            speed:p.shadowSpeed,
            click(e){
                if (p.shadowAsClose)
                    t.close();
            },
            onhide(){
                f.jshadow('destroy');
            },
            onshow(){
                
            }
        });
            
    }else{
        if (p._shadow)
            f.jshadow("hide");
    }
    
    p._shadow = (bool&&p.modal);
    
    if (bool)
        JX.visible(own,true);
    if (p.animate>0)
        t._fade(!bool);
    else
        JX.visible(f,bool);
};

mform.prototype._fade=function(bool){
    var t=this,p=t.param,f=p.jq.frame,l=p.lock,pos,opacity;
    
    if (bool){
       JX.visible(f,false);
    }else{
        
        l.lock('align');
        
        opacity = f.css('opacity');
        f.css('opacity',0.01);
        
        JX.visible(f,true);
        t._align();
        
        if (p.animateType==='slide'){
            pos = JX.abs(f);
            JX.abs(f,{y:pos.y+pos.h/2,h:0});
        }else if (p.animateType==='slideDown'){
            pos = JX.abs(f);
            JX.abs(f,{y:pos.y,h:0});
        }else    
            JX.visible(f,false);
        
        f.css('opacity',opacity);
        
        if (l.can('fade')){
            l.lock('fade');
        
        Ws.align('begin');    
            
            if (p.animateType==='fade')
            f.fadeIn({
                duration:p.animate,
                complete(){
                    l.unlock('fade');
                    l.unlock('align');
                    Ws.align('end');
                }
            });
            if ((p.animateType==='slide')||(p.animateType==='slideDown'))
                f.animate({
                    height:pos.h,
                    top   :pos.y
                },
                p.animate,
                ()=>{
                    JX.abs(f,pos);
                    l.unlock('fade');
                    l.unlock('align');                    
                    Ws.align('end');
                });

        }
    }    
};

mform.prototype.lock=function(func,event){
    var t=this,p=t.param,l=p.lock;
    
    if (func===undefined) return false;
    if (event===undefined) event = 'is';

    if (event=='begin')
        l[func]+=1;
        
    if (event=='end')
        l[func]-=1;
    
    return (l[func]>0);    
};

mform.prototype._area_culc=function(){
    /*предварительный размеров основного расчет фрейма*/
    var t=this,p=t.param,h=JX.pos(p.jq.header),c=JX.pos(p.jq.own),f=JX.pos(p.jq.footer);
    return {
            w   :   Math.max(h.w,c.w,f.w)+(p.padding.left+p.padding.right),
            h   :   h.h+c.h+f.h
    };
};

mform.prototype._recreate_buttons = function(){
    var t=this,p=t.param,css=p.css,bcss,c='';
    
    
    $.each(p.buttons,function(id,o){
        bcss =css.btn_common+' ';
        if ((o.css)&&(o.css.btn)) bcss+=o.css.btn; else bcss+=css.btn;
        if (o.disable)
            if ((o.css)&&(o.css.btn_disable)) bcss+=' '+o.css.btn_disable; else bcss+=' '+css.btn_disable;
        
        c+=ut.tag({id:id,css:bcss,style:"position:absolute",value:(o.caption?o.caption:'')});
    });
    
    p.jq.footer.html(c);
    $.each(p.buttons,function(id,o){
        o.jq = p.jq.footer.find("#"+id);
        $.data(o.jq[0],'data',o);
    });
    
};

mform.prototype.align=function(){
    var t=this,p=t.param,l=p.lock;
    if (!l.can('align')) return;
    
    l.lock('align'); 
    t._align();
    l.unlock('align');
    
    
};

mform.prototype._align=function(){
    
    var t=this,p=t.param,pos=p.position,screen = JX.screen(),r,hPadding={},enablePin = false;
    if (!t.attr('visible')) return;

    var brdr = JX._border(p.jq.frame[0]).t;
    
    JX.visible(p.jq.header,p.showHeader);
    JX.visible(p.jq.footer,p.showFooter);
    JX.visible(p.jq.close,p.needCloseBtn&&p.showHeader);
    enablePin = p.showPin&&p.showHeader&&(['fullscreen','horiz'].indexOf(p.stretch)==-1);
    JX.visible(p.jq.pin,enablePin);
    
    enablePin = enablePin&&t.pinDown();
    
    if (p.stretch === 'horiz'){
        
        JX.abs(p.jq.frame,{x:0,y:(screen.h-p.height)/2,w:screen.w,h:p.height});
        JX.stretch(p.jq.own,{margin:p.padding});

    }else{
        if (p.stretch === 'fullscreen') {
            
            JX.stretch_scr(p.jq.frame,{margin:p.margin});
            
        }else{
            
            if (p._resizable===undefined){
                if (enablePin){
                    
                    if (p._pinFixed.cling === 'left'){
                        JX.abs(p.jq.frame,{x:p._pinFixed.left});
                    }else{
                        JX.abs(p.jq.frame,{x:screen.x+screen.w-p._pinFixed.right});
                    }
                    
                }else if ((pos.keep)||(pos._keeping==undefined)){
                    pos._keeping=true;
                    if (pos.type=='custom')
                        JX.abs(p.jq.frame,pos);
                    else if ((pos.type=="cling")&&(JX.is_jquery(pos.clinger))){
                        JX.cling(pos.clinger,p.jq.frame,pos.cling);
                    }else{ 
                        pos.align.by = JX.abs(p.jq.workplace);
                        var mmm = $.extend(true,{},pos.align);
                        mmm.margin = p.margin;
                        JX.place(p.jq.frame,mmm);
                    }
                }    
            }
        }
    }
    
    JX.arrange([p.jq.header,p.jq.content,p.jq.footer],{direct:"vert",type:"stretch",align:"stretch",margin:brdr,stretch:p.jq.content});
    
    
    var f = JX.abs(p.jq.frame);
    
    if (p.stretch === 'horiz'){
        
        hPadding = {left:(screen.w-p.width)/2,right:(screen.w-p.width)/2};
        JX.stretch(p.jq.own,{margin:hPadding});
        
    }else
        JX.stretch(p.jq.own,{margin:p.padding});

    if (p.showHeader&&p.placeCloseOnTopRight&&p.needCloseBtn&&(p.stretch=='custom')){
        
        JX.visible(p.jq.close_frame,true);

        r = JX.pos(p.jq.close);
        var d = r.w/(p.bumpClose=='max'?r.w:p.bumpClose);
        
        r.x = f.x+f.w-r.w/2-d;
        r.y = f.y-r.h/2+d;

        JX.abs(p.jq.close,{x:r.x,y:r.y});
        JX.abs(p.jq.close_frame,r);

        JX.arrange([p.jq.caption],{direct:"horiz",type:"stretch",align:"center",stretch:[{idx:0}],margin:{left:p.padding.left,right:p.padding.right}});

    }else{
        
        JX.visible(p.jq.close_frame,false);
        if (p.stretch === 'horiz')
            JX.arrange([p.jq.caption,...p.jq.headerButtons,p.jq.close],{direct:"horiz",type:"stretch",align:"center",stretch:[{idx:0}],margin:hPadding});
        else
            JX.arrange([p.jq.caption,...p.jq.headerButtons,p.jq.pin,p.jq.close],{direct:"horiz",type:"stretch",align:"center",stretch:[{idx:0}],margin:{left:p.padding.left,right:p.padding.right}});
        
    }    
    
    if (p.showFooter){
        if (p.stretch === 'horiz')
            JX.arrange(p.jq.footer.children(),{direct:"horiz",type:p.buttonPlace,align:"center",gap:5,margin:hPadding});
        else
            JX.arrange(p.jq.footer.children(),{direct:"horiz",type:p.buttonPlace,align:"center",gap:5,margin:{left:p.padding.left,right:p.padding.right}});
    }    
    
    JX.visible(p.jq.resize,((p.resizable)&&(p.stretch=='custom')));
    
    if ((p.resizable)&&(p.stretch=='custom')){
        r = JX.pos(p.jq.resize);
        JX.pos(p.jq.resize,{x:f.w-r.w/2,y:f.h-r.h/2});
    }
    
    
    if (p.onAlign) p.onAlign({sender:t});
};






