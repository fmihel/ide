/*global JX,jhandler,navigator*/
/**
 * dvc - информация о габаритах устройства
 * Основной задачей данного класса я вляется определение момента 
 * смены ландшафтной ориентации экрана на портретную и обратно,
 * а так же возвожность пересчета из системы координат броузера
 * в метрическую и обратно.
 * 
 * Проверка смены ориентации 
 * dvs.onOrientation(function(){
 *    
 *        //произошла смена ориентации
 *    
 *});
 * 
 * 
 * Пересчет из ск броузера в мм 
 * dvs.toMM(px);
 * 
 * обратно
 * dvs.toPX(mm);
 * 
 * 
 * 
 */
 
var dvc={
    
    os:'Windows',
    platform:'PC',
    mobile:false,
    landscape:undefined,
    tablet_area:80, /*область в мм, по которой вычисляем габаритность устройства, если хотябы одна сторона меньше, то устройство маленький тел, в противном случае планшет*/
    devicePixelRatio:1,
    device:{w:0,h:0},/*размер в px устройства ( то что заявил производитель)*/
    browser:{w:0,h:0},/*размер в px броузера (может не совпадать с устройством)*/
    viewport:{w:0,h:0},/*размер в px видимой области броузера (изменяется когда на мобильном появляется клавиатура и т.д. в ск броузера)*/
    size:{w:0,h:0}, /* габариты устройства в мм*/
    overallness:"monitor",/*габаритность устройства (монитор, телефон, планшет) monitor|phone|tablet*/
    kmm:1.55, /* коэф пикселизации, найден экспериментальным путем :( */
    isIE:false,
    /*zoom:1, коэфф масшабирования */
    browserName:"uncknown",
    chromium:false,
    _timer:null,
    _handler:undefined,
    _landscaping:0,
    _sfull:false,
onOrientation:function(o){
    /*if (typeof(o) == "function") o = {func:o};
    o.group = "orientation";
    */
    dvc._handler.add(o);
},/*
onFullscreen:function(o){
    if (typeof(o) == "function") o = {func:o};
    o.group = "fullscreen";
    dvc._handler.add(o);
},
onViewport:function(o){
    if (typeof(o) == "function") o = {func:o};
    o.group = "viewport";
    dvc._handler.add(o);
},*/
toMM:function(px){
    var t = dvc;
    return JX.translate(px,0,t.browser.w,0,t.size.w);
},
toPX:function(mm){
    var t = dvc;
    return JX.translate(mm,0,t.size.w,0,t.browser.w);
},
_debug_info:function(){
    var t = dvc;
    var wp = Qs.workplace;
    
    var txt = t.device.w+','+t.device.h+',';
        txt+=t.viewport.w+','+t.viewport.h+',';
        txt+=t.browser.w+','+t.browser.h+',';
        txt+=t.size.w+','+t.size.h+',';
    if (wp){
        wp = JX.pos(wp);
        txt+=wp.w+','+wp.h+',';
    }
        
    if (txt!==t._debug){        
        t._debug = txt;
        console.info('--------------------------------------------');
        if (wp) console.info('workplace = {w:'+wp.w+', h:'+wp.h+'}');
        console.info('device    = {w:'+t.device.w+', h:'+t.device.h+'}');
        console.info('browser   = {w:'+t.browser.w+', h:'+t.browser.h+'}');
        console.info('viewport  = {w:'+t.viewport.w+', h:'+t.viewport.h+'}');
        console.info('size      = {w:'+t.size.w+', h:'+t.size.h+'}');
    }    
},
fullscreen:function(enable/*bool or undef*/){
    if (enable===undefined){
        //return (document.fullscreenEnabled || document.mozFullScreenEnabled || document.webkitFullscreenEnabled);
        var fs = (document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement || document.msFullscreenElement);
        return ((fs!==null)&&(fs!==undefined));
    }else{
        
        var dom = document.documentElement;

        if (enable){
            if(dom.requestFullscreen) 
                dom.requestFullscreen();
            else if(dom.mozRequestFullScreen) 
                dom.mozRequestFullScreen();
            else if(dom.webkitRequestFullscreen)
                dom.webkitRequestFullscreen();
            else if(dom.msRequestFullscreen)
                dom.msRequestFullscreen();
        }else{
            if(document.cancelFullscreen)
                document.cancelFullscreen();
            else if(document.exitFullscreen)
                document.exitFullscreen();
            else if(document.mozCancelFullScreen)
                document.mozCancelFullScreen();
            else if(document.webkitExitFullscreen)
                document.webkitExitFullscreen();
            else if(document.msExitFullscreen)
                document.msExitFullscreen();                
        }
        
        dvc.landscape = undefined;
        
    }
},
_browserName:function(){
    var t=dvc;
    var agent = navigator.userAgent;    

    if ((agent.search(/MSIE/) > -1)||(agent.search(/\.NET/) > -1)) t.browserName = 'ie';
    else if ((agent.search(/Edge/) > -1)) t.browserName = 'edge';
    else if ((agent.search(/Firefox/) > -1)) t.browserName = 'firefox';
    else if ((agent.search(/Opera/) > -1)||(agent.search(/OPR\//) > -1)) t.browserName = 'opera';
    else if ((agent.search(/Chrome/) > -1)) t.browserName = 'chrome';
    else if ((agent.search(/Safari/) > -1)) t.browserName = 'safari';
    t.isIE = (t.browserName==='ie');
    
},
_init:function(){
    var t=dvc;
    
    t._browserName();
    t._handler = new jhandler();
    
    t._platform();
    t._landscape();
    t._update_area();
    
    t._timer = setInterval(
        function(){
            t._update();
        },
        100
    );
    /*
    setInterval(function(){
        t._debug_info();
    },200);
    */
    t.chromium = ((t.browserName==='chrome')||(t.browserName==='opera'));
},
_update:function(){
    var t=dvc,h=t._handler,fs = t.fullscreen();
    
    if (t._landscape()||(t._sfull!==fs)){
        if (t._tua!==undefined) clearTimeout(t._tua);
        t._sfull=fs;
        t._tua =  setTimeout(function(){
            t._update_area();
            h.do('all');
            t._tua = undefined;
        },500);
    }   
    
},
_update_area:function(){
    var t=dvc;
    
    t._dpr();
    
    t._viewport();
    t._device();
    t._browser();
    t._size();
    t._overallness();

},    
_viewport:function(){
     var t=dvc,r = {w:window.innerWidth,h:window.innerHeight};
     if(!JX.eq(r,t.viewport)){
        t.viewport = r;
        return true;
     }
     return false;
},
_platform:function(){
    var t=dvc;
    if (navigator.userAgent.match(/Android/i)){ 
        t.os='Android';
        t.platform = 'uncknown';
        t.mobile=true;
    }else if (navigator.userAgent.match(/BlackBerry/i)){ 
        t.os='BlackBerry';
        t.platform = 'BlackBerry';
        t.mobile=true;
    }        
    else if (navigator.userAgent.match(/iPhone/i)){ 
        t.os='iOS';
        t.platform = 'iPhone';
        t.mobile=true;
    }
    else if (navigator.userAgent.match(/iPad/i)){ 
        t.os='iOS';
        t.platform = 'iPad';
        t.mobile=true;
    }
    else if (navigator.userAgent.match(/iPod/i)){ 
        t.os='iOS';
        t.platform = 'iPod';
        t.mobile=true;
    }
    else if (navigator.userAgent.match(/Opera Mini/i)){ 
        t.os='OperaMini';
        t.platform = 'uncknown';
        t.mobile=true;
    }
    else if (navigator.userAgent.match(/IEMobile/i)){
        t.os='Windows';
        t.platform = 'uncknown';
        t.mobile=true;
    }
},
_landscape:function(){
    var t=dvc,l = (window.innerHeight<window.innerWidth),res = false;
        
    if ((t.mobile) && (screen)&&(screen.orientation)&&(screen.orientation.type))
        l = (screen.orientation.type === 'landscape-primary');
    
    res = (l!==t.landscape);
    t.landscape = l;
    
    return res;
},
zoom:function(){
    var t =dvc,
    r = (window.devicePixelRatio?window.devicePixelRatio:Math.sqrt(screen.deviceXDPI * screen.deviceYDPI) / 96);
    r = screen.width*r;
    
    return Math.round(r/window.screen.width*100);
    
},
_dpr:function(){
    var t =dvc,r = (window.devicePixelRatio?window.devicePixelRatio:Math.sqrt(screen.deviceXDPI * screen.deviceYDPI) / 96);
    
    if (r!== t.devicePixelRatio){
        t.devicePixelRatio = r;
        return true;
    }    
    return false;
},
_device:function(){
    var t=dvc,r={w:screen.width*t.devicePixelRatio,h:screen.height*t.devicePixelRatio};
    if (!JX.eq(r,t.device)){
        t.device = r;
        return true;
    }
    return false;
    
},
_browser:function(){
    var t=dvc,r;
    
    if (t.mobile)
        r = {
            w:window.innerWidth,
            h:  (screen.height*(window.innerWidth/screen.width)).toFixed(0)
        };
    else
        r = {
            w:window.innerWidth,
            h:window.innerHeight
        };
    
    if (!JX.eq(r,t.browser)){
        t.browser = r;
        return true;
    }    
    
    return false;
    
},
_size:function(){
    var t=dvc,inch = 25.4,dpi = t.devicePixelRatio*96/inch,
    r = {w:(t.device.w/dpi/t.kmm).toFixed(1),h:(t.device.h/dpi/t.kmm).toFixed(1)};
    
    if (!JX.eq(r,t.size)){
        t.size= r;
        return true;
    }
    return false;
},
_overallness:function(){
    var t=dvc,s = t.size,r = "monitor";
    if (t.mobile)
        r = ((s.w<t.tablet_area)||(s.h<t.tablet_area)?"phone":"tablet");

    if (r!==t.overallness){
        t.overallness = r;
        return true;
    }
    return false;
}

};

/*init object*/
dvc._init();