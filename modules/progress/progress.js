var progress={
param:null,
_is_init:false,
init:function(o){
    var t = progress;
    t.param = $.extend(true,{
        own:$('body'),
        
        stick:{by:null,vert:"center",horiz:"right"},
        pos:{x:0,y:0},
        caption:'',
        all:100,
        current:0,
        css:{
            frame:"prgs_frame",
            indikator:"prgs_indikator",
            caption:"prgs_caption"
        }
    },o);
    
    var p=t.param,code='',
    id_frame = ut.id("prgs"),
    id_ind = ut.id("id"),
    id_caption = ut.id("cap");
    
    code = ut.tag('<',{id:id_frame,css:p.css.frame,style:'position:absolute'});
    code+= ut.tag({id:id_ind,css:p.css.indikator,style:'position:absolute'});
    code+= ut.tag({id:id_caption,css:p.css.caption,style:'position:absolute'});
    code+= ut.tag('>');
    
    p.own.append(code);
    p._frame = p.own.find('#'+id_frame);
    p._ind = p.own.find('#'+id_ind);
    p._caption = p.own.find('#'+id_caption);
    t._is_init = true;
    
},
align:function(){
    
    var t=progress,p=t.param,a,b;
    if (!t._is_init) return;
    if (!t.visible()) return;
    
    
    if (nil(p.stick.by))
        JX.abs(p._frame,p.pos);
    else{
        a = JX.abs(p.stick.by);
        b = JX.abs(p._frame);
        JX.abs(p._frame,{x:a.w-b.w-24,y:a.y+3, w:p.pos.w,h:p.pos.h});
        
    }    
    
    
    JX.stretch(p._caption,{margin:2});
    
    var pos = JX.pos(p._caption);
    pos.w = ut.translate(p.current,0,p.all,0,pos.w);
    
    JX.pos(p._ind,pos);
},
visible:function(){
    var t=progress,p=t.param;
    
    if (arguments.length==0)
        return JX.visible(p._frame);
    
    JX.visible(p._frame,arguments[0]);
    
    if (arguments[0]) 
        t.align();
},    
do:function(o){
    
    var t=progress,p=t.param,
    
    a=$.extend(true,{
        all:p.all,
        current:0,
        caption:p.caption
    },o);
    
    p.caption   = a.caption;
    p.all       = a.all;
    p.current   = a.current;
    
    p._caption.html(p.caption);
    
    t.visible(true);    
    
}
};