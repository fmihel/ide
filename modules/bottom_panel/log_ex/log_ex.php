<?php


//define('LOG_EX_MONITOR_COUNT','2');

MODULES::ADD('log_ex');

class log_ex extends WSI_BOTTOM_PANEL{
    function __construct($owner){
        parent::__construct($owner);
        
        RESOURCE('modules/bottom_panel/log_ex/log_panel.js');
        RESOURCE('modules/bottom_panel/log_ex/log_panel.dcss');
        RESOURCE('plugins','common/timer.js');
        RESOURCE('plugins','mselect/mselect.js');
        
    }
    public function CONTENT(){
        $this->panel_width= 205;
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
    
    
    private function _load($filename,$line){
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
            
            $all   = count($data);
            $count = $all;
            
            if (($line==-1) && ($count>$limit)) 
                $line = $count-$limit;  // начинаем от конца 
            
            if ($count-$line>$limit) 
                $count=$line+$limit;
            
            if ($count<$line) 
                $line = $count-1; 
            
            
            if($count!=$line)
                for($i=$line+1;$i<$count;$i++){
                    if (trim($data[$i])!=='')
                        $log[]=$data[$i];
                    else 
                        if ($i==$count-1) $count--;
                }
            
            
            return array('res'=>1,'log'=>$log,'line'=>($count-1),'count'=>$all);
        }else
            return array('res'=>0,'msg'=>'file not exists['.$filename.']');

        
    }
    private function load($filename,$line,$full){
        $limit = 5;
        
        global $DECONET_CODE_PAGES; // defined in editors.php
        //_LOGF($filename,'filename',__FILE__,__LINE__);
    
        //if ($filename=='../../trial/test/log.txt')
        //    _LOGF($line,'$line',__FILE__,__LINE__);
        
        if (file_exists($filename)){
            
            //_log("line=$line",__FILE__,__LINE__);
            $content = file_get_contents($filename);
            
            $code_page = ENCODING::simple_codepage($content);
             if (array_search($code_page,$DECONET_CODE_PAGES)!==false)
                $content = iconv('Windows-1251',"UTF-8",$content);
            
            $data = explode("\n",$content);

            $log = array();
            
            $all   = count($data);
            $count = $all;
            

            if (($line==-1) && (!$full) && ($count>$limit)) 
                $line = $count-$limit;  // начинаем от конца 
            
            $current = $line+1;
            for($i=$line+1;($i<$count)&&($i<$line+$limit);$i++){
                if (trim($data[$i])!==''){
                       $log[]=$data[$i];
                }
                if (($i<$count-1)||(trim($data[$i])!==''))
                    $current++;
                
            };
            
            if (($all===1)&&(trim($data[0])==='')){ 
                $all = 0; 
                $current = 0;
            };
            
            return array('res'=>1,'log'=>$log,'line'=>$current-1,'count'=>$all);
            
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
            $full = ($REQUEST->VALUE['full']==0?false:true);
             $response=$this->load($filename,$line,$full);
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