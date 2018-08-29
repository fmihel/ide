/*global JX,$,Qs,Ws,ut*/

function jfunc_hint_test () {

    jfunc_hint({msg:'test msg..'});

}

function jfunc_hint(o){
    var t= jfunc_hint,p=t.param;
    var a= $.extend(true,{
       msg:'...' 
    },o);
    
    if (t.show_in_last()){
        p.msg.text(a.msg);

        if(!JX.visible(p.frame)){
            JX.visible(p.frame,true);
            t._align();            
            JX.visible(p.frame,false);
            
            p.frame.fadeIn(p.speed,function(){
                t._align();
            });
        }
        t._align();
    }
}

jfunc_hint.init=function(o){
    var t=this;
    var a=$.extend(true,{
        own:Qs.body,
        stick:Qs.body,
        enable:true,
        speed:200,
        css:{
            frame:'fh_frame',
            btn:'fh_btn',
            msg:'fh_msg',
            show_in_last:'fh_show_in_last',
            no_show_in_last:'fh_no_show_in_last'
        }
    },o);
    t.param = a;
    t._create();
    t._event();
    t.show_in_last(a.enable);
    Ws.align(function(){t._align();});
};

jfunc_hint.hide=function(){
    var t=this,p=t.param;
    p.frame.fadeOut(p.speed);
};

jfunc_hint.show_in_last=function(o){
    var t=this,p=t.param,css=p.css;
    if(o===undefined)
        return p.enable;
    p.enable=o;
    
    if (o===true)
        p.show_in_last.removeClass(css.no_show_in_last);
    else
        p.show_in_last.addClass(css.no_show_in_last);
};

jfunc_hint._create=function(){
    var t=this,p=t.param,css=p.css,code='';
    p.id = ut.id('jfunc_hint');
    code+=ut.tag('<',{id:p.id,css:css.frame,style:'position:absolute;display:none'});
    code+=ut.tag({css:css.msg,style:'position:absolute'});
    code+=ut.tag({css:css.show_in_last,style:'position:absolute'});
    code+=ut.tag({css:css.btn,style:'position:absolute'});
    code+=ut.tag('>');
    p.own.append(code);
    
    p.frame = p.own.find('#'+p.id);
    p.msg = p.own.find('.'+css.msg);
    p.show_in_last = p.own.find('.'+css.show_in_last);
    
    p.btn = p.own.find('.'+css.btn);
    
};

jfunc_hint._align=function(){
    if (!editors) return;
    var cur = editors.current();
    if (!cur) return;
    
    var t=this,p=t.param;
    
    
    var stick=(editors?editors.current().panel:p.stick);
    
    var o = JX.abs(stick);
    var frame = JX.pos(p.frame);
    
    /*if(pos_top)
        JX.abs(p.frame,{x:o.x,y:o.y,w:o.w});
    else
        JX.abs(p.frame,{x:o.x,y:o.y+o.h-frame.h,w:o.w});    
    */
    
    JX.abs(p.frame,{x:o.x+o.w-frame.w,y:o.y+o.h});
    
    JX.arrange([p.msg,p.show_in_last,p.btn],{direct:'horiz',type:'stretch',align:'center',stretch:p.msg,margin:{left:10,right:2},gap:2});
};

jfunc_hint._event=function(){
    var t=this,p=t.param;
    
    p.btn.on('click',function(){
        t.hide();
    });
    p.show_in_last.on('click',function(){
        t.show_in_last(!t.show_in_last());
    });
    
};