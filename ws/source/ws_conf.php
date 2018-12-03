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
    static function GET($name,$default=''){
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
    
};

class _WS_CONF{
    public $param;
    
    function __construct(){
        $this->param = array();
        $this->load();
    }
    
    function load(){
        
        $file = 'ws_conf.php';
        if (file_exists($file)){
            
            require_once $file;
            $this->param = ARR::extend($this->param,$ws_conf);
            
        }else{    
            
            $file = 'ws_conf.json';
            if (file_exists($file)){
                $cont = file_get_contents($file);
                $this->param = ARR::extend($this->param,$cont);
            }
        };    
    }
    
    function def($name,$mean){
        if (!isset($this->param[$name]))
            $this->param[$name] = $mean;
    }

    function set($name,$mean){
        $this->param[$name] = $mean;
    }
    
    function get($name,$default){

        $this->def($name,$default);
        return $this->param[$name];
    }
    
};

$_ws_conf = new _WS_CONF();

?>