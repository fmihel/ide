<?php

RESOURCE('modules/bottom_panel/log/log.js');
RESOURCE('modules/bottom_panel/log/log.dcss');

define('LOG_MONITOR_COUNT','2');

MODULES::ADD('log');
class log extends WSI_BOTTOM_PANEL{
    
    public function CONTENT(){
        $this->panel_width= 590;
        parent::CONTENT();
        FRAME('bottom')->READY('Log.init()');
        
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