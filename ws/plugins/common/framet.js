/** 
 * генератор структуры DOM
 * Ex1 :
 * var c = framet({id:"my"})  
 * >>> c = '<div id="my" style="position:absolute"></div>'
 *
 * Ex2 :
 * var c = framet({id:"my",abs:false})  
 * >>> c = '<div id="my"></div>'
 *
 * Ex3 :
 * var c = framet({id:"my",css:"myClass",val:"text"})  
 * >>> c = '<div id="my" class="myClass" style="position:absolute">text</div>'
 *
 * Ex4 :
 * var c = framet([{css:"myClass",val:"text"},{css:"myClass",val:"text"}])  
 * >>> c = '<div id="xxxx" class="myClass" style="position:absolute">text</div> <div id="xxxx" class="myClass" style="position:absolute">text</div>'
 *
 * Ex5 :
 * var c = framet({css:"myClass",val:"text",child:[{css:"item"},{css:"item"}]})  
 * >>> c = '<div id="xxxx" class="myClass" style="position:absolute">text</div> <div id="xxxx" class="myClass" style="position:absolute">text</div>'
 * 
 * Ex6 :
 * var c = framet({css:["myClass","common"],style:{color:"red"},val:"text"})  
 * >>> c = '<div id="my" style="position:absolute;color:red" class="myClass common">text</div>'
 * 
 * Ex7: упрощенный вариант описания
 * var c = _f({val:"text",background:"red",title:"hint"})
 * >>> c = '<div style="background:red;position:absolute" title="hint">text</div>'
 * 
 * Вариант к возможной реализации
 * framet('panel |$v1| {text-indent:10px} "$css" ',[
 *          'item1|text1| "$item"',
 *          'item2|text2| "$item"',
 *        ],
 * {
 *  v1:"text",
 *  css:css.panel,
 *  item:"itemPanel"
 * });
 */ 

function framet(o,n,rt){
    var c = '',i,d,ty,kn,key,ks = ['style','attr','tag','id','val','value','css','child','abs'],at=['title','disabled'],
    num = ['width','height','left','top'],anum=['w','h','x','y'],ani,typo;
    
    if (Array.isArray(o)){ 
        for(i=0;i<o.length;i++) 
            c+=framet(o[i],n,rt);
        
    }else{
        
        typo=typeof o;
        
        if (typo==='function'){
            var out =o(n);
            if (typeof out==='string')
                c=out;
        }else if (typo==='string'){
                c=o;
        }else{
            var root = (rt===undefined?true:rt);
            var p=
            {
                abs:(n)&&(n.abs!==undefined)?n.abs:true,
                genId:(n)&&(n.genId!==undefined)?n.genId:true,
            };
    
            var 
            t = o.tag===undefined?'div':o.tag,
            k = o.css===undefined?undefined:o.css,
            s = '',
            id = o.id===undefined?(p.genId?ut.id('id'):undefined):o.id,
            v = o.val===undefined?(o.value===undefined?'':o.value):o.val,
            a='',
            ads='',ada='';
        
            // все что не зарезервировано, считаем либо стилем либо атрибутом
            for (key in o){
                if (ks.indexOf(key)===-1){
                    if (at.indexOf(key)!==-1)
                        ada+=(ada!==''?' ':'')+key+'="'+o[key]+'"';
                    else{
                        kn = key;
                        
                        ani = anum.indexOf(kn);
                        kn  = (ani!==-1?num[ani]:kn);
                        
                        ads+=(ads!==''?';':'')+kn+':'+o[key];
                    
                        if (num.indexOf(kn)!==-1){
                            d = o[key]+'';
                            if ((parseInt(d).toString()===d)||(parseFloat(d).toString()===d)) ads+='px';
                        }    
                    }    
                }
            }/*for*/
        
            if (Array.isArray(k))
                k = k.join(' ');
            
        
            ty = typeof o.style;
            if (ty==='undefined')
                s = (((p.abs)&&(o.abs!==false))||(o.abs===true))?'position:absolute':'';
            else if (ty ==='string')
                s = (((p.abs)&&(o.abs!==false))||(o.abs===true))?'position:absolute;':''+o.style;
            else if (ty ==='object'){
                s = '';
            
                for (key in o.style){
                    kn = key;
                    
                    ani = anum.indexOf(kn);
                    kn  = (ani!==-1?num[ani]:kn);
                
                    s+=(s!==''?';':'')+kn+':'+o.style[key];
                
                    if (num.indexOf(kn)!==-1){
                        d = o.style[key]+'';
                        if ((parseInt(d).toString()===d)||(parseFloat(d).toString()===d)) s+='px';
                    }    
                }/*for*/    

                if ((o.style.position===undefined)&&( ((p.abs)&&(o.abs!==false)) || (o.abs===true)))
                    s='position:absolute'+(s!==''?';':'')+s;

            }/*if (ty ==='object') */
            
            if (ads!=='')
                s+=(s!==''?';':'')+ads;
        
        
            if (o.attr) for (key in o.attr)
                a+=(a!==''?' ':'')+key+'="'+o.attr[key]+'"';
    
            if ((t==='input')&&(v!==''))
                a+=(a!==''?' ':'')+'value="'+v+'"';
        
            if (ada!=='')
                a+=(a!==''?' ':'')+ada;
        
            c+='<'+t;
            c+=(id?' id="'+id+'"':'');
            c+=(k?' class="'+k+'"':'');
            c+=(s!==''?' style="'+s+'"':'');
            c+=(a!==''?' '+a:'');
            c+=t==='input'?'/>':'>';
            if (o.child)c+=framet(o.child,n,false);
    
            if (t!=='input'){
                c+=v;
                c+='</'+t+'>';
            }
        }/*else if (function)*/    
    }
    
    return c;
}


(function( $ ){
$.framet = function(o,p){
    return framet(o,p);
};

$.fn.framet = function(o,p){
    var c = framet(o,p);
    return this.append(c);
};
/*    
var m={
name:"framet",
render:function(obj,param){
    var c = framet(obj,param);
    
    return c;     
}
};
*/
})(jQuery);