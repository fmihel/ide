<?php

if(!isset($Application)) 
    require_once '../utils/application.php';

$_modules = array();
class MODULES{
    
    public static function ADD(/* [<$module_class_name>] || [<$tags>,<$module_class_name>] */){
        global $_modules;
        $tags='COMMON';    
        if(func_num_args()>1){
            $tags=strtoupper(func_get_arg(0));
            $module_class_name=func_get_arg(1);
        }else 
            $module_class_name=func_get_arg(0);

        //-----------------------------------
        $tags = STR::to_array(',',$tags);
        //-----------------------------------
        
        array_push($_modules,array('CLASS'=>$module_class_name,'ENABLE'=>true,'MODULE'=>null,'TAGS'=>$tags));
        
    }
    
    public static function ENABLE($param){
        global $_modules;
        
        $names = array();
        $tags = array();
        $enable = (!isset($param['ENABLE'])?true:$param['ENABLE']);
        $by_tag = false;
        
        if (isset($param['TAG'])){
            if (is_array($param['TAG']))
                $tags = $param['TAG'];
            else
                $tags = STR::to_array(',',$param['TAG']);
            $by_tag = true;
            
        }else if (isset($param['MODULE'])){
            if (is_array($param['MODULE']))
                $names = $param['MODULE'];
            else
                $names = STR::to_array(',',$param['MODULE']);
                
                
        }else{
            
            for($i=0;$i<count($_modules);$i++)
                array_push($names,$_modules[$i]['CLASS']);   
        };

        for($i=0;$i<count($_modules);$i++){
            $use = false;
            if ($by_tag){
                for($t=0;$t<count($tags);$t++){
                    $use=in_array($tags[$t],$_modules[$i]['TAGS']);
                    if($use) break;
                }
            }else{
                for($n=0;$n<count($names);$n++){
                    $use=($names[$n]==$_modules[$i]['CLASS']);
                    if($use) break;
                }
            }
            
            if ($use) $_modules[$i]['ENABLE'] = $enable;
        }
        
        
    }
    
    public static function CREATE($owner){
        global $_modules;
        for($i=0;$i<count($_modules);$i++){
            if ($_modules[$i]['ENABLE']){
                $class = $_modules[$i]['CLASS'];
                $_modules[$i]['MODULE'] = new $class($owner);
            }
        }
        
    }
    public static function EXIST($name){
        global $_modules;
        for($i=0;$i<count($_modules);$i++){
            if ($_modules[$i]['CLASS']==$name){
                return true;
            }
        }
        return false;
    }

    public static function CONTENT(){
        global $_modules;
        for($i=0;$i<count($_modules);$i++){
            if ($_modules[$i]['ENABLE']){
                $module = $_modules[$i]['MODULE'];
                $module->CONTENT();
            }
        }
    }
    
    public static function AJAX(&$response){
        global $_modules;
        for($i=0;$i<count($_modules);$i++){
            if ($_modules[$i]['ENABLE']){
                $module = $_modules[$i]['MODULE'];
                if ($module->AJAX($response)) return true;
            }
        }
        return false;
    }
    
};

class WS_MODULE extends WS_CONTENT{
    public $owner;
    
    function __construct($owner){
        parent::__construct();
        $this->owner = $owner;
    }
} 
?>