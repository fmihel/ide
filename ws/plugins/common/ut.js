/*global $,nil,navigator,Qs*/
/** @module ws/plugins/ut*/

/**
 * коллекция утилиток для различной надобности :)
 * @summary утилиты
 * @class
 * 
*/ 
var ut={
/** сравнение объектных ссылок*/
oEq:function(a,b){
    
    if ((typeof a !== 'object') || (typeof b != 'object'))
        return false;
        
    var testField = ut.random_str(10);
    while((a[testField]!==undefined)||(b[testField]!==undefined))
        testField = ut.random_str(10);
    
    a[testField] = testField;
    
    if (b[testField] === testField){
        delete b[testField];    
        delete a[testField];
        return true;
    }else{
        delete a[testField];
        return false;
    }
},

/** расширяет объект o методами add . Метод не пересоздает объект ( в отличии от jQuery extend, а ссылается на него)*/
extend:function(add,o){
    for (var key in add) {
        if (o[key]===undefined)
            o[key]=add[key];
    }        
    return o;
},
os:function(){
    if (ut._os===undefined){
        ut._os = { mobile:false,platform:'Windows'};
    
    if (navigator.userAgent.match(/Android/i)) 
        ut._os={mobile:true,platform:'Android'};
    else if (navigator.userAgent.match(/BlackBerry/i))
        ut._os={mobile:true,platform:'BlackBerry'};
    else if (navigator.userAgent.match(/iPhone|iPad|iPod/i))
        ut._os={mobile:true,platform:'iOS'};       
    else if (navigator.userAgent.match(/Opera Mini/i))
        ut._os={mobile:true,platform:'i'};        
    else if (navigator.userAgent.match(/IEMobile/i))
        ut._os={mobile:true,platform:'Windows'};        
    }
    return ut._os;
},
/** 
 * добавляет свойства в объект (к приеру если имя свойство формируется динамически)
 * var a = {};
 * var b = {};
 * var field1 = "field1";
 * var field2 = "field2";
 * 
 * a[field1] = "test";
 * a[field2] = "test";
 * b.a = a;
 * 
 * b.a = ut.emb(field1,"test",field2,"test");
 * 
*/
emb:function(){
    var a=arguments,c=a.length/2,i,r={};
    for(i=0;i<c;i++)
        r[a[i*2]]=a[i*2+1];
    return r;    
},
/** список атрибутов dom */
attrs:function(o,prm){
    let d,jq,
    p=$.extend(true,{
        exclude:[] //exclude tag
    },prm);
    
    if (JX.is_jquery(o)){
        d = o[0];
        jq = o;
    }else{
        d = o;
        jq = $(d);
    }
    var res = {};
    jq.each(function(){
        $.each(this.attributes, function() {
            if(this.specified){ 
                if (p.exclude.indexOf(this.name)==-1)
                    res[this.name] = this.value;
            }    
        });
    });    
    
    return res;
},
mobile:function(){
    var os=ut.os();
    return os.mobile;
},
random:function(min,max){
    return Math.floor(Math.random() * (max - min + 1)) + min; 
},
random_str:function(count){
    var res = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    for(var i=0;i<count;i++ ) res+=possible.charAt(Math.floor(Math.random() * possible.length));
    return res;    
},
fill:function(n,o){
    var a=$.extend(true,{
        len:5,
        char:'0',
        left:true
    },o);
    var out = String(n);
    var cnt=a.len-out.length;
    if (cnt>0) for(var i=0;i<cnt;i++)
        out=(a.left?a.char+out:out+a.char);
    return out;
},
id:function(name){
    return name+ut.random(1000000,9999999);    
},
slash:function(str,left,right){
    left  = left||left===undefined?true:false;
    right = right||right===undefined?true:false;
    str = str.trim();
    
    
    if ((left)&&(str.charAt(0)!=="/"))
        str = "/"+str;
    else if ((!left)&&(str.charAt(0)==="/"))    
        str = str.slice(1);
    
    if ((right)&&(str.charAt(str.length-1)!=='/')){
            str = str+'/';
    }else  if ((!right)&&(str.charAt(str.length-1)=='/'))
        str = str.slice(0,str.length-1);
    return str;
    
},
extFileName:function(f){
    return f.replace(/^.*[\\\/]/, '');
},
extPath:function(f){
    f = f.trim();
    if (f.substring(f.length-1,1)=='/')
        return f;
    else    
        return f.substring(0, f.lastIndexOf('/'))+"/";
},
ext:function(f){
    /*extension*/
    return f.split('.').pop();
},
hrefPath:function(){
    return ut.extPath(ut.href());
},
href:function(){
    return window.location.href;
},

date:function(d){
    d = (d===undefined?new Date():d);
                
    return {
        y:d.getFullYear(),
        m:d.getMonth(),
        d:d.getDate(),
        h:d.getHours(),
        i:d.getMinutes(),
        s:d.getSeconds()
    };
},

dateTo:function(format,d){
    /* Yy-m-d Hh-i-s */
    format = format === undefined?"Y/m/d H:i:s":format;
    d = (d===undefined?new Date():d);
                
    var t = ut.date(d);
    Y = t.y+"",
    y = Y[2]+Y[3],
    m = (t.m+1)+"",
    H = t.h,
    s = t.s+"",
    i = t.i+"",
    h = (H>12?H-12:H)+"",
    d = t.d+"";
    H+="";
                
    m=m.length>1?m:"0"+m;
    d=d.length>1?d:"0"+d;
    i=i.length>1?i:"0"+i;
    s=s.length>1?s:"0"+s;
    H=H.length>1?H:"0"+H;
    h=h.length>1?h:"0"+h;
                
    return format.replaceAll(["Y","y","m","d","h","H","i","s"],[Y,y,m,d,h,H,i,s]);
},
            
dateFrom:function(from,format){
    var t = {Y:-1,y:-1,m:-1,d:-1,H:-1,h:-1,i:0,s:0},i,len,key = "YymdHhis",
    cur = ut.date(new Date());
                    
    for(i=0;i<format.length;i++){
        id = key.indexOf(format[i]);
        if (id>=0){
            len = (id>0?2:4);
            t[key[id]] = parseInt(from.substring(0,len));
            from = from.substring(len,from.length);
        }else
            from=from.substring(1,from.length);
    }
                
    cur.y=cur.y+"";
    t.Y = t.Y!==-1?t.Y:(t.y===-1?cur.y:(cur.y[0]+cur.y[1]+t.y));
    t.H = t.H!==-1?t.H:(t.h===-1?0:(t.h));
                
    if (t.m===-1) 
        t.m=cur.m;
    else
        t.m--;
                    
    if (t.d===-1) t.d=cur.d;
        return new Date(t.Y,t.m,t.d,t.H,t.i,t.s);
},

translate:function(y,y1,y2,x1,x2){
    
    if ((y2 - y1) === 0) return 0;
    return (x2*(y-y1)+x1*(y2-y))/(y2-y1);
    
},
/** добавляет префикс ко всем вложенным элементам, являющиеся строками */
addPref:function(o,pref,left){
    
    
    if ((typeof pref!=='string')||(pref==='')) 
        return o;

    if (left===undefined) left = true;
    
    if (typeof o==='string')
        return (left?pref:'')+o+(!left?pref:'');
        
    if ($.isArray(o)){
        for(var i=0;i<o.length;i++)
            o[i] = ut.addPref(o[i],pref,left);    
        
    }else if (typeof o==='object'){ 
        for(var key in o)
            o[key]=ut.addPref(o[key],pref,left);
    }
    
    return o;
},
/** удаляет префикс во всех вложенных элементах, являющиеся строками */
delPref:function(o,pref,left){
    
    
    if ((typeof pref!=='string')||(pref==='')) 
        return o;

    if (left===undefined) left = true;
    
    if (typeof o==='string'){
        if (left){
            if (o.indexOf(pref)===0)
                o=o.substr(pref.length);
        }else{
            var p = o.lastIndexOf(pref);
            if (o.lastIndexOf(pref)===(o.length-pref.length))
                o=o.substr(0,p);
        }
    }    
        
    if ($.isArray(o)){
        for(var i=0;i<o.length;i++)
            o[i] = ut.delPref(o[i],pref,left);    
        
    }else if (typeof o==='object'){ 
        for(var key in o)
            o[key]=ut.delPref(o[key],pref,left);
    }
    
    return o;
},
/** позволяет получить значение вложенной переменной 
 * Ex: к примеру имеем структуру
 * s = {
 *      user:{
 *          data:[
 *              "apple",
 *              "orange"
 *          ]
 *      }    
 * }
 * 
 * Если структура формируется динамические то
 * для получения значения data необходимо выполнить
 *  if (s!==undefined)&&(s.user!==undefined)&&(s.user.data!==undefined)&&(s.user.data[0])
 *     c =  s.user.data[0];
 * else
 *     c = false;
 * 
 * или
 *   c = ut.get(s,"user","data",0,false);
 * 
*/
get:function(){
    if (arguments.length<2) return false;
    var a=arguments,i,t,m,
        v = a[0],d = a[a.length-1];
            
    if (v===undefined) return d;
            
    for(i=1;i<a.length-1;i++){
        m = v[a[i]];
        if (m === undefined)
            return d;
        v=m;
    }
            
    return v;
            
},
tag:function(/**/){
    /*global $*/
    var ag = arguments,b='',o=undefined;
    if (ag.length===0) return '';
    if (ag.length===1){
        if (typeof ag[0]==='string')
            b=ag[0];
        else
            o=ag[0];
    }else{
        if (typeof ag[0]==='string'){
            b=ag[0];
            o=ag[1];
        }else{
            b=ag[1];
            o=ag[0];
        }    
    }
    
    var a=$.extend({
        tag:'div',/*tag name*/
        id:'',/*id*/
        css:'',/*classes*/
        style:'',/*style*/
        pos:{},/*pos{x,y,w,h}*/
        value:'',/*valeu <div><div>*/
        attr:{}/*attributes*/
    },o);
    
    if(b==='>')
        return "</"+a.tag+">";
    var p='',c='<'+a.tag;
    
    if (Array.isArray(a.css)) a.css = a.css.join(' ');
    
    c+=(a.id!==''?' id="'+a.id+'"':'');
    c+=(a.css!==''?' class="'+a.css+'"':'');
    if(a.pos){
        p+=(a.pos.x?'left:'+a.pos.x+'px;':'');
        p+=(a.pos.y?'top:'+a.pos.y+'px;':'');
        p+=(a.pos.h?'height:'+a.pos.h+'px;':'');
        p+=(a.pos.w?'width:'+a.pos.w+'px;':'');
    }
    c+=(((a.style!=='')||(p!==''))?' style="'+a.style+';'+p+'"':'');
    if(a.attr)$.each(a.attr,function(n,p){
        c+=' '+n+'="'+p+'"';
    });    
    if (a.tag==='input'){
        c+=(a.value!==''?' value="'+a.value+'"':'');
        c+='/>';
        return c;
    }
    c+='>';
        
    if (a.tag!=='input')
        c+=(a.value!==''?a.value:'');
        
    if (b!=='<')
        c+='</'+a.tag+'>';

    return c;
},
url_comb:function(url){
    url = url.trim();            
    if ((url.indexOf('https://') !== 0)&&(url.indexOf('http://') !== 0)){            
        if (url.indexOf('www.') !==0)
            url = 'www.'+url;
        url = "http://"+url;
    }    
    return url;
},
url_add:function(url,keyvals){
    url=ut.url_comb(url);
    
    var ii = url.indexOf("?");
    if (ii == -1) 
        url=url+"?";
    ii = url.indexOf("?");
    
    var c='';
    $.each(keyvals,function(k,v){
        c+=(c!==''?'&':'')+k+'='+v;
    });
    
    return url+(ii < (url.length-1)?'&':'')+c;
},
url_nocache:function(url){
    var p={};
    p[ut.random_str(7)]=ut.random_str(7);
    return ut.url_add(url,p);
},
url_params:function(url/*set,over*/){
    /*return array of url params or set array to url params, if over === true then set union with existings params, else replace exists params*/
    var pairs;
    if (arguments.length>1){
        var  params=arguments[1];
        
        var over = (arguments.length>1?arguments[2]:false);
        if (over){
            var prev = ut.url_params(url);
            params = $.extend(true,prev,params);
        }
        
        pairs = [];
        for (var key in params)
            if (params.hasOwnProperty(key))
                pairs.push(encodeURIComponent(key) + '=' + encodeURIComponent(params[key]));
        var pr = pairs.join('&');
        
        var u = ut.url_parsing(url);
        return u.protocol+"//"+u.domen+"/"+u.path+u.file+"?"+pr;
        
    }else{
        var request = {};
        if (url.indexOf("?")===-1) return {};
        
        pairs = url.substring(url.indexOf("?") + 1).split("&");
        for (var i = 0; i < pairs.length; i++) {
            if(!pairs[i])
                continue;
            var pair = pairs[i].split("=");
            request[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
        }
        return request;
    }    
},
_url_parsing:function(url){
    var l = document.createElement("a");
    url = url.trim();
    if (url.indexOf('www.')==0)
        url = 'http://'+url;
    if ((url.indexOf('https://')==-1)&&(url.indexOf('http://')==-1))
        url = 'http://'+url;
        
    l.href = url;
    return l;
},
url_parsing:function(url){
    url = url.trim();
    if (url=='')
        return{ url:'',protocol:'',host:'',domen:'',path:'',file:'',params:'',param:{}};    

    var p=ut._url_parsing(url),
    path = ut.extPath(p.pathname),
    params = p.search;
    
    if (path[0]==='/')
        path=path.substr(1);
    if (params[0]==='?')
        params=params.substr(1);
        
    
    var res 
    = { 
        /* full url */url:url,               
        /* https:   */protocol:p.protocol,
        /* www.yandex.ru */host:p.host,
        /* www.yandex.ru */domen:p.hostname,
        /* path1/path2/path3/  */path:path,
        /* file.js   */file:ut.extFileName(p.pathname),
        /* "param1='12'&param2='3'  "*/params:params,
        /* {param1:'12',param2:'3'} */param:ut.url_params(url)
    };
    
    return res;

    
},
text_size:function(o){
    /*определение длины и ширины строки в px */
    var a = $.extend(true,{
        text:'',
        by:(Qs&&Qs.body?Qs.body:undefined),
        font:undefined,
        size:undefined
    },o);
    
    if (a.font===undefined) 
        a.font = a.by.css('font-family');
    if (a.size===undefined) 
        a.size = parseInt(a.by.css('font-size'));

    var str = document.createTextNode(a.text),res,
    obj = document.createElement("a");
    
    obj.style.fontSize = a.size + "px";
    obj.style.fontFamily = a.font;
    obj.style.margin = 0 + "px";
    obj.style.padding = 0 + "px";
    obj.appendChild(str);
    
    document.body.appendChild(obj);
    
    res = {w:obj.offsetWidth,h: obj.offsetHeight};
    document.body.removeChild(obj);
    
    return res;
    
    
},
reverse:function(arr){
    var out=[];
    for(var i=0;i<arr.length;i++) out.push(arr[arr.length-(i+1)]);
    return out;
},
table_add:function(o){
    var c='',tr,i;
    var a=$.extend(true,{
        table:undefined,
        id:ut.id('tr'),
        cols:[],
        before:-1,
        css:{
            tr:'',
            td:''
        }
    },o);
    
    if (nil(a.table)){
        console.error('table is not defined ut.table_add');
        return;
    }

    c+=ut.tag('<',{tag:'tr',id:a.id,css:a.css.tr});
    for(i=0;i<a.cols.length;i++)
        c+=ut.tag({tag:'td',css:(typeof a.css.td == 'string'?a.css.td:a.css.td[i]),value:a.cols[i]});
    c+=ut.tag('>',{tag:'tr'});
    
    tr=$(c);
    
    var b = a.table.find("tbody");
    if (b.find("tr").length===0)
        b.append(tr);
    else 
    if(typeof a.before=='number'){    
        if (a.before===0)
            tr.insertBefore(b.find("tr:first"));
        else
        if (a.before==-1)
            tr.insertAfter(b.find("tr:last"));
        else
            tr.insertBefore(b.find("tr").eq(a.before));
    }
    
    return a.table.find('#'+a.id);
},
table_create:function(o){
    var c='';
    var a=$.extend(true,{
        id:ut.id('table'),
        own:undefined,
        style:'',
        css:'',
        attr:[]
        
    },o);
    
    if (nil(a.own)){
        console.error('own is not defined ut.table_create');
        return;
    }
    
    c=ut.tag('<',{tag:'table',id:a.id,style:a.style,css:a.css,attr:a.attr});
    c+=ut.tag({tag:'thead'});
    c+=ut.tag({tag:'tbody'});
    c+=ut.tag('>',{tag:'table'});    
    
    a.own.append(c);
    
    return a.own.find('#'+a.id);
},
table_delete:function(o){
    var a=$.extend(true,{
        table:undefined,
        tr:undefined, /*tr or idx (if idx = -1 then delete last row)*/
        idx:undefined
    },o);    
    
    if (nil(a.table)){
        console.error('table is not defined ut.table_delete');
        return;
    }
    
    if (a.tr){
        a.tr.remove();        
    }else if(a.idx!==undefined){
        var rows = ut.table_rows(o);
        if (a.idx==-1) a.idx = rows.length-1;
        
        if ((a.idx>=0)&&(a.idx<rows.length))
            rows[a.idx].remove();
    } 
},
table_clear:function(o){
    var a=$.extend(true,{
        table:undefined,
    },o);    
    
    if (nil(a.table)){
        console.error('table is not defined ut.table_clear');
        return;
    }
    a.table.children().eq(1).find("tr").remove();
},
table_rows:function(o){ /*return array of DOM tr!!! */
    var a=$.extend(true,{
        table:undefined,
    },o);    
    
    if (nil(a.table)){
        console.error('table is not defined ut.table_rows');
        return;
    }
    
    return a.table.children().eq(1).children();
},
table_header:function(o){
    /*создание или заполнение заголовка*/
    var c='',i;
    var a=$.extend(true,{
        caption:"",/*string || array [string || {text:'',width:''}]*/
        idx:-1, /*if caption string? then set col header in idx*/
        table:undefined,
        css:{
            tr:"",
            td:""/* may be array of classes for tds*/
        }
    },o);    
    
    if (nil(a.table)){
        console.error('table is not defined ut.table_header');
        return;
    }
    
    var h = a.table.find("thead");
    
    if (typeof a.caption=='string'){
        h.children().children().eq(a.idx).html(a.caption);
    }else{
        h.html("");
        c+=ut.tag('<',{tag:'tr',css:a.css.tr});
        for(i=0;i<a.caption.length;i++){
            c+=ut.tag({tag:'td',
                css:(typeof a.css.td == 'string'?a.css.td:a.css.td[i]),
                value:(typeof a.caption[i]=='string'?a.caption[i]:a.caption[i].text),
                style:(typeof a.caption[i]=='string'?'':
                    (a.caption[i].width?'width:'+a.caption[i].width+(typeof a.caption[i].width!='string'?'px':''):'')
                    )
            });
        }
        c+=ut.tag('>',{tag:'tr'});
        
        var tr = $(c);
        h.append(tr);
    }
    
    return tr;
},
extFloat:function(s,def){
    if (typeof s==='number')
        return s;
    try{    
        var f= /\d+[\.\,]?\d*/.exec(s.replaceAll(',','.'));
        if (f!==null)
            return parseFloat(f[0]);
        else
            return (def!==undefined?def:0);
    }catch(e){
        return def;
    }        
},
fixFloat:function(f/*precession,def*/){
    var p= arguments.length>1 ? arguments[1] : 2,
        def=arguments.length>2 ? arguments[2] : 0,
        s,pos;
        
    if (typeof f!=='number')
        f = ut.extFloat(f,false);
        
    if (f!==false){    
        s = f.toString(),
        pos = s.indexOf('.');
        s=s.substring(pos);
        if ((pos>=0)&&(s.length>(p+1)))
            return f.toFixed(p);
        return f;
    }
    return def;
},
/**
* скроллирует 
* внешний объект o.scroll:jQuery, 
* до момента, пока 
* o.target:jQuery не окажется в области видимости
* alg - тип алгоритма
* alg = "simple" - просто смещает target так что бы верхняя граница совпадала с верхней границей scroll
* alg = "reach"  - target по наиболее короткому расстоянию от текущего сместиться в область видимости scroll
* 
* 
*/
scroll:function(o){
    let a=$.extend(true,{
        scroll:null,
        target:null,
        animate:0,
        off:0,
        alg:"simple"/* reach */
    },o),
    posTar = JX.abs(a.target),
    posScr = JX.abs(a.scroll),
    delta;
    
    if (a.alg ==='reach'){

        if ((posTar.h>posScr.h) || (posTar.y<posScr.y))
            delta = posTar.y-posScr.y+a.scroll.scrollTop()-a.off;
        else
            delta = posTar.y-(posScr.y+posScr.h-posTar.h)+a.scroll.scrollTop()+a.off;    

    }else
        delta = posTar.y-posScr.y+a.scroll.scrollTop()-a.off;    
     
    
    if (a.animate == 0)
        a.scroll.scrollTop(delta);
    else    
        a.scroll.animate({scrollTop:delta},a.animate);
    
        
    
}    

};