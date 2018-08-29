/*global $,Qs,ut,JX*/
function JMENU(o){
    var t=this;
    
    /*-------------------------------*/
    var own  = (o.own?o.own:$("body"));
    var d = $.data(own[0],'jmenu');
    if (d) d._destroy();
    /*-------------------------------*/
    
    t.param = $.extend(true,{
        own:(Qs?Qs.body:$("body")),
        id:ut.id("menu"),
        items:{},
        shadow:true,
        show:false,
        click:undefined,
        width:150,
        opacity:0,
        css:{
            item:"",
            mline:"ui-widget-mline"
        }
    },o);
    var p=t.param;

    t._create();
    t.show(p.show);
    
    /*-------------------------------*/
    $.data(own[0],'jmenu',this);
    /*-------------------------------*/
}

JMENU.prototype._create=function(){
    var t=this,p=t.param,
    c=t._code(p.items,p.id);

    p.own.width((p.width+2)+'px');
    p.own.append(c);
    p.frame = p.own.find('#'+p.id);
    p.frame.menu({
        items: "> :not(."+p.css.mline+")",
        select:function(event,ui){ 
        if(p.click){
            var item=$.data(ui.item[0],'dat');
            if ((!item.child)||(item.child.length==0))
            p.click({sender:t,event:event,ui:ui,id:ui.item[0].id,item:item});
        }
    }
    });
    if (p.shadow)
        p.own.jshadow({opacity:p.opacity,onhide:function(){t.show(false);}});
    t._setdata(p.items);
};

JMENU.prototype._setdata=function(items){
    var t=this,p=t.param;
    for(var i=0;i<items.length;i++){
        var li = p.own.find('#'+items[i].id);
        if(li.length>0)
            $.data(li[0],'dat',items[i]);
        if (items[i].child) t._setdata(items[i].child);
    }    
};

JMENU.prototype._destroy=function(){
    if (this.param.shadow)
        this.own.jshadow('destroy');

    this.param.frame.menu('destroy');
};

JMENU.prototype.show=function(){
    var t=this,p=t.param,arg=arguments;
    if (arg.length==0)
        return JX.visible(p.own);
    else{
        if (JX.visible(p.own)!==arg[0]){
            p.frame.menu( "collapseAll", null, true );
            //p.frame.menu( "collapse");
            //--------------------
            if (arg[0]){
                JX.visible(p.own,true);
                if (p.shadow)p.own.jshadow("show");
            }else{
                JX.visible(p.own,false);
                if (p.shadow)p.own.jshadow("hide");
            }
            //--------------------
            
        }
    }
};

JMENU.prototype.enable=function(id){
    var t=this,p=t.param,arg=arguments,css="ui-state-disabled";
    
    var li = p.own.find("#"+id);
    if (arg.length==0)
        return (!li.hasClass(css));
    else{
        if (arg[1])
            li.removeClass(css);
        else
            li.addClass(css);
    }
};

JMENU.prototype._code=function(items){
    var t=this,p=t.param,c='',d,disable="ui-state-disabled";
    var id=(arguments.length>1?arguments[1]:ut.id('item'));
    c+=ut.tag('<',{tag:'ul',id:id});
    for(var i=0;i<items.length;i++){
        d=items[i];
        (d.id==undefined?d.id=ut.id('item'):0);
        var css = '';
        
        if(d.caption=='-'){
            css=p.css.mline;
        }else 
            css=((d.enable!==undefined)&&(!d.enable)?disable:'');
        
        c+=ut.tag('<',{tag:'li',id:d.id,css:css});
        
        c+=ut.tag({value:(d.caption=='-'?'':d.caption),css:(css==p.css.mline?'':p.css.item),style:(p.css.item==''?'width:'+(p.width-20)+'px':'')});
        if (d.child)c+=t._code(d.child);
        
        c+=ut.tag('>',{tag:'li'});
    }
    c+=ut.tag('>',{tag:'ul'});
    return c;
};
