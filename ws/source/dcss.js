/*global $,JX,dvc, jhandler,Ws */
/**
 * Dcss - модуль обработки динамической загрузки тем
 * Темы описываются в серверном файле xxxx.dcss, и представляют
 * собой объект
 * $dcss = {
 *    styles:{style1:[var1,var2,...],style2:[...],...}
 * }
 * Ex:
 * $dcss = {styles:{
 *      device:[mobile,desktop],
 *      color:[light,dark]
 * }}
 * 
 * Dcss.load - загрузка темы
 * Dcss.onLoad - список обработчиков, вызываемых после успешной подгрузки темы
 * 
 * 
*/
var Dcss = {
    styles:{},
    handler:new jhandler(),
    story:[],
onLoad:function(o){
    if (o===undefined)
        Dcss.handler.do('all');
    else    
        Dcss.handler.add(o);
},
/**
 * load - загрузка тем
 * load(param)
 * param:
 *  styles:object   - описание необходимой к загрузке темы Ex. {color:"light"}
 *  done: ф-ция вызываемая после успешной загрузки темы, но до onLoad
 *  error: ф-ция в случае ошибки
 *  custom: собственный обработчик после загрузки темы (код должен быть примерно
 *   custom:function(data){
 *      
 *       Dcss.fromData(data,dvc.landscape);
 *       Ws.align();
 *       Dcss.onLoad();  
 *       
 *  }     
 * 
 * 
*/
load:function(o){
    var t=Dcss,h=t.handler,
    
    a = $.extend(true,{
        styles:t.styles,
        done:undefined,
        error:undefined,
        custom:undefined,
        forced:false
    },o);
    
    var succ = function(data,a,h){
        t.fromData(data,dvc.landscape,dvc.fullscreen());
        Ws.align();
        if (a.done) 
            a.done();
        h.do('all');
    };
    
    var have =t.styleGet(a.styles,dvc.landscape,dvc.fullscreen());
    if (!have){
    Ws.ajax({id:'LOAD_DCSS_STYLE',
        value:{
            styles:a.styles,
            device:{
                    browser:dvc.browser,
                    size:   dvc.size,
                    mobile: (dvc.mobile?1:0),
                    device: dvc.device
            }
        },
        error:function(){
            console.error('Dcss.load',arguments);
            if (a.error) a.error();
        },
        done:function(data){
            if (data.res===1){
                
                data.styles = a.styles;
                (a.custom?a.custom(data):succ(data,a,h));
                
            }else{
                console.error('data.res!==1...');
                if (a.error) a.error();
            }
            
        }
    });    
    }else{
        //succ(have,a,h);
        (a.custom?a.custom(have):succ(have,a,h));
    }    
    
},
fromData:function(data,landscape,fullscreen){
    var t = Dcss,i,head;
    
    t.styles    = $.extend(true,{},data.styles);
    
    t.vars      = $.extend(true,{},data.vars);
                
    head = $('head');
    for(i=0;i<data.files.length;i++) head.append('<link rel="stylesheet" href="'+data.files[i]+(Ws.version!==''?'?'+Ws.version:'')+'"/>');
    
    $('#ws_style').html(data.css);
    
    t.styleStory(data,landscape,fullscreen);
},
styleStory:function(data,landscape,fullscreen){
    var t = Dcss,have=t.styleGet(data.styles,landscape,fullscreen);
    
    if (!have){
        var styles = $.extend(true,{},data.styles);
        var vars = $.extend(true,{},data.vars);
        var files = $.extend(true,[],data.files);
        var css = data.css;
        
        t.story.push({styles:styles,vars:vars,files:files,css:css,landscape:landscape,fullscreen:fullscreen});
    } 
},
styleGet:function(styles,landscape,fullscreen){
    var t=Dcss,i,st,s = $.extend(true,t.styles,styles);
    
    for(i=0;i<t.story.length;i++){
        st = t.story[i];
        
        if ((st.landscape === landscape)&&(st.fullscreen===fullscreen)&&(t._eqobj(st.styles,s)))
            return t.story[i]; 
        
    }
    return false;
},
_eqobj:function(a,b){
    var r = true;
    $.each(a,function(n,m){
        if ((b[n]===undefined)||(b[n]!==m)){
            r = false;
            return false;
        }
    });
    
    if (r) $.each(b,function(n,m){
        if ((a[n]===undefined)||(a[n]!==m)){
            r = false;
            return false;
        }
    });
    
    return r;
},
get:function(name,def){
    return (Dcss.vars[name]!==undefined?Dcss.vars[name]:def);
}
};

