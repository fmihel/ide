<?php


//define('LOG_EX_MONITOR_COUNT','2');

MODULES::ADD('log_ex');

class log_ex extends WSI_BOTTOM_PANEL{
    function __construct($owner){
        parent::__construct($owner);
        
        RESOURCE('modules/bottom_panel/log_ex/log_panel.js');
        RESOURCE('modules/bottom_panel/log_ex/log_panel.dcss');
        RESOURCE('plugins','common/timer.js');
    }
    public function CONTENT(){
        $this->panel_width= 120;
         $this->caption ='log';
        parent::CONTENT();
        global $USERS;
        
        FRAME('bottom')->READY('
            
            log_panel.init({
                own:Qs.log_ex.panel,
                bottom:Qs.log_ex,
                change:function(e){
                    
                    Ws.ajax({id:"save_log_def",value:{
                        log_ex:log_panel.save()
                    }});
                }
                
            });
            
            log_panel.load('.ARR::to_json($USERS->get('log_ex')).');
        ');
        
    }

    public function fn_update(){
        $item = "Qs.$this->var_name";
        return '';
    }
    
    
    private function load($filename,$line){
        $limit = 5;
        
        global $DECONET_CODE_PAGES; // defined in editors.php
        
        if (file_exists($filename)){
            
            //_log("line=$line",__FILE__,__LINE__);
            $content = file_get_contents($filename);
            
            $code_page = ENCODING::simple_codepage($content);
             if (array_search($code_page,$DECONET_CODE_PAGES)!==false)
                $content = iconv('Windows-1251',"UTF-8",$content);
            
            $data = explode("\n",$content);

            $log = array();
            $count=count($data);
            
            if (($line==-1) && ($count>$limit)) $line = $count-$limit;
            if ($count-$line>$limit) $count=$line+$limit;
            
            if ($count<$line) $line = $count-1; 
            
            
            if($count!=$line)
                for($i=$line+1;$i<$count;$i++){
                    if (trim($data[$i])!=='')
                        array_push($log,$data[$i]);
                    else if ($i==$count-1) $count--;
                }
            
            
            return array('res'=>1,'log'=>$log,'line'=>($count-1));
        }else
            return array('res'=>0,'msg'=>'file not exists['.$filename.']');

        
    }
    
    function AJAX(&$response){
        global $REQUEST;
        global $USERS;
        global $Application;
        
        if ($REQUEST->ID=='save_log_def'){
                
            $log_ex = $REQUEST->VALUE['log_ex'];
            $USERS->put('log_ex',$log_ex);
            $USERS->save_current();
            return true;
        } 
        
        
        
        if ($REQUEST->ID=='log_refresh'){

            $filename = APP::rel_path($Application->PATH,$Application->ROOT.$USERS->get('workplace')).'/'.$REQUEST->VALUE['filename'];
            $line = $REQUEST->VALUE['line'];

             $response=$this->load($filename,$line);
             return true;
        }
        
        if ($REQUEST->ID=='log_clear'){

            $filename = APP::rel_path($Application->PATH,$Application->ROOT.$USERS->get('workplace')).'/'.$REQUEST->VALUE['filename'];
            if (file_exists($filename))
                file_put_contents($filename,'');
                
            $response=array('res'=>1);
            return true;
            
        }
        
        return false;
    }

}

?>