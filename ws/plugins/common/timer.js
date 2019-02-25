
var TIMER={

_timespy:[],
_TIMER_START:(new Date()).getTime(),

timesec:function(){
    
    return ((new Date()).getTime()-TIMER._TIMER_START)/1000;
},
timemilsec:function(){
    return ((new Date()).getTime()-TIMER._TIMER_START);
},
minf:function(minute){

    var d = new Date(0, 1, 1, 0, 0, minute*60, 0);
    var h = d.getHours();
    var m = d.getMinutes();
    var s = d.getSeconds();
    m = String(m).leftPad(2,'0');
    s = String(s).leftPad(2,'0');
    
    if (h>0){
        h = String(h).leftPad(2,'0');
        return  h+':'+m+':'+s;
    }else
        return  m+':'+s;
},
DateToStr:function(delim){
    var d = new Date();
    if (delim===undefined)
        delim = '/';
        
    var h = d.getDate()+'';
    if (h.length<2) h='0'+h;
    
    var m = (d.getMonth()+1)+'';
    if (m.length<2) m='0'+m;
    
    var s = d.getFullYear()+'';
    s = s.substr(2,2);
    
    
    return h+delim+m+delim+s;
    
},
TimeToStr:function(){
    var d = new Date();
    
    var h = d.getHours()+'';
    if (h.length<2) h='0'+h;
    
    var m = d.getMinutes()+'';
    
    if (m.length<2) m='0'+m;
    
    var s = d.getSeconds()+'';
    if (s.length<2) s='0'+s;
    
    return h+':'+m+':'+s;
},
start:function(name){
    var t=TIMER;
    
    if (!name) name = 'delta';
    
    if (!t._check) t._check=[];
    t._check[name] = t.timesec();
    
},
stop:function(name,outAs){
    var t=TIMER;
    if ((outAs!=='float')&&(outAs!=='console')) 
        outAs = 'string';
        
    if (!name) 
        name = 'delta';
        
    var delta = t.timesec()-t._check[name];
    
    if (outAs==='float')    
        return delta;
        
    if (outAs==='string')    
        return name+': '+delta.toFixed(3)+' sec';
    
    if (outAs==='console')    
        console.info(name+': '+delta.toFixed(3)+' sec');
        

},
ontimer:function(func,sec){
    setInterval(func,sec*1000);
},
_idx:function(name){
    var res = -1;
    $.each(TIMER._timespy,function(i,val){
        if (val.name === name) {
                res = i;
                return i;
        }
    });
    return res;    
},
INIT:function(opt){
    // opt = {name:STRING,single:BOOL,delay:FLOAT,enable:BOOL}
    if (!("name" in opt)) 
        return false;
    if (TIMER._idx(opt.name)>-1) 
        return false;
        
    var def = 
    {
        name:"",
        single:false,
        enable:false,
        delay:1,
        start:TIMER.timesec()
    };
    
    $.each(def,function(key,val){
        if (key in opt) 
            def[key]=opt[key];
    });
    
    TIMER._timespy.push(def);
    return true;
},

CHECK:function(name){
    var idx = TIMER._idx(name);
    if (idx===-1)
        return false;
    var t = TIMER._timespy[idx];
    
    if (!(t.enable)) 
        return false;
    var current = 
        TIMER.timesec();
    if (t.start+t.delay<=current) {
        if (t.single)
            t.enable = false;
        else
            t.start = current;
        return true;            
    }
    return false;
},

ENABLE:function(name/*enable*/){
    var idx = TIMER._idx(name);
    if (idx===-1)
        return false;
    var t = TIMER._timespy[idx];
    var enable = t.enable;
    
    if (arguments.length>1){
        
        enable=arguments[1];
        t.enable = enable;
        if (enable)
            t.start = TIMER.timesec();    
    }
    
    return enable;
},

DELAY:function(name,delay){
    var idx = TIMER._idx(name);
    if (idx===-1)
        return false;
    var t = TIMER._timespy[idx];
    t.delay = delay;
}    
};

