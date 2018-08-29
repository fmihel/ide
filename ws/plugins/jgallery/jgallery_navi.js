/*global ut,$,jQuery,JX,Qs,Ws*/
function jgallery_navi(o){
    var t = this;
    t.param = $.extend(true,{
        gallery:null,
        owner:null,
        id:ut.id('jgallery_navi'),
        lock:new jlock(),
        up:null,
        down:null,
        btnNext:null,
        btnPrev:null,
        items:[],
        children:[],
        /** array of css classes uses in Tjgallery_navi */
        css:{
            up:'jgn_up',
            down:'jgn_down',
            item:'jgn_item',
            active:'jgn_active',
            hover:'jgn_hover',
            btn:{next:'jgn_next',prev:'jgn_prev'}
        }
        
    },o);
    var p=t.param;
    
    var id = ut.id('up');
    p.up = ut.tag({id:id,css:p.css.up,style:'position:absolute;overflow-x:hidden;overflow-y:auto'});
    p.owner.append(p.up);
    p.up = p.owner.find('#'+id);
    
    
    id = ut.id('dn');
    p.down = ut.tag({id:id,css:p.css.down,style:'position:absolute'});
    p.owner.append(p.down);
    p.down = p.owner.find('#'+id);
    
    id = ut.id('prv');
    p.btnPrev = ut.tag({id:id,css:p.css.btn.prev,style:'position:absolute'});
    p.down.append(p.btnPrev);
    p.btnPrev = p.owner.find('#'+id);

    id = ut.id('nxt');
    p.btnNext = ut.tag({id:id,css:p.css.btn.next,style:'position:absolute'});
    p.down.append(p.btnNext);
    p.btnNext = p.owner.find('#'+id);

    
    t.put(t.param);
    t.event();
    
    Ws.align(function(){t.align();});
}


jgallery_navi.prototype.onGallery=function(o){
    var t=this,p=t.param;
    
    if (o.event === 'init')
        p.gallery = o.sender;
    
    if (o.event === 'create')
        t.create(o.sender);
        
    if (o.event === 'change')
        t.change(o);
        
};    

jgallery_navi.prototype.create=function(o){
    var t=this,p=t.param,css=p.css,c='',i,cnt;
    
    p.items=[];
    p.up.html('');
    cnt=p.gallery.count();
    
    for(i=0;i<cnt;i++)
        c+=ut.tag({id:i,css:css.item,value:(i+1)});

    p.up.append(c);
    p.children = p.up.children();
    
    $.each(p.children,function(i){
        var ob=$(this);
        p.items.push(ob);
        $.data(ob[0],'data',p.gallery.item(i));
        
        ob.hover(function(){
            var it=$(this);
            p.children.not(it).removeClass(css.hover);
            if (!it.hasClass(css.active))
                it.addClass(css.hover);
        },function(){
            var it=$(this);
            it.removeClass(css.hover);
        });
    });

};

jgallery_navi.prototype.change=function(o){
    var t=this,p=t.param,css=p.css,i,pos,to;
    
    for(i=0;i<p.items.length;i++){
        if (i!==o.data.idx)
            p.items[i].removeClass(css.active);
        else{
            p.items[i].addClass(css.active);
            pos = JX.pos(p.items[i]);
            if((pos.y<p.up.scrollTop())||(pos.y+pos.h>p.up.scrollTop()+p.up.height())){
                
                if(pos.y<p.up.scrollTop())
                    to = pos.y;
                else
                    to = pos.y-p.up.height()+pos.h;

                //p.up.animate({scrollTop:to},200);
                
                p.up.scrollTop(to);
            }    
            
        }    
    }
    
    
    
};    

jgallery_navi.prototype.event=function(){
    var t=this,p=t.param,css=p.css,i;
    p.up.on('click',function(e){
        var o = $(e.target);
        if (o.hasClass(css.item)){
            var item = $.data(o[0],'data');
            p.gallery.current(item);
        }
    });
    
    p.btnPrev.on('click',function(){
        p.gallery.step(-1);
    });
    
    p.btnNext.on('click',function(){
        p.gallery.step(1);
    });
};    
jgallery_navi.prototype.put = function(o){
    var t=this,p=t.param,l=p.lock;
    
    l.lock('align');
    
    $.each(o,function(n,v){
        t.attr(n,v);
    });
    
    if (l.unlock('align'))
        t.align();
};    

jgallery_navi.prototype.attr = function(n/*v*/){
    if (arguments.length===0) 
        return;
        
    var t=this,p=t.param,v,r=(arguments.length===1);
    if (!r) 
        v=arguments[1];

    /*-----------------------------------*/
    /** example
    
    if (n==='visible'){
        if (r) 
            return JX.visible(p.plugin);
        else    
            JX.visible(p.plugin,(v?true:false));
    }
    */
    /*-----------------------------------*/
    t.align();
};

/** Call this method to redraw all DOM elements*/ 
jgallery_navi.prototype.align=function(){
    var t=this,p=t.param,l=p.lock;
    
    if (l.lock('align'))
        t._align();
    l.unlock('align');   
    
};

/** Method calling from Tjgallery_navi.align(). Custom call not needed*/ 
jgallery_navi.prototype._align=function(){
    var t=this,p=t.param;
    
    JX.arrange([p.up,p.down],{direct:"vert",type:"stretch",align:"stretch",stretch:p.up,margin:{left:2}});
    JX.arrange([p.btnPrev,p.btnNext],{direct:"vert",type:"center",align:"left"});
};


    