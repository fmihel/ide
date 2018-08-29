/*global $,JX,ut,Qs*/
function comin(o){
    var t=this;
    t.param=$.extend(true,{
         id:ut.id('comin'),
         own:(Qs?Qs.body:$('body')),
         up_h:32,
         down_h:40,
         btn_close_w:16,
         height:300,
         onAssept:undefined,
         story:{},
         css:{
             frame:'cm_frame',
             
             up:'cm_up',
             title:'cm_title',
             btn_close:'cm_btn_close',
             center:'cm_center',
             down:'cm_down',
             btn_set:'cm_btn_set',
             table_frame:'cm_table_frame',
             
             header:'cm_header',
             row:'cm_row',
             
             edit:'cm_edit'
         }       
    },o);
    
    
    t._init();
    t.align();
}

comin.prototype._init=function(){
    var t=this,p=t.param,css=p.css,c='';
    c+=ut.tag('<',{id:p.id,css:css.frame,style:'position:absolute',pos:{h:p.height}});
    
    c+=ut.tag('<',{css:css.up,style:'position:absolute;overflow:hidden',pos:{h:p.up_h}});
        c+=ut.tag({css:css.title,style:'position:absolute;overflow:hidden;line-height:'+p.up_h+'px',value:'Inspector',pos:{h:p.up_h}});
        c+=ut.tag({css:'ui-icon ui-icon-closethick '+css.btn_close,style:'position:absolute',pos:{w:p.btn_close_w,h:p.btn_close_w}});
    c+=ut.tag('>');
    c+=ut.tag('<',{css:css.center,style:'position:absolute;overflow:hidden'});
    c+=ut.tag({css:css.table_frame,style:'position:absolute;overflow-x:hidden;;overflow-y:auto'});
    c+=ut.tag('>');
    c+=ut.tag('<',{css:css.down,style:'position:absolute;overflow:hidden',pos:{h:p.down_h}});
        c+=ut.tag({css:css.btn_set,style:'position:absolute;line-height:'+(p.down_h-14)+'px',pos:{w:118,h:p.down_h-14},value:'Assept'});
    c+=ut.tag('>');
    c+=ut.tag('>');
    p.own.append(c);
    
    p.frame=p.own.find('#'+p.id);
    
    
    p.up=p.own.find('.'+css.up);
        p.title     = p.up.find('.'+css.title);
        p.btn_close = p.up.find('.'+css.btn_close);
        
    p.center=p.own.find('.'+css.center);
    p.down=p.own.find('.'+css.down);
        p.btn_set = p.down.find('.'+css.btn_set);
        
    p.table_frame=p.own.find('.'+css.table_frame);
    
    p.table = ut.table_create({own:p.table_frame,style:'border:0px;width:100%;border-collapse: collapse;',attr:{'cellspacing':'0','cellpadding':'0','border':'0'}});
    
    ut.table_header({
        table:p.table,
        css:{tr:css.header},
        caption:[{text:'',width:'40%'},''],
    });
    
    t._event();
    JX.visible(p.frame,false);
};
comin.prototype._event=function(){
    var t=this,p=t.param;

    p.frame.draggable({ handle: p.title });
    p.frame.jshadow({opacity:0.1,onhide:function(){t.show({show:false});}});
    p.btn_close.on("click",function(){p.frame.jshadow("hide");});
    
    p.btn_set.on("click",function(){ t.doAssept();});

};
comin.prototype.doAssept=function(){
    var t=this,p=t.param,out;
    if (p.onAssept){ 
        out = t.get();
        t.storyVarSave(out);
        p.onAssept({sender:t,data:out});
    }else
        console.warn('onAssept not defined');
    t.show({show:false});
    p.frame.jshadow("hide");
};    

comin.prototype.show=function(o){
    var t=this,p=t.param;
    var a=$.extend(true,{
        show:true,
        pos:{x:0,y:0},
        caption:'Inspector'
    },o);
    
    if (JX.visible(p.frame)!==a.show){
        if (a.show)
            p.frame.jshadow("show");
            
        p.title.html(a.caption);
        JX.pos(p.frame,a.pos);
        JX.visible(p.frame,a.show);
        t.align();
    }
};

comin.prototype.align=function(){
    var t=this,p=t.param;
    JX.arrange([p.up,p.center,p.down],{direct:"vert",type:"stretch",align:"stretch",stretch:[{idx:1}]});
    JX.arrange([p.title,p.btn_close],{direct:"horiz",type:"stretch",align:"center",stretch:[{idx:0}],margin:{left:10,right:10}});
    JX.pos(p.btn_close,{y:10});
    
    JX.stretch(p.table_frame,{margin:{left:2,right:2}});

    JX.place(p.btn_set,{vert:"center",horiz:"center"});
    
};

comin.prototype.clear=function(){
    var t=this,p=t.param;
    ut.table_clear({table:p.table});    
};

comin.prototype.get=function(){
    var t=this,p=t.param,vr,res=[],tr,css=p.css,
    rows = ut.table_rows({table:p.table});
    
    for(var i=0;i<rows.length;i++){
        vr=$.data(rows[i],'var');
        tr=$(rows[i]);

        if (vr.type=='edit'){
            res.push({name:vr.name,value:tr.find('.'+css.edit).val()});
        }
        
    }
    return res;
    
};
comin.prototype.assept=function(event){
    
    this.param.onAssept=event;
    
};

/** используем предудущие сохраненные настройки как значения по умолчанию*/
comin.prototype.storyVarLoad=function(to){
    var t=this,p=t.param,story=p.story;
    $.each(to,function(i,o){
        if (story[o.name]!==undefined){
            to[i].value = story[o.name];
        }
    });
    
};
/** сохраняем предыдущие настройки, чтобы использовать их в последующем по умолчанию*/
comin.prototype.storyVarSave=function(from){
    var t=this,p=t.param,story=p.story;
    $.each(from,function(i,o){
            story[o.name]=o.value;
    });
        
};
comin.prototype.add=function(o){
    var t=this,p=t.param,css=p.css,c1,c2;
    if (Array.isArray(o)){
        t.storyVarLoad(o);
        var res = [];
        for(var i=0;i<o.length;i++){
            o[i].forced = false;
            res.push(t.add(o[i]));
        }
        p.table.find('input').first().focus();
        t.align();
        return res;
    }else{
        
        var a=$.extend(true,{
            name:'',
            value:'',
            type:'edit',
            forced:true,
            notes:'',
        },o);
        
        c1=a.name;
        c2=a.value;
    
        if (a.type=='edit'){
            c2=ut.tag({tag:'input',attr:{placeholder:a.notes},value:a.value,css:css.edit});
        }
        
        var tr = ut.table_add({table:p.table,cols:[c1,c2],css:{tr:css.row}});
        $.data(tr[0],'var',a);
        tr.find('input').on('keypress',function(e){
            if ( e.which == 13 ) 
                t.doAssept();
        });
        
        if (a.forced) t.align();
        return tr;    
    }
};

