<?php



MODULES::ADD('RIGHT_PANEL');
class RIGHT_PANEL extends WS_MODULE{
    function __construct($owner){
        
        parent::__construct($owner);
        
        RESOURCE('plugins/jvtab/jvtab.js');
        RESOURCE('plugins/jvtab/jvtab.dcss');
        RESOURCE('modules/right_panel/right_panel.dcss');
        
    }
    public function CONTENT(){
    
    FRAME('right')
    ->STYLE('overflow:hidden')
    ->SCRIPT('var right_panel;')
    ->ALIGN('if (right_panel) right_panel.align();')
    ->READY('
        var wsi_rp = "wsi_rp";
        var init_panel = false;
        
        right_panel=new jvtab({
            own:{$},
            onclose:function(o){
                JX.visible(Qs.splitter_cr,false);
                Ws.align();
                if (init_panel)
                    bdata.set(wsi_rp,"close");
            },
            onselect:function(o){
                JX.visible(Qs.splitter_cr,true);
                Ws.align();
                right_panel.update({event:"open",sender:right_panel,item:o.item});
                if (init_panel)
                    bdata.set(wsi_rp,o.item.idx);
            },
            onresize:function(o){
                if (right_panel) right_panel.update({event:"resize",sender:o.sender});
            }
        });

        right_panel.update = function(o){
            var item = right_panel.current();
            if ((item)&&(item.update)) item.update(o);
            if ((item)&&(Qs.Struct)&&(item!==Qs.Struct)) Qs.Struct.update(o);
        }
        
        setTimeout(function(){
            init_panel = bdata.get(wsi_rp,"close");
            if (init_panel == "close")
                right_panel.close();
            else{
                var n = parseInt(init_panel);
                right_panel.current((isNaN(n)?0:n));
            }    
            init_panel = true;                
            
        },1000);
    ');
        
    }
};

class WSI_PANEL extends WS_MODULE{

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
    }
    
    public function fn_update(){
            return '';
    }
    public function CONTENT(){
        $item = "Qs.$this->var_name";
        $panel =  "Qs.$this->var_name.panel";
        FRAME('right')
        ->READY("
            $item=right_panel.add({caption:'$this->caption',id:'$this->id'});
            $item.update=function(o){".WS_UTILS::code_end(str_replace('{$}',$panel,$this->fn_update()))."};
        ");
    }
    
}


?>