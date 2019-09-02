/**
 * библиотека работы с хеш тегами в командной строке
 * Ex: получить значение хеша
 *  console.info(url_hash.text());
 *  
 * Ex: добавить событие на изменение хеша
 *  url_hash.bind((...a)=>{
 *       console.info('hash is changed')
 * })
 * 
 * Ex: изменить хеш но не генерировать событие onChange
 *  url_hash.value("new_hash_string",{callOnChange:false});
 *  или
 *  url_hash.text("new_hash");
 */ 
var url_hash = {
_bind:[],
_needOnChange:true,
init(){
    let t = url_hash;
    $(window).bind( 'hashchange',(...a)=>{ t._onChange(...a);});         
},
_onChange(...a){
    let t = url_hash;
    
    if (t._needOnChange){
        t._bind.map(f=>{
            try{
                f(...a);
            }catch(e){
                console.error(e);
            }
        });        
    }    
    t._needOnChange = true;
},
/**
 * добавить событие на изменение хеша
 */ 
bind(func){
    let t = url_hash;
    if (typeof func === 'function' )
        t._bind.push(func);
},
/**
 *удалить событие на изменение хеша
 */ 
unbind(func){
    let t = url_hash;
    let i = t._bind.indexOf(func);
    if (i>=0) 
        t._bind.splice(i,1);
},
/**
 * получить/изменить текущее значение хеша
 * если в параметре указать 
 * callOnChange = false - то генерироваться событие на изменение хеша не будет
 */ 
value(hash = undefined,o = undefined){
    let t = url_hash,
        a = $.extend(true,{
            callOnChange:true,
        },o);

    if (hash === undefined)
        return window.location.hash.substr(1);

    t._needOnChange = a.callOnChange;
    
    window.location.hash = hash;
    
    return window.location.hash.substr(1);
    
},
/**
 * получить/изменить значение хеша (не вызывает обработчики приаттаченные с помощью bind)
 */ 
text(hash = undefined){
    return url_hash.value(hash,{callOnChange:false});
}
};

url_hash.init();
