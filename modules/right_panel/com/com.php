<?php
MODULES::ADD('Coms');
class Coms extends WSI_PANEL{
   function __construct($owner){
        global $USERS;
        global $Application;
        parent::__construct($owner);
        
        RESOURCE('modules/right_panel/com/com.js');
        RESOURCE('modules/right_panel/com/com.dcss');
        
        
        
        /*подключение предустановленных правил парсинга*/
        RESOURCE('com/rules.js');
        RESOURCE('com/rules.dcss');
        
        /*подключение правил парсинга общих для всех*/
        $file = '../com/rules.js';
        if (file_exists($file))
            RESOURCE($file);
        $file = '../com/rules.dcss';
        if (file_exists($file))
            RESOURCE($file);
            


        /*подключение правил парсинга текущего пользователя*/
        $path = trim($USERS->get('com',false));
        
        if (($path!==false)&&($path!=='')){
        
            $path = APP::rel_path($Application->PATH,$Application->ROOT).APP::slash($path,false,true);
            
            if (file_exists($path.'rules.js'))
                RESOURCE($path.'rules.js');
            if (file_exists($path.'rules.dcss'))
                RESOURCE($path.'rules.dcss');
        }
                

        

   }    

    public function fn_update(){

        $item = "Qs.$this->var_name";
    
        $res = '

            var item = editors.current();
            if ((!item)||(!item.editor)) return;
            if ((o.event==="open")||(o.event==="restory")){
                if (item.type=="editor"){
                    Coms.update();
                }else{
                    Coms.stop();                
                    Qs.Com.panel.html("");
                }
            }else if (o.event==="action"){
                    if (item.type=="editor")
                    Coms.doAction({item:item});
            }else if (o.event==="resize"){
                    
                /*Struct.doresize();*/
        
            }else if (o.event==="change"){
                console.info("com.update");
                if (item.type=="editor")
                    Coms.update();
                    
            }

        ';
    
        return $res;
    }

}

?>