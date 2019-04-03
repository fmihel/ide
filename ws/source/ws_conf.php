<?php
/**
 * Загрузка настроек проекта 
 * Настройки проекта лежат в файле ws_conf.php или ws_conf.json  ( в корне проекта)
 * Файл может отсутствовать!! Приортетным считается php
 * 
 * В самом проекте можно предопределить значения
 * Определние настроек
 * WS_CONF::DEF('renderPath','_render/');
 * WS_CONF::DEF('mode','development');
 * Однако, если в ws_conf.json  соотвествующая настройка , то она переопределит существующую
 * 
 * Если же необходимо, намеренно переопределять параметр из программы, то можно использовать 
 * WS_CONF::SET('mode','production'); 
 * 
 * Получение настоек
 * $mode = WS_CONF::GET('mode');
 * 
 * Example: ws_conf.php
 * <?php
 *  $ws_conf = array(
 *      'mode'=>'production',
 *      'renderPath'=>'_build/',
 *  )
 * ?>
 * 
 * Example: ws_conf.json
 * {
 *      mode:'production',
 *      renderPath:'_build/',
 * }
 * 
*/


class WS_CONF{
    static function GET($name,$default=null){
        global $_ws_conf;
        return $_ws_conf->get($name,$default);
    }
    
    static function SET($name,$mean){
        global $_ws_conf;
        return $_ws_conf->set($name,$mean);
    }
    
    static function DEF($name,$default=''){
        global $_ws_conf;
        return $_ws_conf->def($name,$default);
    }
    
    static function debug_info($cr='<br>'){
        global $_ws_conf;
        return $_ws_conf->debug_info($cr);
    }
    
    static function LOAD($__DIR__,$file='',$reopen=false){
        global $_ws_conf;
        $_ws_conf->loadFromFile($__DIR__,$file,$reopen);
    }    
    
};



class _WS_CONF{
    public $param;
    private $loadedFiles;
    function __construct(){
        $this->param = array();
        $this->loadedFiles = array();
        $this->preLoad();
        
    }
    /** начальная загрузка файла конфигурации */
    private function preLoad(){
        
        $file = 'ws_conf.php';
        if (file_exists($file))
            $this->loadFromFile($file);
        else{
            $file = 'ws_conf.json';
            if (file_exists($file))
                $this->loadFromFile($file);
        };    
    }
    /** 
     * загрузка конфига из файла ( объединяется с текущей конфигурацией) 
     * Возможно ипользовать в двух вариантах
     * 1. loadFromFile(file,[reopen]) тогда file - путь к конфигу относительно папки запускаемого скрипта
     * 2. loadFromFile(dir,file,[reopen]) тогда file - путь относительно абсолютного пути dir ( dir = __DIR__ - то относительно файла который вызывает loadFromFile)
     * 
    */
    function loadFromFile($__DIR__,$file='',$reopen=false){
        global $Application;
        
        if ($file===''){
            
            $file = $__DIR__;
            $dirname = false;
            
        }else{
            if (gettype($file)==='boolean'){
                $reopen = $file;
                $file = $__DIR__;
            }else{
                
                $abs =  APP::abs_path($__DIR__,$file);
                $file = APP::rel_path($Application->PATH,$abs);                
            }    
        };    
        

        
        if (!class_exists('APP')){
            $this->log('class APP not defined, use require_once "...application.php" first','',__FILE__,__LINE__);
            return false;
        };

        $abs = APP::abs_path($Application->PATH,$file);    
        
        if (!$reopen){

            if (array_search($abs,$this->loadedFiles)!==false){
                // $this->log('already opened '.$file,'',__FILE__,__LINE__,'all:0');
                return false;
            }
        }    
        

        if (file_exists($file)){
            
            $ext = pathinfo($file,PATHINFO_EXTENSION);
            if ($ext ==='php'){
                require_once $file;
                $this->param = ARR::extend($this->param,$ws_conf);
                if (array_search($abs,$this->loadedFiles)===false)
                    $this->loadedFiles[]=$abs;
                return true;
            }elseif($ext==='json'){
                $cont = file_get_contents($file);
                $this->param = ARR::extend($this->param,$cont);
                if (array_search($abs,$this->loadedFiles)===false)
                    $this->loadedFiles[]=$abs;
                return true;
            }
        }else{
            $this->log($file,'file not exists',__FILE__,__LINE__);
        }
        return false;
    }
    
    function def($name,$mean){
        if (!isset($this->param[$name]))
            $this->param[$name] = $mean;
    }

    function set($name,$mean){
        $this->param[$name] = $mean;
    }
    
    function get($name,$default=null){
        
        if ( !isset($this->param[$name]) && is_null($default) ){
            $this->log('get undefined var ['.$name.'] ','warn',__FILE__,__LINE__);    
            return null;
        } else {
            $this->def($name,$default);
            return $this->param[$name];
        }    
    }
    function clear(){
        $this->param = array();
    }
    
    function debug_info($cr='<br>'){
        
        $out = 'WS_CONF{'.$cr;
        $out.='->param['.count($this->param).'] ['.$cr;
        $i = 0;
        foreach($this->param as $name=>$val){
            $out.=($i++).': ['.$name.']:'.gettype($val).' = ';
            $out.=print_r($val,true).$cr;
        }    
        $out.=']'.$cr;

        $out.='->loadedFiles['.count($this->loadedFiles).'] ['.$cr;
        $i = 0;
        foreach($this->loadedFiles as $name){
            $out.=($i++).': "'.$name.'"'.$cr;
        }    
        $out.=']'.$cr;
        

        $out.='}'.$cr;
        return $out;
    }
    
    function log($msg,$name='',$file='',$line='',$param=''){
        
        if (function_exists('_LOGF'))
            _LOGF($msg,$name,$file,$line,$param);
        $out = '';    
        
        $out.= $file!==''?'['.$file.']':'';
        $out.= $line!==''?'['.$line.']':'';
        
        if ($name!=='')
            $out.= " ".$name.':'.$msg.'';
        else    
            $out.= ' '.$msg.'';
            
        error_log($out);
    }
    
};

$_ws_conf = new _WS_CONF();


?>