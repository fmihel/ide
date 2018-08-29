<?php



MODULES::ADD('BOTTOM_PANEL');
class BOTTOM_PANEL extends WS_MODULE{
    
   function __construct($owner){
        parent::__construct($owner);
        
        RESOURCE('plugins/jhtab/jhtab.js');
        RESOURCE('plugins/jhtab/jhtab.dcss');

   }    
    
    public function CONTENT(){

    FRAME('bottom')
    ->STYLE('overflow:hidden')
    ->SCRIPT('var bottom_panel,bp_close=false;')
    ->ALIGN('if (bottom_panel) bottom_panel.align();')
    ->READY('
        bottom_panel=new jhtab({
            own:{$},
            btn:{h:32},
            collapsed_height:32,
            
            css:{panel:"jht_panel ws_scrollbar"},
            onclose:function(o){
                if (bp_close)
                    bdata.set({key:"bp_name",val:""});
                Ws.align();
            },
            onselect:function(o){
                Ws.align();
                if (bp_close){

                    bdata.set({key:"bp_name",val:(o.item==Qs.find?"find":"log_ex")});    
                }    
                bottom_panel.update({event:"open",sender:right_panel,item:o.item});
                
            },
            onresize:function(o){
                if (bottom_panel) bottom_panel.update({event:"resize",sender:o.sender});
            }
        });

        bottom_panel.update = function(o){
            var item = bottom_panel.current();
            
            if ((item)&&(item.update)) item.update(o);
            /*if ((item)&&(Qs.Struct)&&(item!==Qs.Struct)) Qs.Struct.update(o);*/
        }
        setTimeout(function(){
            var name = bdata.get({key:"bp_name",def:""});
            if (name =="")
                bottom_panel.close(true);
            else
                bottom_panel.current({item:Qs[name]});
                
            bp_close=true;    
            
        },100);
    ');
        
    }
};

class WSI_BOTTOM_PANEL extends WS_MODULE{

    public $id;
    public $var_name;
    public $caption;
    public $panel;
    public $tabs;
    
    
    
    function __construct($owner){
        parent::__construct($owner);
        $this->var_name =get_class($this);
        $this->caption =$this->var_name;
        $this->id = 'id_'.$this->var_name;
        $this->panel_width = 0;
    }
    
    public function fn_update(){
            return '';
    }
    public function CONTENT(){
        $item   = "Qs.$this->var_name";
        $panel  = "Qs.$this->var_name.panel";
        FRAME('bottom')
        ->READY("
            $item=bottom_panel.add({caption:'$this->caption',id:'$this->id',btn_panel_width:".$this->panel_width."});
            $item.update=function(o){".WS_UTILS::code_end(str_replace('{$}',$panel,$this->fn_update()))."};
        ");
    }
    
}


?>