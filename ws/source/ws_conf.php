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
    static function LOAD($file='',$merge=true){
        global $_ws_conf;
        return $_ws_conf->load($file,$merge);
    }
    static function debug_info($br='<br>')
    {
        global $_ws_conf;
        return $_ws_conf->debug_info($br);
    }
};

class _WS_CONF{
    public $param = array();
    
    function __construct(){
        $this->load();
    }
    /**
     * загрузка настроек
     * @param string $file имя файла в котором храняться параметры по умоляанию
     * @param bool $mereg  true - текущие данные будут смешаны,false - текущие данные будут перезаписаны
     * 
     */
    public function load($file='',$merge = true){
        $conf = false;
        
        if ($file===''){
            $file = 'ws_conf.php';
            $file = file_exists($file)?$file:'ws_conf.json';
        }

        if (!file_exists($file)) return;

        $ext =  pathinfo($file);
        $ext = $ext['extension'];

        if ($ext ==='php'){

            require_once $file;
            //$ws_conf defined in $file
            $conf = ARR::extend($this->param,$ws_conf);
            
        }else{
            
            $cont = file_get_contents($file);
            $conf = ARR::extend($this->param,$cont);

        };
        
        $this->param = $merge?ARR::extend($conf,$ws_conf):$conf;
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

    function debug_info($br){
        $out = '';
        foreach($this->param as $key=>$val)
            $out.=($out!==''?$br:'').$key.''.'['.$val.']';
        
        return $out;
    }
    
    
};

$_ws_conf = new _WS_CONF();


?>