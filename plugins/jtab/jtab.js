/*global $,ut,JX,nil*/
function jtab(o){
    var t=this;
    t.param = $.extend({
        own:$('body'),
        ul:undefined,
        _id:-1,
        onActivate:undefined,
        onClickDel:undefined,
        onAfterDel:undefined,
        onBeforeDel:undefined, /*if def need return true!*/
        onAdd:undefined,
        onSortable:undefined,
        onChanged:undefined,
        panel_height:32,
        css:{
            panel:"jt_panel"
        }
    },o);

}

jtab.prototype._init=function(){
    var t=this,p=t.param;
    if (p._id==-1){
        p.own.html('<ul></ul>');
        p.own.tabs({
           heightStyle:"fill" 
        });
        
        p.ul = p.own.find('ul');
        
        p.own.find( ".ui-tabs-nav" ).sortable({
            axis: "x",
            stop: function() {
                t.refresh();
                if (p.onSortable) p.onSortable({sender:t});
            }
        });                        
        
        p.own.on( "click", "span.ui-icon-close", 
        function() {
            var panelId = $( this ).closest( "li" ).attr( "aria-controls" );
            var item = t.itemById(panelId);

            if (p.onClickDel==undefined)
                t.del(item);
            else
                p.onClickDel({sender:t,item:item});
        });        
        
        p.own.tabs({
            activate: function( event, ui ) {
                if (p.onActivate)
                    p.onActivate({event:"onActivate",item:t.current(),sender:t});
            }
        });        


    }
    
};

jtab.prototype.add=function(o){
    var t=this,p=t.param;
    var a=$.extend({
        name:"",
        panel_height:p.panel_height,
        css:p.css
    },o);
    
    
    
    t._init();
    
    p._id++;
    
    var tmp = "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close' role='presentation'>Remove Tab</span></li>";
    
    var label = a.name,
        id = "tabs-" + p._id,
        li = $( tmp.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ) ),
        tabContentHtml = '<div style="height:100%;width:100%;overflow:hidden"></div>';
 
    p.own.find( ".ui-tabs-nav" ).append( li );
    p.own.append( "<div  id='" + id + "'>" + tabContentHtml + "</div>" );
    p.own.tabs( "refresh" );
    
    
    var frame = p.own.find('#'+id);
    
    var panel = frame.append(ut.tag({css:a.css.panel})).find("."+a.css.panel).css(
        {'position':'absolute','height':(a.panel_height-1)+'px'});
    
    var item = {id:id,content:$(frame.children().eq(0)),panel:panel,frame:frame,li:p.ul.children().last(),changed:false,md5:"-1"};
    
    frame.css({'padding-left':'0px','padding-right':'0px','padding-top':+(a.panel_height)+'px','padding-bottom':'0px'});
    //frame.css({'padding-left':'0px','padding-right':'0px','padding-top':'0px','padding-bottom':'0px'});
    
    p.own.css("padding","0px");

    t.refresh();
    
    $.data(item.li[0],'item',item);
    
    if (p.onAdd) p.onAdd({event:"onAdd",item:item,sender:t});
    
    t.current(item);
    
    return item;
};

jtab.prototype.clear=function(){
    var t=this,item;
    
    while(t.count()>0){
        item=t.item(0);    
        t.del(item);
    }
    
};

jtab.prototype.count=function(){
    var t=this,p=t.param;
    if (t.assigned())
        return p.ul.children().length;
    return 0;
};

jtab.prototype.item=function(i){
    var t=this,p=t.param;
    var li = p.ul.children().eq(i)[0];
    return $.data(li,'item');
};

jtab.prototype.tab_text=function(o){
    var t=this,item=null,href=null,a=$.extend(true,{
        i:-1,
        id:false,
        item:null,
        caption:undefined
    },o);
    
    if (a.i!==-1){
        item=t.item(a.i);
        href=t._a(item.id);
    }else if (a.id!==false){
        href=t._a(a.id);
    }else if (a.item!==null){
        href=t._a(a.item.id);
    }
    
    if (href!==null){
        if (a.caption!==undefined)
            href.html(a.caption);
        else
            return href.text();    
    }
};

jtab.prototype.itemById=function(id){
    var t=this,c=t.count();
    for(var i=0;i<c;i++){
        var o=t.item(i);
        if (o.id==id) return o;
    }
    return null;
};

jtab.prototype.del=function(item){
    var t=this,p=t.param;
    
    
    
    if((p.onBeforeDel===undefined)||(p.onBeforeDel({event:"onBeforeDel",item:item,sender:t})===true)){
        
            
        item.li.remove();    
        item.frame.remove();
        t.refresh({forced:true});

        if(p.onAfterDel)
            p.onAfterDel({event:"onAfterDel",item:item,sender:t});
        
    }
    
};

jtab.prototype.refresh = function(o){
    var t=this,p=t.param;
    var a = $.extend(true,{
        forced:false
    },o);
    
    if (t.assigned()){
        t.tabs_refresh(a);
        t._refresh(true);
    }
};
jtab.prototype._refresh = function(){
    var t=this,p=t.param,
    light=(arguments.length>0?arguments[0]:true);
    
    if (t.assigned()){
        if (!light)p.own.tabs("refresh");
        var item = t.current();
        if (!nil(item)){
            var pos= JX.pos(item.frame);
              
            JX.pos(item.panel,{x:0,y:-1,w:pos.w-1});    
        }
    }
};

jtab.prototype.tabs_refresh=function(o){
    var t=this,p=t.param;    
    
    var a = $.extend(true,{
        forced:false
    },o);
    
    
    if (p._tabs_refresh!==undefined)
        clearTimeout(p._tabs_refresh);
    
    if (!a.forced)
    p._tabs_refresh=setTimeout(function(){
        t._refresh(false);
        p._tabs_refresh=undefined;
    },20);    
    else{
        t._refresh(false);
        p._tabs_refresh=undefined;
    }
    //p.own.tabs("refresh");
    
};

jtab.prototype._li=function(id){
    var t=this;
    return t._a(id).parent();
};

jtab.prototype._a=function(id){
    var t=this,p=t.param;
    return p.ul.find("[href='#"+id+"']");
};

jtab.prototype.idx=function(id){
    var t=this;
    if (typeof id === 'object')
        return t.idx(id.id);
    else{
        var li = t._li(id);
        return li.index();
    }
};

jtab.prototype.current=function(item){
    var t=this,p=t.param;
    if (!t.assigned()) return null;
    if (item==undefined){
        var idx = p.own.tabs("option","active");
        return (idx!==false?t.item(idx):null);
    }else{
        p.own.tabs("option","active",t.idx(item));
        t.refresh();
    } 

};

jtab.prototype.assigned=function(){
    return (this.param._id>-1);
};

jtab.prototype.changed=function(item,bool){
    var t=this,p=t.param,needDo=false;
    if (bool===undefined) return item.changed?true:false;

    bool    = bool?true:false;
    needDo  = (item.changed!==bool);
    
    // откулючаем готовность снятия признака */
    
    t.readyUnChange(item,!bool);
    item.changed = bool;
    
    if (bool) item.md5 = "-1";
        
    if ((needDo)&&(p.onChanged))
        p.onChanged({sender:t,item:item,changed:bool});
};
/** признак готовности файла к снятию признака Changed */
jtab.prototype.readyUnChange=function(item,bool){
    var t=this,p=t.param;
    if (bool===undefined) return item.readyUnChange?true:false;

    item.readyUnChange = bool; 
};