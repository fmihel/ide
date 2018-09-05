/*global JX,$,Qs,Ws,Ut*/

var _prh={press:false,fix:{x:0,y:0},current:{x:0,y:0},h:{ex:100,pr:100}};

var left_panel={

ready:function(){
    var prh = Qs.problem_header,pr=Qs.problem;
        
    prh.on("mousedown",function(){
        _prh.press = true;
        _prh.fix={x:_prh.current.x,y:_prh.current.y};
    }).on("mouseup",function(){
        _prh.press = false;

    }).on("mouseleave",function(e){
        _prh.press = false;
    }).on("mousemove",function(e){
        _prh.current={x:e.pageX,y:e.pageY};
        if (_prh.press){
            _prh.h.ex=JX.pos(Qs.explorer).h;
            _prh.h.pr=JX.pos(Qs.problem).h;
            _prh.h.prh=JX.pos(Qs.problem_header).h;
            _prh.h.exh=JX.pos(Qs.explorer_header).h;
            _prh.h.frame=JX.pos(Qs.left).h;
            
            var delta = (_prh.fix.y-_prh.current.y);
            var seth=false;
            if (delta>0){
                if(_prh.h.ex-delta>20) 
                    seth=true;
                else
                    _prh.press=false;
            }else{
                if(_prh.h.pr+delta>0){ 
                    seth=true;
                }else
                    _prh.press=false;
            }
            
            //if (seth)
            //    console.info(_prh.h.ex-delta);
            
            if (seth)
                JX.pos(Qs.problem,{h:_prh.h.pr+delta});
            
            
            _prh.fix={x:_prh.current.x,y:_prh.current.y};
            left_panel.align();
        }
        
    });
    
},
align:function(o){
    
    var a=$.extend(true,{idx:1},o);
    JX.arrange(Qs.left.children(),{direct:"vert",stretch:[{idx:(JX.pos(Qs.explorer).h<2?3:1)}]});
    JX.arrange(Qs.problem_header.children(),{direct:"horiz",type:"stretch",align:"stretch",stretch:[{idx:3}],gap:2});
    JX.arrange(Qs.explorer_header.children(),{direct:"horiz",type:"stretch",align:"stretch",stretch:[{idx:2}],gap:2});
            
    var cb = JX.pos(Qs.problem).h<2;
    Qs.ph_open_close.removeClass((cb?"ph_oc_d":"ph_oc_u")).addClass((cb?"ph_oc_u":"ph_oc_d"));

    var cb = !(JX.pos(Qs.explorer).h<2);
    Qs.ex_open_close.removeClass((cb?"ph_oc_d":"ph_oc_u")).addClass((cb?"ph_oc_u":"ph_oc_d"));

},
click_explorer_header:function(){
    var h= JX.pos(Qs.explorer).h;
    _prh.press=false;
    if (h>2){
        _prh.h.pr=JX.pos(Qs.problem).h;
        _prh.h.ex=JX.pos(Qs.explorer).h;
                 
        JX.pos(Qs.explorer,{h:0});
    }else{    
        JX.pos(Qs.problem,{h:_prh.h.pr});
        JX.pos(Qs.explorer,{h:(_prh.h.ex>200?_prh.h.ex:200)});
    }
    left_panel.align();    
},
click_problem_header:function(){
    var h= JX.pos(Qs.problem).h;
    var hx= JX.pos(Qs.explorer).h;
    _prh.press=false;
    if ((h>2)&&(hx>0)){
        _prh.press = false;
        _prh.h.pr=JX.pos(Qs.problem).h;
        _prh.h.ex=JX.pos(Qs.explorer).h;
                
        JX.pos(Qs.problem,{h:0});
        JX.pos(Qs.explorer,{h:100});
                
    }else{    
        JX.pos(Qs.explorer,{h:_prh.h.ex});
        JX.pos(Qs.problem,{h:(_prh.h.pr<200?200:_prh.h.pr)});
    }    
                
    left_panel.align();    
}

};
