/*global $,JX,dvc, jhandler,session,ut */
//function Ws(){}

var Ws = {};

Ws.ajax2=function(o){
    var p=$.extend({
        id:-1,
        value:'',
        url:this.url,
        timeout:10000,
        done:Ws.done,
        error:Ws.error,
        before:Ws.before_ajax,        
    },o);
    
    var dat = {AJAX:1,ID:p.id,VALUE:p.value,SHARE:this.share};

    var request = $.ajax({url:p.url,method: "POST",timeout:p.timeout,data:dat});
    if (!("context" in p)) p.context = request;
    
    request.done(function(_data){
        var data = Ws.ajax_response(_data); 
        if(data!==null){
            
            /*include session module if session_mod is require*/
            if ((typeof(session)!=="undefined")&&(Ws.share.session!==undefined)&&(session._fromAjax))
                session._fromAjax(Ws.share.session,data[0].ID);
            /*--------------------------------------------------*/
            
            p.done(data[0].DATA,data[0].ID,p.context);
            
        }else
            p.error(null,"parse data ["+_data+"]",p.context);
                    
    });
    request.fail(function(jqXHR, textStatus){p.error(jqXHR,textStatus,p.context);});    
};

Ws.ajax=function(o){
    var 
    p=$.extend(false,{
        id:     -1,
        value:  '',
        url:    this.url,
        timeout:10000,
        done:   Ws.done,
        error:  Ws.error,
        before: Ws.before_ajax,
        repeat: 2,
    },o),
    
    repeat = p.repeat, 
    
    dat = {AJAX:1,ID:p.id,VALUE:p.value,SHARE:this.share},
    
    _recv=function(){
        var request = $.ajax({
            url:p.url,
            method: "POST",
            timeout:p.timeout,
            data:dat
        });    
        if (!("context" in p)) p.context = request;
        
        request.done(function(_data){
            var data = Ws.ajax_response(_data); 
                
            if(data!==null){
                /*include session module if session_mod is require*/
                if ((typeof(session)!=="undefined")&&(Ws.share.session!==undefined)&&(session._fromAjax))
                    session._fromAjax(Ws.share.session,data[0].ID);
                /*--------------------------------------------------*/
                p.done(data[0].DATA,data[0].ID,p.context);
            }else
                p.error(null,"parse data ["+_data+"]",p.context);
                    
        });
        
        request.fail(function(jqXHR, textStatus){
            repeat--;
            if (repeat<0)
                p.error(jqXHR,textStatus,p.context);
            else{
               console.log('repeat['+repeat+']'+p.id);
               _recv();
            } 
        });
    };
    
    _recv();
    
};

Ws.navigate=function(o){
    var p=$.extend({
        module:"",
        value:"",
        url:this.url,
        before:undefined
    },o);

    var dat = {"MODULE":p.module,"VALUE":p.value,"SHARE":this.share};                    
    var wrapper ="<form method='post' id='_mod_load_frm_' action='";
    wrapper+=this.url+"'>";
    wrapper+="<input type='hidden' name='module' value='"+JSON.stringify(dat)+"' />";
    wrapper+='</form>';
    $('body').append(wrapper);
    
    if ((p.before===undefined)||(p.before(p))){
        $("#_mod_load_frm_").submit();
    }

};

Ws.ajax_response=function(data){
    var res = null;
    try{
        res = $.parseJSON(data);
        this.share = res[1].SHARE;
    
        return res;
    }catch(err){
        return null;     
    }    
};

Ws.before_ajax=function(){
    
};

Ws.done=function(data,id,context){
    
};

Ws.error=function(obj,msg,context){
   console.error('Ws.error:'+msg); 
};

Ws._aligns = [];
Ws._lockAlign = 0;
Ws._only = [];

Ws.align=function(o){
    
    
    if (o===undefined){
        if (Ws._lockAlign!==0) return;

        var recall=[],remove=[],i,item;
        
        Ws._align();
        for(i=0;i<Ws._aligns.length;i++){
            item = Ws._aligns[i];
            try{

                if (item.func) item.func();
                if (item.recall) recall.push(item);

            }catch(e){
                console.error('align: '+Ws._aligns[i].id+' remove handler');
                remove.push(item);
            }
        }
        
        for(i=0;i<remove.length;i++)
            Ws.removeAlign(remove[i].id);


        for(i=0;i<recall.length;i++){
            item = recall[i];        
            if (item.func) item.func();
        }
        
        JX.lh('update');
        
    }else{
        
        if (o==='begin'){
            Ws._lockAlign=1;
            return;
        }
        if (o==='end'){
            Ws._lockAlign=0;
            if (Ws._lockAlign<0){ console.error('Ws._lockAlign < 0 ...!');}
            return;
        }
        
        var a=false,id=ut.id('alfn');
        if (typeof o==='function'){
            a={
                id:id,
                func:o,
                recall:false,
            };
        }else if((o.func)&&(typeof o.func==='function'))
            a = $.extend(false,{
                id:id,
                func:undefined,
                recall:false
            },o);
        
        if (a)
            Ws._aligns.push(a);    
        
        return a;
    }
    
};
Ws.removeAlign=function(id){
    var a = Ws._aligns,i;
    for(i=0;i<a.length;i++){
        if(a[i].id===id){
            a.splice(i,1);
            return;
        }
    }
};

Ws.only=function(name,f){
    if (f === undefined){
        try{
            Ws._only[name]();
        }catch(e){
            console.error(name,e);
        }    
    }else{
        if (!(name in Ws._only)){
            Ws._only[name]=f;
        }else{
            console.error('name ['+name+'] is already exists!');
        }
    }
};
Ws._ready = [];
Ws.ready=function(){
    //var ww=arguments.length>0 ? arguments[0] : null;
    /*global $*/
    /*global ut*/
    if (arguments.length==0){
        for(var i=0;i<Ws._ready.length;i++){
            var f = Ws._ready[i].func;
            try{(f?f():0);}catch(e){console.error('ready: '+Ws._ready[i].id);}
        }
    }else{
        var o=arguments[0],a,id=ut.id('rdfn');
        if(o.func)
            a = $.extend({
                id:id,
                func:undefined,
            },o);
        else
            a={id:id,func:o};
        Ws._ready.push(a);    
    }
    
};