/*
    js thread
    THREAD(function(data,th,event){
        ....
    });
    
    new thread({func:function(data,sender,event){
        if (event=='start') data.i = 0;
        
        //do anything

        data.i=data.i+1;
        if (data.i>5000) sender.stop();
        
    }});
    
    new thread({
        onstart:function(data,sender){
            data.i=0;    
        },
        func:function(data,sender){

            //do anything

            data.i=data.i+1;
            if (data.i>5000) sender.stop();
        },
        onstop:function(data,sender){
            console.info('stop');            
        },
        
    });
    
*/
function THREAD(o){
    return new thread(o);
}
function thread(o){
    /*global $*/
    var t=this;
    
    
    var p = (typeof o ==='function'?{func:o,suspend:false}:o);

    t.param=$.extend({
        interval:1,
        suspend:true,
        forced:1, /*count call func on one iteration*/ 
        data:{},
        onstart:undefined,
        onstop:undefined,
        onerror:undefined,
        func:undefined,
        _timer:-1,
        _event:'start',
        _need_stop:false
    },
    p);
    
    if (!t.param.suspend)
        t.start();
}

thread.prototype.start=function(){
    var t=this,p=t.param;
    if (p.onstart) p.onstart(p.data,t,'start');
    
    p._timer = setInterval(function(){
        
        try{
            var di = p.forced;
            while ((di>0)&&(!p._need_stop)){
                p.func(p.data,t,p._event);
                p._event = 'loop';
                di--;
            }
        }catch(e){
            t._stop();
            if (p.onerror) p.onerror(e,t);            
        }
        
        
        
        if (p._need_stop){ 
            t._stop();
            if (p.onstop) p.onstop(p.data,t,'stop');            
        }
        
    },p.interval);        
};
thread.prototype._stop=function(){
    var t=this,p=t.param;
    clearInterval(p._timer);
    
};
thread.prototype.stop=function(){
    var t=this,p=t.param;
    p._need_stop=true;
};