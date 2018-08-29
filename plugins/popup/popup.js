/*global Qs,$,JX,ut*/

var _popup={param:{}};

function popup(o){
    var p=_popup.param;
    if (!p.own){
        _popup.param=$.extend(true,{
            own:((Qs&&Qs.modal)?Qs.modal:$('body')),
            /*stik:((Qs&&Qs.center)?Qs.center:$('body')),*/
            stik:((Qs&&Qs.page)?Qs.page:$('body')),
            h:36,
            w:250,
            gap:5,
            off:{x:10,y:10},
            delay:3000,
            css:{
                frame:"popup_frame",
                msg:"popup_msg",
                alert:"popup_alert"
            }
        },o);
        p=_popup.param;
        p.frame=p.own.append(ut.tag({css:p.css.frame,style:"position:absolute;left:0px;top:0px;z-index:2000;border:0px;width:0px;height:0px"})).find('.'+p.css.frame);
    }
    _popup._out(o);
}

_popup._out=function(o){
    var p=_popup.param,pos,s,css;
    
    var a=$.extend(true,{
        msg:"no message...",
        type:"msg",
        w:p.w,
        h:p.h,
        css:p.css,
        delay:p.delay
    },o);
    
    var last = p.frame.children().last();
    if (a.type=="msg") 
        css=a.css.msg;
    else
        css=a.css.alert;
    a.w = Math.max(a.w,6*a.msg.length);
    
    s=JX.abs(p.stik);
    s.w-=20;
    s.h-=20;
    if (last.length>0){
        var l = JX.abs(last);
        pos = {x:(s.x+s.w)-a.w-p.off.x,y:l.y-a.h-p.gap,w:a.w,h:a.h};
    }else
        pos = {x:(s.x+s.w)-a.w-p.off.x,y:(s.y+s.h)-a.h-p.off.y,w:a.w,h:a.h};
    
    var c=ut.tag({css:css,value:a.msg,pos:pos,style:"position:absolute;line-height:"+a.h+"px"});
    p.frame.append(c);
    if (a.type!=='msg') console.error(a.msg);
    
    last = p.frame.children().last();
    last.hide(0).fadeIn(100,function(){
        last.fadeOut(a.delay,function(){last.remove();});
    });
};
