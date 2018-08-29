/*global $,ut,JX*/
function jmenu(o){
    var t=this;
    var p=$.extend(true,{
        own:$('body'),
        stick:null,
        
        cont:null,
        pos:undefined,
        id:ut.id('jmc'),
        width:100,
        off:{x:4,y:0,sx:-9,sy:-5},
        use_shadow:true,
        onClick:undefined,/*to close need return [true]!!!*/
        show:false,
        css:{
            frame:"jm_frame",
            item:"jm_item",
            hover:"jm_item_hover",
            select:"jm_item_select",
            sub:"jm_item_sub",
            disable:"jm_item_disable",
            separator:"jm_item_separator"
        }
    },o);
    
    
    t.param=p;
    if (p.show) t.show();
    
}


jmenu.prototype.show=function(){
    var t=this,p=t.param,d=p.data,o;
    
    if (p.cont===null){

        //--------------------------------------------------------        
        
        p.own.append(ut.tag({id:p.id,style:"position:absolute"}));
        p.cont = p.own.find('#'+p.id);
        var frame = t._frame(d,ut.id('jmf'));

        //--------------------------------------------------------
        
        o = JX.mouse(); 
            o.w=5;o.h=5;
        if (p.stick)
            o = JX.abs(p.stick);
        if (p.pos){
            o = p.pos;
            o.w=5;o.h=5;
        }
            
        JX.pos(frame,{x:o.x+p.off.sx,y:o.y+o.h+p.off.sy});
        var scr=JX.screen();
        scr.w=scr.w-5;
        scr.h=scr.h-5;
        var pos =JX.pos(frame);
        if (pos.x+pos.w>scr.w)
            pos.x = o.x-pos.w-p.off.sx;    
        if (pos.y+pos.h>scr.h)
            pos.y = o.y-pos.h-p.off.sy;    
        delete pos.w;
        delete pos.h;
        JX.pos(frame,pos);
        //--------------------------------------------------------
        
        
        if (p.use_shadow){
            p.cont.jshadow({
                show:true,
                opacity:0,
                click:function(e){
                    e.jshadow("destroy");
                    t.hide();    
                }}
            );
        }
        
    }

};



jmenu.prototype._free=function(data){
    var t=this,p=t.param;

    for(var i=0;i<data.length;i++){
        if (data[i].item_children) delete data[i].item_children;
        if (data[i].children) t._free(data[i].children); 
    }
};

jmenu.prototype.hide=function(){
    var t=this,p=t.param;
    if (p.cont!==null){
        if (p.use_shadow) p.cont.jshadow("destroy");
        t._free(p.data);
        p.cont.remove();
        p.cont = null;
    }
};

jmenu.prototype._close_over=function(list){
    var t=this;
    $.each(list,function(i,o){
        var a=$(o);
        var d = $.data(a[0],'data');
        if (d.item_children) {
            d.item_children.remove();
            delete d.item_children;
            if (d.children)
            for(var j=0;j<d.children.length;j++){
                var c=d.children[j].item_children;
                if(c){
                    t._close_over(c.children());
                    c.remove();
                    delete d.children[j].item_children;
                }
            }
        }
    });
};

jmenu.prototype._event_hover=function(item){
    
    var t=this,p=t.param,css=p.css;
    var data = $.data(item[0],'data');
    
    if(((!data.children)||(data.children.length==0))&&((data.enable==undefined)||(data.enable===true)))
    item.on("click",function(){
        if (p.onClick){
            if (p.onClick({sender:item,data:data,id:item[0].id}))
                t.hide();
        }else
            t.hide();
    });
    
    item.hover(function(){
        var o=$(this);
        
        var d = $.data(o[0],'data');
        o.siblings().removeClass(css.select);
        
        if ((d.enable==undefined)||(d.enable==true)){
            o.addClass(css.hover);
        
            if ((d.children)&&(!d.item_children)){
                o.addClass(css.select);
                d.item_children = t._frame(d.children,ut.id('jmf'));
                t._align_item(o,d.item_children);
            }
        }
        var pr=o.parent().children().not(o);
        t._close_over(pr);

    },function(){
        var o=$(this);
        o.removeClass(css.hover);
        
    });
    
    
};
jmenu.prototype._align_item=function(own,item){
    var t=this,p=t.param;
    var o = JX.abs(own);
    
    JX.abs(item,{x:o.x+p.width+p.off.x,y:o.y+p.off.y});
    var pos = JX.abs(item);
    var scr=JX.screen();
    scr.w=scr.w-5;
    scr.h=scr.h-5;
    
    if (pos.x+pos.w>scr.w)
        pos.x = o.x-pos.w-p.off.x;    

    if (pos.y+pos.h>scr.h)
        pos.y = scr.h-pos.h;    
    
    delete pos.h;
    delete pos.w;
    JX.abs(item,pos);
    
    
    
};

jmenu.prototype._frame=function(data,id){
    var t=this,p=t.param,css=p.css,code='',i,item,codes=[];
    
    
    codes = t._codes(data);
    code=ut.tag('<',{id:id,css:css.frame,style:"position:absolute",pos:{w:p.width}});
    for(i=0;i<codes.length;i++)
        code+=codes[i].code;
    code+=ut.tag('>');
    
    p.cont.append(code);
    
    for(i=0;i<codes.length;i++){
        
        item=p.cont.find("#"+codes[i].id);
        $.data(item[0],'data',codes[i].data);
        
        t._event_hover(item);
    }
    
    var out = p.cont.find('#'+id);
    $.data(out[0],"data",data);
    
    return out;
    

};

jmenu.prototype._codes = function (data) {
    var t=this,codes=[];
    
    for(var i=0;i<data.length;i++){
        if ((data[i]=='-')||(data[i].caption=='-')){
            data[i]={caption:'-',enable:false};
        }
        
        var code = t._code_item(data[i]);
        
        /*
        if ((data[i].children)&&(data[i].children.length>0))
            code.children=t._code(data[i]);
        */
        
        code.data=data[i];
        code.idx = i;
        
        codes.push(code);    
    }
    return codes;
};

jmenu.prototype._code_item=function (item) {
    var t=this,p=t.param,css=p.css,o=item,a;
    if (typeof item=="string") o={caption:item};
    a=$.extend(true,{
        caption:"item",
        id:ut.id("jmi"),
        css:css.item
    },o);
    

    var csss = a.css;
    if (a.children) csss+=' '+css.sub;
    if (a.caption=='-') csss+=' '+css.separator;
    else
        if ((a.enable!=undefined)&&(a.enable==false)) csss+=' '+css.disable;
    
    var code=ut.tag({id:a.id,css:csss,value:(a.caption!=='-'?a.caption:'')});
    
    return {code:code,id:a.id};
    
};
jmenu.prototype.align = function (argument) {
    
};