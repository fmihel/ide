<?php

MODULES::ADD('FILETREE');
class FILETREE extends WS_MODULE{

    function __construct($owner){
        parent::__construct($owner);
        
        RESOURCE('plugins/jstree/dist/jstree.min.js');
        RESOURCE('modules/left_panel/filetree.dcss');
        RESOURCE('plugins/filetree/filetree.js');
        RESOURCE('plugins/jstree/dist/themes/default/style.css');

    }
    
    public function CONTENT(){
        
        $tree = FRAME("tree",FRAME('explorer')->CLASSES('ws_scrollbar')->STYLE("overflow-x:hidden;overflow-y:auto"));
    }

    public function AJAX_duplicate(&$response){
        global $REQUEST;
        global $Application;
        global $USERS;
        
        if ($REQUEST->ID=='duplicate'){
            $src = $REQUEST->VALUE['src'];
            
            $path = APP::slash(APP::rel_path($Application->PATH,$Application->ROOT.$USERS->get('workplace','')),false,true);
            $src = APP::slash($path.$src,false,false);
            $parent = explode(_DIRECTORY_SEPARATOR,$src);
            array_pop($parent);
            $parent = APP::slash(implode(_DIRECTORY_SEPARATOR,$parent),false,true);
            $name = substr($src,strlen($parent));
            
            if (is_dir($src)){
                $idx = 1;
                $new = $parent.$name.'_'.$idx;
                while(file_exists($new)){
                    $idx++;
                    $new = $parent.$name.'_'.$idx;
                }
                //_LOG("src[$src] parent[$parent] name[$name] new[$new]");
                if (!DIR::copy($src,$new))
                    $response = array("res"=>0,'msg'=>"Duplicate with errors!");
            }else{
                $ext = APP::ext($name);
                $name = APP::without_ext($name);
                $idx = 1;
                $new = $parent.$name.'_'.$idx.'.'.$ext;
                while(file_exists($new)){
                    $idx++;
                    $new = $parent.$name.'_'.$idx.'.'.$ext;
                }
                
                if (!copy($src,$new))
                        $response = array("res"=>0,'msg'=>"Error duplicate [$src]!");
                //_LOG("src[$src] parent[$parent] name[$name] new[$new]");
            }
            $response = array("res"=>1);
            return true;
            
        }    
         return false;
    }
    // in TWS->AJAX handler use next code:
    // if ($this->AJAX_paste($response)) return true;
    public function AJAX_paste(&$response){
        global $REQUEST;
        global $Application;
        global $USERS;
        
        if ($REQUEST->ID=='paste'){
            $response = array("res"=>1);
            
            $from = $REQUEST->VALUE['from'];
            $to = $REQUEST->VALUE['to'];
            
            $path = APP::slash(APP::rel_path($Application->PATH,$Application->ROOT.$USERS->get('workplace','')),false,true);
            
            $from = APP::slash($path.$from,false,false);
            $to   = $path.$to; 
            if (!is_dir($to))
                $to = APP::get_path($to);
            $to = APP::slash($to,false,true);
            
            $last = array_pop(explode(_DIRECTORY_SEPARATOR,$from));
            
            if (is_dir($from)){
                
                //_LOG("path:[$path] from:[$from], to:[$to] root:[$last] pos[$pos] ",__FILE__,__LINE__);
                
                if (strpos($to,$from)===0)
                    $response = array("res"=>0,'msg'=>'Can`t copy inside!');
                elseif (!DIR::copy($from,$to.$last))
                    $response = array("res"=>0,'msg'=>"Copy with errors!");
                    
                
            }else{
                if (file_exists($to.$last))
                    $response = array("res"=>0,'msg'=>"File [$last] exist!");
                else
                    if (!copy($from,$to.$last))
                        $response = array("res"=>0,'msg'=>"Error copy [$last]!");
            }
            
            return true;
        }
        
        return false;
    }
    
    // in TWS->AJAX handler use next code:
    // if ($this->AJAX_user_is_loading($response)) return true;
    public function AJAX_user_is_loading(&$response){
        global $REQUEST;
        global $USERS;
        if ($REQUEST->ID=='user_is_loading'){
                $response =array('res'=>(($USERS->get('session')==$REQUEST->SHARE('session','error'))?1:0));
            return true;
        }
        return false;
    }
    
    public function AJAX(&$response){
        global $REQUEST;
        if ($this->AJAX_paste($response)) return true;
        if ($this->AJAX_user_is_loading($response)) return true;
        if ($this->AJAX_duplicate($response)) return true;
        return false;
    }
    
};



?>