<?php

MODULES::ADD('Struct');
class Struct extends WSI_PANEL{
   
   function __construct($owner){
        parent::__construct($owner);
        
        RESOURCE('modules/right_panel/struct/struct.js');
        RESOURCE('modules/right_panel/struct/struct.dcss');

   }    
   
    public function fn_update(){

    $item = "Qs.$this->var_name";
    
    $res = '
    
    var item = editors.current();
    if ((!item)||(!item.editor)) return;

    if ((o.event==="open")||(o.event==="restory")){
        if (item.type=="editor"){
            Struct.update();
        }else{
            Struct.stop();                
            Qs.Struct.panel.html("");
        }
    }else if (o.event==="action"){
        if (item.type=="editor")
            Struct.doaction({item:item});
        
    }else if (o.event==="resize"){
                    
            Struct.doresize();
        
    }else if (o.event==="change"){
        if (item.type=="editor"){
            
            if (Struct.delay_change){
                clearTimeout(Struct.delay_change)
            }
            Struct.delay_change = setTimeout(function(){
                Struct.update();
                Struct.delay_change = null;
            },1000);            


        }
    }
    
    ';
    
    return $res;
    }

}

?>