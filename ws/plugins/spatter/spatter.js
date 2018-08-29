/** эффект бросания объекта from в точку to  */
function spatter(o){
    var 
    s,id,p,
    a = $.extend(true,{
        from:undefined, /*объект jquery*/
        css:undefined,/* объект | функция*/
        to:undefined,   /*объект jquery | {x,y} */
        own:Qs&&Qs.modal?Qs.modal:$('body'),/*объект jquery | undef*/
        start:undefined,/** обработчик перед стартом */
        done:undefined,/** обработчик завершения */
        duration:500,   /** время полета*/
        opacity:0,
        offX:0,
        offY:0,
        stick:'bottom',/*normal,bottom*/
        
    },o);
    
    if((!a.from)||(!a.to)) return;
    
            
    s = a.from.clone();
        
        
    
    if (a.from[0].tagName==="TR"){
        id = ut.id("ta");
        a.own.append("<table id='"+id+"' style='position:absolute'></table>");
        s = a.own.find("#"+id).append(s);
    }else if (a.from[0].tagName==='TD'){
        s.css({display:'block',position:'absolute'});
    }
        

    if (typeof a.css==='function')
        a.css(s);
    else if (typeof a.css==='object')
        s.css(a.css);
    
    
    a.own.append(s);
            
    p = JX.abs(a.from);
    JX.abs(s,p);
    s.css({opacity:"1"});
    
    
    if (JX.is_jquery(a.to))
        a.to = JX.abs(a.to);
        
    if ((a.stick==='bottom'))
        a.to.y+=a.to.h; 
    
    if (a.start)
        a.start(a);
    
    s.animate({
        left:a.to.x+a.offX,
        top:a.to.y+a.offY,
        opacity:a.opacity,
    },{
        duration:a.duration,
        done:function(){
            s.remove();
            if (a.done) a.done();
        }
    });
    
}