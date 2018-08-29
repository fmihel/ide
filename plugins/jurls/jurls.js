/*global $,JX,Qs,Ws,ut,nil*/
var jurls={

param:{
    
},
create:function(o){
    var t=jurls,c='';
    t.param=$.extend(true,{
        own:(Qs?Qs.body:$('body')),

        id:ut.id('jurl'),
        item_height:32,
        max_height:33*10,
        click:undefined,
        change:undefined,    
        css:{
            frame:'ju_frame',
            list:'ju_list',
            item:'ju_item',
            item_move:'ju_item_move',
            item_num:'ju_num',
            item_url:'ju_url',
            item_clear:'ju_clear'
        }    
    },o);
    var p=t.param,css=p.css;
    
    c+=ut.tag('<',{id:p.id,css:css.frame,style:"position:absolute"});
    
    var id_list=ut.id('jutl_list');
    c+=ut.tag({id:id_list,css:css.list+' ws_scrollbar'});
    c+=ut.tag('>');
    
    p.own.append(c);
    
    p.frame=p.own.find('#'+p.id);
    p.list=p.own.find('#'+id_list);
    
    p.frame.jshadow({
        opacity:0,
        click:function(){
            t.show(false);
    }});
    t._event();
    t.put(p);
    t.align();
    return t;
},
_item_code:function(d,id){
    var t=jurls,p=t.param,css=p.css,c='';

    c+=ut.tag('<',{id:'item_'+id,css:css.item});
    c+=ut.tag('<',{style:'position:absolute'});
    
    c+=ut.tag({style:'position:absolute',pos:{w:p.item_height*2+7},css:css.item_num});
    c+=ut.tag({style:'position:absolute',css:css.item_url,value:d.url});
    c+=ut.tag('<',{style:'position:absolute',pos:{w:p.item_height},css:css.item_clear});
        c+=ut.tag({style:'width:16px;height:16px',css:'ui-icon ui-icon-close'});
    c+=ut.tag('>');
    
    c+=ut.tag('>');
    c+=ut.tag('>');
    return c;
},
_event:function(){
    var t=jurls,p=t.param,css=p.css,d;
    p.list.sortable({
            revert: true,
            start:function(e,ui){
                ui.item.addClass(css.item_move);
                p._lock = true;
            },
            stop:function(e,ui){
                ui.item.removeClass(css.item_move);
                t._update_num();t.align();t.change();
                p._lock = undefined;
            }});
    p.list.disableSelection();
    
    
    p.list.on('click',function(e){
        var tar = $(e.target);
        if (tar.hasClass(css.item_url)||tar.hasClass(css.item_num)){
            d=$.data(tar.parent().parent()[0],'dat');
            if (p.click){
                p.click({sender:t,dat:d,url:d.url});
            }
            t.show(false);
            t.change();
        }else
        if ((tar.hasClass(css.item_clear))||(tar.hasClass('ui-icon'))){
            var par = (tar.hasClass(css.item_clear)?tar.parent().parent():tar.parent().parent().parent());
            d=$.data(par[0],'dat');
            d.url = '';
            par.find('.'+css.item_url).urlLight({url:''});
            t.change();
        }
    });
},
change:function(){
    var t=jurls,p=t.param;
    if (p.change) p.change({sender:t});
},
add:function(url) {
    var t=jurls,p=t.param,css=p.css,o;
    var find=t.search(url);
    if (!nil(find))
        return find;
    find=t.search('');/*search empty place*/
    if (!nil(find)){
        /*find.item.find('.'+css.item_url).text(url);*/
        find.item.find('.'+css.item_url).urlLight({url:url});
        find.dat.url = url;
        t.change();
        return find;
    }
    var id=p.list.children().length+1;
    var d={url:url};

    p.list.append(t._item_code(d,id));

    o=p.list.find('#item_'+id);
    $.data(o[0],'dat',d);
    
    t._urlLight(o.find('.'+css.item_url));
    
    
    t._update_num();
    t.align();
    t.align({delay:10});
    t.change();
    
},
search:function(url){
    /*ret: null or {item:jquery,dat:{}}*/
    var t=jurls,p=t.param,ch=p.list.children(),res=null;
    
    $.each(ch,function(i,e){
        var d=$.data(e,'dat');
        if (d.url==url){res={item:$(e),dat:d};return false;}
    });
    
    return res;
},
_urlLight:function(to){
    var t=jurls,p=t.param;
    to.urlLight({
        readOnly:true,
        placeholder:'',
        align:"stretch",
        click:function(){
            if (p._lock===undefined)
                to.trigger('click');    
        },
        css:{
            frame:"uli3_frame",
            edit:"uli3_edit",
            
            protocol:"uli3_protocol",
            domen:"uli3_domen",
            path:"uli3_path",
            file:"uli3_file",
            params:"uli3_params",
            placeholder:"uli3_placeholder"
        }                    
    });
},
set_data:function(data){
    var t=jurls,p=t.param,list=p.list,c='',id,i,o,css=p.css;
    list.html('');
    id=0;
    for(i=0;i<data.length;i++){
        id++;
        c+=t._item_code(data[i],id);
    }
    
    list.append(c);
    id=0;
    for(i=0;i<data.length;i++){
        id++;
        o = p.list.find('#item_'+id);
        $.data(o[0],'dat',data[i]);
        
        t._urlLight(o.find('.'+css.item_url));
        
    }
    
    t._update_num();
    t.align();
    return t;
},
get_data:function(){
    var t=jurls,p=t.param,res=[],c=p.list.children(),ch=[],n=true;
    ch=ut.reverse(c.get());
    
    $.each(ch,function(i,o){
        var d = $.data(o,'dat');
        
        if((d)&&((d.url!=='')||(!n))){
            res.push(d);            
            n=false;
        }
    });
    
    return ut.reverse(res);
},
put:function(o){
    var t=jurls,p=t.param;
    
    $.each(o,function(k,v){
    switch (k){
        case 'pos':t._pos(v);break;
        case 'data':t.set_data(v);break;
        
            
        }/*switch*/
    });

    return t;
},
show:function(show){
    var t=jurls,p=t.param;
    
    if (show!==undefined){
        JX.visible(p.frame,show);
        if (show){
            p.frame.jshadow('show');
            t.align();
            t.align({delay:10});
        }else
            p.frame.jshadow('hide');
    }else
        return JX.visible(p.frame);
    
},
align:function(){
    var t=jurls,p=t.param,ch=p.list.children(),ich,css=p.css;
    
    var par=$.extend(true,{
        delay:0,
    },arguments);
    
    if (par.delay===0){
        
        var h=Math.max((p.item_height+1)*2,Math.min(ch.length*(p.item_height+1),p.max_height));

        JX.pos(p.frame,{h:h});
        
        var a = JX.pos(p.frame);
        JX.pos(p.list,{w:a.w,h:a.h});
    
        $.each(ch,function(i,o){
            var item=$(o);
            var a = JX.pos(item);
            ich = item.children();
            
            JX.pos(ich,{w:a.w,h:a.h});
            JX.arrange(ich.children(),{direct:'horiz',type:'stretch',align:'stretch',stretch:[{idx:1}]});  
            ich.find('.'+css.item_url).urlLight("align");
            
        });
    }else
        setTimeout(function(){t.align();},par.delay);
},
_update_num:function(){
    var t=jurls,p=t.param,n=1,css=p.css;
    $.each(p.list.children(),function(i,o){
        var num = $(o).find('.'+css.item_num);
        num.text(n++);
    });
    
},
_pos:function(pos){
    var t=jurls,p=t.param;
    JX.abs(p.frame,pos);
    t.align();
}
    
};