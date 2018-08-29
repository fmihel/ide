/*global $,Qs,ut,JX*/
var code_complit={
param:{
    css:{
        frame:"cc_frame",
        item:"cc_item",
        select:"cc_select"
    }
},
list:[],
key:[], 
data:{}, 
onEnter:undefined,
init:function(){
    var t =code_complit,p=t.param,css=p.css;
    Qs.code_complit.on('click',function(e){
        var tar=$(e.target);
        if(tar.hasClass(css.item)){
            t.current({item:tar});        
        }
    });    
},
get_funcs:function(o){
    
    var t=code_complit,data=t.data,d,all_exts = ['php','css'],i,j,k,out=[];
    
    var a=$.extend(true,{
        key:[],
        ext:undefined
    },o);
    
    if (nil(a.ext)) a.ext = all_exts;
    
    if (a.key.length===0) return [];
    if (a.ext.length==0) a.ext=all_exts;

    
    for(i=0;i<a.ext.length;i++){
        d=data[a.ext[i]];
        for(j=0;j<d.length;j++){
            for(k=0;k<a.key.length;k++)
            if(d[j].k.indexOf(a.key[k])==0){
                d[j].key=a.key[k];
                out.push(d[j]);
            }
        }        
    }
    
    return out;
},
update:function(o){
    var t = code_complit,css=t.param.css,i,code='';
    Qs.code_complit.html('');
    Qs.code_complit.scrollTop(0);
    
    if (o.filled){
        t.list=[];
        for(i=0;i<o.ext.length;i++)
            t.list=t.list.concat(t.data[o.ext[i]]);
    }else{
        t.list=t.get_funcs(o);
    }    
    
    t.key=o.key;
    for(i=0;i<t.list.length;i++){
        code+=ut.tag({id:i,value:t.list[i].n,css:css.item+(i==0?' '+css.select:'')});
    }

    Qs.code_complit.append(code);
    t.show(code!=='');

},
show:function(){
    var vis = JX.visible(Qs.code_complit);
    if (arguments.length==0){
        return vis;
    }else{
        if(arguments[0]){
            Qs.code_complit.show();
            var cc = JX.pos(Qs.code_complit);
            var pos = editors.abs_cursor_coord();
            if (!vis){
                JX.abs(Qs.code_complit,{x:pos.x-10,y:pos.y+20});
            }
            if ((pos.y+pos.h+cc.h)>JX.screen().h){
                pos.y=pos.y-cc.h-20;
                JX.abs(Qs.code_complit,{y:pos.y+20});
            }    
        }else{
            Qs.code_complit.hide();
        }
    }
},
dokey:function(o){
    var t = code_complit,p=t.param,css=p.css,item=null,current=t.current();

    if (o.key=='Up'){
        t.current({item:current.item.prev()});
        t.scroll_to_current();
    }

    if (o.key=='Down'){
        t.current({item:current.item.next()});
        t.scroll_to_current();
    }
    
    if (o.key=='Enter'){
        if (t.onEnter)
            t.onEnter({sender:t,current:current});
            t.show(false);
    }
    
    

        
    
},
scroll_to_current:function(){
    var t = code_complit,current=t.current().item;
    
    var p1 = JX.pos(Qs.code_complit);
    var p2 = JX.pos(current);
    var scroll = Qs.code_complit.scrollTop();
    var h =p1.h-24;
    
    if (!JX.inline(p2.y-scroll,0,h)){
        
        Qs.code_complit.scrollTop(p2.y-h); 
    }
        
},
current:function(){
    var t = code_complit,p=t.param,css=p.css,id=-1,    
    item = Qs.code_complit.find('.'+css.select),
    data=null;
    
    if (arguments.length==0){

        if (item.length>0){
            id=item[0].id;
            data=t.list[id];
        }
        return {item:item,data:data,id:id};
    }else{
        var a=$.extend(true,{
            item:undefined
        },arguments[0]);
        
        if ((nil(a.item))||(a.item.length==0)) return;
        
        Qs.code_complit.find('.'+css.select).removeClass(css.select);
        a.item.addClass(css.select);
    }    
}


};