/*global JX,$,jQuery,ut,Ws,jlock,dvc*/
(function( $ ){
var m={
name:"grid",
init:function(param){
    var o = m.obj(this);

    if (o===undefined){
        var p=$.extend(true,{},param);
        p.plugin = this;
        m.obj(this,new Tgrid(p));
    }else{
        if ($.isArray(param)){
            //m.data(param);
            o.put({data:param});
        }else    
            o.put(param);                
    }
    return this;     
},
ready:function(){
    return (m.obj(this)!==undefined);
},
destroy:function(){
    var o = m.obj(this);
    o.done();
    m.obj(this,undefined);    
    return this;
},
obj:function(t/*set*/){
    if (arguments.length>1){ 
        if (arguments[1]===undefined)
            $.removeData(t[0],m.name);
        else    
            $.data(t[0],m.name,arguments[1]);
    }    
    return $.data(t[0],m.name);
},
put:function(param){
    m.obj(this).put(param);
    return this;
},
get:function(name){
    return m.obj(this).attr(name);
},
/** очистка данных (структура не меняется)*/
clear:function(){
    var o = m.obj(this);
    return o.clear();
},
/** очистка структуры и данных */

free:function(){
    var o = m.obj(this);
    return o.free();
},
/** поиск яейк
 * @param {jquery} jq - объект который находится в ячейке таблицы
 * @return {
 *      source:jq,
 *      tr:tr,  
 *      td:td,
 *      data:data,
 *      name:name,
 *      id:tr[0].id
 *  };
*/
closest:function(jq){
    var o = m.obj(this);
    return o.closest(jq);
},
/**
 * цикл по строкам таблицы
 * @param {function} func - ф-ция, в которую передается строка на каждом шаге (если ф-ция вернет true,цикл будет прерван)
 * @param {undefined|string|json|function} param  string='all' | 'visible' | 'rows' | 'selected' | 'hide' | 'groups'
 *                             json = {filter:string(то же что и string | 'mark') | function ,mark:{undefined|string}}
 * all - цикл по всем строкам без исключения
 * visible - только видимые
 * rows - все строки постороенные по данным пользователя
 * selected - выделенные
 * hide - скрытые
 * groups - цикл по группам
 * mark - цикл по записям отмеченным именем mark
 * 
 * function - пользовательская ф-ция фильтра, должна возвращать true,если фильтр удовлетворителен
 * 
*/ 
each:function(func,param){
    var o = m.obj(this);
    if (o)
    o.each(func,param);
    return this;
},
/**
 * возвращает массив строк таблицы, отфильтрованных параметром param
 * по умолчанию фильтр установлен в 'all'
 * @param {undefined|string|json|function} - соответствует each (..,param)
 * @return [tr1,tr2,...]
 */ 
map:function(param){
    var o = m.obj(this);
    return o.map(param);
},
/**
 * кол-во записей 
 * count() - кол-во фидимых записей
 * count('all') - кол-всех записей
 * count(..,true) - обновление информации перед
*/
count:function(by,update){
    var o = m.obj(this);
    return o.count(by,update);
},
first:function(){
    var o = m.obj(this);
    return o.first();
},
last:function(){
    var o = m.obj(this);
    return o.last();
},
next:function(tr){
    var o = m.obj(this);
    return o.next(tr);
},
prev:function(tr){
    var o = m.obj(this);
    return o.prev(tr);
},
find:function(p){
    var o=m.obj(this), t=typeof p;
    
    if (t==='number')
        p={idx:p};
    else if(t==='string')
        p={id:p};
    else if (t==='function')
        p={func:p};

    return o.find(p);
    
},
/** возвращает массив выделенных строк, либо признак 
 * выделена ли, переданная как параметр tr, строка
 * или устанваливает возможность выделять или нет строки
 */
selected:function(tr){
    var o = m.obj(this),out;
    
    if (typeof tr==='boolean'){
        o.put({selected:tr});
        return;
    }    
    
    
    if (tr===undefined)
        return o.map('selected');
    
    out = o.select(tr);
    return tr.length===1?out[0]:out;

},
/** разрещает/запрещает подсветку строк или возвращает признак разрешения*/
hovered:function(tr){
    var o = m.obj(this),out;
    
    if (typeof tr==='boolean')
        o.put({hovered:tr});
    
    return o.attr('hovered');
    
},

/** выделяет строку или массив строк*/
select:function(tr){
    var o = m.obj(this);
    o.select(tr,true);
    return this;
},
/** снимает выделение со строки или массива*/
unselect:function(tr){
    var o = m.obj(this);
    o.select(tr,false);
    return this;
},
/**
 * скроллирует таблицу
 * <br>plugin wrapper: <div class="plugin_wrapper">
 * jQuery.grid("scroll",{to:tr});
 * </div>
 * @param {jQuery|string|json} param 
 * @example 
 * $('#div').grid('scroll',tr);
 * @example 
 * $('#div').grid('scroll',"top");
 * @example 
 * $('#div').grid('scroll',{to:"top"});
 * @example 
 * $('#div').grid('scroll',"bottom");
 *   
*/
scroll:function(param){
    var o = m.obj(this);
    o.scroll(param);
    return this;
},
inViewPort:function(tr,strong=false){
    var o = m.obj(this);
    return o.inViewPort(tr,strong);
},

/** блокирует пересчет габаритов таблицы пока не будет вызван end 
 * ф-ция стековая, т.е. что бы на n кол-в раз вызовов begin необходимо столько же раз вызвать end 
*/
begin:function(){
    var o = m.obj(this);
    o.begin();
    return this;
},




/**
 * Установка/получение данных/ преобразование данных
 *
 * Преобразование:
 * @param jquery | [jquery,jquery,...]
 * @return data | [data,data,...]
 * 
 * Установка;
 * @param {array|json} 
 * array = [{field:mean,...},{..},{...}]
 * json = {
 *      data:[{field:mean,...},{..},{...}],
 *      insertTo:last|first|{before:tr}|{after:tr}
 *      group:false | {id:int,caption:string}
 * }
 * 
 * 
 * 
 * Получить данные:
 * @param {undefined|string|json}
 *   string and json см. each
 * 
 * 
 */ 
data:function(param,to){
    var o = m.obj(this),d,out=[];
    
    
    if (to!==undefined){
        
        if (to==='selected'){
            to = o.map('selected');
            if (to.length===0)
                return;
            else
                to = to[0];
        }
        
        if (JX.is_jquery(to)){
            d = $D(to);
            d = $.extend(true,d,param);
            o.put({data:[d]});
        }
        
    }else{
        if ((JX.is_jquery(param))||(($.isArray(param))&&(param.length>0)&&(JX.is_jquery(param[0])))){
        
            if (param.length>0){
                $.each(param,function(i,o){
                    var from=JX.is_jquery(o)?o[0]:o;
                    var tr=$.data( from  ,'data');
                });
                return out;
            }else
                return $.data(param[0],'data');
        
        }
        if ($.isArray(param)) 
            param = {data:param};
    
        if ((typeof param === 'object')&&('data' in param)){
            o.put(param);
            return;
        }
        return o.getData(param);
    }    
},
/** разблокирует пересчет габаритов */
end:function(bool){
    var o = m.obj(this);
    o.end(bool);
    return this;
},
/** возвращает объект group 
 * @param {id|int|tr|group}
 * @return {json} group = {up:jquery,down:jquery,panel:jquery}
*/
group:function(param){
    var o = m.obj(this);
    return o.group(param);
},
/** возвращает массиы всех групп */
groups:function(update){
    var o = m.obj(this);
    return o.groups(update);
},
/** сворачивает/разворачивает группы или возвращает признак крыта или нет группа
 * @param {boolean|id|int|tr|group}  
 * @param {undefined|boolean}
 * @return {undefined|boolean}
 * 
 * Ex. сворачивание одной группы
 * grid('hideGroup','cat1',true); //cat1 - id группы
 * 
 * Ex. свернуть все группы
 * grid('hideGroup',true);
 * 
 * Ex. получить состояние группы в которую входит строка tr
 * var hide = {$}.grid('hideGroup',tr);
 * 
*/
hideGroup:function(param,hide){
    var o = m.obj(this);
    if (typeof param==='boolean'){
        var g = o.groups(),i;
        for(i=0;i<g.length;i++)
            o.hideGroup(g[i],param,false);    
    }else
        return o.hideGroup(param,hide,false);
},
/** 
 * помечает запись/записи
 * или возвращает признак помечена ли запись 
*/
mark:function(name,trs,bool){
    var o = m.obj(this);
    
    return o.mark(name,trs,bool);
},
/** скрывает записи, */
hide:function(trs,bool){
    var o = m.obj(this);
    
    return o.hide(trs,bool);
},

/** удаляет запись */
delete:function(trs){
    var o = m.obj(this);
    o.delete(trs);
    return this;
},
/** изменяет настройки конкретного поля
 * или возвращает настройки указаннаго
 * Ex.
 * grid("field",{name:'ID',visible:true})
 * grid("field",'ID',{visible:true})
 * 
 * Ex.
 * var f = grid("field","ID");
 * 
*/
field:function(name,param){
    var o = m.obj(this),a=false;
    if (typeof name==="object"){
        a=name;
    }else if (typeof name==="string"){
        if (typeof param==='object'){
            a=$.extend(true,{},param);
            a.name = name;
        }    
    }
    
    if (a) 
        o.put({fields:[a]});
    else
    if (typeof name==="string")
        return o.getField(name); 
    
},
/* подмвечивает строку в стиле hover */
hover:function(tr){
    var o = m.obj(this);
    
    if (tr===undefined)
        return o.hover(undefined);
    else    
        o.hover(tr);
        
    return this;
},

/** перемещает колонку 
 * Ex:
 * grid("moveColumn",{
 *      field:'ID',
 *      to:2,
 *})
 * Ex:
 * grid("moveColumn",{
 *      field:'ID',
 *      place:'last'
 *})
 * Ex:
 * grid("moveColumn",{
 *      field:'ID',
 *      to:'NAME',
 *      place:'before'
 *})
* 
*/
moveColumn:function(a){
    var o = m.obj(this);
    o.moveColumn(a);
    return this;
},
/** пересчет ориентации и расположения */
align:function(){
    var o = m.obj(this);
    o.align();
    return this;
},
refresh:function(){
    var o = m.obj(this);
    o._updateFastAccess();
    o.doChange();
    return this;
},
/** пересчет внутреннего буффера */
update:function(){
    var o = m.obj(this);
    o._updateFastAccess();
    return this;
},
attach:function(toEvent,func,name){
    var o = m.obj(this);
    return o.attach({event:toEvent,func:func,name:name});
},
detach:function(){
    var o = m.obj(this),a=arguments;
    if (a.length>1)
        o.detach(a[0],a[1]);
    else
        o.detach(a[0]);
}
};

$.fn.grid = function(n){
    if(m[n])
        return m[n].apply(this,Array.prototype.slice.call( arguments, 1 ));
    else 
    if ((typeof n==='object')||(!n)){
        return m.init.apply(this,arguments);
    }else{
        
        if (typeof n==='string'){
            if (arguments.length>1){
                var nn={};
                nn[arguments[0]]=arguments[1];
                return  m.put.apply(this,[nn]);
            }else
                return  m.get.apply(this,[n]);
                 
        }    
        /*
        if (typeof n==='string')
            return  m.get.apply(this,[n]);
        else    
            $.error( 'Not exists method ' +  n + ' for jQuery.grid');
        */    
    }    
};
})(jQuery);

function Tgrid(o){
    var t = this;
    t.init(o);
}

Tgrid.prototype.init = function(o){
    var t=this;
    t.param = $.extend(true,
    {
        plugin:null,
        fields:[],
        id:{
            header:ut.id('hdr'),
            frameCells:ut.id('fcls'),
            cells:ut.id('cls')
        },
        jq:{
            header      :null, 
            headerTd    :null,
            frameCells  :null,
            cells       :null,
            all         :null,
            trs         :null,
        },
        
        lock:new jlock(),
        handler:new jhandler(),
        indexField:"ID",
        _maxid:0,
        _alignFunc:ut.id('afg'),
        //trRef:'_jq73sddx',
        /** 
         * если fixedWidth>0 то ширина таблицыбудет независима от ширина plugin,но не меньше
         * при этом таблица будет располагаться по вертикали в соответствии с правилом fixedWidthAlign
        */
        fixedWidth:false,
        /** правило для fixedWidth>0*/
        fixedWidthAlign:"center",
        /** внутренний отступ от родительского объекта */
        margin:0,
        /** если true то высота таблицы будет фикированной,false- plugin будет растягиваться чтобы вместить все*/ 
        fixedHeight:true,
        /** добавляется к высоте при fixedHeight = false*/
        fixedOff:5,
        /** отобрать/нет заголовок */
        visibleHeader:true,
        flyingHeader:false,
        /*flyParent:o.plugin.parent(),*/
        marginHeaderFrame:{left:0,right:0,top:0,bottom:0},
        /** отложенная перерисовка */
        alignDelay:0,
        /**при клике на элементае в ячейке, будет выбрана соотв строка */   
        deepSelect:true,
        /** рзрешает/запрещает подсветку */
        hovered:true,
        /** разрешать/нет выделять строки*/
        selected:true,
        /** повторный клик отменяет выбор (multiSelect=false)
        * при toggleSelect=false всегда отображается выделенная строка 
        */
        toggleSelect:false,
        /** можно выбрать несколько строк*/
        multiSelect:false,
        /**анимация при раскрытии группы*/
        animateGroup:100,
        /** указывает, что заголовок группы, будет задерживаться в области видимости, пока группа видна */
        flyingGroup:true,
        /** можнооткрыть только одну группу */
        toggleGroup:false,
        /** смещение заголовка */
        marginGroup:{left:0,right:0,top:0,bottom:0},
        
        /** аниация скроллинга */
        animateScroll:500,
        /** ширина полоски скролла */
        scrollWidth:18,
        
        /** привязать перерисовку к глобальному Ws.align*/
        alignAutoEnable:true,
        events:['create','change','hover','hoverGroup','click','dblClick','select','setValue','delete','clickHeader'],
        /** вызыватся при создании кода элемента */
        onCreate:undefined,
        /** вызываетя при изменении данных */
        onChange:undefined,
        /** проход курором через cтроки */
        onHover:undefined,
        /** смена текущей видимой группы */
        onHoverGroup:undefined,
        /** клик на строке*/
        onClick:undefined,
        onClickHeader:undefined,
        /** двойнойклик на строке*/
        onDblClick:undefined,
        /** выбор строки */
        onSelect:undefined,
        /** при перезаписывании данных,данные сначала будут поступать сюда */
        onSetValue:undefined,
        /** удаление строки*/
        onDelete:undefined,
        cssScroll:'ws_scrollbar',
        cssPref:'',
        css:{
            header:'grid_header',
            cells_frame:'grid_cells_frame',
            cells:'grid_cells',
            header_tr:'grid_header_tr',
            header_td:'grid_header_td',
            tr:'grid_tr',
            td:'grid_td',
            hover:'grid_tr_hover',
            select:'grid_tr_select',
            group:'grid_group',
            close:'grid_close',
            group_up:'grid_group_up',
            group_down:'grid_group_down',
            headerFrame_left:'grid_header_frame_left',
            headerFrame_right:'grid_header_frame_right',
            headerFrame_top:'grid_header_frame_top',
            headerFrame_bottom:'grid_header_frame_bottom'

        }
        
    },o);
    
    if (t.param.cssPref!=='')
        ut.addPref(t.param.css,t.param.cssPref);

    t._createFrames();
    t._event();
        
    //t.param.flyParent=t.param.plugin.parent();
    //if (!('flyParent' in t.param))
    //    t.param.flyParent = t.param.plugin.parent()    
    t.put(t.param);
    
    
    Ws.align({
        id:t.param._alignFunc,
        recall:false,
        func: function(){
            t._alignAuto();
        }
    });
    
    if (!('flyParent' in t.param))
        t.param.flyParent = t.param.plugin.parent()    
        
    t.param.flyParent.on('scroll',{grid:t},t._alignFlyingHeader);
    
};
  

Tgrid.prototype.done=function(){
    var t=this,p=t.param;
    this.free();
    this.param.alignAutoEnable=false;
    this.param.plugin.html("");
    this.param.flyParent.off('scroll',t._alignFlyingHeader);
    Ws.removeAlign(p._alignFunc);
    
};



/**
 * @function
 * @param {json} o
*/
Tgrid.prototype.free=function(){
    var t=this,p=t.param;

    p.jq.header.html('');
    
    p.jq.headerTd=null;
    
    p.fields=[];
    
    t.clear();
    
};

/**
 * @function
 * @param {json} o
*/
Tgrid.prototype.clear=function(){
    var t=this,p=t.param;
    p.jq.cells.html("");
    p.jq.frameCells.children('div').remove();
    p.groupInView = undefined;
    p._prevHover = undefined;
    
    t.all(true);
    t.trs(true);
    t.groups(true);
    t.align();
};


/**
 * Добавляет данные в таблицу
 * ВНИМАНИЕ!! Если в добавляемом массиве, будут строки с одинаковыми id, то добавлена будет только первая строка
 * все последующие строки с совпадающим id, будут игнорироваться
 * 
 * 
 * @function
 * @param {json} o
*/
Tgrid.prototype.setData=function(o){
    
    var t=this,p=t.param,item,css=p.css,trs,tr,all = t.all().length,
    c='',ids=[],idi=[],data,i,group=false,codePanelGroup=false;
    
    if ($.isArray(o)) o = {data:o};
    
    var a = $.extend(true,{
        data:[],
        forced:false,
        insertTo:"last", /*first,last, {before:tr} {after:tr} */ 
        group:false /* {id:string,caption:string}*/
    },o);
    
    
    if (a.data.length===0) return;
    
    t._createDefaultFieldsStruct(a.data);
    
    /*---------------------------------------*/
    if (a.group!==false){
        group = t.group(a.group);
        if (!group){
            c+=t._codeGroup(a.group,'<');
            codePanelGroup = t._codePanelGroup(a.group);
        }    
    }    
    /*---------------------------------------*/
    
    for(i=0;i<a.data.length;i++){
        item = a.data[i];
        
        if ((a.forced)||(!(p.indexField in item)))
            tr=t._codeTr({item:item,data:a.data});
        else{
            tr = t.find({id:item[p.indexField]});
            if (tr.length>0)
                t._dataToTr(item,tr);
            else{
                
                tr=t._codeTr({item:item,data:a.data});
                if (ids.indexOf(tr.id+'')>=0){
                    console.info('duplicate id:'+tr.id+' i:'+i,a.data[i]);
                    tr.code = false;
                }
            }
        }
        if (tr.code){
            c+=tr.code;
            ids.push(tr.id+'');
            idi.push(i);
        }    
    }    
    
    
    /*---------------------------------------*/
    if ((a.group!==false)&&(!group)){
        c+=t._codeGroup(a.group,'>');
    }    
    /*---------------------------------------*/

    //p.jq.cells.append(c);
    // insert to -----------------------------
    t.all();
    trs = p.jq.all;
    tr = $(c);
    if ((trs.length===0)||(a.insertTo==='last'))
        p.jq.cells.append(tr);
    else{
        if (a.insertTo==="first")
            tr.insertBefore(trs.first());
        else if (a.insertTo.before!==undefined)
            tr.insertBefore(o.a.insertTo.before);
        else if (a.insertTo.after!==undefined)
            tr.insertAfter(a.insertTo.after);
    }    
    /*---------------------------------------*/

    if (codePanelGroup){
        // !!!!!!!!!!!!!!
        // необходимо втавлять панель группы в соотв с порядком групп в таблице 
        // найти ближайшую верхнюю группу и вставить поле нее (либо в вверх)
        
        var prev = tr.last().next();
        group = false;
        while (prev.length>0){
            if (!t.not_group(prev)){
                group=t.group(prev);
                break;
            }
            prev=prev.next();
        }
        tr = $(codePanelGroup);
        if (group)
            tr.insertBefore(group.panel);
        else
            p.jq.frameCells.append(tr);
    }
    
    /*---------------------------------------*/
    /* related data with tr */
    for(i=0;i<ids.length;i++){
        var dat = a.data[idi[i]];
        //dat[p.trRef] = p.jq.cells.find('#'+ids[i].id);
        //$.data(dat[p.trRef][0],'data',dat);
        tr = p.jq.cells.find('#'+ids[i]);
        $.data(tr[0],'data',dat);
    }
    
    
    /*---------------------------------------*/
    t.all(true);
    /* если данных нет, то кол-во видимых равно кол-ву данных , т.к. данные сначала видимы */
    if (all===0){
        p.jq.trs = p.jq.all.not('.'+css.group_up).not('.'+css.group_down);
        p._trs=[];
        $.each(p._all,function(i,obj){
            if (t.not_group(obj))
                p._trs.push(obj);
        });
        
    }else
        t.trs(true);
    
    
    t.groups(true);
    /*---------------------------------------*/
    t._setColsWidthStyle();

    t.align();
    
    t.doChange();
    
};


/**
 * @function
 * @param {json} o
*/
Tgrid.prototype.begin=function(){
    var t=this,p=t.param,l=p.lock;
    l.lock('align');
    l.lock('style');
    l.lock('change');
};

/**
 * @function
 * @param {json} o
*/
Tgrid.prototype.end=function(o){
    var t=this,p=t.param,l=p.lock;
    if (o===undefined) o = true;
    
    if (l.unlock('style')&&(o))
        t._setColsWidthStyle();

        
    if (l.unlock('align')&&(o))
        t.align();

    if (l.unlock('change')&&(o))
        t.doChange();
    
};



/**
 * @function
 * @param {json} o
*/
Tgrid.prototype.getData=function(o){
    var t=this,out=[];
    if ((o==='undefined')||((typeof o==='object') && (o.filter==='undefined')))
        o={filter:"rows"};
        
    t.each(function(m){
        out.push($.data(m.tr[0],'data'));    
    },o);

    return out;
    
};


/**
 * @function
 * @param {json} o
*/
Tgrid.prototype._codeGroup=function(o,side){
    var t=this,p=t.param,css=p.css,i,f,c='',usr;

    if (side==='<')
        usr = {event:'group_up',  id:o.id,style:'',css:css.group_up}; 
    else    
        usr = {event:'group_down',id:o.id+'dn',style:'',css:css.group_down}; 
    t.doCreate(usr);
    
    c+=ut.tag('<',{tag:'tr',id:o.id,style:usr.style,css:usr.css});
    
    for(i=0;i<p.fields.length;i++){
        f=p.fields[i];
        c+=ut.tag({tag:'td',id:f.name});
    }
    
    c+=ut.tag('>',{tag:'tr'});
    
    return c;
};

/**
 * @function
 * @param {json} o
*/
Tgrid.prototype._codeTr=function(o){
    var t=this,p=t.param,css=p.css,i,f,c='',v,id,usr;
    
    if (p.indexField in o.item)
        id = o.item[p.indexField];
    else{    
        p._maxid++;
        id = p._maxid;
    }    
    
    usr = {event:'tr',item:o.item,data:o.data,id:id,style:'',css:css.tr,attr:[]}; 
    t.doCreate(usr);
    
    c+=ut.tag('<',{tag:'tr',id:id,style:usr.style,css:usr.css,attr:usr.attr});
    
    for(i=0;i<p.fields.length;i++){
        f=p.fields[i];
        v='';
        if (f.name in o.item)
            v = o.item[f.name];
        
        usr = {event:'td',item:o.item,data:o.data,id:id,style:'',css:css.td,name:f.name,value:v}; 
        t.doCreate(usr);
            
        c+=ut.tag({tag:'td',id:f.name,value:usr.value,css:usr.css,style:usr.style});
    }
    
    c+=ut.tag('>',{tag:'tr'});
    
    return {code:c,id:id};
};

/**
 * @function
 * @param {json} o
*/
Tgrid.prototype._codePanelGroup=function(o){
    var t=this,p=t.param,css=p.css,usr;
    

    usr = {event:'panel_group',id:o.id,value:(o.caption?o.caption:'group '+o.id),style:'',css:css.group}; 
    t.doCreate(usr);
    
    return ut.tag({id:o.id,style:'position:absolute;'+usr.style,css:usr.css,value:usr.value});

};

/**
 * @function
 * @param {json} o
*/
Tgrid.prototype._dataToTr=function(data,tr){
    var t=this,p=t.param,td,
        current=$.data(tr[0],'data');
    for(var key in data){
        
        var old = current[key];
        current[key]=data[key];
        
        td = tr.children('#'+key);
        if (td.length>0){
            if ((!p.onSetValue)||(!p.onSetValue({sender:t,tr:tr,td:td,name:key,data:current,value:data[key],oldValue:old})))
                td.html(data[key]);

        }
    }    
};

/** set fields struct from data if user not defined fields*/

Tgrid.prototype._createDefaultFieldsStruct=function(data){
    var t = this,p=t.param,i,o={},out = [];
    
    if (p.fields.length>0) return;
        
    for(i=0;i<data.length;i++){
        $.each(data[i],function(n){
            if (o[n]===undefined){
                out.push({name:n});
                o[n]=true;
            }    
        });
    }

    t.put({fields:out});

};    


/** формируем основные фреймы плагина */
Tgrid.prototype._createFrames=function(){
    var t=this,p=t.param,css=p.css,c='',id=p.id;
    
    c+= ut.tag('<',{id:id.frameCells,css:css.cells_frame+' '+p.cssScroll,style:"position:absolute;overflow-x:hidden;overflow-y:auto"});
    c+= ut.tag({id:id.cells,tag:'table',css:css.cells,style:"position:absolute;border-collapse: collapse;table-layout: fixed;",attr:{border:0,cellspacing:0,cellpadding:0}});
    c+= ut.tag('>');
    c+= ut.tag({id:id.header,tag:'table',css:css.header,
        style:"position:absolute;border-collapse: collapse;table-layout: fixed;overflow:hidden",attr:{border:0,cellspacing:0,cellpadding:0}});
    
    p.plugin.append(c);
    
    p.jq.header = p.plugin.find('#'+id.header);
    p.jq.frameCells = p.plugin.find('#'+id.frameCells);
    p.jq.cells = p.plugin.find('#'+id.cells);
    
    
    id.headerFrame={left:ut.id('hFl'),right:ut.id('hFr'),top:ut.id('hFt'),bottom:ut.id('hFb')};
    
    c=ut.tag({id:id.headerFrame.left,style:'position:absolute',css:css.headerFrame_left});
    c+=ut.tag({id:id.headerFrame.right,style:'position:absolute',css:css.headerFrame_right});
    c+=ut.tag({id:id.headerFrame.top,style:'position:absolute',css:css.headerFrame_top});
    c+=ut.tag({id:id.headerFrame.bottom,style:'position:absolute',css:css.headerFrame_bottom});
    p.plugin.append(c);

    p.jq.headerFrame = {
        left:p.plugin.find('#'+id.headerFrame.left),
        right:p.plugin.find('#'+id.headerFrame.right),
        top:p.plugin.find('#'+id.headerFrame.top),
        bottom:p.plugin.find('#'+id.headerFrame.bottom)
    };
    
};

/** сосздаем кол-во колонок указанное в fields */
Tgrid.prototype._createHeader=function(){
    var t=this,p=t.param,css=p.css,i,c='',f=p.fields;


    
    c+= ut.tag('<',{tag:'tr',css:css.header_tr});
    for(i=0;i<f.length;i++){
        usr = {event:'header_td',id:f[i].name,value:f[i].caption,style:"overflow:hidden;",css:css.header_td}; 
        t.doCreate(usr);
        c+= ut.tag({tag:'td',id:usr.id,style:usr.style,value:usr.value,css:usr.css,attr:{title:f[i].name}});
        
        //c+= ut.tag({tag:'td',id:f[i].name,style:"overflow:hidden",value:f[i].caption,css:css.header_td});
    }    
    c+= ut.tag('>',{tag:'tr'});
    
    p.jq.header.html(c);
    p.jq.headerTd = p.jq.header.find('td');
    
    
};

/**
 * установка ширины столбцов
 * @function
 * @param {json} o
*/
Tgrid.prototype._setColsWidthStyle=function(){
    var t=this,p=t.param,i,f,first=true,l=p.lock,w,last;
    
    if (!l.can('style')) return;
    
    var h = p.jq.headerTd;
    var c = p.jq.cells.find('td');
    
     
    if ((h)&&(h.length)) h.css('border-left-width','1px');
    if ((c)&&(c.length)) c.css('border-left-width','1px');
    

    
    for(i=0;i<p.fields.length;i++){
        f=p.fields[i];

        h = p.jq.header.find('td:nth-child('+(i+1)+')');
        c = p.jq.cells.find('td:nth-child('+(i+1)+')');
        
        if ((f.visible===undefined)||(f.visible===true)){
            
            //if (h.css("display")=="block"){
                h.show();
                c.show();
            //}

            if(first){
                h.css('border-left-width','0px');
                c.css('border-left-width','0px');
            }
            first=false;
            
            w = '';
            if (f.width)
                w = f.width+(typeof(f.width)==='number'?'px':'');
            c.css('width',w);
            
            last={h:h,c:c};
        }else{
                h.hide();
                c.hide();
        }
        
    }
    

};

/**
 * @function
 * @param {json} o
*/
Tgrid.prototype.moveColumn=function(o){
    var t=this,p=t.param,
    a = $.extend(true,{
        field:  -1, /* int|string|field */
        to:     -1, /*  int|string|field  */
        place:"before" /** last,first,after,before*/
    },o);
    
    if (p.fields.length === 0) return;
    
    if (a.place === 'last'){
        a.place = 'after';
        a.to = p.fields.length-1;
    }else
    if (a.place === 'first'){
        a.place = 'before';
        a.to = 0;
    }
    
    if ((a.field===-1)||(a.to===-1))
        return;
    
    
    
    var idx = t.getIndexField(a.field);
    a.to    = t.getIndexField(a.to);
    if (idx == a.to) return;
    
    
    var trs = p.jq.all;
    
    
    if (trs)
    trs.each(function(){
        var cols=$(this).children('th,td');
        if (a.place==='before')
            cols.eq(idx).detach().insertBefore(cols.eq(a.to));
        else    
            cols.eq(idx).detach().insertAfter(cols.eq(a.to));

    });
    
    trs = p.jq.header.children();
    trs.each(function(){
        var cols=$(this).children('th,td');
        if (a.place==='before')
            cols.eq(idx).detach().insertBefore(cols.eq(a.to));
        else    
            cols.eq(idx).detach().insertAfter(cols.eq(a.to));
    });
    
    
    if (a.place === 'before'){
        var to = idx<a.to?a.to-1:a.to;
        var f = p.fields;
        var el = [f[idx]];
        f.splice(idx,1);
        p.fields = f.slice(0,to).concat(el,f.slice(to));
    }else{
        var to = idx<a.to?a.to-1:a.to;
        var f = p.fields;
        var el = [f[idx]];
        f.splice(idx,1);
        p.fields = f.slice(0,to+1).concat(el,f.slice(to+1));
        
    }    

    
    p.jq.headerTd = p.jq.header.find('td');
    // удаляю сохраненное в JX значение бордера, что бы правильно пересчитались размеры
    JX.reset(p.jq.headerTd);
    
    t._setColsWidthStyle();
    t.align();
    
};

/** сворачивание/разворачивание группы
 * @param {string|int|jquery|object} tr - указатель на группу
 * @param {undefined|bool} bool - если не указан, то ф-ция вернет состояние группы
 * @param {boolean} animate - включить/выключить анимацию при раскрытии
 * @function
 * @param {json} o
*/
Tgrid.prototype.hideGroup=function(tr,bool,animate){
    var t=this,p=t.param,css=p.css,
    trs,a,i,
    ag=p.animateGroup,
    group=false;
    
    if (animate===false) ag=0;
    
    group=t.group(tr);
    
    if (!group) return false;
   
    if (bool===undefined)
        return (!JX.visible(group.down));
    else{
        trs=t.groupTrs(group);
        
        t._attr({tr:trs,name:"_group_",value:"hide",remove:!bool});
            
        for(i=0;i<trs.length;i++){
            if (bool){
                if ((ag>0)&&(i<4))
                    trs[i].hide({
                        duration:ag,
                        step:function(){t._alignGroups();},
                        complete:function(){t._alignGroups();},
                    });
                else    
                    trs[i].hide();
            }else{
                // показываем, если только элемент не был скрыт 
                a=(t._attr({tr:trs[i],name:'_hide_'})!=='true');
                if (a){ 
                    if ((ag>0)&&(i<4))
                    trs[i].show({
                        duration:ag,
                        step:function(){t._alignGroups();},
                        complete:function(){t._alignGroups();},
                    });
                    else    
                        trs[i].show();
                }    
            }
        }
            
        if (bool){
            group.down.hide();
            group.panel.addClass(css.close);
        }else{
            group.down.show();
            group.panel.removeClass(css.close);
        }
        
        if(ag>0)    
            setTimeout(function(){
                t.trs(true);
                t.align();
            },ag*4);    
        else{
            t.trs(true);
            t.align();
        };
            
    }
};

/**
 * Возвращает массив всех строк входящих в группу tr
 * @param {group|jquery|string|int} o
 * @return []
*/
Tgrid.prototype.groupTrs=function(tr){
    var t=this,group,n,out=[];
    
    group=t.group(tr);
    if (!group) return [];
    n=group.up.next();
    
    while(t.not_group(n)){
        
        out.push(n);
        n=n.next();

    }
    return out;
    
};

/**
 * Ищет и возвращает группу по входному параметру o
 * @param {group|jquery|id|int} o - 
 * 
 * group- бъект группы 
 * jquery - любая строка внутри группы
 * id - идентификатор группы
 * int - порядковы номер группы
 * 
 * @return false|group = {up:jquery,down:jquery,panel:jquery}
 * 
*/
Tgrid.prototype.group=function(o){
    var t=this,p=t.param,css=p.css;
    if ((p.jq.groups===undefined)||(p.jq.groups.length===0)||(o===undefined))
        return false;
    if (typeof o==='object'){
        if (JX.is_jquery(o)) 
            o={tr:o};
        else if ('up' in o)
            return o;
            
    }else if (typeof o==='string')
        o={id:o};
    else if (typeof o==='number')
        o={idx:o};
        
    var a = $.extend(true,{
        id:undefined,  
        idx:undefined,
        tr:undefined,
    },o),
    gs=t.groups(),g=p.jq.groups,tr=false;
    
    if (a.id!==undefined)
        tr = g.filter('#'+a.id);
    else if (a.idx!==undefined)
        tr = gs[a.idx];
    else if (a.tr!==undefined){
        if (!t.not_group(a.tr))
            tr = a.tr;
        else{
            var prev=a.tr.prev();
            while (prev.length>0){
                
                if (prev.hasClass(css.group_up)){
                    tr=prev;
                    break;
                }
                if (prev.hasClass(css.group_down))
                    break;
                    
                prev=prev.prev();
            }
        }    
        
    }
        
    
    if ((tr)&&(tr.length>0))
        return $.data(tr[0],'data');
    return false;    
};


Tgrid.prototype.not_group=function(tr){
    var t=this,p=t.param,css=p.css;

    return ((!tr.hasClass(css.group_up))&&(!tr.hasClass(css.group_down)));
};


/**
 * @function
 * @param {json} func
 * @param {json} o
*/
Tgrid.prototype.each=function(func,o){
    var t=this,p=t.param,css=p.css,trs=false,i,tr,need,d;
    
    if (typeof o==='string') o={filter:o};
    else if (typeof o==='function') o={filter:func};
    
    var a = $.extend(true,{
        filter:"all", /* all|rows|selected|hide|groups|mark*/
        mark:'',      /* mark namm for filter="mark"*/
        update:false,
    },o);
    
    
    if ((a.filter==='all')||(typeof a.filter==='function'))
        trs = t.all(a.update);
    else if (a.filter==='visible')    
        trs = t.trs(a.update);
    
    
    if (trs!==false){
        for(i=0;i<trs.length;i++){
            d = {sender:t,i:i,tr:trs[i],trs:trs};
            if ((typeof a.filter!=='function')||(a.filter(d)===true)){
                if (func(d)===true)
                    break;
            }    
        }
    }else if (a.filter==='groups'){
        trs = t.groups(a.update);
        for(i=0;i<trs.length;i++){
            
            if ((func({sender:t,i:i,group:$.data(trs[i][0],'data')}))===true)
                break;
        }    
        
    }else{    
        trs = t.all(a.update);
        for(i=0;i<trs.length;i++){
            tr=trs[i];
            need=false;
            if (t.not_group(tr)){
                if (!need) need = (a.filter==='rows');    
                if (!need) need = (a.filter==='selected')&&(tr.hasClass(css.select));    
                if (!need) need = (a.filter==='hide')&&(t.hide(tr)[0]);
                if (!need) need = (a.filter==='mark')&&(t.mark(a.mark,tr)[0]);    
            }
            if ((need)&&(func({sender:t,i:i,tr:tr}))===true)
                break;
        }
        
    }
};

/**
 * @function
 * @param {json} o
*/
Tgrid.prototype.map=function(o){
    var t=this,out=[];

    if (typeof o==='string') 
        o={filter:o};
    else if (typeof o==='function') 
        o={filter:o};
    
    var a = $.extend(true,{
        filter:"all",
        update:false,
    },o);
    
    if (a.filter==='all') return t.all(a.update);
    if (a.filter==='visible') return t.trs(a.update);
    
    t.each(function(m){
        out.push(m.tr);
    },a);
    
    return out;
    
};
/** возвращает первую видимую строку */
Tgrid.prototype.first=function(){
    var t=this,trs=t.trs();
    if (trs.length>0)
        return trs[0];
};
/** возвращает последнюю видимую строку */
Tgrid.prototype.last=function(){
    var t=this,trs=t.trs();
    if (trs.length>0)
        return trs[trs.length-1];
};

/** возвращает следующую видимую строку */
Tgrid.prototype.next=function(tr){
    var t=this,p=t.param,out,trs = t.trs();

    if ((tr === undefined)||(tr.length===0))
        return trs.length?trs[0]:undefined;
    
    out = tr.next()
    while( (out.length>0)&&((!t.not_group(tr))||(!JX.visible(out))) )
        out=out.next();
        
    return out;
    
    
};
/** возвращает предыдущую видимую строку */
Tgrid.prototype.prev=function(tr){
    var t=this,p=t.param,trs = t.trs();
    
    if ((tr === undefined)||(tr.length===0))
        return trs.length?trs[0]:undefined;
    
    out = tr.prev();
    while( (out.length>0)&&((!t.not_group(tr))||(!JX.visible(out))) )
        out=out.prev();
        
    return out;
};

/**
 * @function
 * @param {json} o
*/
Tgrid.prototype.find=function(o){
    var t=this,p=t.param,tr=[],
    
    a = $.extend(true,{
        id:undefined,
        idx:undefined,
        func:undefined,
        update:false
    },o);
    
    if (a.id!==undefined)
        return p.jq.cells.children('#'+a.id);
    if (a.idx!==undefined)
        return t.all(a.update)[a.idx];
    if (typeof a.func==='function'){
        t.each(function(s){
            if (a.func(s)===true) {
                tr=s.tr;
                return true;
            }
        },{update:a.update});
    }
    
    return tr;
};


/**
 * Получить клетку, строку и данные по объекту from входящему в клетку
 * @function
 * @param {json} o
*/
Tgrid.prototype.closest=function(source){
    var t=this,p=t.param,
    jq = JX.is_jquery(source)?source:$(source),
    td = jq.closest("td"),
    tr = td.parent(),
    data=$.data(tr[0],'data'),
    name = td.length?td[0].id:undefined;
    
    return {source:jq,tr:tr,td:td,data:data,name:name,id:tr[0].id};
    
};

/**
 * @function
 * @param {json} o
*/
Tgrid.prototype._event=function(){
    
    var t=this,p=t.param,css=p.css,jq = p.jq;
 
    jq.frameCells.on("click",function(e){
        var bool = false,td,tr,et = $(e.target);
        
        if (et.hasClass(css.group)||(!t.not_group(et))){
            tr=$.data(e.target,'data').up;                
            bool = true;
        }else if (et.hasClass(css.tr)){
            bool=true;
            tr = et;
            
        }else if (p.deepSelect){
            
            td = et.closest('td');
            bool=(td.length>0);
            if (bool)
                tr = td.parent();
        }else{ 
            bool = e.target.tagName==='TD';
            td = et;
            if (bool)
                tr = td.parent();
        }    
        
        if (bool){
        if (t.not_group(tr)){
            
            if (p.selected){
                var select = tr.hasClass(css.select);
                if (!p.multiSelect)
                    t.select(jq.all.not(tr),false);
                    
                if (!select)
                    t.select(tr,true);
                else if ((p.toggleSelect)||(p.multiSelect))
                    t.select(tr,false);
                    
            }
            t.doClick({tr:tr,target:et});
        }else{
            var hide = t.hideGroup(tr);
            if (p.toggleGroup){
                if (!hide)
                    t.hideGroup(tr,true);
                else{
                    
                    var gr = t.group(tr);
                    var open = false;
                    t.each(function(o){
                        if (o.group.up[0].id!==gr.up[0].id)
                            t.hideGroup(o.group,true,false);    
                        else    
                            open = o.group;
                    },'groups');

                    if(open)
                        t.hideGroup(open,false);
                }    
                
            }else
                t.hideGroup(tr,!hide);
        }
            
        }//bool
    });/*.on('mousemove',function(e){
        var _t =$(this);
        if (_t.hasClass(css.group)||(!t.not_group(_t))){
            
        }    
    };*/
    
    jq.frameCells.on('scroll',function(){
        if (p.flyingGroup)
            t.align();
        else
            t.updateGroupInView();
    });
    
    jq.cells.on("dblclick",function(e){
        var bool = true,td,tr;
        
        bool = e.target.tagName==='TD';
        if (bool){
            td = $(e.target);
            tr = td.parent();
            if (t.not_group(tr))
                t.doDblClick({tr:tr,data:$.data(tr[0],'data')});
                
                
        }
    });
    
    p.jq.frameCells.on('mousemove',function(e){
        var et = $(e.target);
        // сначала предполагаем, что 
        var tr = et.closest('tr');
        // 
        if (tr.length===0){
            tr = et.closest('.'+css.group);
            if (tr.length>0){
                tr = $.data(tr[0],'data').up;
            }
        }
        
        if (tr.length>0){
            if ((p._prevHover===undefined)||(p._prevHover[0].id!==tr[0].id)){
                t.hover(tr);
                /*
                jq.trs.not(tr).removeClass(css.hover);
                if (t.not_group(tr)){
                    if (!tr.hasClass(css.select))
                        tr.addClass(css.hover);
                    t.doHover({tr:tr,prev:p._prevHover});
                    p._prevHover = tr;
                }else{
                    t.doHover({tr:undefined,prev:p._prevHover});
                    p._prevHover=undefined;
                }    
                */
            }    
        }
    });
    
    p.jq.frameCells.on('mouseout',function(e){
        if ($(e.relatedTarget).closest('#'+p.jq.frameCells[0].id).length===0){
            t.hover(false);
            /*  jq.trs.removeClass(css.hover);
                t.doHover({tr:undefined,prev:p._prevHover});
                p._prevHover = undefined;
            */
        }    
    });
    
    p.jq.header.on('click',function(e){
        var td=$(e.target);
        if (td[0].tagName!=='TD')
            td = td.closest('TD');
        if (td.length>0)
            t.doClickHeader({td:td,name:td[0].id});
    });
};

/** подсветка в стиле hover */
Tgrid.prototype.hover=function(tr){
    var t=this,p=t.param,css=p.css,jq = p.jq;

    if (tr===undefined)
        return p._prevHover;
    
    if (!jq.trs) return;
    
    jq.trs.removeClass(css.hover);
    
    if (!p.hovered) 
        return;
    
    if (tr!==false){
        if (t.not_group(tr)){
            if (!tr.hasClass(css.select))
                tr.addClass(css.hover);
                t.doHover({tr:tr,prev:p._prevHover});
                p._prevHover = tr;
        }else{
            t.doHover({tr:undefined,prev:p._prevHover});
            p._prevHover=undefined;
        }
    }else{
        
        t.doHover({tr:undefined,prev:p._prevHover});
        p._prevHover = undefined;
        
    }    
     
}
/**
 * @function
 * @param {json} o
*/
Tgrid.prototype.doChange=function(o){
    var t=this,p=t.param,l=p.lock;
    if (!l.can('change')) return;
    if (p.onChange){
        if (o) 
            o.sender = t;
        else
            o={sender:t};
        
        p.onChange(o);
    }
    t.notify('change',o);
    
};

/**
 * @function
 * @param {json} o
*/
Tgrid.prototype.doClick=function(o){
    var t=this,p=t.param;
    if (p.onClick){
        if (o) 
            o.sender = t;
        else
            o={sender:t};
        if ((o.tr)&&(!o.data)) o.data = $.data(o.tr[0],'data');
        p.onClick(o);
    }; 
    t.notify('click',o);
};
Tgrid.prototype.doClickHeader=function(o){
    var t=this,p=t.param;
    if (p.onClickHeader){
        if (o) 
            o.sender = t;
        else
            o={sender:t};
        p.onClickHeader(o);
    }; 
    t.notify('clickHeader',o);
};
/**
 * @function
 * @param {json} o
*/
Tgrid.prototype.doDblClick=function(o){
    var t=this,p=t.param;
    if (p.onDblClick){
        if (o) 
            o.sender = t;
        else
            o={sender:t};
        if ((o.tr)&&(!o.data)) o.data = $.data(o.tr[0],'data');
        p.onDblClick(o);
    }
    t.notify('dblClick',o);
};

/**
 * @function
 * @param {json} o
*/
Tgrid.prototype.doSelect=function(o){
    var t=this,p=t.param;
    if (p.onSelect){ 
        if (o) 
            o.sender = t;
        else
            o={sender:t};
            
        if ((o.tr)&&(!o.data)) o.data = $.data(o.tr[0],'data');
        
        p.onSelect(o);
    };
    t.notify('select',o);
};
/**
 * @function
 * @param {json} o
*/
Tgrid.prototype.doHover=function(o){
    var t=this,p=t.param;
    if (p.onHover){
        if (o) 
            o.sender = t;
        else
            o={sender:t};
        p.onHover(o);
    };
    t.notify('hover',o);
};

Tgrid.prototype.doHoverGroup=function(o){
    var t=this,p=t.param;
    if (p.onHoverGroup){
        if (o) 
            o.sender = t;
        else
            o={sender:t};
        p.onHoverGroup(o);
    };
    t.notify('hoverGroup',o);
};

/**
 * @function
 * @param {json} o
*/
Tgrid.prototype.doCreate=function(o){
    if (!o) return;
    var t=this,p=t.param;
    if (p.onCreate){
        o.sender = t;
        p.onCreate(o);
    };
    t.notify('create',o);
};

/**
 * @function
 * @param {json} o
*/
Tgrid.prototype.doDelete=function(o){
    var t=this,p=t.param;
    if (p.onDelete){
        if (o) 
            o.sender = t;
        else
            o={sender:t};
        p.onDelete(o);
    };
    t.notify('delete',o);
};
Tgrid.prototype.notify=function(event,param){
    var t=this,p=t.param,h=p.handler;
    param = $.extend(false,{
        sender:t,
        event:event
    },param);

    h.do({group:event,param:param});    
};

Tgrid.prototype.attach=function(o){
    var t = this,h=t.param.handler,
    a=$.extend(false,{
        event:'',
        func:undefined,
        name:ut.id('jgrh')
    },o);

    if (a.event==='') return false;
    
    a.group = a.event;
    return h.add(a).name;

};
/** 
 * Ex: detach(name) 
 * Ex: detach(fromEvent,name)
*/
Tgrid.prototype.detach=function(){
    var t = this,p=t.param,h=p.handler,e=p.events,k,a=arguments;
    if (a.length>1)
        h.remove({name:a[1],group:a[0]});
    else
        for (k in e) h.remove({name:a[0],group:k});

};
/**
 * @function
 * @param {json} o
*/
Tgrid.prototype.hide=function(tr,bool){
    var t=this,p=t.param,out=[];
    
    if (tr===undefined) return;
    if (typeof tr === 'boolean'){
        
        bool = tr;
        tr = (!bool ? t.map("hide") : t.trs() );
        
    }
    $.each(tr,function(i,d){
        
        var _tr=JX.is_jquery(d)?d:$(d);
        if (bool===undefined)
            out.push(t._attr({tr:_tr,name:'_hide_'})==='true');
        else{
            t._attr({tr:_tr,name:'_hide_',value:'true',remove:!bool});
            // скрываем в любом случае, показываем только если он в открытой группе
            if (bool)
                _tr.hide();
            else if (!t.hideGroup(_tr))
                _tr.show();
                
            t.trs(true);

            if (i===tr.length-1)
                t.doChange();
        }    
    });
        
    return out;    
};

Tgrid.prototype.delete=function(trs){
    var t=this,p=t.param,l=p.lock,i;
    
    l.lock('align');
    if ($.isArray(trs)) for(i=0;i<trs.length;i++)
        trs[i].remove();
    else
        trs.remove();

    if (l.unlock('align'))
        t.align();
    
    t._updateFastAccess();
        
    t.doDelete();
    t.doChange();
};

/**
 * @param {} o
 * 
*/ 
Tgrid.prototype.select=function(o,bool){
    var t=this,p=t.param,css=p.css,i,out=[],trs = t.trs(),ty=typeof o;
    
    if (o === -1) o = undefined;

    if (ty ==='number'){
        
        if (o<trs.length)
            o = trs[o];
        else
            return false;
    }
    
    if (ty === 'string'){
        if (trs.length===0)
            return;
        if (o==='last')
            o = trs[trs.length-1];
        else if (o==='first')    
            o = trs[0];
        else
            return;
    }


    if (bool===undefined){
        $.each(o,function(i,a){
            out.push($(a).hasClass(css.select));    
        });
        return out;
    }


    if (o===undefined)
        o = t.map("rows");
    

    if (Array.isArray(o))
        for(i=0;i<o.length;i++) t.select(o[i],bool);
    else{
        if (bool){
            o.removeClass(css.hover);
            o.addClass(css.select);
            t.doSelect({tr:o});
        }else
            o.removeClass(css.select);
    }
    
};

/**
 * @function
 * @param {json} o
*/
Tgrid.prototype._attr=function(o){
    var t=this,p=t.param,i,
    a = $.extend(true,{
        tr:undefined,
        name:undefined,
        value:undefined,
        remove:false
    },o);
    
    if ($.isArray(a.tr)){
        for(i=0;i<a.tr.length;i++){
            t._attr({
                tr:a.tr[i],
                name:a.name,
                value:a.value,
                remove:a.remove
            });
        }
    }else{
        if (a.remove)
            a.tr.removeAttr(a.name);
        else    
        if (a.value===undefined)
                return a.tr.attr(a.name);
        else
            a.tr.attr(a.name,a.value);
    }
    
};

Tgrid.prototype._css = function(css){
    var t = this,p=t.param;
    if (css===undefined)
        return p.css;
        
    for (var key in css) {    
        if (key in p.css)
            p.plugin.find('.'+p.css[key]).removeClass(p.css[key]).addClass(css[key]);
    }
    
    p.css=$.extend(true,p.css,css);
    t._updateFastAccess();
};
Tgrid.prototype._css2 = function(from,to){
    var t = this,p=t.param;

    for (var key in from) {    
        if (key in to)
            p.plugin.find('.'+from[key]).removeClass(from[key]).addClass(to[key]);
    }
    
};

/**
 * возвращает признак виден или нет вертикальный скролл бар
 * @function
 * @param {json} o
*/
Tgrid.prototype.scrollVisible=function(){
    var t=this,p=t.param,jq=p.jq;
    
    return (JX.pos(jq.frameCells).h<JX.pos(jq.cells).h);
};
/** признак того, что строка находится в области видимости */
Tgrid.prototype.inViewPort=function(tr,strong=false){
    var t=this,p=t.param,
    a=JX.abs(p.plugin),b= JX.abs(tr);
    
    return strong?JX.insider(a,b):JX.iscrossr(a,b);
};

/**
 * Scrolling the table depending on the value of the parameter
 * 
*/
Tgrid.prototype.scroll=function(o){
    var t=this,p=t.param,tr,trs = p.jq.trs;
    
    if ((typeof(o)==='string')||(o instanceof jQuery))
        o = {to:o};
    else if ((typeof o === 'object') && ('up' in o))
        o = {to:o};
    

    var a=$.extend(true,{
        to:undefined, /* 'top'|'bottom'|tr |object(group)|selected*/
        animate:p.animateScroll,
        off:0,
    },o);

    if (a.to === undefined ) 
        return;
        
    if ((typeof a.to === 'object') && !(a.to instanceof jQuery)){
        if ('up' in a.to)
            a.to = a.to.up;
        else
            return;
    };
    
    var scrl = function(tr){
        if (tr === 0){
            if (a.animate===0)
                p.jq.frameCells.scrollTop(0);
            else
                p.jq.frameCells.animate({scrollTop:0},a.animate);
            
        }else if (tr.length){
            var pos = JX.pos(tr[0]);
            pos.y-=a.off;
            if (a.animate===0)
                p.jq.frameCells.scrollTop(pos.y);
            else
                p.jq.frameCells.animate({scrollTop:pos.y},a.animate);
        } 
    };
    
    if (a.to==='top')
        scrl(0);
    else if (a.to==='bottom'){
        if (trs.length>0)
            scrl(trs.last());
    }else if (a.to==='selected'){
        
         tr = o.map('selected');
         if (tr.length>0)
            scrl(tr[0]);
    }else
        scrl(a.to);

};

/**
 * пометка строки или строк
 * 1. mark(name) - возвращает все строки помеченные name
 * 2. mark(name,tr|[tr1,tr2,..]) - возвращает массив пометок
 * 3. mark(name,tr|[],bool) - помечает или снимает пометку
 */ 
Tgrid.prototype.mark=function(name,trs,bool){
    var t=this,p=t.param,out;
    
    if (trs===undefined)
        return p.jq.all.filter("["+name+"='1']");
    
    if (bool===undefined){
        out=[];        
        
        $.each(trs,function(i,o){
            
           out.push(t._attr({tr:JX.is_jquery(o)?o:$(o),name:name})=='1');
           
        });
        return out;    

    
    }else{ 
        if (bool===true)
            t._attr({tr:trs,name:name,value:'1'});
        else    
            t._attr({tr:trs,name:name,remove:true});
    }        
};


/**
 * @function
 * @param {json} o
*/
Tgrid.prototype.getIndexField=function(o){
    var t=this,p=t.param,i;

    if (typeof o === 'number')  
        return ((o>=0)&&(o<p.fields.length))?o:-1;
    
    for(i=0;i<p.fields.length;i++){
        if ((typeof o === 'string')&&(o===p.fields[i].name)) return i;
        if ((typeof o === 'object')&&(o.name===p.fields[i].name)) return i;
    }
    
    return -1;
};

Tgrid.prototype.getField=function(o){
    var t=this,p=t.param,i;
    
    i = t.getIndexField(o);

    return i>=0?p.fields[i]:false;
};


/**
 * @function
 * @param {json} o
*/
Tgrid.prototype._putFields=function(f){
    var t=this,p=t.param,i,idx,field;
    
    if (p.fields.length===0){
        
        for(i=0;i<f.length;i++){
            f[i]=$.extend(true,{
                caption:f[i].name,
                visible:true,
                width:undefined
            },f[i]);
        };
        p.fields = $.extend(true,[],f);
        
        
    }else{
        for(i=0;i<f.length;i++){
            field = t.getField(f[i]);
            if (field)
                field = $.extend(true,field,f[i]);
        };
        
    }
    
    if ((p.jq.headerTd===null)||(p.jq.headerTd.length===0))
        t._createHeader();
    t._setColsWidthStyle();
};


Tgrid.prototype.put = function(o){
    var t=this,p=t.param,l=p.lock;
    
    l.lock('align');

    if ('indexField' in o)
        p.indexField = o.indexField;
    
    if ('fields' in o)
        t._putFields(o.fields);

    if ('data' in o)
        t.setData(o);
    
    
    $.each(o,function(n,v){
        t.attr(n,v);
    });
    
    if (l.unlock('align')){
        t.align();
        
    }    
};    

Tgrid.prototype.attr = function(n/*v*/){
    if (arguments.length===0) return;
    var t=this,p=t.param,css=p.css,i,v,r=(arguments.length===1);
    
    if (!r) 
        v=arguments[1];

    /*-----------------------------------*/
    if (n==='fields'){
        if (r) 
            return p.fields;
    }
    
    /*-----------------------------------*/
    if (n==='margin'){
        if (r) 
            return p.margin;
        else
            p.margin = JX.margin(v);
    }
    /*-----------------------------------*/

    if (n==='marginGroup'){
        if (r) 
            return p.marginGroup;
        else
            p.marginGroup = JX.margin(v);
    }
    /*-----------------------------------*/
    
    if (n==='marginHeaderFrame'){
        if (r) 
            return p.marginHeaderFrame;
        else
            p.marginHeaderFrame = JX.margin(v);
    }    
    
    /*-----------------------------------*/
    if (n==='hovered'){
        if (r) 
            return p.hovered;
        else
            p.hovered = v?true:false;
    }
    /*-----------------------------------*/
    if (n==='flyingHeader'){
        if (r) 
            return p.flyingHeader;
        else{
            p.flyingHeader = v?true:false;
        }    
    }
    if (n==='flyParent'){
        if (r)
            return p.flyParent;
        else{
             if (p.flyParent!==undefined)
                t.param.flyParent.off('scroll',t._alignFlyingHeader);
                
             if (v){
                p.flyParent = v;
                t.param.flyParent.on('scroll',{grid:t},t._alignFlyingHeader);
             }else    
                p.flyParent = undefined;
        }    
    }
    /*-----------------------------------*/
    
    if (n==='flyingGroup'){
        if (r) 
            return p.flyingGroup;
        else
            p.flyingGroup = v?true:false;
    }
    /*-----------------------------------*/
    if (n==='indexField'){
        if (r) 
            return p.indexField;
        else
            p.indexField = v;
    }
    /*-----------------------------------*/
    if (n==='scrollWidth'){
        if (r) 
            return p.scrollWidth;
        else
            p.scrollWidth = v;
    }
    /*-----------------------------------*/
    if (n==='onChange'){
        if (r) 
            return p.onChange;
        else
            p.onChange = v;
    }
    /*-----------------------------------*/
    if (n==='onHover'){
        if (r) 
            return p.onHover;
        else
            p.onHover = v;
    }
    /*-----------------------------------*/
    if (n==='onHoverGroup'){
        if (r) 
            return p.onHoverGroup;
        else
            p.onHoverGroup = v;
    }
    /*-----------------------------------*/
    if (n==='onClick'){
        if (r) 
            return p.onClick;
        else
            p.onClick = v;
    }
    /*-----------------------------------*/
    if (n==='onDblClick'){
        if (r) 
            return p.onDblClick;
        else
            p.onDblClick = v;
    }
    /*-----------------------------------*/
    if (n==='onDelete'){
        if (r) 
            return p.onDelete;
        else
            p.onDelete = v;
    }
    /*-----------------------------------*/
    if (n==='onSelect'){
        if (r) 
            return p.onSelect;
        else
            p.onSelect = v;
    }
    /*-----------------------------------*/
    if (n==='onCreate'){
        if (r) 
            return p.onCreate;
        else
            p.onCreate = v;
    }
    /*-----------------------------------*/
    if (n==='onSetValue'){
        if (r) 
            return p.onSetValue;
        else
            p.onSetValue = v;
    }
    /*-----------------------------------*/
    if (n==='selected'){
        if (r) 
            return p.selected;
        else
            p.selected = v?true:false;
    }
    /*-----------------------------------*/
    if (n==='toggleSelect'){
        if (r) 
            return p.toggleSelect;
        else
            p.toggleSelect = v?true:false;
    }
    /*-----------------------------------*/
    if (n==='fixedHeight'){
        if (r) 
            return p.fixedHeight;
        else
            p.fixedHeight = v?true:false;
    }
    /*-----------------------------------*/
    
    if (n==='fixedOff'){
        if (r) 
            return p.fixedOff;
        else
            p.fixedOff = v;
    }
    /*-----------------------------------*/
    if (n==='visibleHeader'){
        if (r) 
            return p.visibleHeader;
        else{
            p.visibleHeader = v?true:false;
            JX.visible(p.jq.header,v);
            JX.visible(p.jq.headerFrame.left,v);
            JX.visible(p.jq.headerFrame.right,v);
            JX.visible(p.jq.headerFrame.top,v);
            JX.visible(p.jq.headerFrame.bottom,v)
        }    
    }
    /*-----------------------------------*/
    if (n==='alignDelay'){
        if (r) 
            return p.alignDelay;
        else
            p.alignDelay = v;
    }
    /*-----------------------------------*/
    if (n==='deepSelect'){
        if (r) 
            return p.deepSelect;
        else
            p.deepSelect = v;
    }
    /*-----------------------------------*/
    if (n==='multiSelect'){
        if (r) 
            return p.multiSelect;
        else
            p.multiSelect = v?true:false;
    }
    /*-----------------------------------*/
    if (n==='toggleGroup'){
        if (r) 
            return p.toggleGroup;
        else
            p.toggleGroup = v?true:false;
    }
    /*-----------------------------------*/
    if (n==='animateGroup'){
        if (r) 
            return p.animateGroup;
        else
            p.animateGroup = v;
    }
    /*-----------------------------------*/
    if (n==='animateScroll'){
        if (r) 
            return p.animateScroll;
        else
            p.animateScroll = v;
    }
    
    if (n==='alignAutoEnable'){
        if (r) 
            return p.alignAutoEnable;
        else
            p.alignAutoEnable = v?true:false;
    }
    /*-----------------------------------*/
    if (n==='cssPref'){
        if (r) 
            return p.cssPref;
        else if (v!==p.cssPref){
            
            var norm = $.extend(true,{},css); 
            ut.delPref(norm,p.cssPref);

            
            var newCSS = $.extend(true,{},norm);
            ut.addPref(newCSS,v);
            
            
            t._css2(css,newCSS);
            //t._css(newCSS);
            p.cssPref = v;
            p.css=$.extend(true,p.css,newCSS);
            t._updateFastAccess();

        }    
    }
    /*-----------------------------------*/
    if (n==='css'){
        if (r) 
            return p.css
        else{    
           t._css(v);
           
        }    
    }
    /*-----------------------------------*/
    if (n==='trs'){
        if (r) 
            return p.jq.trs;
    }
    /*-----------------------------------*/
    if (n==='all'){
        if (r) 
            return p.jq.all;
    }
    /*-----------------------------------*/
    if (n==='jq'){
        if (r) 
            return p.jq;
    }
    /*-----------------------------------*/
    if (n==='heightHeader'){
        if (r) 
            return JX.pos(p.jq.header).h;
    }
    /*-----------------------------------*/
    if (n==='heightCells'){
        if (r) 
            return JX.pos(p.jq.cells).h;
    }
    /*-----------------------------------*/
    if (n==='widthColsFixed'){
        if (r){ 
            var w = 0;
            for(i=0;i<p.fields.length;i++){
                if (
                    (typeof p.fields[i].width === 'number')
                    &&
                    ((p.fields[i].visible===undefined)||(p.fields[i].visible===true))
                ){
                    w+=p.fields[i].width;
                }
            }
            return w+p.fields.length;
        }    
    }
    /*-----------------------------------*/

    t.align();
};

/**
 * @function
 * @param {json} o
*/
Tgrid.prototype._updateFastAccess=function(){
    var t=this;
    t.all(true);
    t.trs(true);
    t.groups(true);
};
Tgrid.prototype.count=function(by,update){
    var t=this,p=t.param,css=p.css;
    by = by===undefined?'visible':by;
    update = update===undefined?false:(update===true?true:false);
    
    if (by==='visible')
        return t.trs(update).length;
    else    
        return t.all(update).length;
};
/** массив всех видимых строк */
Tgrid.prototype.trs=function(update){
    var t=this,p=t.param,css=p.css;
    
    // p._trs.length ==== 0 добавлено, на случай, если данные пришли еще до отображения и все строки нивидимы
    
    if (((update!==undefined)&&(update))||(!p._trs)||(!p._trs.length===0)){
        p.jq.trs = p.jq.cells.children(':visible').not('.'+css.group_up).not('.'+css.group_down);
        p._trs = [];
        $.each(p.jq.trs,function(i,o){
            p._trs.push($(o));    
        });
    }
    return p._trs;
};    
/** массив всех строк (включая заголовки групп)*/
Tgrid.prototype.all=function(update){
    var t=this,p=t.param;
    if (((update!==undefined)&&(update))||(!p._all)){
        p.jq.all=p.jq.cells.children();
        p._all = [];
        $.each(p.jq.all,function(i,o){
            p._all.push($(o));    
        });
    }
    return p._all;
};    
/** массив групп*/
Tgrid.prototype.groups=function(update){
    var t=this,p=t.param,css=p.css,dn,panels;
    
    if (((update!==undefined)&&(update))||(!p._groups)){
        p.jq.groups=p.jq.cells.children('.'+p.css.group_up);
        panels=p.jq.frameCells.children('div');
        dn=p.jq.cells.children('.'+css.group_down);
        p._groups = [];
        $.each(p.jq.groups,function(i,o){
            var g=$(o);
            var d=dn.eq(i);
            var panel=panels.eq(i);
            var data={up:g,down:d,panel:panel};
            
            $.data(g[0],'data',data);
            $.data(d[0],'data',data);
            $.data(panel[0],'data',data);
            
            p._groups.push(g);    
        });
    }
    return p._groups;
};    


Tgrid.prototype.align=function(o){
    var t=this,p=t.param,l=p.lock;

    if (p._alignTimer){ 
        clearTimeout(p._alignTimer);
        p._alignTimer=undefined;
    };
        
    if ((p.alignDelay<=0)||((o!==undefined)&&(o.forced===true))){
        if (l.lock('align'))
            t._align();
        l.unlock('align');    
            
    }else    
        p._alignTimer=setTimeout(function(){
            if (l.lock('align'))
                t._align();
            l.unlock('align');    
        },p.alignDelay);

};

/** 
 * устанавливаем ширину столбцов заголовка равной ширине столбцов таблицы с данными 
 */
Tgrid.prototype._alignCols=function(){
    var t=this,p=t.param,trs = t.trs(),up,cells=p.jq.cells,i,f,w,
    empty = (trs.length===0);

    if (!empty)
        up = trs[0].find('td');
        
    for(i=0;i<p.fields.length;i++){
        f=p.fields[i];
        if ((f.visible===undefined)||(f.visible)){

            if (empty){
                //случай, когда нет ни одной видимой колонки у контейнера
                w= f.width+'';
                w=(f.width===undefined?'':(w.indexOf('%')===-1?w+'px':f.width));
                p.jq.headerTd.eq(i).css('width',w)
                
            }else{
                //w = JX.pos(up[i]).w+1;
                //w = up.eq(i).width()+(dvc.isIE?0:1);
                w = JX.pos(up[i]).w;
                if (dvc.isIE){
                    w=Math.ceil(w)-1;
                }
                
                //w = up.eq(i).width();
                JX.pos(p.jq.headerTd[i],{w:w});
            }        
        }
            
    }//for
    
    
};

/** расчет текущей видимой группы
 * @return {json} group = {up,down,panel};
 * @function
 * @param {json} o
*/
Tgrid.prototype._groupInView=function(){
    var t=this,p=t.param,css=p.css,
    g=t.groups(),i,d,up,down,scroll = p.jq.frameCells.scrollTop();
    
    
    for(i=0;i<g.length;i++){
        d=$.data(g[i][0],'data');
        
        up=JX.pos(d.up);
        down = JX.visible(d.down)?JX.pos(d.down):up;
        
            
        if ((g.length===1)||((up.y<=scroll)&&(down.y+down.h>scroll)))
            return d;        
    }
};

/**
 * обновление информации по текущей отображаемой группе
 * @function
 * @param {json} o
*/
Tgrid.prototype.updateGroupInView=function(o){
    var t=this,p=t.param,
    g = t._groupInView();
    
    
    if ((p.groupInView===undefined)||((g!==undefined)&&(g.up[0].id!==p.groupInView.up[0].id))){
        
        if (g!==undefined){    
            t.doHoverGroup({current:g,prev:p.groupInView});
            p.groupInView = g;
        }    
    };    
};

/** расчет расположения панелей-заголовков групп
 * @function
 * @param {json} o
*/
Tgrid.prototype._alignGroups=function(bound){
    var t=this,p=t.param,css=p.css,mg=p.marginGroup,
    g=t.groups(),i,d,pos,posd,h,
        scroll = p.jq.frameCells.scrollTop(),
        vs = t.scrollVisible()?p.scrollWidth:0;
    
    bound=bound?bound:JX.pos(p.plugin);
    
    for(i=0;i<g.length;i++){
        d=$.data(g[i][0],'data');
        pos=JX.pos(d.up);
        pos.x = pos.x+mg.left;
        pos.w=bound.w-(mg.right+mg.left+vs);
        
        pos.y+=mg.top;
        pos.h-=(mg.top+mg.bottom);
        
        if (p.flyingGroup){
            posd=JX.pos(d.down);
            
            if ((pos.y<scroll)&&(posd.y>scroll+pos.h))
                pos.y = scroll+1;
        }    
        JX.pos(d.panel,pos);
    }

};
/** ф-ция для вызова из Ws.align */
Tgrid.prototype._alignAuto=function(){
    var t=this,p=t.param;
    if (p.alignAutoEnable)
        t.align();
        
};    
Tgrid.prototype._alignOld=function(){
    var t=this,p=t.param,jq=p.jq,pos,bound=JX.pos(p.plugin),mf=p.marginHeaderFrame;
    
    
    
    // основные облости (заголовок и контейнер для таблицы)
    if (p.visibleHeader){
       JX.pos(jq.header,{w:bound.w});
       JX.arrange([jq.header,jq.frameCells],{direct:'vert',type:(p.fixedHeight?'stretch':'top'),align:'stretch',gap:0,stretch:[{idx:1}]});
    }else    
       JX.stretch(jq.frameCells);
    
        
    JX.pos(jq.cells,{w:bound.w,x:0});
    
    // для случая нефиксировнной высоты таблицы, пересчет высоты контейнера    
    if (!p.fixedHeight){
        JX.pos(jq.cells,{w:bound.w,x:0});
    
        pos = JX.pos(jq.cells);
        JX.pos(jq.frameCells,{h:pos.h+p.fixedOff});
        
        JX.pos(p.plugin,{h:pos.h+jq.header.height()+p.fixedOff});
    }
            
    // подгоняем ширину колонок заголовка под колонки контейнера
    if (p.visibleHeader)
        t._alignCols();

    t._alignGroups(bound);    
    
    // рамка заголовка 
    pos = JX.pos(jq.header);
    JX.pos(jq.headerFrame.left,{x:0-mf.left,y:0-mf.top,h:pos.h+mf.top+mf.bottom,w:1});
    JX.pos(jq.headerFrame.right,{x:bound.w-1+mf.right,y:0-mf.top,h:pos.h+mf.top+mf.bottom,w:1});
    JX.pos(jq.headerFrame.top,{x:0-mf.left,y:0-mf.top,h:1,w:pos.w+mf.left+mf.right});
    JX.pos(jq.headerFrame.bottom,{x:0-mf.left,y:pos.h+mf.bottom,h:1,w:pos.w+mf.left+mf.right});
    
    t.updateGroupInView();
};

Tgrid.prototype._alignFlyingHeader=function(ev){
    var t=ev.data.grid,
        p=t.param;
    if (!p.flyingHeader) return;

    var jq=p.jq,
        flyParent = p.flyParent,
        //flyParent=p.plugin.parent(),
        dy = flyParent.scrollTop(),
        dx = flyParent.scrollLeft(),
        fpPos = JX.pos(flyParent),
        pPos = JX.pos(p.plugin);
        pPos.x-=dx;
        pPos.y-=dy;
    
    //определить находится ли таблица в области видимости
    if (JX.iscrossr(fpPos,pPos)){
        // заголовок выше верхней границы
        if (pPos.y<fpPos.y)
            JX.pos(jq.header,{y:fpPos.y-pPos.y});
        else
            JX.pos(jq.header,{y:0});
    }    
    
    t._alignHeader(t);
};

Tgrid.prototype._alignHeader=function(tt){
    var t=(tt?tt:this),p=t.param,jq=p.jq,pos,
        bound=JX.pos(p.plugin),
        mf=p.marginHeaderFrame;
    // рамка заголовка 
    if (p.flyingHeader){
        
        pos = JX.pos(jq.header);
        JX.pos(jq.headerFrame.left,{x:0-mf.left,y:0-mf.top,h:pos.h+mf.top+mf.bottom,w:1});
        JX.pos(jq.headerFrame.right,{x:bound.w-1+mf.right,y:0-mf.top,h:pos.h+mf.top+mf.bottom,w:1});
        
        JX.pos(jq.headerFrame.top,   {x:0-mf.left,  y:pos.y-mf.top,         h:1,    w:pos.w+mf.left+mf.right});
        
        JX.pos(jq.headerFrame.bottom,{x:0-mf.left,  y:pos.y+pos.h+mf.bottom,    h:1,    w:pos.w+mf.left+mf.right});
        
    }else{
        pos = JX.pos(jq.header);
        JX.pos(jq.headerFrame.left,{x:0-mf.left,y:0-mf.top,h:pos.h+mf.top+mf.bottom,w:1});
        JX.pos(jq.headerFrame.right,{x:bound.w-1+mf.right,y:0-mf.top,h:pos.h+mf.top+mf.bottom,w:1});
        JX.pos(jq.headerFrame.top,{x:0-mf.left,y:0-mf.top,h:1,w:pos.w+mf.left+mf.right});
        JX.pos(jq.headerFrame.bottom,{x:0-mf.left,y:pos.h+mf.bottom,h:1,w:pos.w+mf.left+mf.right});
    }    
    
};    

Tgrid.prototype._align=function(){
    var t=this,p=t.param,jq=p.jq,pos,bound=JX.pos(p.plugin),mf=p.marginHeaderFrame;

    // основные облости (заголовок и контейнер для таблицы)
    if (p.visibleHeader){
       JX.pos(jq.header,{w:bound.w});
       
       JX.arrange([jq.header,jq.frameCells],{
           direct:'vert',
           type:(p.fixedHeight?'stretch':'top'),
           align:'stretch',
           margin:p.margin,
           gap:0,stretch:[{idx:1}]});
           
       //if (p.flyingHeader)
    //        t._alignFlyingHeader();
    }else    
       JX.stretch(jq.frameCells,{margin:p.margin});
    
        
    JX.pos(jq.cells,{w:bound.w,x:0});
    
    // для случая нефиксировнной высоты таблицы, пересчет высоты контейнера    
    if (!p.fixedHeight){
        JX.pos(jq.cells,{w:bound.w,x:0});
    
        pos = JX.pos(jq.cells);
        JX.pos(jq.frameCells,{h:pos.h+p.fixedOff});
        
        JX.pos(p.plugin,{h:pos.h+(p.visibleHeader?jq.header.height():0)+p.fixedOff});
    }
            
    // подгоняем ширину колонок заголовка под колонки контейнера
    if (p.visibleHeader)
        t._alignCols();

    t._alignGroups(bound);    
    
    t._alignHeader();      
    
    t.updateGroupInView();
};