<?php

MODULES::ADD('find');
define('MAX_FIND_COUNT',500); /*see MAX_FIND_COUNt in find.js*/

class find extends WSI_BOTTOM_PANEL{
    function __construct($owner){
        parent::__construct($owner);
        
        RESOURCE('modules/bottom_panel/find/find.js');
        RESOURCE('modules/bottom_panel/find/find.dcss');
    }   
    public function CONTENT(){
        $this->panel_width= 550;
        parent::CONTENT();
        FRAME('bottom')->READY('Find.init()');
        
    }
    public function fn_update(){

        $item = "Qs.$this->var_name";
        $res = '
            if (o.event=="open") 
                Find.param.input.focus();
        ';
    
        return $res;
    }
    private function _find($str,$template){
  
        $res    = array();

        $template=$this->_slash($template);
        
        if (preg_match_all($template,$str,$find,PREG_OFFSET_CAPTURE)){
            
            $lines  = array();
            if (preg_match_all('/\n/',$str,$_lines,PREG_OFFSET_CAPTURE)){
                for($i=0;$i<count($_lines[0]);$i++)
                    $lines[]=$_lines[0][$i][1];
            }

            $find = $find[0];
            $cnt = min(count($find),MAX_FIND_COUNT);
            
            for($i=0;$i<$cnt;$i++){
                
                $text = $find[$i][0];
                $_off = $find[$i][1];
                
                $line = 0;
                for($line=0;$line<count($lines);$line++)
                    if ($_off<$lines[$line]) break;
                
                $line_pos = ($line>0?$lines[$line-1]+1:0);
                $line_next = $lines[$line];
                
                $pos = $_off-$line_pos;

                $left  = htmlspecialchars(substr($str,$line_pos,$pos));
                $right = htmlspecialchars(substr($str,$line_pos+$pos+strlen($text),$line_next-$line_pos-strlen($text)-$pos));
                $text = htmlspecialchars($text);
                
                array_push($res,array('line'=>$line,'pos'=>$pos,'off'=>$_off,'find'=>$text,'left'=>$left,'right'=>$right));
            }   
            if (count($find)>MAX_FIND_COUNT)
                array_push($res,array('line'=>0,'pos'=>0,'off'=>0,'find'=>'more than '.MAX_FIND_COUNT,'left'=>'>>>','right'=>'<<<'));
            
        }
        return $res;
    }
    private function _slash($key){
        $key = trim($key);
        return ($key[0]!=='/'?'/':'').$key.($key[strlen($key)-1]!=='/'?'/':'');
    }    
    private function _prefind($files,$key){
        global $USERS;
        global $Application;
        
        $out = array();
        
        $template = $this->_slash($key);
        
        //_LOG($template,__FILE__,__LINE__);
        
        
        for($i=0;$i<count($files);$i++){
            
            $from   = $Application->PATH;
            $to     = $Application->ROOT.$USERS->get('workplace');
            $file   = APP::rel_path($from,$to).'/'.$files[$i]['path'];
            
            $str = file_get_contents($file);
            
            if (preg_match($template,$str))
                array_push($out,$files[$i]);
            
        }
        
        return $out;
    }

    public function finds($file,$key){
        
        return $this->_find(file_get_contents($file),APP::slash($key,true,true));
        //return $file.' '.$key;
    }
    
    public function AJAX(&$response){
        
        global $REQUEST;
        global $USERS;
        global $Application;
        
        if ($REQUEST->ID == 'get_dir_list'){
            
            $ext    = $REQUEST->VALUE['ext'];
            $key    = $REQUEST->VALUE['key'];
            $add    = trim($REQUEST->VALUE['path']);
            
            $add = (($add == '/')||($add==''))?'':APP::slash($add,true,true);
            
            
            $from   = $Application->PATH;
            $to     = $Application->ROOT.$USERS->get('workplace');
            $path   = APP::rel_path($from,$to);

            $dir = DIR::lstruct($path.$add,$ext);
            
            if ($add!=='') for($i=0;$i<count($dir);$i++)
                $dir[$i]['path']=$add.$dir[$i]['path'];
            
            $files = $this->_prefind($dir,$key);    
            //_LOGF($dir,'dir',__FILE__,__LINE__,'arr:3');
            

            //$response = array('res'=>1,'files'=>$this->_prefind(DIR::lstruct($path,$ext),$key));
            $response = array('res'=>1,'files'=>$files);
            return true;
        };
        
        if ($REQUEST->ID == 'find'){
            
            $file   = $REQUEST->VALUE['file'];
            $key    = $REQUEST->VALUE['key'];
            //$add    = $REQUEST->VALUE['path'];
            
            //$add = $add == '/'?'':APP::slash($add,true,false);
        
            $from   = $Application->PATH;
            $to     = $Application->ROOT.$USERS->get('workplace');
            $path   = APP::rel_path($from,$to);
        
            $response = array('res'=>1,'finds'=>$this->finds($path.'/'.$file,$key));
            return true;
        };
        
        
        return false;
    }

}

?>