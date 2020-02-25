<?php

/** 
 * Класс вывода в log файл, с интерфейсом похожим на интерфейс console для js
 * Для вывода использует Application-LOG
*/

class console{

    private static $params= [
        
        'assLimit'  =>20,
        'arrLimit'  =>20,
        'strLimit'  =>100,
        'horiz'     =>true,
        'numColor'  =>'#2D7AF2',
        'strColor'  =>'#EB272E',    
        'boolColor' =>'#58B029',
        'nullColor' =>'#A074C7',    
        'deep'      =>5,
        'showStrEncoding'=>false,        
        
    ];
    

    /** 
     * get or set console param
     * @return array of params 
    */
    public static function params($params = false){
        if (gettype($params) === 'array')
            self::$params = array_merge(self::$params,$params);
        return self::$params;
    }

    public static function log(...$args){
        global $Application;
        
        $trace = self::trace();
        
        if (count($args) === 1 )
            $Application->LOG($Application->_fmtVarLog($args[0],self::$params),$trace['file'],$trace['line']);
        else
            $Application->LOG($Application->_fmtVarLog($args,self::$params),$trace['file'],$trace['line']);
    }

    public static function all(...$args){
        global $Application;
        
        $trace = self::trace();
        
        if (count($args) === 1 )
            $Application->LOG($Application->_fmtVarLog($args[0],['all'=>0]),$trace['file'],$trace['line']);
        else
            $Application->LOG($Application->_fmtVarLog($args,['all'=>0]),$trace['file'],$trace['line']);
    }
    



    public static function error(...$args){
        global $Application;
        $p = array_merge(self::$params,['all'=>0]);
        
        $trace = self::trace();
        $left = '<span style="color:#ffffff;background:#840000;border-radius:9px;padding-left:5px;padding-right:5px;padding-bottom:2px">';
        $right = '</span>';
        
        if (count($args) === 1 ){
            
            
            if (is_a($args[0],'Exception') || is_a($args[0],'\Exception'))
                $Application->LOG($left.'Exception:'.$right.$Application->_fmtVarLog($args[0]->getMessage(),$p),$trace['file'],$trace['line']);
            else
                $Application->LOG($left.'Error:'.$right.$Application->_fmtVarLog($args[0],$p),$trace['file'],$trace['line']);
        }else{
            $Application->LOG($left.'Error:'.$right.$Application->_fmtVarLog($args,$p),$trace['file'],$trace['line']);
        }
    }

    private static function trace(){

        $p = self::$params;
        $trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS,3);
        $len = count($trace);
        $out = [
            'line'=>false,
            'func'=>false,
            'type'=>false,
            'file'=>false,
            'class'=>false,
            'fmt'=>0
        ];

        if ($len===2){
            $out['fmt']=1;            
            
            $out['line'] = isset($trace[1]['line']) ? $trace[1]['line'] : false;
            $out['file'] = isset($trace[1]['file']) ? $trace[1]['file'] : false;
            
        };
        
        if ($len===3){        
            $out['fmt']=2;;            
            
            $out['line']=isset($trace[1]['line']) ? $trace[1]['line'] : false;
            $out['file']=isset($trace[1]['file']) ? $trace[1]['file'] : false;
            $out['func']=isset($trace[2]['function']) ? $trace[2]['function'] : false;
            $out['type']=isset($trace[2]['type']) ? $trace[2]['type'] : false;
            $out['class']=isset($trace[2]['class']) ? $trace[2]['class'] : false;
        }

        return $out;
    }

    
};   
    


?>