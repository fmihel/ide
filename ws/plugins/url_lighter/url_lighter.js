/*global ut,$,jQuery,JX*/
(function( $ ){

var m={

name:"urlLight",    
init:function(param){
    var o = m.obj(this);
    if (param===undefined) 
        param = {};
        
    if (o===undefined){ 
        param.plugin = this;
        param.own = this;
        
        m.obj(this,new url_lighter(param));
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
align:function(param){
    m.obj(this).align();
    return this;
},
get:function(name){
    return m.obj(this).attr(name);
}

};

$.fn.urlLight = function(n){
    if(m[n])
        return m[n].apply(this,Array.prototype.slice.call( arguments, 1 ));
    else 
    if ((typeof n==='object')||(!n)){
        return m.init.apply(this,arguments);
    }else
        $.error( 'Not exists method ' +  n + ' for jQuery.urlLight');
};
})(jQuery);

function url_lighter(o){
    this.init(o);
}

url_lighter.prototype.init=function(o){
    var t=this,
    a = $.extend(true,{
        own:undefined,
        jq:{
        },
        gap:1,
        text_align:"left",
        url:false,
        readOnly:false,
        placeholder:'enter url',
        click:undefined,
        align:'center',
        css:{
            frame:'uli_frame',
            edit:'uli_edit',
            
            protocol:'uli_protocol',
            domen:'uli_domen',
            path:'uli_path',
            file:'uli_file',
            params:'uli_params',
            placeholder:'uli_placeholder'
            
        }
    },o);
    
    t.param = a;
    
    if (a.url===false) 
        a.url=a.own.text();    
    a.own.html("");
    
    
    t._create();
    t._event();
    t.put(a);
    /*Ws.align(function(){t.align();});*/
    
};

url_lighter.prototype.done=function(){
    
};
url_lighter.prototype.url=function(url){
    var t=this,p=t.param,j=p.jq;
    if (url!==undefined){
        url=ut.url_parsing(url);
        url.protocol+='//';
        url.path='/'+url.path;
        
        if (url.params!=='') url.params="?"+url.params;
        if ((url.file==='')&&(url.params=='')&&(url.path==='/')) url.path = '';
    
        j.edit.val(url.url);
    
        j.path.text(url.path);
        j.file.text(url.file);
        j.domen.text(url.domen);
        j.protocol.text(url.protocol);
        j.params.text(url.params);
    
        t._culc_width(url);
        t.align();
    }else
        return j.edit.val();

};

url_lighter.prototype.put=function(o){
    var t=this;
    $.each(o,function(k,v){
        t.attr(k,v);    
    });    
    t.align();
};    

url_lighter.prototype.attr=function(k/*v*/){
    var t=this,p=t.param,j=p.jq,
    r=(arguments.length===1),
    v=(!r?arguments[1]:undefined);

    /*-----------------------------*/
    if (k=='url'){
        if (r) 
            return t.url();
        else
            t.url(v);
    }
    /*-----------------------------*/
    if (k=='placeholder'){
        if (r) 
            return p.placeholder;
        else{
            t.placeholder = v;
            j.placeholder.text(v);
        }    
    }
    /*-----------------------------*/
    if (k=='gap'){
        if (r) 
            return p.gap;
        else
            p.gap = v;
    }
    /*-----------------------------*/
    if (k=='readOnly'){
        if (r) 
            return p.readOnly;
        else
            p.readOnly = (v==true?'true':false);
    }
    /*-----------------------------*/
    if (k=='text_align'){
        if (r) 
            return p.text_align;
        else{
            p.text_align = (v=="right"?"right":"left");
            j.edit.css("text-align",p.text_align);
        }    
    }
    /*-----------------------------*/
    
};    

url_lighter.prototype._w=function(name,url){
    var t=this,p=t.param,j=p.jq,
    size = ut.text_size({text:url[name],by:j.frame});
    JX.pos(j[name],{w:size.w+p.gap});
};    

url_lighter.prototype._culc_width=function(url){
    var t=this;
    
    t._w('protocol',url);
    t._w('domen',url);
    t._w('path',url);    
    t._w('file',url);    
    t._w('params',url);

};


url_lighter.prototype._create=function(){
    var t=this,p=t.param,c='',o=p.own;
    p.id={
        frame:ut.id("ulf"),
        edit:ut.id('ule'),
        
        placeholder:ut.id('ulph'),
        protocol:ut.id('ulp'),
        domen:ut.id('uld'),
        path:ut.id('ulp'),
        file:ut.id('ulfi'),
        params:ut.id('ulps'),

    };
    
    
    c+=ut.tag('<',{id:p.id.frame,css:p.css.frame,style:'position:absolute'});
    
    c+=ut.tag({tag:"input",id:p.id.edit,css:p.css.edit,style:'position:absolute'});
    c+=ut.tag({id:p.id.placeholder,css:p.css.placeholder,value:p.placeholder,style:'position:absolute'});
    
    c+=ut.tag({id:p.id.protocol,css:p.css.protocol,style:'position:absolute'});
    c+=ut.tag({id:p.id.domen,css:p.css.domen,style:'position:absolute'});
    c+=ut.tag({id:p.id.path,css:p.css.path,style:'position:absolute'});
    c+=ut.tag({id:p.id.file,css:p.css.file,style:'position:absolute'});
    c+=ut.tag({id:p.id.params,css:p.css.params,style:'position:absolute'});
    
    c+=ut.tag('>');
    
    o.append(c);
    p.jq.frame = o.find("#"+p.id.frame);
    p.jq.edit = o.find("#"+p.id.edit);
    
    p.jq.placeholder   = o.find("#"+p.id.placeholder);
    p.jq.protocol   = o.find("#"+p.id.protocol);
    p.jq.domen      = o.find("#"+p.id.domen);
    p.jq.path       = o.find("#"+p.id.path);
    p.jq.file       = o.find("#"+p.id.file);
    p.jq.params     = o.find("#"+p.id.params);
    

};

url_lighter.prototype._focused=function(bool){
    var t=this,p=t.param,j=p.jq;
    if (p.readOnly) return;
    
    p.edit_focus = bool;
    t.align();
    
    if (bool)
        j.edit.focus();
    else
        t.url(j.edit.val());    
    
    t.align();
    
};

url_lighter.prototype._event=function(){
    var t=this,p=t.param,j=p.jq;
    var click = function(){
        if (!p.readOnly)
            t._focused(true);
        else
            if (p.click) p.click();
        return false;    
    };
    
    j.frame.on('click',function(){ 
        if ((p.readOnly)&&(p.click))
            p.click();
    });
    
    j.protocol.on("click",click);
    j.path.on("click",click);
    j.file.on("click",click);
    j.domen.on("click",click);
    j.params.on("click",click);
    j.placeholder.on("click",click);
    
    j.edit.on("focusout",function(){t._focused(false);});
    j.edit.keypress(function(e){if(e.which == 13) t._focused(false);}); 
    
};

url_lighter.prototype.align=function(){
    var t=this,p=t.param,j=p.jq;
    
    var s = j.frame.css('font-size'),f=j.frame.css('font-family');
    
    if ((s!==p._prev_size)||(f!==p._prev_font)){
        p._prev_size=s;
        p._prev_font=f;
        t.url(t.attr('url'));            
    }
    
    var u = (t.url()!=='');
    var need = ((!p.edit_focus)&&(u));
    
    JX.visible(j.edit,p.edit_focus);        
    JX.visible(j.path,need);
    JX.visible(j.domen,need);
    JX.visible(j.protocol,need);
    JX.visible(j.params,need);
    JX.visible(j.file,need);
    
    JX.visible(j.placeholder,(!p.edit_focus)&&(!u));
    
    JX.stretch(j.frame);
    
    if (p.edit_focus)
        JX.arrange([j.edit],{direct:"horiz",type:"stretch",align:"center",stretch:j.edit});
    else{
        if (u)
            JX.arrange([j.protocol,j.domen,j.path,j.file,j.params],{direct:"horiz",type:p.text_align,align:p.align});
        else    
            JX.arrange([j.placeholder],{direct:"horiz",type:p.stretch,align:p.align});
    }    
    
    
};


