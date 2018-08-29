<?php
$_CT=array();

class CT{
    
    public static function START($name,$limit = 0,$round=2){
        global $_CT;
        $_CT[$name] = array('start'=>microtime(true),'limit'=>$limit,'round'=>$round);
    }
    
    public static function STOP($name){
        global $_CT;
        $delta = round(microtime(true)-$_CT[$name]['start'],$_CT['round']);
        
        if ($delta>$_CT[$name]['limit']){
            $msg = "$name time:".$delta.'[s]';
            _LOG($msg,__FILE__,__LINE__);
        }    
    }
    
    public static function MIDDLE_START($name){
        global $_CT;
        
        if (!isset($_CT[$name]))
            $_CT[$name]=array('start'=>microtime(true),'all'=>0,'step'=>1);
        else
            $_CT[$name]=array('start'=>microtime(true),
                'all'=>$_CT[$name]['all'],
                'step'=>$_CT[$name]['step']+1);
    }
    
    public static function MIDDLE_STOP($name){
        global $_CT;
        $_CT[$name]['all']+=microtime(true)-$_CT[$name]['start'];
    }    
    
    public static function MIDDLE_LOG($name){
        global $_CT; 
        $msg = $name.' step = '.$_CT[$name]['step'].' all = '.round($_CT[$name]['all'],4).' middle = '.round($_CT[$name]['all']/$_CT[$name]['step'],4).'[sec]';
        
        _LOG($msg,__FILE__,__LINE__);
    }
};
?>