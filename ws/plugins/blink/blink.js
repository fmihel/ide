function blink(to,o){
    var 
    c,sp={},st={},id,
    def = typeof blinkDefault!=='undefined'?blinkDefault:{
        own:(typeof Qs!=='undefined'&&Qs.modal)?Qs.modal:$('body'),
        duration:200,
        restory:100,
        type:'opacity',//restory
        css:undefined,
        opacity:0,
        scale:10,
        done:undefined,
        animate:{
        }
    },
    a = $.extend(true,def,o);
    
    
    c =to.clone();
    
    if (typeof a.css==='object')
        c.css(a.css);
    else if (typeof a.css==='string') 
        c.addClass(a.css);

    if (to[0].tagName==="TR"){
        id = ut.id("blink");
        a.own.append("<table id='"+id+"' style='position:absolute'></table>");
        c = a.own.find("#"+id).append(c);
        //c.css({position:'absolute'});
    }else if (to[0].tagName==='TD')
        c.css({display:'block',position:'absolute'});

    if (typeof a.css==='function')
        a.css(c);
    
    a.own.append(c);
    
    sp = JX.abs(to);
    JX.abs(c,sp);
    
    for(var style in a.animate)
        st[style] = to.css(style);
    
    
    if (a.scale>0){
        
        a.animate.left   = '-='+a.scale+'px';
        a.animate.top    = '-='+a.scale+'px';
        a.animate.width  = '+='+2*a.scale+'px';
        a.animate.height = '+='+2*a.scale+'px';

        st.left     = sp.x+'px';
        st.top      = sp.y+'px';
        st.width    = sp.w+'px';
        st.height   = sp.h+'px';
    }
    
    if (a.type!=='restory')
        a.animate.opacity = a.opacity;
    
    if (a.start) a.start(to);
    
    c.animate(a.animate,{
        duration:a.duration,
        done:function(){
            if ((a.type==='restory')&&(a.restory>0)){
                c.animate(st,{
                    duration:a.restory,
                    done:function(){
                        c.remove();
                        if (a.done) a.done(to);
                    }
                }
                );
            }else{
                c.remove();
                if (a.done) a.done(to);
                
            }
        }
        
    });

    
    
}