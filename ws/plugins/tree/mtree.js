/*global ut,$,jQuery,JX,Qs,Ws*/
/** @module wsi/ide/ws/plugins/tree/mtree*/
/**
 * plugin: mtree<br>    
 * file  : mtree.js<br>
 * path  : wsi/ide/ws/plugins/tree/<br>
 * @class mtree
 *
*/
(function( $ ){
/** @lends module:wsi/ide/ws/plugins/tree/mtree~mtree# */
var m={
name:"mtree",
/**
 * Create plugin and handler object {@link module:wsi/ide/ws/plugins/tree/mtree~Tmtree Tmtree}
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.mtree(param)
 * </div>
 * @param {json} param full list of param see in {@link module:wsi/ide/ws/plugins/tree/mtree~Tmtree.param Tmtree.param}
 * @return this
 * @example 
 * $('#div').mtree({
 * ...
 * });
 * @example get attr (1)
 * var t = $('#div').mtree("multiSelect");
 * @example get attr (2)
 * var t = $('#div').mtree("get","multiSelect");
 * @example set attr (1)
 * $('#div').mtree("put",{multiSelect:true});
 * @example set attr (2)
 * $('#div').mtree({multiSelect:true});
 * 
 *   
*/
init:function(param){
    var o = m.obj(this);

    if (o===undefined){
        p=$.extend(true,{},param);
        p.plugin = this;
        m.obj(this,new Tmtree(p));
    }else
        o.put(param);                
        
    return this;     
},
/**
 * Deleted plugin and related objects
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.mtree("destroy")
 * @return this
 * </div>
*/
destroy:function(){
    var o = m.obj(this);
    o.done();
    m.obj(this,undefined);    
    return this;
},
/** 
 * return true if plugin is initializing!!! 
 * @return boolean
*/
assigned:function(){
    return (m.obj(this)!==undefined)
},
/**
 * Story the handler object Tmtree in jquery 
 * @private
 * @return Tmtree
*/
obj:function(t/*set*/){
    if (arguments.length>1){ 
        if (arguments[1]===undefined)
            $.removeData(t[0],m.name);
        else    
            $.data(t[0],m.name,arguments[1]);
    }    
    return $.data(t[0],m.name);
},
/**
 * Sets the parameters of the object Tmtree, see {@link module:wsi/ide/ws/plugins/tree/mtree~Tmtree.param }
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.mtree('put',param)
 * </div>
 * @param {json} param full list of param see in {@link module:wsi/ide/ws/plugins/tree/mtree~Tmtree.param}
 * @return this
 * @example 
 * $('#div').mtree('put',{ 
 *     param1:value1,
 *     param2:value2 
 * });
*/
put:function(param){
    m.obj(this).put(param);
    return this;
},
/**
 * Gets the value of the object's parameters (see {@link module:wsi/ide/ws/plugins/tree/mtree~Tmtree.param})
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.mtree("get",name)
 * </div>
 * @param {string} name name of param in mtree  {@link module:wsi/ide/ws/plugins/tree/mtree~Tmtree.param}
 * @return mixed type
 * @example 
 * var v = $('#div').mtree('get',"deepSelect");
 *   
*/
get:function(name){
    return m.obj(this).attr(name);
},

/**
 * set or return selected node
 * if params = undefined then return all selected node
 * if params = -1  - unselect all node
 * if params = node return select or no node
 * if params (node, bool) when set or unset node select
 * 
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.mtree("select",param)
 * </div>
 * @param {json} param
 * @return mixed type
 * @example 
 * var v = $('#div').mtree('select',node);
 *   
*/
select:function(/*node,bool*/){
    var o = m.obj(this);
    return o.select(arguments[0],arguments[1]);    
},
selected:function(){
    var o = m.obj(this);
    return o.select();    
},
current:function(node){
    var o = m.obj(this);
    if (node===undefined){
        var s = o.select();
        return s.length?s[0]:undefined;
    }else{
        o.select(-1);
        o.select(node,true);
    }
},
/**
 * цикл по узлам
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.each("each",{...})
 * </div>
 * @param {json | function} param 
 * @return plugin
 * @example 
 * $('#div').mtree('each',function(){
 *      
 * });
 * 
 * @example 
 * $('#div').mtree('each',{
 *      step:function(o){
 *          var current = o.current;
 *          var i = o.i;
 *          var all= o.all;  
 *      },
 *      done:function(){
 *      },
 *      by:"nodesArray"
 * });
 *   
*/
each:function(/*func or param*/){
    var o = m.obj(this);
    o.each(arguments[0]);    
    return this;
},
root:function(node){
    var o = m.obj(this);
    return o.root(node);    
},
parent:function(node){
    var o = m.obj(this);
    return o.parent(node);    
},
/**
 * Поиск 
 * @param {json | string | function} param  {
 *      filter:string ("selected","closest") | function - функция фильтра, возвращает true если передаваемый узел удовлетворяет поиску
 *      id: mixed - ищет node  c id
 *      onlyFirst:bool найдет только первый
 *      update: bool - обновит буффер перед поиском
 * } 
 * @return array список найденных узлов      
 * 
 *     
 * } 
*/ 
find:function(param){
    var o = m.obj(this);
    return o.find(param);
},
/**
 * Сворачивание узлов
 * @param {mixed} param 
 * param = undefined - сворачиваются все узлы
 * param ={ node:jq - сворачиваемый узел, animate:bool - включить/отключить анимацию}
 * 
*/ 
collapse:function(param){
    var o = m.obj(this);
    o.collapse(param);    
    return this;
},
/**
 * Разворачивание узлов
 * @param {mixed} param 
 * param = undefined - разворачивается текущий выделенный узел
 * param ={ node:jq - разворачиваемый узел, animate:bool - включить/отключить анимацию,withParent:bool - разворачивается вся ветка до узла}
 * 
*/ 
expand:function(param){
    var o = m.obj(this);
    o.expand(param);    
    return this;
},
toViewPort:function(node){
    var o = m.obj(this);
    o.toViewPort(node);    
    return this;
},
visible:function(node,bool){
    var o = m.obj(this);
    return o.visible(node,bool);    
    
},
/** врзвращает/устанавливает переменную текущего состояния */
state:function(param){
    var o = m.obj(this);
    return o.state(param);    
},
icon:function(node,icon){
    var o = m.obj(this);
    
    
    return o.icon(node,icon);    
},
attach:function(toEvent,fn){
    var o = m.obj(this);
    return o.attach(toEvent,fn);    
},
detach:function(name){
    var o = m.obj(this);
    o.detach(name);    
    return this;
}


};

$.fn.mtree = function(n){
    if(m[n])
        return m[n].apply(this,Array.prototype.slice.call( arguments, 1 ));
    else 
    if ((typeof n==='object')||(!n)){
        return m.init.apply(this,arguments);
    }else{
        if (typeof n==='string')
            return  m.get.apply(this,[n]);
        else    
            $.error( 'Not exists method ' +  n + ' for jQuery.mtree');
    }    
};
})(jQuery);

/**
 * Handler for mtree plugin. Propertys this class story in param,
 * see {@link module:wsi/ide/ws/plugins/tree/mtree~Tmtree.param}
 * @class Tmtree
 * @property {json} param see {@link module:wsi/ide/ws/plugins/tree/mtree~Tmtree:param param}
 * 
*/
function Tmtree(o){
    var t = this;
    t.init(o);
}
/** Constructor of Tmtree. Initial creation and initialization of the object.*/
Tmtree.prototype.init = function(o){
    var t=this;
    /** @namespace module:wsi/ide/ws/plugins/tree/mtree~Tmtree:param */
    t.param = $.extend(true,
    /** @lends module:wsi/ide/ws/plugins/tree/mtree~Tmtree:param#*/
    {
        /** 
         * ref on plugin (jQuery) wrapper, initializing in {@link module:wsi/ide/ws/plugins/tree/mtree~mtree.init}
         * @type {jQuery}
        */
        plugin:null,
        
        /** 
         * unique identificator 
         * @type {string}
        */
        id:ut.id('mtree'),
        /** 
         * Trigger for  check  the  last call. Use if you need to make sure that this is the last function call 
         * @type {jlock}
         * @example 
         * this.param.lock.lock("my");
         * ...
         *   this.param.lock.lock("my");
         *   ...
         *   ...
         *   if (this.param.lock.unlock("my"))
         *           func(); // not be calling        
         * ...
         * ...
         * if (this.param.lock.unlock("my"))
         *        func(); // be calling        
         * 
        */
        lock:new jlock(),
        /**
         * collection of handlers
         * @example  //add handler
         * handler.add({
         *     func:myFunc,
         *     group:'onselect'
         * })
         * @example //call handler
         * handler.do("onselect");
        */
        handler:new jhandler(),
        _nodePref:'node',
        _maxLoop:2000,
        _nodes:undefined,
        _items:undefined,
        
        /** включает возможность выделять строки */
        canSelected:true,
        /** возможность выделения нескольких строк */
        multiSelect:false,
        /** если установлен  в true то клик будет сквозь все элементы расположенные в ячейке */
        deepSelect:true,
        /** только конечные узлы (те которые не имеют child) */
        selectOnlyLastNode:true,
        /** если установлен, то клик по выделенной строке будет снимать выделение, если нет то только по другой строке */
        toggleSelect:false,
        /** сворачивать только при клике на item или unfold или icon */
        collapseOn:"item",
        /** при разворачивании будут сворачиваться узлы НЕ входящие в ветку текущего*/
        collapseEnv:false,
        /** скорость анимации */
        animate:400,
        /**/
        
        onClick:undefined,
        onDblClick:undefined,
        onSelect:undefined,
        onAfterLoad:undefined,
        /** array of css classes uses in Tmtree */
        css:{
            frame:'mtree_frame',
            node:"mtree_node",
            select:"mtree_select",
            
            item:"mtree_item",
            hover:"mtree_item_hover",
            
            
            off:"mtree_off",
            unfold:"mtree_unfold",
            open:"mtree_open",
            close:"mtree_close",
            caption:"mtree_caption",
            
            child:"mtree_child",
            icon:"mtree_icon",
            icons:{
                folder:"mtree_folder",
                file:"mtree_file"
            }
        }
        
    },o);
    
    var p = t.param;

    t.put(p);
    t._event();
};

/** Destructor of Tmtree. Call on plugin is deleted.*/
Tmtree.prototype.done=function(){
    
};
/** получение/сохранение состояния дерева */
Tmtree.prototype.state=function(data){
    var t = this,p=t.param,css=p.css,i;
    
    if (data===undefined){
        var out={open:[],select:[]};
        t.each({
            step:
            function(s){
                if ((!s.info.last)&&(!t.collapsed(s.node)))
                    out.open.push(s.node[0].id);
                if (t.select(s.node))  
                    out.select.push(s.node[0].id);
            }    
        });
        return out;
    }else{
        var _node = function(id){
            return p.plugin.find('#'+id);
        };
        p.lock.lock('animate');
        t.collapse();
        t.select(false);
        for(i=0;i<data.open.length;i++)
            t._collapse(_node(data.open[i]),false);

        for(i=0;i<data.select.length;i++)
            t.select(_node(data.select[i]),true);
        

        p.lock.unlock('animate');
    }
};

/** create node code*/
Tmtree.prototype._create_node=function(o){
    var t=this,p=t.param,css=p.css,c='',i;
    
    var a=ut.extend({
        id:t._get_id(),
        caption:"caption",
        icon:Object.keys(css.icons)[0],
        child:[],
        css:undefined,
    },o);
    
    
    c+=ut.tag('<',{id:a.id,css:css.node+(a.css?' '+a.css:'')});
        
        c+=ut.tag('<',{css:css.item,style:"overflow:hidden"});
            
            c+=ut.tag({css:css.off,style:'float:left;height:100%;'});
            c+=ut.tag({css:css.unfold+(a.child.length>0?" "+css.open:""),style:'float:left;height:100%;'});
            c+=ut.tag({css:css.icon+" "+css.icons[a.icon],style:'float:left;height:100%;'});
            c+=ut.tag({css:css.caption,style:'float:left;height:100%;max-width:0px;white-space:nowrap',value:a.caption});            
            
        c+=ut.tag('>');
        
        c+=ut.tag('<',{css:css.child});
        for(i=0;i<a.child.length;i++)
            c+=t._create_node(a.child[i]);
        c+=ut.tag('>');
        
    c+=ut.tag('>');
    
    o.id = a.id;
    
    return c;
};
/** признак что узел свренут */
Tmtree.prototype.collapsed=function(node){
    var     u = node[0]._info;
    return ( (!u.last)&&(!JX.visible(u.child)) );   
};

Tmtree.prototype.collapse=function(o){
    var t = this,p=t.param,
    a=$.extend(true,{
        node:undefined,
        withChilds:false,
        animate:true,
        env:false,
    },o);
    
    
    if (a.node===undefined){
        t.each({
            start:
            function(){
                p.lock.lock('animate');    
            },
            step:
            function(s){
                t._collapse(s.node,true);
            },
            done:
            function(){
                p.lock.unlock('animate');    
            },
        });
    }else{
        if (!a.env){
            if (!a.animate) p.lock.lock('animate');
            t._collapse(a.node,true);
            if (!a.animate) p.lock.unlock('animate');
        }else{
        
            var closest = t.find({filter:"closest",node:a.node});
            
            var is_closest=function(n){
                for(var j=0;j<closest.length;j++)
                    if (n[0].id==closest[j][0].id) return true;      
                return false;
            };
            
            t.each({
                start:
                function(){
                    if (!a.animate) p.lock.lock('animate');
                },
                step:
                function(s){
                    if ((!t.collapsed(s.node))&&(!t.nodeInside(a.node,s.node))&&(!is_closest(s.node)))
                        t._collapse(s.node,true);
                },
                done:
                function(){
                    if (!a.animate) p.lock.unlock('animate');
                },
            });
            
        }    
    }
};
Tmtree.prototype.expand=function(o){
    var i,t = this,p=t.param;
    
    var a = $.extend(true,{
        node:undefined,
        withParents:true,
        animate:true,
    },o);
    
    if (a.node===undefined){
        a.node = t.select();
        if (a.node.length===0) return;
        a.node = a.node[0];
    }
    
    
    
    if (!a.animate) p.lock.lock('animate');
    if (a.withParents){
        var nodes = t.find({filter:'closest',node:a.node});
        for(i=0;i<nodes.length;i++)
            t._collapse(nodes[nodes.length-i-1],false);
    }else
        t._collapse(a.node,false);
        
    if (!a.animate) p.lock.unlock('animate');
    
};
/** выдает скорость анимации */
Tmtree.prototype._animate=function(){
    var t=this,p=t.param;
    return (p.lock.can('animate')?p.animate:0);
    
};

/** низкоуровневый метод сворачивания, разворачивания узла */
Tmtree.prototype._collapse=function(node,bool){
    var t=this,p=t.param,css=p.css,
        u = node[0]._info,
        current = t.collapsed(node);

    
    if (bool === undefined)
        return current;
    else{
        
        if (bool==='toggle')
            return t._collapse(node,!current);    
        else if (bool){
            if (u.childs.length>0){
                u.item.find('.'+css.unfold).addClass(css.close);
                u.child.slideUp(t._animate());
                return true;
            }    
        }else{
            
            u.item.find('.'+css.unfold).removeClass(css.close);
            //var hidden = u.c
            //if (node.attr('_hide')!==='true'){
                
            //    u.child.slideDown(0);
                
            //}else{
                u.child.slideDown(t._animate());
            return false;
            
        }    

    }    
};


/**
 * сохраняем и если нужно обновляем массивы для быстрого доступа к узлам 
 * @param {bool} update если true то массивы будут пересозданы
 * @return {
 *   nodes: jQuery (все узлы node)
 *   items: jQuery (все заголовки items для соотвествующих nodes)
 *   aNodes: array массив jQuery для каждого узла nodes 
 * }
*/ 
Tmtree.prototype.buffer=function(update){
    var t=this,p=t.param,css=p.css,i;
    
    if ((update===true)||(p._nodes===undefined)){
        p._nodes = p.plugin.find('.'+css.node);
        p._items = p.plugin.find('.'+css.item);
        p._aNodes = [];
        
        for(i=0;i<p._nodes.length;i++){
            var node = $(p._nodes[i]);
            var item = node.children('.'+css.item);
            var child = node.children('.'+css.child);
            var childs = child.children();
            var parent= node.parent();
            var root = (parent[0].id==p.plugin[0].id);
            
            if (!root)
                parent=parent.parent();    
                
            node[0]['_info'] = {
                
                node        :node,
                item        :item,
                child       :child,
                childs      :childs,
                count       :childs.length,
                parent      :parent,
                count       :child.children().length,
                off         :item.children('.'+css.off), /* отступ {jquery}*/
                unfold      :item.children('.'+css.unfold),/* индикатор свернут/развернут {jquery}*/
                icon        :item.children('.'+css.icon), /* иконка {jquery}*/
                text        :item.children('.'+css.caption),/*текст {jquery}*/
                last        :childs.length===0, /* признак что узел последний {bool}*/
                root        :root/*узел корневой {bool}*/

            }            

            p._aNodes.push(node);
        }    
        
    }
    
    return {
        nodes:p._nodes,
        items:p._items,
        aNodes:p._aNodes
    }

};

/**
 * возвращает true если узел down лежит внутри up 
*/ 
Tmtree.prototype.nodeInside=function(up,down){
    var loop=this.param._maxLoop;
    while(loop){
        loop--;
        if (down[0].id==this.param.plugin[0].id) return false;
        if (up[0].id==down[0].id) return true;
            down=down.parent();
        
    }
    return false;
};
/** поиск по дереву */
Tmtree.prototype.find=function(o){
    var t=this,p=t.param,css=p.css,out=[];
    
    if ((typeof(o)=="string")||(typeof(o)=="function")) 
        o = {filter:o};
        
    var a = $.extend(true,{
        filter:'selected',
        id:undefined,
        node:undefined,
        onlyFirst :false,
        update:false,
    },o);    
    
    if (a.id!==undefined) 
        a.onlyFirst = true;
        
    if (a.filter==='closest'){
        
        if (!a.node) {
            a.node=t.select();
            if (a.node.length===0) return out;
            a.node = a.node[0];
        }    
        
        var loop=p._maxLoop,node = a.node;
        while(loop>0){
            loop--;

            if (node[0].id == p.plugin[0].id) 
                break;

            if (node.hasClass(css.node))
                out.push(node);

            node=node.parent();
        };
    
    
    }else
    
    t.each({
        
        step:
        function(s){
            var need = false;
            
            if (a.id!==undefined)
                need = (s.node[0].id==a.id);
            else if (a.filter === 'selected')
                need = (s.info.item.hasClass(css.select));
            else if (typeof(a.filter)==='function')
                need = a.filter(s);

            
            if(need){
                out.push(s.node);
                if (a.onlyFirst)
                    return true;
            }
            
        }
    })

    
    
    return out;
    
    
};
/**
 * цикл по всем узлам (линейно)
*/ 
Tmtree.prototype.each=function(o){
    var t=this,p=t.param,i;        
    if (typeof(o)==='function') o ={step:o};
    
    if (!o.step) return;
    
    var a = $.extend(true,{
        update:false,   /* update buffer before loop */
        step:undefined, /* function call on every step */
        start:undefined, /* function call before start */
        type:'each',    /*each,closest*/
        
        startNode:undefined, /* start node for type closes */
        done:undefined,  /* function call on end loop */
    },o);
    
    var nodes = t.buffer(a.update).aNodes;
    
    if(a.start) a.start({sender:t,nodes:nodes});
    
    if (a.type=='each'){    
        for(i=0;i<nodes.length;i++){
            var info = nodes[i][0]._info;
            if (a.step({sender:t,node:nodes[i],info:info,i:i,nodes:nodes}) === true)
                break;
        }        
    }else if (a.type==='closest'){
        var sel = t.select();
        var cur = a.startNode?a.startNode:(sel.length?sel[0]:undefined);
        i = 0;
        
        while (cur){
            var info = cur[0]._info;
            if (a.step({sender:t,node:cur,info:info,i:i,isRoot:(info.root?true:false)}) === true)
                break
            cur = t.parent(cur);    
            i++;
        }
        
        
    }    

    if(a.done) a.done({sender:t,all:nodes,count:nodes.length});
};
/** скролируем пока не узел не попадет в область видимости 
*/
Tmtree.prototype.toViewPort=function(node){
    var t=this,p=t.param,pos,n;
    
    if (node===undefined){
        node = t.select;
        
        if (node.length===0) 
            return;
            
        node = node[0];
    }
    
    
    pos = JX.abs(p.plugin);
    n = JX.abs(node);
    
    if (!JX.iscrossr(pos,n)){
        
        var y = n.y-pos.y;
        p.plugin.scrollTop(y);
    }
};    
/**
 * изменяет иконку у узла
 * @param {jQuery | array} node - узел или список узлов, для которых преобразование
 * @param {undfined | string} icon имя из css.icons для установки или неопределенное значение ( тогда ф-ция вернет текущее значение иконки)
*/ 
Tmtree.prototype.icon=function(node,icon){
    
    var t=this,p=t.param,css=p.css,icons=css.icons,i;
    if (!node) return;
    
    if ((node instanceof jQuery) && (node.length===1)){
        var div = node[0]._info.icon;
        var current = '';        
        
        for (var name in icons){ 
             if (div.hasClass(icons[name])){
                current = name;
                break;
            }
        };    

        if (icon===undefined)
            return current;    
        else{
        
            if (current!=='')
                div.removeClass(icons[current]);
            div.addClass(icons[icon]);    

        }
    }else{
        
        $.each(node,function(i,o){
            t.icon($(o),icon);
        });
        
    }    
}
/**
 * Включает/выключает видимость узла/узло, или возвращает признак видимости
 * @param {bool|string|jquery|undefined}
 * @param {bool|undefined}
 * @return {bool|undefined}
 * Ex: скрыть узел
 *      visible($("node1"),false);
 * 
 * Ex: показать узел
 *      visible($("node1"),true);
 * 
 * Ex: видимость узла
 *      var visible = visible($("node1"));
 * 
 * Ex: список всех скрытых узлов
 *   var visibles = visible(undefined,false);
 * 
 * Ex: список всех видимых узлов узлов
 *   var visibles = visible(undefined,true);
 * 
 * Ex: показать все скрытые узлы
 *   var visibles = visible(true);
 * 
 */ 
Tmtree.prototype.visible=function(node,bool){
    var t=this,p=t.param,css=p.css,un=p._unVisible,nd,out=[];
    
    if (node===undefined){
        return bool?p._nodes.not("[_hide='true']"):p._nodes.filter("[_hide='true']");
    }
    
    if ((typeof node==='boolean')&&(node)){
        var out=t.visible(undefined,false);
        t.visible(out,true);
        return; 
    }
    
    if (typeof node==='string')
        node = p.plugin.find('#'+node);
            
    if (node.length===0)
        return;
    
    if (bool===undefined)
        return (nd.attr('_hide')==='true')
            
    $.each(node,function(i,n){
         nd= $(n);
         if (bool)
            nd.removeAttr('_hide');
         else
            nd.attr('_hide','true');

         JX.visible(nd,bool); 
    });
        
}

/**
 * без параметров возвращает список выделенных узлов
 * с одним параметром возвращает выделен или нет узел или если параметр bool то снимает выделение
 * с двумя параметрами выделяет/отменяет узел
*/ 
Tmtree.prototype.select=function(node,bool){
    var t=this,p=t.param,css=p.css,u,i,n;
    
    if (node===undefined){
        var out = [];    
        nodes = t.buffer().aNodes;
        for(i=0;i<nodes.length;i++){
            
            u=nodes[i][0]._info;
            if (u.item.hasClass(css.select))
                out.push(nodes[i]);
        }        
        return out;
    }else{
        if (typeof(node)==='boolean'){
            
            t.buffer().items.removeClass(css.select);
            
        }else{
            
            if (typeof node==='string')
                node = p.plugin.find('#'+node);
            
            if (node.length===0)
                return;
            
            if (node.length>1){
                $.each(node,function(i,n){
                    t.select($(n),bool);                    
                });
            }else{
                
                u=node[0]._info;
                if (bool===undefined)
                    return u.item.hasClass(css.select);
                else{
            
                    if (bool){
                        u.item.removeClass(css.hover);
                        u.item.addClass(css.select);    
                    }else{
                 
                        u.item.removeClass(css.select);    
                    }
            
                }
            }
        }    
    }
};

/** events handlers*/
Tmtree.prototype._event=function(){
    var t=this,p=t.param,css=p.css,node,u,prm;
    
    p.plugin.on("click",function(e){
        
        var tar = $(e.target);
        node = t.closest(tar);
        
        if (node){
            u = node[0]._info;
            
            if  (
                (!u.last)&&
                (
                    (p.collapseOn==='item') 
                    || 
                    ((p.collapseOn==='icon')&&(tar.hasClass(css.icon)))
                    ||
                    ((p.collapseOn==='unfold')&&(tar.hasClass(css.unfold)))
                )
            )                
            if (!t._collapse(node,"toggle")&&p.collapseEnv){
                t.collapse({node:node,env:true});
            }    
            
            if (p.canSelected){
                
                var select = t.select(node);
                
                if ((!p.selectOnlyLastNode)||(u.last)){
                    
                    if (p.multiSelect){
                        t.select(node,!select);
                    }else{
                        t.select(false);
                        t.select(node,p.toggleSelect?!select:true);
                        
                    }    
                    
                    if (!select){
                        prm = {sender:t,node:node,info:u};
                        if (p.onSelect) 
                            p.onSelect(prm);
                        t.notify('select',prm);    
                    }    
                }    
            }
            
            prm = {sender:t,node:node,info:u};
            if (p.onClick) p.onClick(prm);
            t.notify('click',prm);
            
            return true;
        }

    }).on('dblclick',function(e){
        
        var tar = $(e.target);
        node = t.closest(tar);
        
        if (node){
            u = node[0]._info;
            prm = {sender:t,node:node,info:u};
            if (p.onDblClick) 
                        p.onDblClick(prm);
                t.notify('dblclick',prm);    
            return true;
        }

    }).on('mousemove',function(e){
        var tar = $(e.target);
        node = t.closest(tar);
        
        if (node){
            t.buffer().items.removeClass(css.hover);
            if (!t.select(node)){
                u=node[0]._info;
                u.item.addClass(css.hover);
            }    
        }
        
    });
};
/**
 * возвращает ближайший узел node или false
*/ 
Tmtree.prototype.closest=function(o){
    var t=this,p=t.param,css=p.css;
    
    var loop = p._maxLoop;
    while(loop>0){
        loop--;
        
        if (o[0].id==p.plugin[0].id) 
            return false;
            
        if (o.hasClass(css.node)) 
            return o;    
        o=o.parent();    
    };
    
    return false;
};

/**
 * возвращает самый верхний корневой узел
*/ 
Tmtree.prototype.root=function(node){
    var t=this,p=t.param;

    if (node===undefined){
        var sel = t.select();
        if (sel.length)
            node = sel[0];
        else
            return null;
    }

    
    info = node[0]._info;
    var loop = p._maxLoop;
    
    while(loop>0){
        loop--;
        if (node[0]._info.root) 
            return node;
        
        node = node[0]._info.parent;
        
        if (!node) break;
    }    
    return null;
};

Tmtree.prototype.parent=function(node){
    var t=this,p=t.param;
    if (node===undefined){
        var sel = t.select();
        if (sel.length)
            node = sel[0];
        else
            return;
    }
    
    if (node[0]._info.root) 
            return;
            
    return node[0]._info.parent;        
     
};    


/** 
 * data to tree
 * o = {data:[
 *      {id:"a1",caption:"a1 node"},
 *      {id:"a2",caption:"a2",child:[
 *          {id:"..."},
 *          {id:"..."}
 *      ]}
 * ]}
*/
Tmtree.prototype.setData=function(o){
    var t=this,p=t.param,css=p.css,c='',i,a;
    
    /*--------------------------------------*/
    if (Array.isArray(o)) 
        o = {data:o};
    /*--------------------------------------*/
    a = $.extend(true,{},o);
    /*--------------------------------------*/
    
    for(i=0;i<a.data.length;i++)
        c+=t._create_node(a.data[i]);    
    
    
    p.plugin.html(c);
    
    /*--------------------------------------*/
    
    t._assign_data(a.data);
    t.buffer(true);
    
    /*--------------------------------------*/
    t._reset_margin_left();
    /*--------------------------------------*/
    
    var prm = {sender:t};
    if (p.onAfterLoad) 
        p.onAfterLoad(prm);
    t.notify('afterLoad',prm);    
    
    /*--------------------------------------*/
};

Tmtree.prototype._assign_data=function(data){
    var t=this,p=t.param,i,node;
    for(i=0;i<data.length;i++){
        node=p.plugin.find('#'+data[i].id);
        if (node.length>0){
            $.data(node[0],'data',data[i]);
        }else
            console.error('assign data id='+data[i]+' to null node');
        if ((data[i].child)&&(data[i].child.length>0)){
            t._assign_data(data[i].child);
            delete data[i].child;
        }    
    }
};

Tmtree.prototype.getData=function(o){
    var t=this,p=t.param,css=p.css,out=[];
    
    var _child=function(child){
        var ret = [];
        for(var i=0;i<child.length;i++){
            var un=child[i]._info;
            var dat = $.extend(false,{},$.data(child[i],'data'));
            dat.child = _child(un.childs);
            ret.push(dat);
        };
        return ret;
    };
    
    var a=$.extend(true,{
        
    },o);
    
    $.each(p.plugin.children(),function(i,n){
        var u=n._info;
        var data=$.extend(false,{},$.data(n,'data'));
        data.child=_child(u.childs);
        out.push(data);
    });
    
    return out;
    
};
/** пересчет отступа каждого узла от левой границы, для отображения ввиде дерева */
Tmtree.prototype._reset_margin_left=function(node,level){
    var t=this,p=t.param,css=p.css;
    level = (level===undefined?-1:level);
    
    if (node===undefined){
        $.each(p.plugin.children(),function(i,n){
            t._reset_margin_left($(n),level+1);
        });
    }else{
        var info = node[0]._info;
        
        if (info.off.length>0){

            JX.pos(info.off,{w:level*24});
            $.each(info.childs,function(i,n){
                t._reset_margin_left($(n),level+1);
            });
        };
        
        //JX.pos(info.off,{w:level*24});
        //JX.pos(info.frame,{h:level*24});
        
        //JX.arrange(info.frame.children(),{direct:"horiz",type:"left",align:"stretch"})
        
    }
};

/** получает уникальный id для узла*/
Tmtree.prototype._get_id=function(){
    var t = this,p=t.param;
    
    if (p._id===undefined) p._id = 0;
    p._id+=1;
    return p._nodePref+p._id;
    
};


Tmtree.prototype.notify=function(event,arg){ 
    var t=this,p=t.param,h=p.handler;
    
    if (typeof arg === "object"){
        arg.sender = this;
        arg.event = event;
    }else
        arg={sender:this,event:event};
        
    h.do({group:event,param:arg});
};

Tmtree.prototype.attach=function(toEvent,o){
    var t = this,h=t.param.handler;

    if (typeof o==='function')
       o={func:o};
  
    if (typeof o==='object'){
       o.group = toEvent;
       return h.add(o).name;
    }
  
    return false;    
};

Tmtree.prototype.detach=function(event){
    var t = this,h=t.param.handler;
    h.remove({name:name,group:'click'});
    h.remove({name:name,group:'dblclick'});
    h.remove({name:name,group:'select'});
    h.remove({name:name,group:'afterLoad'});
};

/**
 * Set parameters to {@link Tmtree.param}
 * @param {json} o
 * @example xxx.put({name:"value"});
*/ 
Tmtree.prototype.put = function(o){
    var t=this,p=t.param,l=p.lock;
    
    l.lock('align');
    if (o.data)
        t.setData(o);            
    
    $.each(o,function(n,v){
        t.attr(n,v);
    });
    
    if (l.unlock('align'))
        t.align();
};    
/**
 * Set or get parameters from {@link Tmtree.param}
 * @param {string} n name of parameter
 * @param {undefined | mixed} v undefined or mixed data 
 * @example xxx.attr("select",true);
 * @example var v = xxx.attr("select");
*/ 
Tmtree.prototype.attr = function(n/*v*/){
    /*-----------------------------------*/
    if (arguments.length===0) 
        return;
    var t=this,p=t.param,v,r=(arguments.length===1),css=p.css;
    if (!r) 
        v=arguments[1];

    /*-----------------------------------*/
    /** example
    
    if (n==='visible'){
        if (r) 
            return JX.visible(p.plugin);
        else    
            JX.visible(p.plugin,(v?true:false));
    }
    */
    /*-----------------------------------*/
    if (n==='data'){
        if (r) 
            return t.getData();
    }
    /*-----------------------------------*/
    if (n==='canSelected'){
        if (r) 
            return p.canSelected;
        else    
            p.canSelected=(v?true:false);
    }
    /*-----------------------------------*/
    if (n==='multiSelect'){
        if (r) 
            return p.multiSelect;
        else    
            p.multiSelect=(v?true:false);
    }
    /*-----------------------------------*/
    if (n==='deepSelect'){
        if (r) 
            return p.deepSelect;
        else    
            p.deepSelect=(v?true:false);
    }
    /*-----------------------------------*/
    if (n==='selectOnlyLastNode'){
        if (r) 
            return p.selectOnlyLastNode;
        else    
            p.selectOnlyLastNode=(v?true:false);
    }
    /*-----------------------------------*/
    if (n==='toggleSelect'){
        if (r) 
            return p.toggleSelect;
        else    
            p.toggleSelect=(v?true:false);
    }
    /*-----------------------------------*/
    if (n==='collapseOn'){
        if (r) 
            return p.collapseOn;
        else    
            p.collapseOn=v;
    }
    /*-----------------------------------*/
    if (n==='collapseEnv'){
        if (r) 
            return p.collapseEnv;
        else    
            p.collapseEnv=(v?true:false);
    }
    /*-----------------------------------*/
    if (n==='animate'){
        if (r) 
            return p.animate;
        else    
            p.animate=v;
    }
    /*-----------------------------------*/
    if (n==='onClick'){
        if (r) 
            return p.onClick;
        else    
            p.onClick=v;
    }
    /*-----------------------------------*/
    if (n==='onDblClick'){
        if (r) 
            return p.onDblClick;
        else    
            p.onDblClick=v;
    }
    /*-----------------------------------*/
    if (n==='onSelect'){
        if (r) 
            return p.onSelect;
        else    
            p.onSelect=v;
    }
    /*-----------------------------------*/
    if (n==='icons'){
        if (r) 
            return p.css.icons;
        else    
            css.icons=$.extend(true,css.icons,n);
    }
    /*-----------------------------------*/

    t.align();
};

/** Call this method to redraw all DOM elements*/ 
Tmtree.prototype.align=function(){
    var t=this,p=t.param,l=p.lock;
    
    if (l.lock('align'))
        t._align();
    l.unlock('align');   
    
};

/** Method calling from Tmtree.align(). Custom call not needed*/ 
Tmtree.prototype._align=function(){
    var t=this,p=t.param;
};


    