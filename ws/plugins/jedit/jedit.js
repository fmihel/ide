/*global ut,$,jQuery,JX,Qs,Ws*/
(function( $ ){ 
var m={
name:"jedit",
init:function(param){
    var o = m.obj(this),p;

    if (o===undefined){
        p=$.extend(true,{},param);
        p.plugin = this;
        m.obj(this,new Tjedit(p));
    }else
        o.put(param);                
        
    return this;     
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
created:function(){
    return (m.obj(this)!==undefined);
},
/** возвращает отображаемое значение jedit */
text:function(){
    var o = m.obj(this);
    return o.attr('text');
},
clear:function(){
    var o = m.obj(this);
    return o.clear();
},
value:function(mean,lockChange){
    var o = m.obj(this);
    
    if (mean===undefined)
        return o.attr('value');
    else{
        if (lockChange===true) 
            o.begin('change');
            
        o.put({value:mean});
        
        if (lockChange===true){ 
            o.changed(false);
            o.end('change');
        }    
    }    
},
valueKey:function(key,lockChange){
    var o = m.obj(this);
    
    if (key===undefined)
        return o.attr('valueKey');
    else{
        if (lockChange===true) o.begin('change');
        o.put({valueKey:key});
        
        if (lockChange===true){ 
            o.changed(false);
            o.end('change');
        }    
    }    
},
changed:function(bool){
    var o = m.obj(this);
    return o.changed(bool);
},
combo:function(event,param){
    var o = m.obj(this);
    return o.combo(event,param);
},
begin:function(event){
    var o = m.obj(this);
    return o.begin(event);
},
end:function(event){
    var o = m.obj(this);
    return o.end(event);
},

put:function(param){
    m.obj(this).put(param);
    return this;
},
get:function(name){
    return m.obj(this).attr(name);
},
on:function(event,fn){
    var o = m.obj(this);
    o.on(event,fn);
    return this;
},
trigger:function(to,event,p){
    var o = m.obj(this);
    o.trigger(to,event,p);
    return this;
    
},
setData:function(data){
    var o = m.obj(this);
    o.setData(data);
},
getData:function(data){
    var o = m.obj(this);
    return o.getData(data);
},
focus:function(){
    var o = m.obj(this);
    o.focus();
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
},
align:function(){
    var o = m.obj(this);
    o.align();
}
/*,
combo:function(action,param){
    var o = m.obj(this);
    if (o===undefined){
        console.error('plugin jedit not create!!');
        return;
    }    
        
    if (typeof action === 'object'){
        o.combo('put',action);
        return this;
    }else
        return o.combo(action,param);
}*/

};

$.fn.jedit = function(n){
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
            $.error( 'Not exists method ' +  n + ' for jQuery.jedit');
        */    
    }    
};
})(jQuery);

function Tjedit(o){
    var t = this;
    t.init(o);
}

Tjedit.prototype.init = function(o){
    var t=this;
    
    
    t.param = $.extend(true,{
        plugin:null,
        modal:Qs.modal,
        _alignName:ut.id('jealw'),
        jq:{
            icon:undefined,
            label:undefined,
            input:undefined,
            btn:undefined,
            btn_add:undefined,
            btn_combo:undefined,
            text:undefined,
            field:undefined,
            combo:undefined,
            checkbox:undefined,
            dim:undefined,
            memo:undefined,
            tip:undefined,
            icon_tip:undefined,
        },
        id:{
            icon:ut.id('jei'),
            label:ut.id('jel'),
            field:ut.id('jef'),
            input:ut.id('jei'),
            text:ut.id('jet'),
            btn:ut.id('jeb'),
            btn_combo:ut.id('jec'),
            btn_add:ut.id('jea'),
            combo:ut.id('jecb'),
            checkbox:ut.id('jecx'),
            dim:ut.id('jedx'),
            memo:ut.id('jemm'),
            tip:ut.id('jetip'),
            icon_tip:ut.id('jeitip'),
        },
        /** имя поля для запитки из data */
        field:undefined,
        /** тип отображния
         * edit - редактируемое поле
         * text - статический текст
         * button - кнопка mbtn ( по умолчанию раскрывает список combo)
         * checkbox - переключатель
        */
        
        type:"edit",
        /** значение атрибута type для тега edit */
        attr:{type:"text"},
        /** дополнительные смещения элементов */
        off:{
            btn_combo:{x:0,y:-1}
        },
        prop:{},
        readOnly:false,
        /** признак неактивности компонента */
        disable:false,
        /** отображать или нет состовляющие элементы компонента */
        disables:{
            label:false,
            icon:true,
            icon_tip:true,
            btn_combo:true,
            btn_add:true,
            dim:true
        },
        /* размерность */
        dim:'м',
        /** надпись на лейбле */
        caption:'',
        /** значение для установки */
        value:'',
        /** сохраненное начение для проверки состояния изменения данных */
        _storyValue:'',
        /** расположение по вертикали компонента внутри родительского фрейма center,top,bottom*/
        align:'center',
        /** включает перерисовку в зависимости*/
        alignGlobal:true,
        
        alignCheckbox:"left",/** left center right*/
        /** отступы внутри родительского фрейма */
        margin:0,
        /* установка ширины родительского фрейма */
        width:0,
        /** установка ширины внутренних компонентов*/
        widths:{
            label:110,
        },
        /** отступ лейбла от поля со значением value*/
        gapLabel:0,
        topLabel:false,
        /** порядок, расположение внутренних компонентов */
        arrange:{
            order:['icon','label','value','combo','btn','dim','icon_tip'],
            stretch:"value",
        },
        
        /** признак что комбо проиницилизирован */
        _initCombo:false,
        
        /** настройки grid для combo */
        combo:{
            visibleHeader:false,
            fixedHeight:false,
            key:'',         // поле, которое будет отображено
            valueKey:''     // поле значение которого будет использовано при запросе value
            
        },
        comboShadow:0.01,
        
        lock:new jlock(),
        handler:new jhandler(),
        /** 
         * при использовании свойства text логическое значение будет преобразовано исходя из правила в
         * boolAsText.
         * boolAsText<>[,] то преобразования не будет
        */
        boolAsText:["нет","да"],
        tip:{
            msg     :"",
            place   :"bottom",
            width   :"auto",
            height  :"auto",
            modal   :true,
            delay   :0,
            arrOff  :"center",/** int or center */
            arrW:   14,
            show    :false,
            padding:{left:5,top:5,bottom:5,right:20},
            pivot:{a:"center",b:"center"}
        },
        /** признак, что edit находится в состоянии ошибки */
        _error:false,
        /** изменение наcтупает на потерю фокуса или на нажатие enter */
        changeOnKeyEnter:true,
        /** задержка на выполнение реакции "change"*/
        changeDelay:1000,
        _changeTimer:undefined,
        /** btnComboClick, btnClick,btnAddClick,comboClick,change,
            можно использовать данные переменные для передачи в массиве параметров для инициализации событий 
            onBtnComboClick:undefined, 
            onBtnClick:undefined,
            onBtnAddClick:undefined,
            onComboClick:undefined,
            onChange:undefined,
            onDraw:undefined,
        
        */
        events:[],
        cssPref:'',
        css:{
            icon:'je_icon',
            label:'je_label',
            field:'je_field',
            input:'je_input',
            changed:'je_changed',
            text:'je_text',
            btn:'je_btn',
            btn_combo:'je_btn_combo',
            btn_add:'je_btn_add',
            combo:'je_combo',
            disable:'je_disable',
            input_fone:'je_input_fone',
            text_fone:'je_text_fone',
            btn_fone:'je_btn_fone',
            checkbox:'je_checkbox',
            checked:'je_checkbox_checked',
            dim:'je_dim',
            memo:'je_memo',
            
            error:'je_error',
            
            icon_tip:'je_icon_tip',
            tip:        'je_tip',
            tipShadow:  'je_tipShadow',
            tipBtn:     'je_tipBtn',
            tipArrow:   'je_tipArrow',
            tipBorder:  'je_tipBorder',
            tipUp:      'je_tipUp',
            tipText:    'je_tipText'
        }
        
    },o);
    
    if (t.param.caption==='')
        t.param.caption = t.param.plugin.text();
    
    
    t.param.margin=JX.margin(t.param.margin);
    if (t.param.cssPref!=='')
        ut.addPref(t.param.css,t.param.cssPref);
        
    t._create();
    t.put(t.param);

    //t.align();
    
    Ws.align({
        func:
        function(){
            
            if (t.param.alignGlobal)
                t.align();
        },
        recall:true,
        id:t.param._alignName
    });
    
    t.changed(false);
    t.group();

};

Tjedit.prototype.group=function(group){
    if (typeof jgroup!=='undefined')
        jgroup.add(this,group);
};

Tjedit.prototype.ungroup=function(group){
    if (typeof jgroup!=='undefined')
        jgroup.remove(this,group);
};

Tjedit.prototype.done=function(){
    var t = this,p=t.param;
    
    t.ungroup(-1);
    p.events=[];
    Ws.removeAlign(p._alignName);
    if (p.jq.combo.grid('ready')){
        p.jq.combo.grid('destroy');
        p.jq.combo.jshadow('destroy');
    }
    p.jq.combo.remove();
    
    if (p.tip._delay){ clearTimeout(p.tip._delay);p.tip._delay=undefined;};
    if (p.jq.tip.modal!==undefined){
        p.jq.tip.modal.jshadow('destroy');
        p.jq.tip.modal.remove()
    }    
    p.jq.tip.frame.remove();
    
    p.plugin.html("");
};

Tjedit.prototype._create = function(){
    var t = this,p=t.param,css=p.css,c='',id=p.id,jq = p.jq;
    
    c+=ut.tag({id:id.icon,css:css.icon,style:'position:absolute'});
    c+=ut.tag({id:id.label,css:css.label,value:p.caption,style:'position:absolute'});
    c+=ut.tag({id:id.field,css:css.field,style:'position:absolute'});
    c+=ut.tag({tag:'input',id:id.input,css:css.input,value:p.value,style:'position:absolute'});
    
    c+=ut.tag({tag:'textarea',id:id.memo,value:p.value,css:css.memo,style:'position:absolute;resize: none;outline:none'});

    c+=ut.tag({id:id.text,css:css.text,value:p.value,style:'position:absolute'});
    c+=ut.tag({id:id.btn,css:css.btn,value:p.value,style:'position:absolute'});
    c+=ut.tag({id:id.btn_combo,css:css.btn_combo,style:'position:absolute'});
    c+=ut.tag({id:id.icon_tip,css:css.icon_tip,style:'position:absolute'});
    
    c+=ut.tag({id:id.btn_add,css:css.btn_add,style:'position:absolute'});
    c+=ut.tag({id:id.checkbox,css:css.checkbox,style:'position:absolute'});
    c+=ut.tag({id:id.dim,css:css.dim,style:'position:absolute'});
    
    p.plugin.html(c);
    jq.icon = p.plugin.find('#'+id.icon);
    jq.label = p.plugin.find('#'+id.label);
    jq.field = p.plugin.find('#'+id.field);
    jq.input = p.plugin.find('#'+id.input);
    jq.checkbox = p.plugin.find('#'+id.checkbox);
    jq.text     = p.plugin.find('#'+id.text);
    jq.btn      = p.plugin.find('#'+id.btn);
    jq.btn_combo = p.plugin.find('#'+id.btn_combo);
    jq.icon_tip = p.plugin.find('#'+id.icon_tip);

    jq.btn_add  = p.plugin.find('#'+id.btn_add);
    jq.dim      = p.plugin.find('#'+id.dim);
    jq.memo = p.plugin.find('#'+id.memo);
    
    jq.btn.mbtn({css:{disable:css.disable}});
    jq.btn_combo.mbtn({css:{disable:css.disable}});
    jq.btn_add.mbtn({css:{disable:css.disable}});
    
    p.modal.append(ut.tag({
        id:id.combo,
        css:css.combo,
        style:"position:absolute;display:none",
        
    }));
    jq.combo = p.modal.find('#'+id.combo);
    
    
    c =ut.tag('<',{id:id.tip,css:css.tip,style:'position:absolute;display:none'});
        c+=ut.tag({css:css.tipShadow,style:'position:absolute;transform: rotate(45deg);'});
        c+=ut.tag('<',{css:css.tipBorder,style:'position:absolute'});
            c+=ut.tag({css:css.tipArrow,style:'position:absolute;transform: rotate(45deg);'});
            c+=ut.tag('<',{css:css.tipUp,style:'position:absolute'});
                c+=ut.tag({css:css.tipText,style:'position:absolute'});
            c+=ut.tag('>');
            c+=ut.tag({css:css.tipBtn,style:'position:absolute',value:'&#215;'});
        c+=ut.tag('>');
    c+=ut.tag('>');
    
    p.plugin.append(c);
    var tip = p.plugin.find('#'+id.tip);
    jq.tip={
        frame   :tip,
        modal   :undefined,  
        shadow  :tip.find('.'+css.tipShadow),
        border  :tip.find('.'+css.tipBorder),
        arrow   :tip.find('.'+css.tipArrow),
        up      :tip.find('.'+css.tipUp),
        text    :tip.find('.'+css.tipText),
        btn     :tip.find('.'+css.tipBtn),
    };
    
    
    t._updateArrange();

    
    t._event();
};

Tjedit.prototype._event = function(){
    var t = this,p=t.param,jq=p.jq;
    jq.btn_combo.mbtn({click(){
        if (p.readOnly) return;
        
        if (!t.do('btnComboClick'))
            t.combo('open');
    }});
    
    jq.btn.mbtn({click(){
        if (p.readOnly) return;
        
        if (!t.do('btnClick'))
            t.combo('open');
    }});
    
    jq.btn_add.mbtn({click(){
        if (p.readOnly) return;
        t.do('btnAddClick');
    }});
    
    jq.memo.on('keyup',()=>{
        if (p.readOnly) return;
        
        t.begin('draw');
        t.put({value:jq.memo.val()});
        t.end('draw');
        
        t.changed(t.changed());
        
    });


    jq.input.on('focusout',()=>{
        if ((t.param.changeOnKeyEnter)&&(t.changed())){
            t.do("change",{enableChange:true});
        }
    });
    jq.input.on('keyup',()=>{
        if (p.readOnly) return;
        
        t.begin('draw');
        t.put({value:jq.input.val()});
        t.end('draw');
        t.changed(t.changed());
    });
    jq.input.on('paste',()=>{
        if (p.readOnly) return;
        // ставлю задержку на реакцию вставки paste, так как событие срабатывает до того как меняется атрибут attr
        setTimeout(()=>{
            t.begin('draw');
            t.put({value:jq.input.val()});
            t.end('draw');
            t.changed(t.changed());
        },1000);
    });

    jq.input.on('mouseup',()=>{
        if (p.readOnly) return;
        if (p.attr.type==='number'){
            t.begin('draw');
            t.put({value:jq.input.val()});
            t.end('draw');
            t.changed(t.changed());
        }    
    });
    
    jq.input.on('keydown',e=>{
        if (p.readOnly) return;
        
        if(e.which == 13){
            if (t.param.changeOnKeyEnter){
                t.do("change",{enableChange:true});
                t.changed(false);
            }

            if (t.eventDefined('keyEnter')){
                t.do('keyEnter')
                e.preventDefault();
                return;
            }    
        };
        
        if(e.which == 38){
            if (t.eventDefined('keyArrowUp')){
                t.do('keyArrowUp');
                e.preventDefault();
                return;
            }    
        };
        
        if(e.which == 40){
            if (t.eventDefined('keyArrowDown')){
                t.do('keyArrowDown');
                e.preventDefault();
                return;
            }    
        };
        /*
        if(e.which == 8){
            if (t.eventDefined('keyBackspace')){
                t.do('keyBackspace');
                //e.preventDefault();
                return;
            }    
        };
        */
        t.do('keyDown');
        
    });


    jq.input.on('keyup',e=>{
        if (p.readOnly) return;
        
        if(e.which == 8){
            if (t.eventDefined('keyBackspace')){
                t.do('keyBackspace');
                //e.preventDefault();
                return;
            }    
        };

    });


    jq.label.on('click',()=>{
        if (p.readOnly) return;
        
        if (p.type==='checkbox')
            jq.checkbox.trigger("click");
        
    });
    
    jq.checkbox.on('click',()=>{
        if (p.readOnly) return;
        
        if (!t.attr('disable'))
            t.attr("checked",!t.attr("checked"));    
    });
    
    jq.tip.btn.on("click",()=>{
        t.put({tip:{show:false}});
    });
    
    jq.icon_tip.on("click",()=>{
        t.put({tip:{show:true}});
    });
};

Tjedit.prototype.changed = function(bool){
    var t = this,p=t.param,jq=p.jq;
    
    if (bool === undefined)
        return t.attr('value')!==p._storyValue;
    
    if (bool){
        jq.input.addClass(p.css.changed);
        jq.text.addClass(p.css.changed);
        jq.btn.addClass(p.css.changed);
        jq.memo.addClass(p.css.changed);
    }else{
        jq.input.removeClass(p.css.changed);
        jq.text.removeClass(p.css.changed);
        jq.btn.removeClass(p.css.changed);
        jq.memo.removeClass(p.css.changed);
        p._storyValue = t.attr("value");
    }    
        
};
Tjedit.prototype._css = function(css){
    var t = this,p=t.param;
    if (css===undefined)
        return p.css;
        
    for (var key in css) {    
        if (key in p.css)
            p.plugin.find('.'+p.css[key]).removeClass(p.css[key]).addClass(css[key]);
    }
    
    p.css=$.extend(true,p.css,css);
};

Tjedit.prototype.trigger = function(to,ev,p){
    var t = this,p=t.param,jq = p.jq,obj;
    if ((to === 'memo') && (p.type==="memo"))
        obj = jq.memo;
  
    if ((to === 'edit')&&(p.type==="edit"))
        obj = jq.input;
    
    if ((to === 'button')&&(p.type==="button"))
        obj = jq.btn;
        
    if (obj) 
        obj.trigger(ev,p);
};    
    
Tjedit.prototype.focus = function(){
    var t = this,p=t.param;
    
    if (p.type==='edit'){
        p.jq.input.focus();
    }
}
/** интерфейс для combo
 combo() - возвращает ссылку на объект
 combo('ready') - признак что комбо создан
 combo('clear') - очистка строк грид
 combo('free')  - очистка строк и структуры
 combo('opened')  - признак что комбо открыт
 combo('open') - открывает комбо
 combo('close') - закрывает combo
 combo(json|array)  - передача данных и настроек в combo
*/
Tjedit.prototype.combo = function(param,param2){
    var t = this,p=t.param,c=p.jq.combo,first;

    if (param === undefined)
        return c;
        
    if ($.isArray(param))
        param = {data:param};
    
    if (typeof param === 'string'){
        if (param==='ready'){
            return c.grid('ready');
            
        }else if (param==='clear'){
            if (c.grid('ready'))
                c.grid('clear');

        }else if (param==='select'){
            if (c.grid('ready')){
                var tr = c.grid('find',param2);
                if (tr.length){
                    c.grid('unselect');
                    c.grid('select',tr);
                    if (p.combo.valueKey!=='')
                        p.combo.value = $D(tr)[p.combo.valueKey];
                    t.put({value:$D(tr)[p.combo.key]});
                }    
                
            }    
        }else if (param==='selected'){
            return c.grid('selected');
        }else if (param==='data'){
            if (c.grid('ready')){
                var sel = c.grid('selected');
                return sel.length?$D(sel[0]):undefined;
            }
            return undefined;
            
        }else if (param==='opened'){

            return ((c.grid('ready'))&&(JX.visible(c)));
                                
        }else if (param==='free'){
            if (c.grid('ready')){
                c.grid('free');
                c.grid({fields:[]});
            }    
        }else if (param==='open'){
            
            if (c.grid('ready')){
                c.detach().appendTo(p.modal);                
                
                JX.visible(c,true);
                c.grid('update');
                c.jshadow('show',{opacity:p.comboShadow});
            }
            
        }else if (param==='close'){
            if (c.grid('ready')){
                JX.visible(c,false);
                c.jshadow('hide');
            }    
        };
        
    }else{
        
        if (!$.isEmptyObject(param)){
            p.combo = $.extend(false,p.combo,param);
            
            if ('height' in p.combo)
                JX.pos(c,{h:p.combo.height});
            
            if ('data' in p.combo){
                first=!c.grid('ready');
                
                c.grid(p.combo);
                            
                if (first) {
                    c.grid('attach','click',function(o){
                        if (!t.do('comboClick',o)){
                            if (p.combo.valueKey!=='')
                                p.combo.value = $D(o.tr)[p.combo.valueKey];

                            t.put({value:$D(o.tr)[p.combo.key]});
                            
                            t.changed(t.changed());
                            t.combo('close');
                        }    
                    });
                    c.jshadow({
                        opacity:0.01,
                        show:false,
                        toBack:true,
                        click:
                        function(){
                            t.combo("close");
                        }
                    });
                    
                }    
            }
        }    
    }
    
        
    t.align();    
};
/** установка режима disable 
*/
Tjedit.prototype.disable = function(bool){
    var t=this,p=t.param,jq=p.jq,arr=[],css=p.css,i;
    if (bool === undefined)
        return p.disable;
    
    p.disable = bool;
    jq.input.prop('readonly',(p.disable));
    jq.memo.prop('readonly',(p.disable));
    
    arr = [jq.text,jq.input,jq.label,jq.field,jq.checkbox,jq.dim,jq.memo];
    
    for(i=0;i<arr.length;i++)
        bool?arr[i].addClass(css.disable):arr[i].removeClass(css.disable);

    arr = [jq.btn_add,jq.btn,jq.btn_combo];
    
    for(i=0;i<arr.length;i++)
        arr[i].mbtn('disable',bool);
        
};
/** установка ширин внутренних компонентов
 * widths() - возвращает текущие ширины объктов
 * widths('update') - обновляет ширины по сохраненным данным
 * width(json) - устанвока новых ширин
*/
Tjedit.prototype.widths = function(o){
    var t=this,p=t.param,jq=p.jq;
    if (o===undefined)
        return p.widths;
        
    if (o!=='update')    
        p.widths=$.extend(true,p.widths,o);
        
    if ('label' in p.widths)
        JX.pos(jq.label,{w:p.widths.label});
    if ('value' in p.widths){
        JX.pos(jq.field,{w:p.widths.value});
        JX.pos(jq.memo,{w:p.widths.value});
    }    
    if ('btn_combo' in p.widths)
        JX.pos(jq.btn_combo,{w:p.widths.btn_combo});
    if ('btn_add' in p.widths)
        JX.pos(jq.btn_add,{w:p.widths.btn_add});
    if ('dim' in p.widths)
        JX.pos(jq.dim,{w:p.widths.dim});
    
        
};
/** 
 * метод берет значение из data[p.field] и устанавливает его в value, предваритекльно
 * вызывает обработчик для значения onSetData, в котором можно изменить выводимое значение value
 * 
*/
Tjedit.prototype.setData = function(data){
    var t=this,p=t.param;
    if ((p.field===undefined) || (typeof data!=='object') || ( data[p.field]===undefined)) return;
    
    var prm = 
    {
        field:p.field,
        data:data
    };
    
    if (t.combo('ready')&&(p.type==='button')&&((p.combo.key!=='')||(p.combo.valueKey!=='')))
        prm.valueKey = data[p.field];
    else
        prm.value = data[p.field];
    
    t.do('setData',prm);
    if ('valueKey' in prm)
        t.attr("valueKey",prm.valueKey);
    else
        t.attr("value",prm.value);
};
/** 
 * метод помещает в data[p.field] значение value предварительно вызва обработчик onGetData
*/
Tjedit.prototype.getData = function(data){
    var t=this,p=t.param;
    if (p.field===undefined){
        if (typeof data !=='object') 
            return {}
        else
            return data;
    };
    
    var prm = {value:t.attr('value'),field:p.field,data:data};
    
    t.do('getData',prm);
    
    if (typeof data !=='object'){ 
        var out = {};
        out[p.field] = prm.value;
        return out;
    }else{
        data[p.field] = prm.value;
        return data;
    }    
    
    
};
Tjedit.prototype.clear = function(){
    var t=this;
    t.put({value:""});
};
Tjedit.prototype.put = function(o){
    var t=this,p=t.param,l=p.lock;
    
    l.lock('align');
    
    $.each(o,function(n,v){
        t.attr(n,v);
    });
    
    if (l.unlock('align')){
        t._updateArrange();
        t.align();
    }    
};    

Tjedit.prototype.attr = function(n/*v*/){
    var t=this,p=t.param,v,r=(arguments.length===1),jq = p.jq,css=p.css;
    /*-----------------------------------*/
    if (arguments.length===0) return;
    /*-----------------------------------*/
    if (!r) v=arguments[1];
    /*-----------------------------------*/
    if (n==='cssPref'){
        if (r) 
            return p.cssPref;
        else if (v!==p.cssPref){
            
            ut.delPref(css,p.cssPref);
            
            p.cssPref = v;
            var newCSS = $.extend(true,{},css);
            ut.addPref(newCSS,v);
            
            t._css(newCSS);
           
        }    
    }
    /*-----------------------------------*/
    if (n==='css'){
        if (r) 
            return p.css;
        else    
           t._css(v);
    }
    /*-----------------------------------*/
    if (n==='comboShadow'){
        if (r) 
            return p.comboShadow;
        else    
           p.comboShadow = v;
    }
    /*-----------------------------------*/
    if (n==='readOnly'){
        if (r) 
            return p.readOnly;
        else{    
            p.readOnly = v?true:false;
            jq.memo.attr('readonly', p.readOnly);
            jq.input.attr('readonly', p.readOnly);
        }   
    }
    /*-----------------------------------*/
    if (n==='field'){
        if (r) 
            return p.field;
        else{    
           p.field = ((typeof v==='string')&&(v.length>0)?v:undefined);
        }   
    }
    /*-----------------------------------*/
    if (n==='error'){
        if (r) 
            return p._error;
        else{    
           v = v?true:false;
           if (v!==p._error){
                p._error = v;
                [jq.label,jq.input,jq.text,jq.memo,jq.btn].forEach((e,i)=>{
                    (v?e.addClass(p.css.error):e.removeClass(p.css.error));
                });
           }
           
           
           
        }   
    }
    /*-----------------------------------*/

    if (n==='boolAsText'){
        if (r) 
            return p.boolAsText;
        else    
           p.boolAsText = ((Array.isArray(v))&&(v.length>1)?v:false);
    }
    /*-----------------------------------*/
    if (n==='changeOnKeyEnter'){
        if (r) 
            return p.changeOnKeyEnter;
        else    
           p.changeOnKeyEnter =  v?true:false;
    }
    /*-----------------------------------*/
    if (n==='tip'){
        if (r) 
            return p.tip;
        else{
            
            if (typeof(v)==='string'){
                p.tip.msg = v;
                p.tip.show = true;
            }else
                p.tip = $.extend(true,p.tip,v);
                
                
            t.tip();
            
        }
           
    }
    /*-----------------------------------*/
    if (n==='alignGlobal'){
        if (r) 
            return p.alignGlobal;
        else    
           p.alignGlobal =  v?true:false;
    }
    /*-----------------------------------*/
    
    if (n==='disable'){
        if (r) 
            return t.disable();
        else
            t.disable(v);
    }
    /*-----------------------------------*/
    if (n==='alignCheckbox'){
        if (r) 
            return p.alignCheckbox;
        else
            p.alignCheckbox = v;
    }
    /*-----------------------------------*/
    if (n==='checked'){
        if (r)
            return jq.checkbox.hasClass(css.checked);
        else{    
            if (v)
                jq.checkbox.addClass(css.checked);
            else    
                jq.checkbox.removeClass(css.checked);
                
            t.do("change");    
        }    
    }    
    /*-----------------------------------*/
    if (n==='valueKey'){
        if (r){
            return p.combo.valueKey!==''?p.combo.valueKey:p.combo.key;
        }else{
            /** вставка значения в combo*/
            if (t.combo('ready')&&((p.combo.key!=='')||(p.combo.valueKey!==''))){
                var key = p.combo.valueKey!==''?p.combo.valueKey:p.combo.key;

                var tr = jq.combo.grid('find',function(d){
                    return $D(d.tr)[key]==v;
                });
                    
                if (tr.length){
                    v = $D(tr)[p.combo.key];
                    jq.combo.grid('unselect');
                    jq.combo.grid('select',tr);
                    if (p.combo.valueKey!=='')
                        p.combo.value = $D(tr)[p.combo.valueKey];
                }else
                    v = '';
            }            
            t.attr("value",v);
        }
    }    
    /*-----------------------------------*/
    if (n==='value'){
        if (r){
            
            if (p.type==="memo")
                return jq.memo.val();
            if (p.type==="edit")
                return jq.input.val();
            if (p.type==="text")
                return jq.text.text();
            if (p.type==="button"){
                if (p.combo.valueKey!=='')
                    return p.combo.value;
                else    
                    return jq.btn.text();
            }    
            if (p.type==="checkbox")
                return t.attr('checked');
                
        }else{    
            
            if (p.type!=="checkbox"){
                
                var fmt = {value:v};
                t.do('draw',fmt);
                v = fmt.value;
                
                jq.text.html(v);
                jq.memo.val(v);

                var prev = jq.input.val();
                if (v!==prev){
                    jq.input.val(v);
                    
                    t.changed((prev!=='')&&(v!==p._storyValue));                
                }
                
                jq.btn.html(v);
                t.do("change");
                
            }else
                t.attr('checked',v?true:false);
        }    
    }
    /*-----------------------------------*/
    if (n==='text'){
        if (r){
            if (p.type==="memo")
                return jq.memo.val();
            if (p.type==="edit")
                return jq.input.val();
            if (p.type==="text")
                return jq.text.text();
            if (p.type==="button")
                return jq.btn.text();
            if (p.type==="checkbox"){
                let res = t.attr('checked');
                if (p.boolAsText!==false)
                    return res?p.boolAsText[1]:p.boolAsText[0];
                else
                    return res;
            }    
        };    
    }
    
    /*-----------------------------------*/
    if (n==='caption'){
        if (r) 
            return p.caption;
        else{    
           jq.label.html(v);
           p.caption = v;
        }   
    }
    /*-----------------------------------*/
    if (n==='dim'){
        if (r) 
            return p.dim;
        else{    
           jq.dim.html(v);
           p.dim = v;
        }   
    }
    /*-----------------------------------*/
    if (n==='gapLabel'){
        if (r) 
            return p.gapLabel;
        else    
           p.gapLabel = v;
    }
    /*-----------------------------------*/
    if (n==='topLabel'){
        if (r) 
            return p.topLabel;
        else    
           p.topLabel = v?true:false;
    }
    /*-----------------------------------*/
    if (n==='align'){
        if (r) 
            return p.align;
        else    
           p.align = v;
    }
    /*-----------------------------------*/
    if (n==='margin'){
        if (r) 
            return p.margin;
        else    
           p.margin = $.extend(false,p.margin,JX.margin(v));
    }
    /*-----------------------------------*/
    if (n==='width'){
        if (r) 
            return JX.pos(p.plugin).w;
        else{
            if ((typeof v === 'number')&&(v>0)){    
                p.width = v;
                JX.pos(p.plugin,{w:v});
           }
        }   
    }
    /*-----------------------------------*/
    if (n==='widths'){
        if (r) 
            return p.widths;
        else
            t.widths(v);
    }
    /*-----------------------------------*/
    if (n==='type'){
        if (r) 
            return p.type;
        else
            p.type = v;    
    }
    /*-----------------------------------*/
    if (n==='combo'){
        if (r){
            
        }else
            t.combo(v);
    }
    /*-----------------------------------*/
    if (n==='disables'){
        if (r)
            return p.disables;
        else
            p.disables= $.extend(false,p.disables,v);
    }
    /*-----------------------------------*/
    if (n==='arrange'){
        if (r)
            return p.arrange;
        else
            p.arrange= $.extend(false,p.arrange,v);
    }
    /*-----------------------------------*/
    if (n==='attr'){
        if (r)
            return p.attr;
        else{
            p.attr = $.extend(true,p.attr,v);
            for(var name in p.attr)
                jq.input.attr(name,p.attr[name]);
                
        }    
    }
    /*-----------------------------------*/
    if (n==='prop'){
        if (r)
            return p.prop;
        else{
            p.prop = $.extend(true,p.prop,v);
            for(var name in p.prop)
                jq.input.prop(name,p.prop[name]);
                
        }    
    }
    /*-----------------------------------*/
    if (n==='onBtnComboClick'){
        if (r)
            return t.on('btnComboClick');
        else
            t.on('btnComboClick',v);
         
    }
    /*-----------------------------------*/
    if (n==='onBtnClick'){
        if (r)
            return t.on('btnClick');
        else
            t.on('btnClick',v);
    }
    /*-----------------------------------*/
    if (n==='onBtnAddClick'){
        if (r)
            return t.on('btnAddClick');
        else
            t.on('btnAddClick',v);
    }
    /*-----------------------------------*/
    if (n==='onComboClick'){
        if (r)
            return t.on('comboClick');
        else
            t.on('comboClick',v);
    }
    /*-----------------------------------*/
    if (n==='onChange'){
        if (r)
            return t.on('change');
        else
            t.on('change',v);
    }
    /*-----------------------------------*/
    if (n==='onKeyEnter'){
        if (r)
            return t.on('keyEnter');
        else
            t.on('keyEnter',v);
    }
    /*-----------------------------------*/
    if (n==='onKeyBackspace'){
        if (r)
            return t.on('keyBackspace');
        else
            t.on('keyBackspace',v);
    }
    /*-----------------------------------*/
    if (n==='onKeyArrowUp'){
        if (r)
            return t.on('keyArrowUp');
        else
            t.on('keyArrowUp',v);
    }
    /*-----------------------------------*/
    if (n==='onKeyArrowDown'){
        if (r)
            return t.on('keyArrowDown');
        else
            t.on('keyArrowDown',v);
    }
    /*-----------------------------------*/
    if (n==='onKeyDown'){
        if (r)
            return t.on('keyDown');
        else
            t.on('keyDown',v);
    }
    /*-----------------------------------*/
    if (n==='onSetData'){
        if (r)
            return t.on('setData');
        else
            t.on('setData',v);
    }
    /*-----------------------------------*/
    if (n==='onGetData'){
        if (r)
            return t.on('getData');
        else
            t.on('getData',v);
    }
    /*-----------------------------------*/
    if (n==='group'){
        if (r){
            if (typeof jgroup!=='undefined')
                return jgroup.groups(t);
            else
                return [];
        }else
            t.group(v);
    }
    /*-----------------------------------*/
    if (n==='ungroup'){
        if (!r)
            t.ungroup(v);
    }
    /*-----------------------------------*/
    if (n==='btn_add'){
        if (r)
            return jq.btn_add
    }
    /*-----------------------------------*/

    t.align();
};

Tjedit.prototype.tip=function(){
    var t=this,p=t.param,tip = p.tip;
    let delay=()=>{
        if ((tip.show)&&(tip.delay>0)){
            if (tip._delay) clearTimeout(tip._delay);
            tip._delay = setTimeout(function(){
                tip._delay = undefined;
                t.put({tip:{show:false}});
            },tip.delay);
        }
    };
    
    p.jq.tip.text.html(p.tip.msg);
    if (JX.visible(p.jq.tip.frame)!=tip.show){
        
        //JX.visible(p.jq.tip.frame,tip.show);
        if (tip.show)
            p.jq.tip.frame.fadeIn({duration:200,step(){ t._align_tip();}});
        else
            p.jq.tip.frame.fadeOut(200);
        delay();

        if (tip.modal){
            if (tip.show){
                
                /** созддаем панель заведомо выше компонента */
                if (p.jq.tip.modal===undefined){
                    
                    let mid = ut.id('mid');
                    Qs.modal.append('<div id="'+mid+'"></div>');
                    p.jq.tip.modal = Qs.modal.find('#'+mid);

                }else
                    p.jq.tip.modal.detach().appendTo(Qs.modal);
                
                 
                p.jq.tip.modal.jshadow({
                    show:true,
                    opacity:0.01,
                    toBack:true,
                    click(e){
                        t.put({tip:{show:false}});
                    },
                    onhide(){
                        
                    },
                    onshow(){
                    }
                });
            }else{
                p.jq.tip.modal.jshadow("hide");
            }    
        }
    }else{
        delay();        
    }

};    

Tjedit.prototype.begin=function(event){
    var t=this,p=t.param,l=p.lock;
    return l.lock(event);
};

Tjedit.prototype.can=function(event){
    var t=this,p=t.param,l=p.lock;
    return l.can(event);
};

Tjedit.prototype.end=function(event){
    var t=this,p=t.param,l=p.lock;
    return l.unlock(event);
};

Tjedit.prototype.on=function(event,func){
    var t=this,p=t.param,e=p.events;
    if (func===undefined)
        return (event in e?e[event].func:undefined);
    else    
        e[event] = {func:func};
};

Tjedit.prototype.eventDefined=function(event){
    var t=this,p=t.param,e=p.events;
    return ((event in e)&&(e[event].func));
};

Tjedit.prototype.do=function(event,param){
    var t=this,p=t.param,e=p.events,h=p.handler,out,
    prm = $.extend(false,{
        sender:t,
        edit:t,
        plugin:p.plugin,
        event:event,
        enableChange:(!p.changeOnKeyEnter)
    },param);

    if ((event==='change')&&(!prm.enableChange)&&(p.type==='edit')) return;
    
    if (!t.can(event)) return true;
    
    t.begin(event);
    
        out = ((event in e)&&(e[event].func(prm)));
    
        if (out){
            try{
                e[event].func(param);
            }catch(err){
                console.error(err);
            }
        }    
        h.do({group:event,param:prm});    
    
    t.end(event);
    
    return out;
};

Tjedit.prototype.attach=function(o){
    var t = this,h=t.param.handler,
    a=$.extend(false,{
        event:'',
        func:undefined,
        name:ut.id('jedit')
    },o);

    if (a.event==='') return false;
    
    a.group = a.event;
    return h.add(a).name;

};
/** 
 * Ex: detach(name) 
 * Ex: detach(fromEvent,name)
*/
Tjedit.prototype.detach=function(){
    var t = this,p=t.param,h=p.handler,e=p.events,k,a=arguments;
    if (a.length>1)
        h.remove({name:a[1],group:a[0]});
    else
        for (k in e) h.remove({name:a[0],group:k});

};

Tjedit.prototype.align=function(){
    var t=this,p=t.param,l=p.lock;
    
    if (l.lock('align'))
        t._align();
    l.unlock('align');   
    
};

/** расчет упорядоченного массива отображаемых компоентов */
Tjedit.prototype._updateArrange=function(){
    var t=this,p=t.param,ar = p.arrange,i,or=ar.order,o,pos,chrome,jq=p.jq,css=p.css;
    ar._obj = [];
    ar._stretch = false;
    ar._gapLabel = '';
    
    
    for(i=0;i<or.length;i++){
        o=false;
        if (or[i]==='label'){ 
            if (!p.disables.label){
                o=p.jq.label;
            
                if (ar.stretch==='label')
                    ar._stretch=o;    
                
                if (!p.topLabel)
                    ar._obj.push(o)    
                
                if (ar._gapLabel==='')
                    ar.gapLabel = 'left';
                        
            }        
        }else if (or[i]==='icon'){ 
            if (!p.disables.icon){
                o=p.jq.icon;
                if (!p.topLabel)
                    ar._obj.push(o)    
            }        
        }else if (or[i]==='icon_tip'){ 
            if (!p.disables.icon_tip)
                ar._obj.push(p.jq.icon_tip);        
        }else if (or[i]==='value'){
            o=p.jq.field;
                
            if (ar.stretch==='value')
                ar._stretch=o;
            ar._obj.push(o)    

            //if (!p.disables.btn_combo)
            //    ar._obj.push(p.jq.btn_combo)    
                
            if (ar._gapLabel==='')
                    ar.gapLabel = 'right';
                
        }else if (or[i]==='btn'){ 
            if (!p.disables.btn_add)
                ar._obj.push(p.jq.btn_add);    
        }else if (or[i]==='dim'){ 
            if (!p.disables.dim){
                o=p.jq.dim;
            
                if (ar.stretch==='dim')
                    ar._stretch=o;    
                ar._obj.push(o);    
            }        
        }
    }
    
    JX.visible(p.jq.icon,      !p.disables.icon);
    JX.visible(p.jq.icon_tip,  !p.disables.icon_tip);
    
    JX.visible(p.jq.label,      !p.disables.label);
    JX.visible(p.jq.memo,      p.type==='memo');
    JX.visible(p.jq.input,      p.type==='edit');
    JX.visible(p.jq.checkbox,   p.type==='checkbox');
    JX.visible(p.jq.text,       p.type==='text');
    JX.visible(p.jq.btn,        p.type==='button');
    
    JX.visible(p.jq.btn_add,    !p.disables.btn_add);
    JX.visible(p.jq.btn_combo,  !p.disables.btn_combo);
    JX.visible(p.jq.dim,        !p.disables.dim);
    
    p.jq.field.removeClass(css.input_fone).removeClass(css.text_fone).removeClass(css.btn_fone);
    
    if (p.type==='edit')
        p.jq.field.addClass(css.input_fone);
    if (p.type==='text')
        p.jq.field.addClass(css.text_fone);
    if (p.type==='button')
        p.jq.field.addClass(css.btn_fone);
        
    
};
Tjedit.prototype._align_tip=function(){
    let t=this, p=t.param, tip = p.tip, cling ,jq=p.jq.tip, css=p.css,pos,otype,opos,w,h,off,a,s,b,width,height,pivot;
    let obj = {memo:p.jq.memo,edit:p.jq.input,checkbox:p.jq.field,text:p.jq.text,button:p.jq.btn},
        btnComboW=(p.disables.btn_combo?0:JX.pos(p.jq.btn_combo).w);
    
    if (!tip.show) return;

    //let plgn = tip.width==='edit'? obj[p.type]:p.plugin;
    let plgn = p.plugin;
    
    pos = JX.abs(plgn);

    if (tip.place === "bottom")
        pivot=$.extend(true,{a:"left",b:"left"},tip.pivot);
    else if (tip.place === "top")
        pivot=$.extend(true,{a:"left",b:"left"},tip.pivot);
    else if (tip.place === "left")
        pivot=$.extend(true,{a:"center",b:"center"},tip.pivot);   
    else if (tip.place === "right") 
        pivot=$.extend(true,{a:"center",b:"center"},tip.pivot);   

    
    if (tip.width === "auto")
        width = pos.w<300?pos.w:300;
    else if (tip.width === "edit")
        width = JX.pos(obj[p.type]).w+btnComboW;
    else
        width = tip.width;
        
    if (tip.height === "auto"){    
        let hadd = (tip.padding.top?tip.padding.top:0)+(tip.padding.bottom?tip.padding.bottom:0);
        height = (tip.msg.split('<br>').length+1)*14+hadd;
        
        height = (height<(hadd+28))?(hadd+28):(height>120?120:height);
    }else
        height = tip.height;
    
    
    w   = tip.arrW;
    h   = tip.arrW*4;
    
    off = tip.arrOff==="center"?(((tip.place==="left")||(tip.place==="right"))?height/2-w/2:width/2) :tip.arrOff;
    off = off>=0?off:(((tip.place==="left")||(tip.place==="right"))?height+off-w:width+off-w);
    

    if (tip.place === "bottom"){
        let add = {x:0,y:0};
        if (tip.width==='edit'){
            add  = {y:tip.arrW/4,x:btnComboW/2+1};
            plgn = obj[p.type];
        }
        cling={side:{a:"bottom",b:"top"},pivot:pivot,abs:true,off:add};
        a = {w:w,h:w,x:off,y:-w/2+1};   
        s = {w:w,h:w,x:off,y:1};   
        b = {top:w/2};
        if (tip.width==='edit') plgn = obj[p.type];
        
    }else if (tip.place === "top"){ 
        let add = {x:0,y:0};
        if (tip.width==='edit'){
            add  = {y:-tip.arrW/4,x:btnComboW/2+1};
            plgn = obj[p.type];
        }
        
        cling = {side:{a:"top",b:"bottom"},pivot:pivot,abs:true,off:add};   
        a = {w:w,h:w,x:off,y:height-w-1};   
        s = {w:w,h:w,x:off,y:height-w-1};   
        b = {bottom:w/2};   

    }else if (tip.place === "left"){ 

        cling = {side:{a:"left",b:"right"},pivot:pivot,abs:true};   
        a = {w:w,h:w,x:width-w-1,y:off};   
        s = {w:w,h:w,x:width-w-1,y:off};   
        b = {right:w/2};   
        
    }else if (tip.place === "right"){ 
        cling = {side:{a:"right",b:"left"},pivot:pivot,abs:true};   
        a = {w:w,h:w,x:-w/2+1,y:off};   
        s = {w:w,h:w,x:1,y:off};   
        b = {left:w/2};   
    }    
    
    JX.pos(jq.frame,{w:width,h:height});
    JX.cling(plgn,jq.frame,cling);
    

    JX.pos(jq.arrow,a);
    JX.pos(jq.shadow,s);
    JX.stretch(jq.border,{margin:b});
    
    
    JX.stretch(jq.up,{margin:1});
    JX.stretch(jq.text,{margin:tip.padding});
    
    JX.place(jq.btn,{vert:"top",horiz:"right"});
            
    
    
    
};
Tjedit.prototype._align=function(){
    var t=this,p=t.param,ar=p.arrange,pos,jq=p.jq,sy=0,i,chrome=false,a,b,c,chrm=0,css=p.css;
    
    if ((p._alignFirst===undefined)&&(!JX.visiblex(p.plugin))) 
        return;
    p._alignFirst = false;
    

    t.widths('update');

    JX.arrange(ar._obj,{direct:"horiz",type:(ar._stretch?'stretch':'left'),align:p.type!=='memo'?p.align:'top',stretch:ar._stretch,margin:p.margin});
    
    if (!p.disables.label){
        
        pos = JX.pos(jq.label);

        if (p.topLabel){
            
            a=JX.pos(jq.field);
            JX.pos(jq.label,{x:a.x,y:a.y-pos.h-p.gapLabel,w:a.w});

        }else{
            
            pos = {w:pos.w-p.gapLabel,x:pos.x+(ar._gapLabel==='right'?p.gapLabel:0)};
            JX.pos(jq.label,pos);
            
        }    
    };    
    
    pos=JX.pos(jq.field);
    
    if (!p.disables.btn_combo)
        pos.w-=JX.pos(jq.btn_combo).w;
        
    if (p.type==='edit'){
        
        if (((pos.h%2===0)&&(dvc.chromium)) )/*|| ((pos.h%2!==0)&&(!dvc.chromium)))*/
            chrm=1;
        
        JX.pos(jq.input,JX.add({x:pos.x,y:pos.y+1,w:pos.w,h:pos.h-2-chrm},{x:-1,y:-1}));

    }else if (p.type==='text'){    
        JX.pos(jq.text,JX.add(pos,{x:-1,y:-1}));
        
    }else if (p.type==='checkbox'){
        
        var chb = JX.pos(p.jq.checkbox);
        
        if (p.alignCheckbox==='right')
            pos.x = pos.x+pos.w-chb.w;    
        else if (p.alignCheckbox==='center')
            pos.x = pos.x+(pos.w-chb.w)/2;
        pos.y = pos.y+(pos.h-chb.h)/2;
        
        JX.pos(jq.checkbox,{x:pos.x-1,y:pos.y-1});
        
    }
    else if (p.type==='button'){    
        
        JX.pos(jq.btn,JX.add(pos,{x:-1,y:-1})); 
    }
    else if (p.type==='memo'){    
        JX.pos(jq.memo,JX.set(pos,{dx:-1,h:JX.pos(p.plugin).h-4,dw:-4,dy:-1}));
    }

    if (!p.disables.btn_combo)
        JX.pos(jq.btn_combo,{x:pos.x+pos.w+p.off.btn_combo.x,y:pos.y+p.off.btn_combo.y});
    
    
    if ((p.combo.data)&&(JX.visible(jq.combo))){
        a = JX.abs(jq.field);
        
        c = {x:a.x,w:a.w+1,y:a.y+a.h};
        
        JX.abs(jq.combo,c);
        
        jq.combo.grid("align");
        
        b = JX.abs(jq.combo);
        if (b.y+b.h>JX.screen().h)
            JX.abs(jq.combo,{y:a.y-b.h});
        
    };
    
    t._align_tip();
    
};
  