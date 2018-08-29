/*global JX,$,ut,Qs*/
function JDIALOG(o){
    return new jdialog(o);
}
function jdialog(o){
    var t=this,i,
    p=$.extend(true,{
        own:(Qs?Qs.modal:$('body')),
        id:ut.id('jdialog'),
        pos:{w:350,h:200,x:10,y:10},
        heights:{header:48,footer:48,close_btn:32,button:24},
        
        caption:'Message',
        msg:'Text in dialog message..',
        modal:true,
        shadow_opacity:0.5,
        onClick:undefined,
        buttons:[],
        draggable:true,
        strip:false,
        close_btn_enable:true,
        margin:{left:10,right:10},
        place:{
            align:"screen",/*"custom",jq*/
            vert:"center",
            horiz:"center",
            margin:{left:0,top:-170,bottom:0,right:0}
        },
        css:{
            frame:'jd_frame',
            header:'jd_header',
            center:'jd_center',
            footer:'jd_footer',
            strip:'jd_strip',
            
            caption:'jd_caption',
            close_btn:'jd_close_btn',
            close_btn_hover:'jd_close_btn_hover',
            msg:'jd_msg',
            
            button:{
                item:'jd_button_item',
                hover:'jd_button_hover',
                
            }            
        }
    },o);
    t.param=p;
    
    if (p.strip) p.modal = true;
    if (!p.close_btn_enable) p.modal = true;
    
    for(i=0;i<p.buttons.length;i++){
        if (typeof p.buttons[i] === 'string')
            p.buttons[i]={caption:p.buttons[i]};
            
        p.buttons[i]=$.extend(true,{
            caption:'button'+i,
            css:p.css.button,
            id:i
        },p.buttons[i]);
    }
    
    
    t._create();
    t._event();
    
    t._align();
    setTimeout(function(){t._align();},100);
    
    
    
}

jdialog.prototype._create=function(){
    var t=this,p=t.param,css=p.css,code='',i,b;
    
    code+=ut.tag('<',{id:p.id,css:css.frame,style:'position:absolute',pos:p.pos});
        if (p.strip)
            code+=ut.tag({css:css.strip,style:'position:absolute',pos:p.pos});
            
        code+=ut.tag('<',{css:css.header,style:'position:absolute',pos:{h:p.heights.header}});
            code+=ut.tag({css:css.caption,style:'position:absolute',value:p.caption});
            code+=ut.tag({css:css.close_btn,style:'position:absolute;display:'+(p.close_btn_enable?'block':'none'),pos:{w:p.heights.close_btn,h:p.heights.close_btn}});
        code+=ut.tag('>');    
            
        code+=ut.tag('<',{css:css.center,style:'position:absolute'});
            code+=ut.tag({css:css.msg,style:'position:absolute',value:p.msg});
        code+=ut.tag('>');    
        
        code+=ut.tag('<',{css:css.footer,style:'position:absolute',pos:{h:p.heights.footer}});
        for(i=0;i<p.buttons.length;i++){
            b=p.buttons[i];
            code+=ut.tag({id:b.id,css:b.css.item,style:'position:absolute;min-width:110px;line-height:'+p.heights.button+'px',pos:{w:110,h:p.heights.button},value:b.caption});            
        }
        code+=ut.tag('>');    

    code+=ut.tag('>');
    
    p.own.append(code);

    p._frame    =p.own.find('#'+p.id);
    p._header   =p._frame.find('.'+css.header);
    p._center      =p._frame.find('.'+css.center);
    p._footer   =p._frame.find('.'+css.footer);
    p._caption  =p._header.find('.'+css.caption);
    p._close_btn=p._header.find('.'+css.close_btn);
    p._msg      =p._center.find('.'+css.msg);
    if (p.strip)
        p._strip      =p._frame.find('.'+css.strip);
    p._buttons=[];
    for(i=0;i<p.buttons.length;i++){
        b=p._footer.find('#'+p.buttons[i].id);
        $.data(b[0],'data',p.buttons[i]);
        p._buttons.push(b);
    }
    
    if (p.modal){
        p._frame.jshadow({
            show:true,
            opacity:p.shadow_opacity,
            click:function(e){
                if (p.close_btn_enable){
                    e.jshadow("hide");
                    if (p.onClick)
                        p.onClick({sender:t,is_close_btn:true});
                    t._free();    
                };
            }}
        );
    }
    
    if ((p.draggable)&&(!p.strip)){
        p._frame.draggable({handle:p._header});
    }

};

jdialog.prototype._free=function(){
    var t=this,p=t.param;
    if (p.modal)
        p._frame.jshadow("destroy");
        
    p._frame.remove();
    
};
jdialog.prototype._event=function(){
    var t=this,p=t.param,i,b;
    
    p._close_btn.on('click',function(){
        if (p.close_btn_enable){
            if (p.onClick)
                p.onClick({sender:t,is_close_btn:true});
            t._free();
        };
    }).hover(function(){
            p._close_btn.addClass(p.css.close_btn_hover);               
        },function(){
            p._close_btn.removeClass(p.css.close_btn_hover);               
            
    });
    
    for(i=0;i<p._buttons.length;i++){
        b=p._buttons[i];
        b.on('click',function(){
            var _this=$(this);
            var data = $.data(this,'data');
            if (p.onClick)
                p.onClick({sender:t,is_close_btn:false,id:data.id,item:_this,button:data});
            t._free();
                        
        }).hover(function(){
            var _this=$(this);
            var data = $.data(this,'data');
            _this.addClass(data.css.hover);               
        },function(){
            var _this=$(this);
            var data = $.data(this,'data');
            _this.removeClass(data.css.hover);               
            
        });
    }
            
};
jdialog.prototype._align=function(){
    var t=this,p=t.param;
    JX.arrange([p._header,p._center,p._footer],{direct:'vert',type:'stretch',align:'stretch',stretch:[{idx:1}],margin:{left:1,top:1,right:1}});
    JX.stretch(p._msg,{margin:p.margin.left});
    JX.arrange([p._caption,p._close_btn],{direct:'horiz',type:'stretch',align:'center',stretch:[{idx:0}],margin:{left:p.margin.left,right:p.margin.right}});
    JX.arrange(p._buttons,{direct:'horiz',type:'right',align:'center',gap:2,margin:{right:p.margin.right}});
    
    var pos = $.extend(true,{x:100,y:100},p.pos);
    
    if (typeof p.place.align === "object"){
        pos = JX.placer(JX.abs(p.place.align),JX.abs(p._frame),p.place);
    }else if (p.place.align === "screen"){
        pos = JX.placer(JX.screen(),JX.abs(p._frame),p.place);        
    }else{
        pos.w=undefined;
        pos.h=undefined;
    }
    JX.abs(p._frame,pos);
    pos = JX.abs(p._frame);
    
    if (p.strip){
        pos.x=0;
        pos.w=JX.screen().w;
        JX.abs(p._strip,pos);
    }    
};