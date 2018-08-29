function jvtab(o){
    /*global $,Qs,ut*/
    var t=this;
    
    t.param = $.extend({
        own:(Qs?Qs.body:$("body")),
        _id:ut.id('jvtab'),
        
        onclose:undefined,
        onselect:undefined,
        onresize:undefined,
        btn:{w:24,h:100},
        animate:500,
        story:{
            own_width:0
        },
        css:{
            left_frame:'jvt_left',
            right_frame:'jvt_right',
            btn:'jvt_btn',
            btn_text:'jvt_btn_text',
            btn_select:'jvt_btn_select',
            panel:'jvt_panel'
        }
    },o);
    
    var p=t.param;
    p.story.own_width=JX.pos(p.own).w;
    
    t._create();
    t._event();
    t.align();
}

jvtab.prototype.add=function(o){
    var t=this,p=t.param,css=p.css,c,item={};
    var a = $.extend({
        caption:'text',
        top:20,
        height:p.btn.h,
        id:ut.id('jvtb')
    },o);
    
    c=ut.tag('<',{id:a.id,style:'overflow-x:hidden;overflow-y:hidden',pos:{w:p.btn.w,h:a.height},css:css.btn});
    c+=ut.tag({style:'display:inline-block;position:relative;width:'+p.btn.w+'px;top:'+a.top+'px;white-space: nowrap;',
                css:css.btn_text,
                value:a.caption});
    c+=ut.tag('>');
    
    p.right.append(c);
    item.btn = p.right.find('#'+a.id);
    
    c=ut.tag({id:a.id+'_panel',style:'height:100%',css:css.panel});
    p.left.append(c);
    item.panel = p.left.find('#'+a.id+'_panel');
    item.idx = (p.left.children().length-1);
    
    
    $.data(item.btn[0],'item',item);
    t.align();
    t.current({item:item,forced:true});
    t.align();
    return item;
};

jvtab.prototype._create=function(o){
    /*global ut*/
    var t=this,p=t.param,css=p.css,c;
    
    p.Lid=ut.id('jvtl');
    p.Rid=ut.id('jvtr');
    
    c=ut.tag({id:p.Lid,style:'position:absolute',css:css.left_frame});
    c+=ut.tag({id:p.Rid,style:'position:absolute',pos:{w:p.btn.w},css:css.right_frame});
    
    p.own.append(c);
    
    p.left = p.own.find('#'+p.Lid);
    p.right = p.own.find('#'+p.Rid);
    
};

jvtab.prototype._event=function(){
    var t=this,p=t.param,css=p.css;
    
    p.right.on("click",function(e){
        var d = $(e.target);
        if (!d.hasClass(css.btn))
            d = d.parent();
        if (d.hasClass(css.btn))
            t._mark($.data(d[0],'item'));
        t.align();
    });
};

jvtab.prototype.close=function(){
    var t=this,
    item = t.current();
    if (item) t._mark(item);
};

jvtab.prototype._mark=function(item){
    /*global Ws*/
    var t=this,p=t.param,css=p.css;
    if (item.btn.hasClass(css.btn_select)){
        p.left.hide();
        p.right.children().removeClass(css.btn_select);
        
        /*change parent width*/
        p.story.own_width = JX.pos(p.own).w;
        JX.pos(p.own,{w:JX.pos(p.right).w});
        /*-------------------*/
        if (p.onclose) p.onclose({sender:this,event:"onclose"});        
    }else{
        
        /*change parent width*/
        if (!JX.visible(p.left))
        JX.pos(p.own,{w:p.story.own_width});
        /*-------------------*/
        
        p.left.show();
        t.current({item:item});
        
    }
    
    Ws.align();
};    

jvtab.prototype.align=function(){
    /*global JX*/
    var t=this,p=t.param;

    JX.arrange([p.left,p.right],{direct:"horiz",stretch:p.left});

    var item=t.current();
    if (item!==null){
        var o = JX.pos(p.own);
        var r = JX.pos(p.right);
        JX.pos(item.panel,{w:o.w-r.w});
    }
    
    if (p.onresize) p.onresize({sender:t});
};

jvtab.prototype.current=function(o){
    var t=this,p=t.param,css=p.css;
    if (o!==undefined){
        if (typeof(o)==='number'){
            o={
                item:$.data(p.right.children().eq(o)[0],'item')
            };    
        }
        
        var a=$.extend({
            item:false,
            forced:false,
        },o);
        /*
        var speed = (a.forced?0:p.animate);
        p.left.children().not(a.item.panel).animate({left:p.own.width()+"px"}, speed,'swing',function(){
            $(this).hide();
            t.align();
        });
        */
        p.left.children().not(a.item.panel).hide();
        a.item.panel.show();
        /*a.item.panel.animate({left:"0px"}, speed);*/
            
        p.right.children().not(a.item.btn).removeClass(css.btn_select);
        if (!a.item.btn.hasClass(css.btn_select)){
            a.item.btn.addClass(css.btn_select);
            if (p.onselect) p.onselect({sender:this,event:"onselect",item:a.item});        
        }    
        return a.item;        
        
    }else{
        var btn=p.right.children("."+css.btn_select);
        return (btn.length>0?$.data(btn[0],'item'):null);
    }
};

jvtab.prototype._destroy=function(o){
    var t=this,p=t.param,css=p.css;
};

