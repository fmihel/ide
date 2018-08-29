/*global location,$,navigator*/

var cook={
get:function(name,def){
    if (def===undefined) def = '';
    var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
    return matches ? decodeURIComponent(matches[1]) : def;
},
exist:function(name){
    var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
    return matches ? true : false;
},
bool:function(name,def){
    return (cook.get(name,def) == "true");
},
set:function(name, value, options) {
    var _COOKIE_MAX_TIME = 315400000;/*10 years*/
    
    var a=$.extend(true,{
        expires:_COOKIE_MAX_TIME,
        path:'/',
        domen:location.hostname
    },options);
    
    var expires = a.expires;

    if (typeof expires == "number" && expires){
        var d = new Date();
        d.setTime(d.getTime() + expires*1000);
        expires = a.expires = d;
    }
    
    if (expires && expires.toUTCString) 
  	    a.expires = expires.toUTCString();

    value = encodeURIComponent(value);

    var updatedCookie = name + "=" + value;

    for(var propName in a){
        updatedCookie += "; " + propName;
        var propValue = a[propName];    
        if (propValue !== true)
            updatedCookie += "=" + propValue;
    }
    
    document.cookie = updatedCookie;
},
del:function(name) {
  cook.set(name, "", { expires: -1 });
},
enabled:function(){
    return navigator.cookieEnabled;
}
    
};
