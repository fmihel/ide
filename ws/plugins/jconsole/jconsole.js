/*global ut,$,jQuery,JX,Qs,Ws*/
/** @module trial/exampler/jconsole*/
/**
 * plugin: jconsole<br>    
 * file  : jconsole.js<br>
 * path  : trial/exampler/<br>
 * @class jconsole
 *
*/
(function( $ ){
/** @lends module:trial/exampler/jconsole~jconsole# */
var m={
name:"jconsole",
/**
 * Create plugin and handler object {@link module:trial/exampler/jconsole~Tjconsole Tjconsole}
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.jconsole(param)
 * </div>
 * @param {json} param full list of param see in {@link module:trial/exampler/jconsole~Tjconsole.param Tjconsole.param}
 * @return this
 * @example 
 * $('#div').jconsole({
 * ...
 * });
 * @example get attr (1)
 * var t = $('#div').jconsole("multiSelect");
 * @example get attr (2)
 * var t = $('#div').jconsole("get","multiSelect");
 * @example set attr (1)
 * $('#div').jconsole("put",{multiSelect:true});
 * @example set attr (2)
 * $('#div').jconsole({multiSelect:true});
 * 
 *   
*/

init:function(data,param){
    var o = m._plugin(this);

    if (param!==undefined){
        if (typeof param==="string") 
            param = {obj:data,name:param};
        else if(typeof param==="object")
            param.obj = data;                
        else
            param = {obj:data};
    }else
        param = {obj:data};

    o.put(param);                
        
    return this;     
},

init2:function(param){
    var o = m.obj(this),p;

    if (o===undefined){
        p=$.extend(true,{},param);
        p.plugin = this;
        m.obj(this,new Tjconsole(p));
    }else
        o.put(param);                
        
    return this;     
},
/**
 * Deleted plugin and related objects
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.jconsole("destroy")
 * @return this
 * </div>
*/
destroy:function(){
    var o = m._plugin(this);
    o.done();
    m.obj(this,undefined);    
    return this;
},
/**
 * Story the handler object Tjconsole in jquery 
 * @private
 * @return Tjconsole
*/
obj:function(t/*set*/){
    if (arguments.length>1) 
        $.data(t[0],m.name,arguments[1]);
    return $.data(t[0],m.name);
},

_plugin:function(_this){
    var o = m.obj(_this);
    
    if (o===undefined)
        return m.obj(_this,new Tjconsole({plugin:_this}));
    else
        return o;

},

/**
 * Sets the parameters of the object Tjconsole, see {@link module:trial/exampler/jconsole~Tjconsole.param }
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.jconsole('put',param)
 * </div>
 * @param {json} param full list of param see in {@link module:trial/exampler/jconsole~Tjconsole.param}
 * @return this
 * @example 
 * $('#div').jconsole('put',{ 
 *     param1:value1,
 *     param2:value2 
 * });
*/
put:function(param){
    m._plugin(this).put(param);
    return this;
},
/**
 * Gets the value of the object's parameters (see {@link module:trial/exampler/jconsole~Tjconsole.param})
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.jconsole("get",name)
 * </div>
 * @param {string} name name of param in jconsole  {@link module:trial/exampler/jconsole~Tjconsole.param}
 * @return mixed type
 * @example 
 * var v = $('#div').jconsole('get',"deepSelect");
 *   
*/
get:function(name){
    return m._plugin(this).attr(name);
},
/**
 * clear plugin
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.jconsole("clear")
 * </div>
 * @param {json} param
 * @return mixed type
 * @example 
 * var v = $('#div').jconsole('clear');
 *   
*/
clear:function(/*param*/){
    var o = m._plugin(this);
    o.clear(arguments[0]);    
    return this;
},
scroll:function(to){
    var o = m._plugin(this);
    o.scroll(to);    
    return this;
}


};

$.fn.jconsole = function(n){
    if(m[n])
        return m[n].apply(this,Array.prototype.slice.call( arguments, 1 ));
    else 
        return m.init.apply(this,arguments);
    
};
})(jQuery);

/**
 * Handler for jconsole plugin. Propertys this class story in param,
 * see {@link module:trial/exampler/jconsole~Tjconsole.param}
 * @class Tjconsole
 * @property {json} param see {@link module:trial/exampler/jconsole~Tjconsole:param param}
 * 
*/
function Tjconsole(o){
    var t = this;
    t.init(o);
}
/** Constructor of Tjconsole. Initial creation and initialization of the object.*/
Tjconsole.prototype.init = function(o){
    var t=this;
    /** @namespace module:trial/exampler/jconsole~Tjconsole:param */
    t.param = $.extend(true,
    /** @lends module:trial/exampler/jconsole~Tjconsole:param#*/
    {
        plugin:null,
        /** значение одного отступа */
        gap:'&nbsp;&nbsp;&nbsp;&nbsp;',
        /** скорость анимации */
        speed:100,
        /** вкл/выкл очистку консоли перед каждым выводом*/
        clearBefore:false,
        /** длинные строки свыше cropString будут обрезаться, чтобы посмотреть полностью нужно кликнуть*/
        cropString:50,
        /** после вывода скролировать на начало объекта */
        scrollAfter:true,
        /** раскрыть первый уровень после вывода */
        expandAfter:true,
        /** куда будут выводиться данные plugin - в plugin browserConsole - в console.info() */ 
        terminal:{plugin:true,browserConsole:true},
        fastInfo:{
            enable:false,
            viewElements:5,
            cropString:10
        },
        
        /** какие данные будут отображаться */
        view:{
            func:true,
            string:true,
            object:true,
            array:true,
            number:true,
            nil:true,
            undef:true,
            bool:true
        },
        /** array of css classes uses in Tjconsole */
        css:{
            //.debug_string,.debug_number,.debug_boolean,.debug_function,.debug_array,.debug_object
            undef:'jconsole_undefined',
            string:'jconsole_string',
            number:'jconsole_number',
            bool:'jconsole_boolean',
            func:'jconsole_function',
            array:'jconsole_array',
            arrayLen:'jconsole_array_len',
            arrayLen2:'jconsole_array_len2',
            arrayIndex:'jconsole_array_index',
            arrayEl:'jconsole_array_el',
            object:'jconsole_object',
            objectName:'jconsole_object_name',
            objectEl:'jconsole_object_el',
            objectProp:'jconsole_object_prop',
            
        }
        
    },o);
    
    t.put(t.param);
};
  

/** Destructor of Tjconsole. Call on plugin is deleted.*/
Tjconsole.prototype.done=function(){
    this.clear();
};
/** 
 * get fast info object
 * @param {object} o
*/
Tjconsole.prototype._fast_info=function(o){
    var t=this,p=t.param,view=p.view,css=p.css, type = typeof(o),out = '',
        loop=0,max=p.fastInfo.viewElements,inf,
        
    info=function(e){
        if (e === null) return view.nil?"null":false;
        if (e === undefined) return view.undef?"undefined":false;
        var t = typeof e;
        
        if ((t==="string")&&(view.string)){ 
                var all = (e.length>p.fastInfo.cropString?e.substring(0,p.fastInfo.cropString)+'..':e).replaceAll('<','&lt;').replaceAll('>','&gt;');
                return ut.tag({tag:'span',css:css.string,value:'"'+all+'"'});
        }        
        if ((t==="number")&&(view.number)) return ut.tag({tag:"span",css:css.number,value:e});
        if ((t==="boolean")&&(view.bool)) return ut.tag({value:e?"true":"false",tag:"span",css:css.bool});
        if ((t==="function")&&(view.func)) return ut.tag({tag:"span",css:css.func,value:"f(...)"});
        if (t==="object"){
            if($.isArray(e)){
                if (view.array) return "["+e.length+"]";
            }else{ 
                var vcn = (e.constructor)&&(e.constructor.name)&&(e.constructor.name!=='Object')?e.constructor.name:'{...}';
                if (view.object) return ''+vcn+'';
            }    
        }
        return false;
    };
    
    
    if (type=="object"){
        if ($.isArray(o)){
              out = '[';
              line = '';
              $.each(o,function(i,e){
                  
                    inf = info(e);
                    if (inf!==false){
                        line+=(line===''?'':', ')+inf;
                        loop++;
                        if (loop>=max){
                            if (loop<o.length) 
                                line+=', ...';
                            return false;  
                        } 
                    }
              });
              
              out+=line+'] '+ut.tag({tag:"span",css:css.arrayLen2,value:' '+o.length+''});
        }else{
              out = '{';
              line = '';
              $.each(o,function(name,e){
                    inf = info(e);
                    if (inf!==false){
                        line+=(line===''?'':' , ')+ut.tag({tag:"span",css:css.objectProp,value:name})+":"+info(e);
                        loop++;
                        if (loop>=max){
                            line+=', ...';
                            return false;  
                        } 
                    }    
              });
              
              out+=(line===''?'...':line)+'}';
                
        }
    }
    return out;
};
/** 
 * debug object
 * @param {json} o
*/
Tjconsole.prototype._debug=function(val,to,level,add,done){
    var t=this,p=t.param,css=p.css;
    var type = typeof(val);
    
    if (level === undefined) 
        level = 0;
        
    if (add === undefined) 
        add = "";

    var gap="";
    for(var i=0;i<level;i++) 
        gap+=p.gap;
        

    add=gap+add;
    /*-------------------------------------------------*/
    if (val === null){
        if (p.view.nil){
            to.append(ut.tag({css:css.nil,value:add+'null'}));
            if (done) done(to.children().last());
        }
        return ;
    }

    if (type === 'undefined'){
        if (p.view.undef){
            to.append(ut.tag({css:css.undef,value:add+'undefined'}));
            if (done) done(to.children().last());
        }    
        return ;
    }

    if (type==='string'){
        if (p.view.string){
            var all = val.replaceAll('<','&lt;').replaceAll('>','&gt;');
            var str = all;
            var needCrop = false;
            if (p.cropString>0){
                if (str.length>p.cropString){
                    str = str.substring(0,p.cropString)+'..';
                    needCrop = true;
                }
            }    
        
            to.append(ut.tag({css:css.string,attr:{crop:'on'},value:add+'"'+str+'"'}));
        
        
            if (needCrop){
                var s = to.children().last();
                $.data(s[0],"str",{all:add+'"'+all+'"',crop:add+'"'+str+'"'});
                s.on("click",function(){
                    var th=$(this);
                    var str = $.data(th[0],'str');
                    th.html(th.attr('crop')==='on'?str.all:str.crop);
                    th.attr('crop',th.attr('crop')==='on'?'off':'on');
                });
            }
            
            if (done) done(to.children().last());
        }
        return ;
    }
    
    if (type==='number'){
        if (p.view.number){
            to.append(ut.tag({css:css.number,value:add+val}));
            if (done) done(to.children().last());
        }    
        return ;
    }
    
    if (type==='boolean'){
        if (p.view.bool){
            to.append(ut.tag({css:css.bool,value:add+(val?'true':'false')}));
            if (done) done(to.children().last());
        }    
        return ;
    }
    
    if (type==='function'){
        if (p.view.func){
            to.append(ut.tag({css:css.func,value:add+'f()'}));
            if (done) done(to.children().last());
        }    
        return ;
    }
    
    if (type === 'object'){
        var div,value;        
        if (Array.isArray(val)){
            if (p.view.array){
            value = p.fastInfo.enable?t._fast_info(val):'['+val.length+']';

            to.append(ut.tag({css:css.array,value:add+ut.tag({tag:'span',css:css.arrayLen,value:value})}));

            div = to.children().last().find('.'+css.arrayLen);
            $.data(div[0],"data",val);
            $.data(div[0],"param",{level:level,value:value});
                    
            div.on("click",function(e){
                var th      = $(this);
                var data    = $.data(this,'data');    
                var param   = $.data(this,'param');    
                var n       = th.next();
                        
                if (n.length===0){
                    th.parent().append(ut.tag({css:css.arrayEl}));
                    n=th.next();
                    
                    n.hide(0);
                    $.each(data,function(i,d){
                        t._debug(d,n,param.level+1,ut.tag({tag:'span',css:css.arrayIndex,value:i+': '}));
                    });
                    n.append(ut.tag({css:css.arrayLen,value:gap+']'}));
                }
                
                n.slideToggle(p.speed,function(){
                    var n = $(this);
                    var th = n.prev();
                    var param = $.data(th[0],'param');
                    
                    n.prev().html(JX.visible(this)?'[':param.value);
                });
            });
            }
        }else if (p.view.object){
            
            var vcn = (val.constructor)&&(val.constructor.name)&&(val.constructor.name!=='Object')?val.constructor.name:'';

            value = p.fastInfo.enable?vcn+t._fast_info(val):vcn+'{...}';
            to.append(ut.tag({css:css.object,value:add+ut.tag({tag:'spane',css:css.objectName,value:value})}));

            div = to.children().last().find('.'+css.objectName);
            $.data(div[0],'data',val);
            $.data(div[0],"param",{level:level,value:value,vcn:vcn});
                    
                    
            div.on('click',function(e){
                var th       = $(this);
                var data    = $.data(this,'data');    
                var param = $.data(this,'param');    
                var n       = th.next();
                        
                if (n.length===0){
                    th.parent().append(ut.tag({css:css.objectEl}));
                    n=th.next();
                    n.hide(0);
                    
                    $.each(data,function(name,d){
                        t._debug(d,n,param.level+1,ut.tag({tag:'span',css:css.objectProp,value:name+': '}));
                    });
                    n.append(ut.tag({css:css.objectName,value:gap+'}'}));
                }
                
                n.slideToggle(p.speed,function(){
                    var n = $(this);
                    var th = n.prev();
                    //var val = $.data(th[0],'data');
                    var param = $.data(th[0],'param');    
                    //var vcn = (val.constructor)&&(val.constructor.name)&&(val.constructor.name!=='Object')?val.constructor.name:'';

                    //n.prev().html(vcn+'{'+(JX.visible(this)?p.gap:'..}'));
                    n.prev().html(JX.visible(this)?param.vcn+'{':param.value);
                });
            });
        }

        if ((level===0)&&(div)){
            var story = p.speed;
            p.speed = 0;
            if (p.expandAfter) 
                div.trigger("click");
            p.speed = story;
            if (done) done(div.parent());
        }    
        
                
    }
            
            
};

Tjconsole.prototype.clear=function(){
    var t=this,p=t.param;
    p.plugin.html("");
};

Tjconsole.prototype.scroll=function(to){
    var t=this,p=t.param,css=p.css;
    if (to === 'bottom'){
        p.plugin.scrollTop(p.plugin[0].scrollHeight);
        return;
    }    
    if (to === 'top'){
        p.plugin.scrollTop(0);
        return;
    }
    
    if (typeof to === 'object'){
        p.plugin.scrollTop(0);
        p.plugin.scrollTop(JX.pos(to).y);
    }
};    
    
Tjconsole.prototype.out=function(obj,name){
    var t=this,p=t.param,css=p.css;
    if (p.terminal.browserConsole)
        name?console.info(name,obj):console.info(obj);
    
    if (!p.terminal.plugin) return;
    
    
    if (p.clearBefore)
        t.clear();
        
    t._debug(obj,p.plugin,0,(name?name+': ':''),function(o){
        if (p.scrollAfter)
            t.scroll(o);
        if (p.plugin.children().length===1)
            o.css('border','none');
    });
    
};

/**
 * Set parameters to {@link Tjconsole.param}
 * @param {json} o
 * @example xxx.put({name:"value"});
*/ 
Tjconsole.prototype.put = function(o){
    var t=this,p=t.param,l=p.lock;
    
    $.each(o,function(n,v){
        t.attr(n,v);
    });
    
    if ('obj' in o)
        t.out(o.obj,o.name);
    
};    
/**
 * Set or get parameters from {@link Tjconsole.param}
 * @param {string} n name of parameter
 * @param {undefined | mixed} v undefined or mixed data 
 * @example xxx.attr("select",true);
 * @example var v = xxx.attr("select");
*/ 
Tjconsole.prototype.attr = function(n/*v*/){
    /*-----------------------------------*/
    if (arguments.length===0) 
        return;
    var t=this,p=t.param,v,r=(arguments.length===1);
    if (!r) 
        v=arguments[1];

    /*-----------------------------------*/
    if (n==='gap'){
        if (r) 
            return p.gap;
        else    
            p.gap = v;
    }
    /*-----------------------------------*/
    if (n==='css'){
        if (r) 
            return p.css;
        else    
            p.css = $.extend(true,p.css,v);
    }
    /*-----------------------------------*/
    if (n==='view'){
        if (r) 
            return p.view;
        else    
            p.view = $.extend(true,p.view,v);
    }
    /*-----------------------------------*/
    if (n==='terminal'){
        if (r) 
            return p.terminal;
        else    
            p.terminal = $.extend(true,p.terminal,v);
    }
    /*-----------------------------------*/
    if (n==='speed'){
        if (r) 
            return p.speed;
        else    
            p.speed = v;
    }
    /*-----------------------------------*/
    if (n==='clearBefore'){
        if (r) 
            return p.clearBefore;
        else    
            p.clearBefore = v;
    }
    /*-----------------------------------*/
    if (n==='cropString'){
        if (r) 
            return p.cropString;
        else    
            p.cropString = (v>0?v:0);
    }
    /*-----------------------------------*/
    if (n==='scrollAfter'){
        if (r) 
            return p.scrollAfter;
        else    
            p.scrollAfter = (v>0?v:0);
    }
    /*-----------------------------------*/
    if (n==='expandAfter'){
        if (r) 
            return p.expandAfter;
        else    
            p.expandAfter = (v>0?v:0);
    }
    /*-----------------------------------*/
    if (n==='fastInfo'){
        if (r) 
            return p.fastInfo;
        else    
            p.fastInfo = $.extend(true,p.fastInfo,v);
    }
    /*-----------------------------------*/
};

