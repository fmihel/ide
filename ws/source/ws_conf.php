<?php
/**
 * Загрузка настроек проекта 
 * Настройки проекта лежат в файле ws_conf.json ( в корне проекта)
 * Файл может отсутствовать!!
 * 
 * В самом проекте можно предопределить значения
 * Определние настроек
 * WS_CONF::DEF('renderPath','_render/');
 * WS_CONF::DEF('mode','development');
 * 
 * Однако, если в ws_conf.json  соотвествующая настройка , то она переопределит существующую
 * 
 * Получение настоек
 * $mode = WS_CONF::GET('mode');
 * 
*/

$_ws_conf = new _WS_CONF();

class WS_CONF{
    static function GET($name,$default=''){
        global $_ws_conf;
        return $_ws_conf->get($name,$default);
    }
    static function DEF($name,$default=''){
        global $_ws_conf;
        return $_ws_conf->def($name,$default);
    }
    
};

class _WS_CONF{
    public $param;
    
    function __construct(){
        $this->param = array();
        $this->load('ws_conf.json');
    }
    
    function load($file){
        if (file_exists($file)){
            $cont = file_get_contents($file);
            $this->param = ARR::extend($this->param,$cont);
        }
    }
    
    function def($name,$mean){
        if ((!isset($this->param[$name]))||($this->param[$name]==''))
            $this->param[$name] = $mean;
    }
    
    function get($name,$default){

        $this->def($name,$default);
        return $this->param[$name];
    }
    
};


?>