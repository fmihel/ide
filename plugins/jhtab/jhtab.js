/*global $,Qs,ut,Ws,JX,Dcss*/

function jhtab(o){
    var t=this;
    
    t.param = $.extend(true,{
        own:(Qs?Qs.body:$("body")),
        _id:ut.id('jhtab'),
        
        onclose:undefined,
        onselect:undefined,
        onresize:undefined,
        btn:{w:70,h:32},
        collapsed_height:32,
        animate:500,
        story:{
            own_height:0
        },
        css:{
            top_frame:      'jht_top',
            bottom_frame:   'jht_bottom',
            btn:            'jht_btn',
            btn_text:       'jht_btn_text',
            btn_select:     'jht_btn_select',
            btn_panel:      'jht_btn_panel',
            panel:          'jht_panel',
            btn_close:      'jht_btn_close'
        },
        sp:{}
    },o);
    
    var p=t.param;
    p.story.own_height=JX.pos(p.own).h;
    
    t._create();
    t._event();
    t.align();
}
jhtab.prototype.close=function(close){
    var t=this,p=t.param;
    if (close===true){
        if (!t.close())
            t._mark(t.current());    
    }else
        return (!JX.visible(p.bottom));    
};
jhtab.prototype.add=function(o){
    var t=this,p=t.param,css=p.css,c,item={};
    var a = $.extend({
        caption:'text',
        btn_panel_width:10,
        id:ut.id("jhtb")
    },o);
    
    c= ut.tag({id:a.id,style:'position:absolute;',pos:p.btn,css:css.btn,value:a.caption});
    c+=ut.tag({style:'position:absolute',pos:{w:a.btn_panel_width,h:p.collapsed_height},css:css.btn_panel});
    p.top.append(c);

    item.btn = p.top.find('#'+a.id);
    item.btn_panel = item.btn.next();
    
    var id = a.id+'_panel';
    c=ut.tag({id:id,style:'height:100%',css:css.panel});
    p.bottom.append(c);
    item.panel = p.bottom.find('#'+id);
    
    
    $.data(item.btn[0],'item',item);
    t.align();
    
    t.current({item:item,forced:true});
    t.align();
    return item;
};

jhtab.prototype._create=function(o){
    var t=this,p=t.param,css=p.css,c;
    
    p.Tid=ut.id('jhtt');
    p.Bid=ut.id('jhtb');
    p.CBid=ut.id('jhtcb');
    
    c =ut.tag({id:p.Tid,style:'position:absolute',css:css.top_frame,pos:{h:p.collapsed_height}});
    c+=ut.tag({id:p.Bid,style:'position:absolute',css:css.bottom_frame});
    c+=ut.tag({id:p.CBid,style:'position:absolute',css:css.btn_close});
    
    p.own.append(c);
    
    p.top   = p.own.find('#'+p.Tid);
    p.bottom = p.own.find('#'+p.Bid);
    p.btn_close = p.own.find('#'+p.CBid);
    
    
};

jhtab.prototype._event=function(){
    var t=this,p=t.param,css=p.css;
    
    p.top.on("click",function(e){
        var d = $(e.target);
        if (!d.hasClass(css.btn))
            d = d.parent();

        if (d.hasClass(css.btn)){
            t._mark($.data(d[0],'item'));
            t.align();
        }
    });
    
    p.top.on("mousedown",function(){
        if (!t.close()){
            p.sp.press = true;
            p.sp.fix={x:p.sp.current.x,y:p.sp.current.y};
            p.top.css('cursor','ns-resize');
        }
        
    });
    p.top.on("mouseup",function(){
        p.sp.press = false;
        p.top.css('cursor','default');
    });
    
    (Qs?Qs.body:$("body")).on("mousemove",function(e){
        p.sp.current={x:e.pageX,y:e.pageY};
        if ((p.sp.press)&&(!t.close())){
            var o=JX.pos(p.own);
            o.h = o.h+(p.sp.fix.y-p.sp.current.y);
            if (o.h>32)
                JX.pos(p.own,{h:o.h});
            else{
                p.sp.press=false;
                p.top.css('cursor','default');
            }
            p.sp.fix={x:p.sp.current.x,y:p.sp.current.y};
            Ws.align();
        }
        
    });
    
    p.btn_close.on("click",function(){
        t.close(true);    
    });
};

jhtab.prototype._mark=function(item){

    var t=this,p=t.param,css=p.css;
    
    if (item.btn.hasClass(css.btn_select)){
        p.bottom.hide();
        p.top.children().removeClass(css.btn_select);
        p.top.children().filter('.'+css.btn_panel).hide();
        
        /*change parent width*/
        p.story.own_height = JX.pos(p.own).h;
        JX.pos(p.own,{h:JX.pos(p.top).h});
        /*-------------------*/
        if (p.onclose) p.onclose({sender:t,event:"onclose"});        
    }else{
        
        /*change parent width*/
        if (!JX.visible(p.bottom))
            JX.pos(p.own,{h:p.story.own_height});
        /*-------------------*/
        
        p.bottom.show();
        t.current({item:item});
        
    }
    
    Ws.align();
    
};    

jhtab.prototype.align=function(){
    
    var t=this,p=t.param,close = t.close(),
    h=(Dcss?Dcss.get('bp_height'):p.btn.h);

    JX.pos(p.top,{h:h});
    JX.arrange([p.top,p.bottom],{direct:"vert",stretch:p.bottom});
    
    JX.arrange(p.top.children(),{direct:"horiz",type:"left",align:"stretch",margin:{left:20}});
    
    var item=t.current();
    if (item!==null){
    
    //    var o = JX.pos(p.own);
    //    var r = JX.pos(p.right);
    //    JX.pos(item.panel,{w:o.w-r.w});
    }
    
    JX.visible(p.btn_close,!close);
    if (!close){
        var top = JX.abs(p.top);
        var b = JX.abs(p.btn_close);
        JX.abs(p.btn_close,{x:top.x+top.w-b.w-1,y:top.y+1});
    }    
    
    if (p.onresize) p.onresize({sender:t});
    
};

jhtab.prototype.current=function(){
    var t=this,p=t.param,css=p.css;
    if (arguments.length>0){
        var a=$.extend({
            item:false,
            forced:false,
        },arguments[0]);
        
        if (a.item){

            p.bottom.children().not(a.item.panel).hide();
            a.item.panel.show();
        
            var top = p.top.children();
            top.not(a.item.btn).removeClass(css.btn_select);
            top.filter('.'+css.btn_panel).not(a.item.btn_panel).hide();
            a.item.btn_panel.show();
        
            if (!a.item.btn.hasClass(css.btn_select)){
                a.item.btn.addClass(css.btn_select);
                    if (p.onselect) p.onselect({sender:this,event:"onselect",item:a.item});        
            }       
            return a.item;        
        }else{
            
        }
        
    }else{
        var btn=p.top.children("."+css.btn_select);
        return (btn.length>0?$.data(btn[0],'item'):null);
    }
};

jhtab.prototype._destroy=function(o){
    var t=this,p=t.param,css=p.css;
};

