/*global $,Qs,ut,JX,nil,Ws,popup,editors*/
var code_complit={
param:{
    focused_sub:false,
    code_correct_funcs:[],
    css:{
        frame:"cc_frame",
        item:"cc_item",
        select:"cc_select",
        have_sub:'cc_have_sub',
        obj:"cc_obj",
        func:"cc_func",
        vars:"cc_var",
        file:"cc_file"
    }
},
list:[],
key:[], 
data:{}, 
onEnter:undefined,
init:function(){
    
    var t =code_complit,p=t.param,css=p.css,i;
    
    Qs.code_complit.on('click',function(e){
        var tar=$(e.target);
        if(tar.hasClass(css.item)){
            t.current({item:tar});        
        }
    });
    
    p.sub_funcs = ut.id('sub_funcs');
    Qs.code_complit.after(ut.tag({id:p.sub_funcs,css:css.frame+'  ws_scrollbar',style:"position:absolute;display:none"}));
    p.sub_funcs = Qs.code_complit.parent().find('#'+p.sub_funcs);

    
    for(i=0;i<p.code_correct_funcs.length;i++) p.code_correct_funcs[i]();
    
    
    t.init_data();
    setTimeout(function(){
        t._load_files();
    },3000);    
    
    
},
init_data:function(){
    var t =code_complit,i,j;
    if (arguments.length>0){
        var a = arguments[0];
        for(i=0;i<a.length;i++){
            if (a[i].s)
            for(j=0;j<a[i].s.length;j++)
                a[i].s[j].parent=a[i];
        }
    }else{
    $.each(t.data,function(i,item){
        t.init_data(item);        
    });}
},
get_funcs:function(o){
    
    var t=code_complit,data=t.data,d,all_exts = ['php','css'],i,j,out=[],k,s,obj,finded=false,have_sub=false;
    
    var a=$.extend(true,{
        key:false,
        ext:undefined
    },o);
    
    if(!a.key) return [];
    
    if (nil(a.ext)) a.ext = all_exts;
    if (a.ext.length==0) a.ext=all_exts;
    
    var key  = a.key.key;
    var name = a.key.name;
    var delim = a.key.delim;
    var nk = name+key;
    var nd = name+delim;
    var dk = delim+key;
    var nkd= name+delim+key;
    
    if (nkd ==='') return [];
    
    
    var global_func = [];
    var complete    = [];
    var object      = [];
    var object_var  = [];
    var addition    = [];
    var p           = null;
    var set_p_null  = false;
    
    for(i=0;i<a.ext.length;i++){
        d=data[a.ext[i]];
        
        if(d) for(j=0;j<d.length;j++){
            p=d[j];
            have_sub = ((p.s)&&(p.s.length>0));
            set_p_null = false;
            
            finded = false;
            var can_delim = (p.s)&&(p.s.length>0)&&(p.d)&&(p.d.indexOf(delim)>-1);

            if (delim===''){ /*нет разделителя*/
                if(p.k.indexOf(nk)==0){
                    p.key = nk;
                    (have_sub?object.push(p):global_func.push(p));
                    finded=true;
                }    
            }else{
                if((p.k === name)&&(can_delim)) { /*полное совпадение имени класса*/
                    finded = false;
                    
                    //object  = [];
                    for(k=0;k<p.s.length;k++){
                        s=p.s[k];
                        if (s.k.indexOf(dk)===0){
                            s.key = nkd;
                            s.up = p;
                            //object.push(s);
                            complete.push(s);
                        }    
                    }
                    //break; /*больше не ищем*/
                        
                }else{ /*добавляем все объекты с разделителем*/
                    if (can_delim){
                        if (key===''){
                            p.key = delim+key;
                            finded=true;
                            object.push(p);
                            set_p_null = true;
                        }else{
                            for(k=0;k<p.s.length;k++){
                                s=p.s[k];
                                if (s.k.indexOf(dk)===0){
                                    s.key = dk;
                                    s.up = undefined;
                                    object.push(s);
                                }    
                            }
                            
                        }    
                    }    
                }    
            }

            if ((finded)&&(have_sub))
            for(k=0;k<p.s.length;k++){
                s=p.s[k];
                
                s.key = p.key;
                s.up = (set_p_null?undefined:p);
                     
                
            }
        }        
    }
    
    /*if (object.length>0) addition=[];*/
    if (complete.length>0)
        obj = [complete]
    else    
        obj = [complete,object_var,object,global_func,addition];
    
    var filter_k = [], filter_o=[],filter_idx=-1;    
    
    for(i=0;i<obj.length;i++)
        for(j=0;j<obj[i].length;j++){
            p = obj[i][j];
            filter_idx = filter_k.indexOf(p.k);
            
            if (((p.s!=undefined)&&(p.s.length>0))||((filter_idx==-1)||(filter_o[filter_idx]!==p.o))){
                out.push(p);
                filter_k.push(p.k);
                filter_o.push(p.o);
            }    
        }    
    
    return out;
},
_type_to_css:function(type){
    var t = code_complit,p=t.param,css=p.css;

    if(nil(type))
        return css.obj;

    if(type=="function")
        return css.func;
    
    if(type!="object")
        return css.vars;

    return css.obj;
},    
update:function(o){
    var t = code_complit,p=t.param,css=p.css,i,code='',sub,val,type,e,file,charW=7.5;
    p.focused_sub = false;
    
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
    t.width = 150;

    for(i=0;i<t.list.length;i++){
        e = t.list[i];
        sub= (e.s?' '+css.have_sub:'');
        type = ' '+t._type_to_css(e.t);

        val= (e.parent? e.parent.k+o.key.delim:'')+e.n;
        file = (e.fo?e.fo:'');

        t.width = Math.max(t.width,(val.length+file.length)*charW);
        
        
        val+=ut.tag({css:css.file,value:file});
        code+=ut.tag({id:i,value:val,css:css.item+sub+type});
    }

    Qs.code_complit.append(code);
    t.show(code!=='');
    
    t.current({item:Qs.code_complit.find("."+css.item).first()});

},
update_sub:function(){
    
    var t = code_complit,p=t.param,css=p.css,c=t.current(),sub=p.sub_funcs,d=c.data.s,code='',i,type;
    
    if ((d)&&(d.length>0)){
        t.show_sub(true);

        sub.html("");
        sub.scrollTop(0);
        
        for(i=0;i<d.length;i++){

            /*if ((c.data.delim===undefined)||(d[i].k.indexOf(c.data.delim)==0))*/
            if((t.key.delim=='')||(d[i].k.indexOf(t.key.delim)==0))        
            {
                type = ' '+t._type_to_css(d[i].t);
                code+=ut.tag({id:i,value:(d[i].d?d[i].d:'')+d[i].n,css:css.item+type});
            }    
        }    
        sub.append(code);    
        
    }else
        t.show_sub(false);
    
    
},
show_sub:function(){
    var t = code_complit,p=t.param;
    var vis = JX.visible(p.sub_funcs);
    if (arguments.length==0)
        return vis;
    else{
        if(arguments[0]){
            p.sub_funcs.show();
            var pos = JX.abs(Qs.code_complit);
            JX.abs(p.sub_funcs,{x:pos.x+pos.w+2,y:pos.y,h:200,w:200});
        }else{
            p.sub_funcs.hide();
        }
    }
    
},
show:function(){
    var t = code_complit,p=t.param;
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
            JX.pos(Qs.code_complit,{w:t.width+10});
            if ((pos.y+pos.h+cc.h)>JX.screen().h){
                pos.y=pos.y-cc.h-20;
                JX.abs(Qs.code_complit,{y:pos.y+20});
            }    
        }else{
            Qs.code_complit.hide();
            t.show_sub(false);
        }
    }
},
dokey:function(o){
    var t = code_complit,p=t.param,css=p.css,item=null,current=t.current(),
    have_sub=((current)&&(current.data.s)&&(current.data.s.length>0));
    

    if (o.key=='Up'){
        t.current({item:current.item.prev()});
        t.scroll_to_current();
    }

    if (o.key=='Down'){
        t.current({item:current.item.next()});
        t.scroll_to_current();
    }
    
    if (o.key=='Enter'){
        if (have_sub)
            t.set_focus_on_sub(true);
        else{    
        if (t.onEnter)
            t.onEnter({sender:t,current:current});
        t.show(false);
        }
    }

    if (o.key=='Right'){
        t.set_focus_on_sub(true);
    }

    if (o.key=='Left'){
            if (p.focused_sub)
                t.set_focus_on_sub(false);
            else{
                t.show(false);
                return false;
            }    
    }
    
    return true;

},
scroll_to_current:function(){
    var t = code_complit,current=t.current().item,p=t.param;
    if (p.focused_sub){
        var p1 = JX.pos(p.sub_funcs);
        var p2 = JX.pos(current);
        var scroll = p.sub_funcs.scrollTop();
        var h =p1.h-24;
    
        if (!JX.inline(p2.y-scroll,0,h))
            p.sub_funcs.scrollTop(p2.y-h); 

    }else{    
        var p1 = JX.pos(Qs.code_complit);
        var p2 = JX.pos(current);
        var scroll = Qs.code_complit.scrollTop();
        var h =p1.h-24;
    
        if (!JX.inline(p2.y-scroll,0,h))
            Qs.code_complit.scrollTop(p2.y-h); 
    };
        
},
set_focus_on_sub:function(bool){
    var t = code_complit,p=t.param,css=p.css,item;
    if (bool){
        if (p.focused_sub) return;
        item=t.current();
        if ((item.data.s)&&(item.data.s.length>0)){
            p.sub_funcs.find('.'+css.item).first().addClass(css.select);
            p.focused_sub=true;
        }
    }else{
        if (p.focused_sub){
            item=t.current();
            item.item.removeClass(css.select);
            p.focused_sub=false;
        }
        
    }
},
current:function(){
    var t = code_complit,p=t.param,css=p.css,id=-1;    
    var data=null,item,parent;
    
    if (p.focused_sub){
        item = p.sub_funcs.find('.'+css.select);
        if (arguments.length==0){
            
            if (item.length>0){
                id=item[0].id;
                
                p.focused_sub = false;
                parent = t.current();
                p.focused_sub = true;

                data=parent.data.s[id];
            }
            return {item:item,data:data,id:id,parent:parent};
        
            
        }else{
            
            var a=$.extend(true,{
                item:undefined
            },arguments[0]);
        
            if ((nil(a.item))||(a.item.length==0)) return;
        
            p.sub_funcs.find('.'+css.select).removeClass(css.select);
            a.item.addClass(css.select);
            
        }    
        
    }else{
        item = Qs.code_complit.find('.'+css.select);
    
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
        
            t.update_sub();
        }    
    }    
},
code_preload:function(func){
    var t = code_complit,p=t.param;    
    p.code_correct_funcs.push(func);
},
code_db_add:function(target,obj_name,obj){
    var t=code_complit;
    
    var exist = t._code_db_get(target,obj_name);
    var data = t._code_obj_info(obj_name,obj);
    
    if (!exist)
        target.push(data);
    else{
        t._code_obj_refresh(exist,data);
        target[exist.idx]=data;
    }
},
code_db_add2:function(target,obj_name,data){
    var t=code_complit;
    
    var exist = t._code_db_get(target,obj_name);

    if (!exist)
        target.push(data);
    else{
        t._code_obj_refresh(exist,data);
        target[exist.idx]=data;
    }
},
_code_db_get:function(from,k){
    var res = null,idx=-1;
    $.each(from,function(i,item){
        if (item.k === k){
            idx=i;
            res = item;
            return true;
        }
    });
    
    return (res===null?false:{item:res,idx:idx});
},
_code_obj_info:function(obj_name,obj){
    var t=code_complit,res ,type,c="<{$cursor}>",dlm=".",names,names1,names2,name;    
    
    res={k:obj_name,n:obj_name,o:obj_name+c,d:[dlm],t:t._typeof(obj)};
    names = [],names1=[],names2=[];
    
    for(name in obj) (name.indexOf("_")===0?names2.push(name): names1.push(name));
    
    names1 = names1.sort();
    names2 = names2.sort();
    
    names=names1.concat(names2);
    
    for(var i=0;i<names.length;i++){
        name = names[i];
        if (!res.s) res.s=[];
            
        type = t._typeof(obj[name]);
        if (type=="function")
            res.s.push({k:dlm+name,n:name,o:dlm+name+"("+c+")",parent:res,t:type});    
        else
            res.s.push({k:dlm+name,n:name,o:dlm+name,parent:res,t:type});    

    }
    
    return res;    
},
_typeof:function(obj){
    var name,type = typeof(obj);
    if (type==="function") for(name in obj){type="object";break;};
    return type;
},
_code_obj_refresh:function(exist,data){
    var o=exist.item, s = o.s,i=0,find;
    
    data.k=o.k;
    data.n=o.n;
    data.o=o.o;
    data.t=o.t;
    
    while (i<data.s.length){
    
        var item = data.s[i];
        var exs = [];find = false;
        
        for(var n=0;n<s.length;n++){
            if (s[n].k===item.k){
                exs.push(s[n]);
                find=true;
            }else if (find) break;
            
        }
        if (exs.length>0){
            data.s[i] = exs[0];
            for(n=1;n<exs.length;n++){
                i++;
                data.s.splice(i,0,exs[n]);
            }    
        }
        i++;    
    };    
},
ext_to_data:function(ext){
    var t=code_complit,d=t.data;
    if (ext=='php') return d.php;
    if (ext=='js') return d.js;
    if ((ext=='css')||(ext=='dcss')) return d.css;
    
    return false;
    
},
_code_short:function(){
/**
 * Эксперементальный метод, реализует сжатие найденных данных.
 * Если в системе много классов с одинаковым наименованием, то все методы группируются
 * в один класс
*/
    var t=code_complit,d=t.data,ext=d.php,i,out=[],
    search=function(a){
        var j=0;
        for(j=0;j<out.length;j++){
            if ((out[j].k==a.k)&&(out[j].n==a.n)&&(out[j].t==a.t))
                return j;
        }
        return false;
    },
    search2=function(tos,fs){
        var j=0;
        for(j=0;j<tos.length;j++){
            if ((tos[j].k==fs.k)&&(tos[j].d==fs.d)&&(tos[j].t==fs.t))
                return j;
        }
        return false;
    },
    
    slice=function(to,from){
        var parent = to,k;
        if (from.d)
            to.d = $.extend(true,from.d,to.d);
            
        if (!from.s) return;
        if (!to.s){
            to.s=from.s;
            for(k=0;k<to.s.length;k++)
                to.s[k].parent=parent;
            return;
        }

        for(k=0;k<from.s.length;k++){
            var idx = search2(to.s,from.s[k]);
            if (idx===false){
                if (!Array.isArray(to.s)) to.s = [];
                to.s.push(from.s[k]);
                from.s[k].parent = parent;
            }
            
        }
    };
    
    
    
    for(i=0;i<ext.length;i++){
        var a = ext[i];
        var idx = search(a);
        if (idx===false){
            out.push(a);    
        }else{
            slice(out[idx],a);
        }
    }
    
    d.php = out;
    
    //for(i=0;i<out.length;i++){
    //    console.info(i,out[i]);
    //}
    
    
},
code_update:function(o){
    var t=code_complit,i,j;
    var a=$.extend(true,{
        filename:"",
        done:undefined,
        clear:true,
        rel_path:0
    },o);
    
    
    var ext = t.ext_to_data(ut.ext(a.filename));
    if (!ext) return;
    
    Ws.ajax({id:"cc_code_file",
        value:{
            name:a.filename,
            rel_path:a.rel_path
        },
        error:function(){
            console.error("Error parsing: "+a.filename);
            if (a.done) a.done();
        },done:function(data){
            if (data.res==1){
                
                if (a.clear){
                    i=0;
                    var pf=(a.rel_path==1?data.name:a.filename);
                    while(i<ext.length)
                        ext[i].f==pf?ext.splice(i,1):i++;
                }
                
                
                
                for(i=0;i<data.struct.length;i++){
                    /*
                    var file =data.struct[i].f;
                    data.struct[i].fo = data.struct[i].f;
                    
                    pos = file.indexOf(Ws.user_data.workplace)+Ws.user_data.workplace.length;
                    file = file.substring(pos);
                    filen = ut.extFileName(file);
                    if (file.length-filen.length>all)
                        file = file.substring(0,all)+'..'+filen;

                    data.struct[i].fo = file;
                    */
                    
                    ext.push(data.struct[i]);
                    if (data.struct[i].s)
                    for(j=0;j<data.struct[i].s.length;j++)
                        data.struct[i].s[j].parent = data.struct[i];
                }    
            }
            t._code_short();
            if (a.done) a.done();
        }});
},
_code_by_file:function(i){
    var t=code_complit,d=t.data,f=t._files,i;
    if (t._files_i<f.length){
    //if (t._files_i<5){
    
        t.code_update({
            filename:f[t._files_i],
            clear:false,
            done:function(){
                progress.do({all:t._files.length,current:t._files_i,caption:"code analize "+t._files_i+'/'+t._files.length});
                t._files_i++;
                t._code_by_file();
                
            }
        })
    }else{
        progress.visible(false);
    }
    
},    
_load_files:function(){
    var t=code_complit;
    Ws.ajax({id:"cc_load_files",value:{
        ext:'php'
        },
        error:function(){
            console.info(arguments);    
        },
        done:function(data){
            if (data.res==1){
                t._files=data.list;
                t._files_i=0;
                t._code_by_file();
            }else{
                console.info(data);
                popup({type:"alert",msg:"Error on code_complit._load_files"});
            }    
        }
    });
},
_culc_local_vars:function(){
    var t=code_complit,p=t.param,e=editors.current();
    
}
};