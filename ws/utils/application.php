<?php

/*
    https://ws-framework-fmihel.c9users.io/ide/ws/utils/application.php
*/

/*
    // -------------------------------------------------------------------------
    // -------------------------------------------------------------------------

    application - модуль загрузчик, для использования в проект должен инициализироваться первым в каждом модуле

    -------------------------------------------------------------------------
    Подключение загрузчика
    -------------------------------------------------------------------------
    Ex:если $Application не инициализирована, то данный модуля является точкой входа в программу
    
    <?php
    if(!isset($Application)){
        require_once '../Common/UApplication.php';
    };
    ....
    ....
    if($Application->is_main(__FILE__)){
        // код выполняемый, в случае если файл является точкой входа в программу
    }
    ?>
    -------------------------------------------------------------------------
    Добавление модулей 
    -------------------------------------------------------------------------
    UNIT([<filename>] || [<tag>,<filename>] ); - filename - путь к файлу относительно стартового моудуля
    ф-ция возвращает путь к модулю по отношению к проекту или по отношению к tag, данный путь нужно использовать в
    ф-ции require_once 
    
    RESOURCE([<filename>] || [<tag>,<filename>] ) - регистрирует ресурсы по отношению тега (ссылка на путь)
    по умолчанию описаны два тега ws - путь к framework ws, и root - путь относительно корневой папки,utils,plugins..
    
    Добавить/изменит собственне ссылки на пути, можно с помощью ф-йии
    $Application->lib(tag,path) 
    
    Ex:
    require_once UNIT('mod_reg.php');
    require_once UNIT('js/form.php');
    require_once UNIT('ws','utils/dirs/files.php');
    
    RESOURCE('js/form.js');
    RESOURCE('js/form.css');
    RESOURCE('utils'js/form.css');

    // -------------------------------------------------------------------------
    // отключить кэширование броузера
    // Перед вызовом модуля application.php 
    // определить переменную
    // $NO_CACHE_APPLICATION = true;
    
    // -------------------------------------------------------------------------

*/

define('_DIRECTORY_SEPARATOR','/');


if (isset($NO_CACHE_APPLICATION)&&($NO_CACHE_APPLICATION===true)){
    
    header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
    header('Pragma: no-cache'); // HTTP 1.0.
    header('Expires: 0'); // Proxies.
    
};

require_once __DIR__.'/console.php'; 

function UNIT(){
    /*filename  || tag(ws/root),filename */
    global $Application;
    
    if(func_num_args()>1){
        $tag=(func_num_args()>0 ? func_get_arg(0) : 'project');
        $file=(func_num_args()>1 ? func_get_arg(1) : '');
        return $Application->includes($tag,$file);
    }else{
        $file=(func_num_args()>0 ? func_get_arg(0) : '');
        return $Application->includes($file);
    };
};

function RESOURCE(){
    /*filename  || tag(ws/root),filename */
    if(func_num_args()>1)
        UNIT(func_get_arg(0),func_get_arg(1));
    else
        UNIT(func_get_arg(0));
};
/** используется для автоматического расчета пути от проекта к ресурсу, 
 * но в случае когда путь задается относительно файла входящего в проект
 * Ex: RESOURCE(WARD(__DIR__,'autorize.js'));
 * Ex: RESOURCE(WARD(__DIR__).'autorize.js');
 * 
*/ 
function WARD($_DIR_,$file=''){
    return APP::ward($_DIR_,$file);   
}

function _LOGF($msg,$name='',$file='',$line='',$param=array()){
    global $Application;
    
    if (gettype($param)==='string')
        $param = APP::extParam($param);    
    
    
    //-----------------------------------------------------------------------
    
    $return = false;
    
    if (isset($param['count'])){

        $idx = false;

        for($i = 0;$i<count($Application->LOG_STORY);$i++){
            $d = $Application->LOG_STORY[$i];
            if (($d['file']===$file)&&($d['line']===$line)){
                $idx = $i;
                break;
            }    
        };  
        
        $lim = ( $idx===false?array('file'=>$file,'line'=>$line,'count'=>$param['count'],'start'=>(isset($param['start'])?$param['start']:0)):$d );
        

        
        $lim['start']--;
    
        if ($lim['start']<0){
            $lim['count']--;
            $return = $lim['count']<0;
        }else
            $return = true;
            
        if ($idx===false)
            $Application->LOG_STORY[]=$lim;
        else
            $Application->LOG_STORY[$idx]=$lim;

    };

    if ($return) 
        return;
    else    
        $Application->LOG(($name!==''?$name.':':'').$Application->_fmtVarLog($msg,$param),$file,$line);
};
function _LOG($msg,$file='',$line=''){
    global $Application;
    $Application->LOG($msg,$file,$line);
};

class APP{
	static function as_http_path($path){
			return str_replace("\\","/",$path);
		}
	static function eq_path($path1,$path2){
				return (self::as_http_path($path1) == self::as_http_path($path2));
		}
    static function slash($dir,$left,$right){
        if (trim($dir)=='') return '';
        /*учитываем вариант когда точка находится в имени корневой папки*/
        $root =  substr($_SERVER['DOCUMENT_ROOT'],strrpos($_SERVER['DOCUMENT_ROOT'],'/')+1);

        $dirs = explode(_DIRECTORY_SEPARATOR,trim($dir));
        $out = '';
        
        $is_dos = false;
        //print_r($dirs);
        //echo '<br>';
        if (count($dirs)>0){
					$s = $dirs[0];
					$is_dos = (substr($s,strlen($s)-1)==':');
				}	
        
        for($i=0;$i<count($dirs);$i++)
            $out.=(strlen($dirs[$i])>0?(strlen($out)>0?_DIRECTORY_SEPARATOR:'').$dirs[$i]:'');
        
        $last=$dirs[count($dirs)-1];
        return ((($left)&&(!$is_dos))?_DIRECTORY_SEPARATOR:'').$out.(($right)&&(($last==$root)||(!strpos($last,'.')))?_DIRECTORY_SEPARATOR:'');
    }
    /**
     * culc relation path from project start file to _dir_
     * use this in set path in RESOURCE or UNIT
     * Вычисляет относительный путь от стартового файла, к указанному пути
     * Ex. APP::ward(__DIR__) - путь от проекта к текущему исполняемому файлу
     * RESOURCE(App::ward(__DIR__).'grid.js');
     * RESOURCE(App::ward(__DIR__,'grid.js'));
    */
    static function ward($_dir_,$file=''){ 
        global $Application;
        $_dir_ = str_replace(array("\\",'/'),_DIRECTORY_SEPARATOR,$_dir_);
        
        $res = trim(self::rel_path(self::slash($Application->PATH,true,true),self::slash($_dir_,true,true)));
        
        return ($res!==''?APP::slash($res,false,true):'').($file!==''?APP::slash($file,false,false):'');
    }    
    static function abs_path($from,$to = ''){
        //S: Получение абсолютного пути из from  в to
         
        if ($to !== '')
        {
            $to     = APP::slash($to,true,false);
            $from   = APP::slash($from,false,false).APP::slash($to,true,false);
        };
            
        $path = $from;
        $path = str_replace(array('/', '\\'), _DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(_DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) 
        {
            if ('.' == $part) 
                continue;
            if ('..' == $part) 
            {
                array_pop($absolutes);
            }else{
                $absolutes[] = $part;
            };
        };
        return APP::slash(implode(_DIRECTORY_SEPARATOR, $absolutes),false,true);
    }
    static function rel_path($from,$to){
        //S: Получение относительного пути
        //from = '/home/decoinf3/public_html/test/myproject/';
        //to = '/home/decoinf3/public_html/rest/a
        //result ../../rest/a
        
        $ps = _DIRECTORY_SEPARATOR;
        $arFrom = explode($ps, rtrim($from, $ps));
        $arTo = explode($ps, rtrim($to, $ps));
        while(count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0]))
        {
            array_shift($arFrom);
            array_shift($arTo);
        }
        return str_pad("", count($arFrom) * 3, '..'.$ps).implode($ps, $arTo);
    }
    static function ext($file){
        //S: получите расширение файла
        $path = self::pathinfo($file);
        return $path['extension'];
    }
    static function get_path($file){
        //S: Выделяет путь файла
        $path = self::pathinfo($file);
        $out = $path['dirname'];
        if ($out=='.') $out='';
        return APP::slash($out,false,true);
    }
    static function get_file($file){
        //S: возвращает имя файла
        $path = self::pathinfo($file);
        return $path['filename'].'.'.$path['extension'];
    }
    static function without_ext($file){
        //S: возвращает имя файла без расширения
        $path = self::pathinfo($file);
        return $path['filename'];
    }
    static function pathinfo($file){
        $out = array('file'=>$file,'dirname'=>'','basename'=>'','extension'=>'','filename'=>'');
        $slash = '/';
        //------------------------------------------------
        $have_oslash = (mb_strpos($file,'\\')!==false);
        if ($have_oslash)
            $file = str_replace('\\',$slash,$file);
        //------------------------------------------------

        $lim = mb_strrpos($file,$slash);
        if ($lim!==false){
            $left = mb_substr($file,0,$lim);
            $right= mb_substr($file,$lim+1);
            
            $out['dirname'] = $left;
            $out['basename'] = $right;
            $out['filename'] = $right;
            
            $pos_ext = mb_strrpos($right,'.');
            if ($pos_ext!==false){
                $out['extension'] = mb_substr($right,$pos_ext+1);
                $out['filename'] = mb_substr($right,0,$pos_ext);
            }
            
        }else{
            $out['basename'] = $file;
            $out['filename'] = $file;
            
            $pos_ext = mb_strrpos($file,'.');
            if ($pos_ext!==false){
                $out['extension'] = mb_substr($file,$pos_ext+1);
                $out['filename'] = mb_substr($file,0,$pos_ext);
            };
            
        }

        //------------------------------------------------
        if ($have_oslash)
            foreach($out as $k=>$v)
                $out[$k] = str_replace($slash,'\\',$v);
        //------------------------------------------------
        
        return $out;
    }
    
    
    static function strrnd($len){
        //S: случайная строк длиной len
        $res = '';
        for($i=0;$i<$len;$i++)  
            if (rand(1,10) > 6)        
                $res.=chr(rand(48,57));
            else
                $res.=chr(rand(65,90));
        return $res;        
    }
    
    static function isAssoc($arr){
        if (is_array($arr)){
            if (array() === $arr) return false;
            return array_keys($arr) !== range(0, count($arr) - 1);
        }
        return false;
        
    }
    static function typeInfo($v){
        $type = gettype($v);
        
        if (($type==='array')&&(self::isAssoc($v)))
            $type = 'assoc';
        
        return $type;
    }
    static function repeatStr($str='',$count=0){
        $res = '';
        for($i=0;$i<$count;$i++)
            $res.=$str;    
        return $res;
    }
    /** преобразует строку вида 
     *  key:val,key:val,...  в соотвествующий хеш массив
     *  Ex: 
     *  $param = APP::extParam("count:10,start:1");
     *  $param = array('count'=>'10','start'=>'1');
     * 
     *  более продвинутая ф-ция см ARR::extend(...)
     */ 
    static function extParam($str){
        
        $arr  = explode(',',$str);
        $res = array();
        for($i=0;$i<count($arr);$i++){
            $d = explode(':',$arr[$i]);
            if (trim($d[0])!=='')
                $res[trim($d[0])] = trim($d[1]);
        }
        return $res;
    }
}

class TApplication{
    
    public $PUBLIC_HTML;// имя конечной корневой папки (public_html - обычно)
    public $HTTP_TYPE;// http or https
    
    public $PATH;       //  путь к проекту (файл с которого происходит старт проекта)
    public $CLASS_PATH; //  путь к данному файлу
    public $ROOT;       //  $_SERVER['DOCUMENT_ROOT'] корневой каталог
    public $FROM_ROOT;  //  путь к проекту начиная от корневого каталога 
    
    public $URL;        //  путь к проекту (без имени файла); site.ru/project/test/
    public $HTTP;       //  http путь к проекту (без имени файла) (http+URL); http://www.site.ru/project/test
    public $DOMEN;      // $_SERVER['HTTP_HOST']; http://www.site.ru/
    
    public $ADDR;        //  путь к проекту  http://site.ru/project/test/index.php
    public $REQUEST;     //  полная строка запроса http://domen.ru/path/file.php?param=78&...
    public $FILENAME;     // имя скрипта (без пути) с которого идет запуск
    
    public $LIBS;       // относительные пути (по отношению к проекту) к ресурсам (php,js,css,...) по умолчанию есть ws и root 
    
    // список ресурсов, подключенных с помощью RECOURCE .. ресурсы укладываются по расширениям файлов и содержат локальный и удаленный пути к ним
    // Ex: $this->EXTENSION['CSS'][0]['local'] or $this->EXTENSION['CSS'][0]['remote']
    public $EXTENSION;  
    
    public $LOG_FILENAME; // путь к лог файлу
    public $LOG_ENABLE;
    private $LOG_SESSION;
    public $LOG_SIZE;
    private $is_init_log;
    public $LOG_STORY = array(); 

    function __construct(){
        //$this->HTTP_TYPE     = isset()isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:$_SERVER['HTTP_X_FORWARDED_PROTO'];
        //$this->HTTP_TYPE     = $_SERVER['HTTPS']==1?'https' :( isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:$_SERVER['HTTP_X_FORWARDED_PROTO']);        
        $this->HTTP_TYPE     = (isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS'])==1?'https' :( isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:$_SERVER['HTTP_X_FORWARDED_PROTO']);
        //
        //$this->HTTP_TYPE     = 'https';
        $this->PUBLIC_HTML   = substr($_SERVER['DOCUMENT_ROOT'],strrpos($_SERVER['DOCUMENT_ROOT'],'/')+1);            
        
        $this->CLASS_PATH   = APP::slash(dirname(__FILE__),false,true);                       
        $this->PATH         = APP::slash(dirname($_SERVER['SCRIPT_FILENAME']),false,true);
        $this->ROOT         = APP::slash($_SERVER['DOCUMENT_ROOT'],false,true);
        $this->DOMEN        = $this->HTTP_TYPE.'://'.$_SERVER['HTTP_HOST'].'/';
        
        $this->FROM_ROOT    = str_replace($this->ROOT,'',$this->PATH);
        
        $this->URL          = $_SERVER['SERVER_NAME'].'/'.$this->FROM_ROOT;
        $this->HTTP         =$this->HTTP_TYPE.'://'.$this->URL;
        $this->ADDR         = $this->HTTP.APP::get_file($_SERVER['SCRIPT_FILENAME']);
        $this->REQUEST      = $this->ADDR.($_SERVER['QUERY_STRING']!==''?'?'.$_SERVER['QUERY_STRING']:'');
        $this->FILENAME     = APP::get_file($_SERVER['SCRIPT_NAME']);
        
        $this->LIBS = array();
        $this->lib('ws',        APP::rel_path($this->PATH,APP::abs_path($this->CLASS_PATH,'../source/')));
        $this->lib('utils',     APP::rel_path($this->PATH,APP::abs_path($this->CLASS_PATH,'../utils/')));
        $this->lib('plugins',   APP::rel_path($this->PATH,APP::abs_path($this->CLASS_PATH,'../plugins/')));

        $this->lib('root',      APP::rel_path($this->PATH,$this->ROOT));
        $this->lib('project',   '');

        $this->_ROOT = APP::rel_path($this->PATH,$this->ROOT);
        
        $this->EXTENSION = array(); 
        
        $this->LOG_FILENAME = APP::rel_path($this->PATH,$this->ROOT).'ws.log';
        $this->LOG_ENABLE = true;
        $this->LOG_TO_ERROR_LOG = false;
        $this->is_init_log = false;        
        $this->LOG_SIZE = 100000;
        
    }
    
    function lib($tag,$relative_path){
        //S: регистрация или изменение пути к папке с ресрусами
        $this->LIBS[$tag] = $relative_path;
    }
    
    public function is_main($file){
        //S: Возвращает true если __FILE__ является файлом старта
        return APP::eq_path($file,$_SERVER['SCRIPT_FILENAME']);
    }
    
    public function includes(/*filename  || tag(ws/root),filename */){
        //S: добавление ресурса в приложение 
        /*D:  
            includes(filename) - добавляет ресурс с использованием пути относительно стартового файла проекта
            includes(tag,filename) - добавляет ресурс относительно папки зарегистрированной под тегом tag
            по умолчанию есть tag='ws' => <root_path>/ws/ и tag='root'=><root_path>
            
            файлы с расширением php подгружаются командой require_once 
            все остальные файлы сохряняются в массиве EXTENSION
            
            Варианты с параметрыми:
            '[theme:name]filename' - будет использоваться только в dcss теме name Ex: [color:dark]style.css
            +filename    - будет использоваться как прямая ссылка ( не пойдет в рендеринг) Ex: +index.js
            Файлы с полным указание url так же непойдут в рендеринг Ex: https://my/build/index.js
            
        */
        
        $tag = 'project';
        if(func_num_args()>1){
            $tag=(func_num_args()>0 ? func_get_arg(0) : $tag);
            $file=(func_num_args()>1 ? func_get_arg(1) : '');
        }else
            $file=(func_num_args()>0 ? func_get_arg(0) : '');
        
        $direct_url = (strpos($file,"+")===0);
        if ($direct_url)
            $file = substr($file,1);
        
        $params = strpos($file,"?");
        
        if ($params!==false){
            $pos    = $params;
            $params = substr($file,$pos);
            $file   = substr($file,0,$pos);
        }else
            $params = '';
        
        $have_dcss = strpos($file,"]");
        if ($have_dcss!==false){
            
            $dcss = substr($file,0,$have_dcss);
            $file = trim(substr($file,$have_dcss+1));
            
            $dcss = str_replace(array('[',']'),'',$dcss);
            $dcss = explode(':',$dcss);
            
            $dcss = array('style'=>trim($dcss[0]),'name'=>trim($dcss[1]));
            
        }else
            $dcss=false;
        
        
        $ext = strtoupper(APP::ext($file));

        if ($ext=='PHP'){
            $filename = APP::slash($this->LIBS[$tag],false,true).APP::slash($file,false,false);
        }
        if(!isset($this->EXTENSION[$ext]))
            $this->EXTENSION[$ext]=array();
        
        $http = strpos($file,'http');
        
        if ($http===0){
            $remote = $file;
            $local = '';
        }else{
            if ($tag=='project'){                
                $dir=APP::get_path($file);
                $remote = $this->HTTP.$dir.APP::get_file($file);
                $local  = $file;
            }else{
                $dir    = APP::slash($this->LIBS[$tag],false,true).APP::get_path($file);
                $dir    = str_replace($this->ROOT,'',APP::abs_path($this->PATH,$dir));
                $remote   = $this->DOMEN.$dir.APP::get_file($file);
                $local =  APP::slash($this->LIBS[$tag],false,true).$file;
            }    
        }

        if ($direct_url)
            $local = '';

        
        $this->_add_to_extension($ext,array('remote'=>$remote,'local'=>$local,'dcss'=>$dcss,'params'=>$params));
            
        if ($ext=='PHP')
            return $filename;
        else
            return $local;
         
            
    }

    /** возвращает хеш сумму md5 по заданному расширению (для проверки появился ли новый файл в списке) 
     * $add - дополнительная строка добавляемая к сумме
    */
    public function getExtHash($ext,$add=''){
        $files = '';
        if (isset($this->EXTENSION[$ext]))    
        for($i=0;$i<count($this->EXTENSION[$ext]);$i++)
            $files.= $this->EXTENSION[$ext][$i]['local'];
        $files.=$add;
        return md5($files);
    }
    
    /** возвращает весь контент ресурса одним файлом */
    public function getExtConcat($ext,$delim){
        if(!isset($this->EXTENSION[$ext]))
            $this->EXTENSION[$ext]=array();
        $res = '';
        if (isset($this->EXTENSION[$ext]))
        for($i=0;$i<count($this->EXTENSION[$ext]);$i++){
            $file = trim($this->EXTENSION[$ext][$i]['local']);
            if (($file!=='')&&(file_exists($file)))
                $res.=($res!=''?$delim:'').file_get_contents($file);
        }
        return $res;
    }
    
    private function _add_to_extension($ext,$dat){
        if(!isset($this->EXTENSION[$ext]))
            $this->EXTENSION[$ext]=array();
        
        for($i=0;$i<count($this->EXTENSION[$ext]);$i++){
            $exist = $this->EXTENSION[$ext][$i];
            if ($exist['remote']==$dat['remote']) return;
        }    
        array_push($this->EXTENSION[$ext],$dat);
    }

    private function _init_log(){
        if (!$this->is_init_log){
            
            //$this->LOG_FILENAME = APP::rel_path($this->PATH,$this->ROOT).$this->LOG_FILENAME;
            $this->LOG_SESSION = APP::strrnd(3);
            
            
            if ((!$this->LOG_TO_ERROR_LOG)&&((!file_exists($this->LOG_FILENAME)||(filesize($this->LOG_FILENAME)>$this->LOG_SIZE))))
            {
                    $fileopen=fopen($this->LOG_FILENAME, "w");
                    fputs($fileopen, '');
                    fclose($fileopen);            
            };
            
            $this->is_init_log = true;    
        }
    }
    /**
     * форматирование переменной для более удобного просмотра внутри лога wsi
    */ 
    public function _fmtVarLog($v,$p=array(),$level=1){
    
        $p = array_merge([
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
        ],$p);
        
        //-----------------------------------------------------------------------
        // использование коротких имен
        $short = array('str'=>'strLimit','ass'=>'assLimit','arr'=>'arrLimit');
        foreach($short as $k=>$val){
            if (isset($p[$k])){
                $p[$val] = $p[$k];
                unset($p[$k]);
            }    
        };
        // использование настройки сразу всем
        if (isset($p['all'])){
            foreach($short as $k=>$val)
                $p[$val] = $p['all'];
        };
        //-----------------------------------------------------------------------

        $type = TYPE::info($v);
        $space = '&nbsp;';
        //$space = '..';
        $tab = STR::repeat($space,$level*2);
        $tab0= STR::repeat($space,($level-1)*2);
        
        $cr = "\n";
        
        
        
        $L = array('assoc'=>'{','array'=>'[','string'=>'','object'=>'','resource'=>'','integer'=>'','double'=>'','NULL'=>'','boolean'=>'','unknown type'=>'');
        $R = array('assoc'=>'}','array'=>']','string'=>'','object'=>'','resource'=>'','integer'=>'','double'=>'','NULL'=>'','boolean'=>'','unknown type'=>'');
        
        $LT = array(
            'string'    =>'<span style="color:'.$p['strColor'].'">',
            'object'    =>'(','resource'=>'^{',
            'integer'   =>'<span style="color:'.$p['numColor'].'">',
            'double'    =>'<span style="color:'.$p['numColor'].'">',
            'NULL'      =>'<span style="color:'.$p['nullColor'].'">',
            'boolean'   =>'<span style="color:'.$p['boolColor'].'">',
            'unknown type'=>''
        );
        $RT = array('string'=>'</span>','object'=>')','resource'=>'}','integer'=>'</span>','double'=>'</span>','NULL'=>'</span>','boolean'=>'</span>','unknown type'=>'');
        
        $p['deep']--;
        if (($p['deep']<0)&&(($type==='assoc')||($type==='object')||($type==='array')))
            return $L[$type].'..'.$R[$type]; 
        
        
        $out = '';
        
        if ($type ==='assoc'){
             
            $keys = array_keys($v);
            $maxlen = 0;
            for($i=0;$i<count($keys);$i++)
                $maxlen = max($maxlen,strlen($keys[$i]));
                
            $all = count($keys);
            $i=0;
            if ($p['assLimit']>0)
                $cnt = min($all,$p['assLimit']-1);
            else 
                $cnt = $all;    
            
            
            $out.=$L[$type].$cr;
            foreach($v as $key=>$val){
                $t = APP::typeInfo($val);
                
                $add = '';
                if (($t==='string')||($t==='boolean')||($t==='double')||($t==='integer')||($t==='NULL'))
                    $add = APP::repeatStr('&nbsp;',$maxlen-strlen($key)); 
                    
                
                $out.=$tab.$key.':'.$add.$this->_fmtVarLog($val,$p,$level+1).$cr;

                
                if (($i===$cnt)&&($cnt!==$all-1)){
                    $out.=$tab.'..'.$cr;
                    break;
                }
                $i++;
            }
            
            $out.=$tab0.$R[$type].$cr;
        }else if ($type ==='array'){    
            $all = count($v);
            
            if ($p['arrLimit']>0)
                $cnt = min($all,$p['arrLimit']-1);
    
            $out.='';
            
            $out1 = $L[$type].$cr;
            $out2 = $L[$type];
            
            $horiz = $p['horiz'];
            $len ='';
            for($i=0;$i<$all;$i++){
                
                
                $out1.=$tab.$i.':'.$this->_fmtVarLog($v[$i],$p,$level+1).$cr;
                
                $t= TYPE::info($v[$i]);
                if (($horiz)&&(($t==='string')||($t==='boolean')||($t==='double')||($t==='integer')||($t==='NULL')))
                    $out2.=($i>0?',':'').$this->_fmtVarLog($v[$i],$p,$level+1);
                else
                    $horiz = false;
                    

                if (($i===$cnt)&&($cnt!==$all-1)){
                    $len = '<font style="font-size:0.8em;color:#494230"> '.$all.' </font>'; 
                    $out1.=$tab.'..'.$cr;
                    $out2.=',..';
                    break;
                }
     
            }
             
            $out.=($horiz?$out2:$out1.$tab0).$R[$type].$len.$cr;
        }else if ($type==='object'){
            if (is_a($v,'Exception') || is_a($v,'\Exception')){
                $name = '<span style="color:#ff0000">Exception:</span>';  
                $out.=$name.'"'.$v->getMessage().'"';
            }else{
                $name = get_class($v);  
                $out.=$name.$this->_fmtVarLog(get_object_vars($v),$p,$level);
            }
    
        }else{
            $add_left = '';
            if ($type==='boolean')
                $v=$v?'true':'false';
            if ($type==='NULL')
                $v='NULL';
            
            if ($type=='string'){
                /** отображение кодировки, если кодировка UTF-8 то не отображаем*/
                $len = mb_strlen($v);
                $slen = strlen($v);
                $coding = mb_detect_encoding($v);
                
                if ($slen!=$len)
                    $slen = "<span style='color:#9081F1'>$len/$slen</span>";
                    
                if ($coding!=='UTF-8') {
                    $scolor = 'color:#9081F1';
                    $ocoding=$coding.','; 
                }else{
                    $scolor='';
                    $ocoding = $coding.',';
                }
                
                $add_left = ( $p['showStrEncoding'] ? "<span style='font-size:0.8em;$scolor'>($ocoding$slen)</span>" : '' );   
                // преобразование в UTF-8 тк только в ней отображается 
                if ($coding!='UTF-8'){
                    $v=mb_convert_encoding($v,'utf-8',$coding);
                    $len = mb_strlen($v);
                }
                
                $v = htmlspecialchars($v); 
                if ($p['strLimit']>0){  
                    if ($p['strLimit']<$len)
                        $v = str_replace("\n"," ",mb_substr($v,0,$p['strLimit']).'..');
                }
                
                
                $s = explode("\n",$v);
                $v = '"';
                $cnt = count($s);
                for($i=0;$i<$cnt;$i++)
                    $v.= ($i>0?$tab:'').$LT[$type].$s[$i].($i<$cnt-1?"\n":'"').$RT[$type];
                
            }
            $out.= $add_left.$LT[$type].$v.$RT[$type];
        }     
    
        
        return $out;        
    }
    
    public function LOG($msg,$file='',$line=''){
        
        if (!$this->LOG_ENABLE) 
            return;
        
        $this->_init_log();
        
        
        
        $date =date("d-M-Y");
        $time =date("H:i:s");
        //$ip   =getenv("REMOTE_ADDR");
        
        $name = ($file!==''?str_replace('/'.$this->ROOT,'',$file):'');
        
        $cr="\n";
        
        //$from = ($file!==''?' '.$name:'').($line!==''?':'.$line:'xxxx').($dir!==''?' '.$dir:'');
        $from = ($file!==''?' '.$name:'').($line!==''?':'.$line:':xxxx');
        if ($this->LOG_TO_ERROR_LOG)
            $out = " ".trim($from).$cr;
        else    
            $out = "[$date $time {".$this->FROM_ROOT.$this->FILENAME."} $from ]".$cr;
        
        if (is_string($msg))
            $out.=trim($msg).$cr;
        else if (is_array($msg)){
            $cnt=count($msg);
            $zer = strlen($cnt.'')+1;
            $cntf = str_pad(''.$cnt,$zer,'0',STR_PAD_LEFT);
            
            for($i=0;$i<$cnt;$i++){
                $out.=str_pad(''.($i+1),$zer,'0',STR_PAD_LEFT).'/'.$cntf.': ';
                if (is_string($msg[$i]))
                    $out.=trim($msg[$i]).$cr;
                else
                    $out.=trim(print_r($msg[$i],TRUE)).$cr;
            }
        }else 
            $out.=trim(print_r($msg,true)).$cr;
            
            
        if($this->LOG_TO_ERROR_LOG){
            error_log($out);
        }else{
            $fileopen=fopen($this->LOG_FILENAME, "a");
            fputs($fileopen, $out);
            fclose($fileopen);            
        };
    }    

    public function debug_info($cr='<br>'){
        $res='';
        $res.='HTTP_TYPES ['.$this->HTTP_TYPE.']'.$cr;
        $res.='PUBLIC_HTML ['.$this->PUBLIC_HTML.']'.$cr;

        $res.='CLASS_PATH ['.$this->CLASS_PATH.']'.$cr;
        $res.='PATH ['.$this->PATH.']'.$cr;
        $res.='ROOT ['.$this->ROOT.']'.$cr;
        $res.='DOMEN ['.$this->DOMEN.']'.$cr;
        $res.='FROM_ROOT ['.$this->FROM_ROOT.']'.$cr;
        $res.='URL ['.$this->URL.']'.$cr;
        $res.='HTTP ['.$this->HTTP.']'.$cr;
        $res.='ADDR ['.$this->ADDR.']'.$cr;
        $res.='REQUEST ['.$this->REQUEST.']'.$cr;
        $res.='FILENAME ['.$this->FILENAME.']'.$cr;
        $res.=$cr;        
        foreach($this->LIBS as $ext=>$path)
            $res.='LIBS['.$ext.']='.$path.$cr;                        
        $res.=$cr;        
        $res.='EXTENSION ['.$cr;
        foreach($this->EXTENSION as $ext=>$mods){
            
            $res.='EXT ['.$ext.']'.$cr;
            
            for($i=0;$i<count($mods);$i++){
                $res.=$i;
                $res.=' remote['.$mods[$i]['remote'].']'.$cr;
                $res.=' local['.$mods[$i]['local'].']'.$cr;
                $res.=' dcss['.print_r($mods[$i]['dcss'],true).']'.$cr;
            };
            
        }
        
        $res.='] EXTENSION'.$cr;
        $res.=$cr;
        
        foreach($_SERVER as $k=>$v){
            $res.='$_SERVER['.$k.']=['.$v.']'.$cr;
        }

        foreach($_ENV as $k=>$v){
            $res.='$_ENV['.$k.']=['.$v.']'.$cr;
        }

        foreach($_REQUEST as $k=>$v){
            $res.='$_REQUEST['.$k.']=['.$v.']'.$cr;
        }
        
        return $res;
    }

}


if (!isset($Application))
    $Application = new TApplication();

if ($Application->is_main(__FILE__)){
    //echo $_SERVER['REQUEST_SCHEME'].'<hr>';
    //echo $_SERVER['REQUEST_SCHEME'].'<hr>';
    //echo $_SERVER['HTTP_X_FORWARDED_PROTO'].'<hr>';
    
    //echo $Application->debug_info();
 } 
?>