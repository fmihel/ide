<?php

if(!isset($Application))
  require_once '../utils/application.php';
require_once UNIT('utils','ctime.php'); 
 

define('DCR',"\n");
//define('DCR',"");


require_once UNIT('utils','common.php');
require_once UNIT('utils','git.php');

require_once UNIT('ws','ws_conf.php');
require_once UNIT('ws','ws_conf_def.php');
require_once UNIT('ws','ws_request.php');
require_once UNIT('ws','ws_common.php');
require_once UNIT('ws','ws_utils.php');
require_once UNIT('ws','ws_frame.php');
require_once UNIT('ws','ws_module.php');
require_once UNIT('ws','ws_dcss.php'); 


RESOURCE('plugins','jquery/jquery-3.3.1.min.js');
RESOURCE('plugins','common/dom.js');
RESOURCE('plugins','common/addition.js');
RESOURCE('plugins','common/byReady.js');
RESOURCE('plugins','common/ut.js');
RESOURCE('plugins','common/jhandler.js');
RESOURCE('plugins','jx/jx.js');
RESOURCE('plugins','common/dvc.js');
RESOURCE('ws','ws.js');
RESOURCE('ws','dcss.js');

/*
    Подключение данных модулей автоматически если  есть расширения jsx ( см WS.RENDER() )
    RESOURCE("https://unpkg.com/react@16/umd/react.production.min.js");
    RESOURCE("https://unpkg.com/react-dom@16/umd/react-dom.production.min.js");
    RESOURCE("https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/6.25.0/babel.min.js");
*/

require_once UNIT('utils','encoding.php');

// ---------------------------------------------------------------

$_WS = null;// ссылка на объект WS инициализируем в конструкторе WS

class WS extends WS_CONTENT{
    public $root;
    
    public $title; // название приложения 
    public $tabColor; // цвет вкладки в chrome mobile
    public $version; // номер версии (используется для кеширования)
    public $loadOptimizedResources; // будут загружаться оптимизированные скрипты ( если существуют), т.е у которых к имени файла добавлено .min
    public $icon;// путь к иконке приложения (если не указан то берется из корня приложения, если  в корне нет, то берет от wsi)
    public $mode;// тип сборки production | development
    public $renderPath;// тип сборки production | development
    function __construct(){
        global $_WS;
        $_WS = $this;
        parent::__construct();
        
        $this->title    = false;
        $this->tabColor = false;
        
        $this->root = null;
        $this->loadOptimizedResources=false;
        $this->icon = '';

        $this->version = '';
        // тип сборки production | development | assembly
        $this->mode         = WS_CONF::GET('mode');
        $this->renderPath   = WS_CONF::GET('renderPath');
    } 
    
    public function RUN(){
        global $REQUEST;
        
        if ($this->ONLOAD()){
            MODULES::CREATE($this);
            
            if($REQUEST->IsAjax())
                $this->CALLBACK();                
            else
                $this->RENDER();        
        };
    }
    
    public function ONLOAD(){
        return true;
    
    }
    
    public function CALLBACK(){
        global $REQUEST;
        $response = '';

        if ($REQUEST->ID =='LOAD_DCSS_STYLE'){
            /*подгрузка dcss*/
            
            $styles = $REQUEST->VALUE['styles'];
            $device = $REQUEST->VALUE['device'];
            
            
            DEVICE::SET($device);
            DCSS::STYLES($styles);
            $dcss = DCSS::RENDER();
            
            $dcss['res'] = 1;
            $response = $dcss;
        }else{
            if (!$this->AJAX($response))
                MODULES::AJAX($response);
        }
        
        
        ENCODING::OUT($REQUEST->RESPONSE($response));
    }
    
    public function RENDER(){
        
        global $CODING;
        global $Application;
        global $REQUEST;
        
        //---------------------------------------------
        if ($this->version !== ''){
            if ($this->version ==='nocache')
                $version = 'nocache='.strtolower(STR::random(5));
            else 
                $version = $this->version;
        }else
            $version ='';
        
        //---------------------------------------------
        
        $styles = DCSS::STYLES();
        $dcss   = DCSS::RENDER();
        
        //---------------------------------------------
        $this->root = FRAME::ADD('')->TAG_NAME('body');
        //---------------------------------------------
            
        $this->CONTENT();
        MODULES::CONTENT();
        
        //---------------------------------------------
        $res ='<?xml version="1.0" encoding="'.$CODING.'"?>'.DCR;
        $res.='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'.DCR;
        $res.='<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" dir="ltr" >'.DCR;
        //---------------------------------------------
        $res.='<head>';
        //---------------------------------------------
        if ($this->tabColor)
            $res.='<meta name="theme-color" content="'.$this->tabColor.'">';
        //---------------------------------------------
        if ($this->title)
            $res.='<title>'.$this->title.'</title>'; 
        //---------------------------------------------
        if ($this->icon===''){
            
            $finded_icon = false;
            $favicons = array('favicon.ico','favicon.png');
            // поиск в корневой папке приложения 
            for($i=0;$i<count($favicons);$i++){
            $file = $favicons[$i];
            if (file_exists($file)){
                $res.='<link rel="shortcut icon" href="'.$Application->HTTP.$favicons[$i].'" type="image/x-icon">';
                $finded_icon = true;
                break;
             }; 
            };
            
            // установка из папки ide 
            if (!$finded_icon){
                /* получаем возможный путь к файлу*/
                $iconWsi = explode('/',APP::slash(str_replace($Application->ROOT,'',__DIR__),false,false));
                $idxWs = array_search('ws',$iconWsi);
                $iconWsi = $Application->DOMEN.APP::slash(implode('/',array_splice($iconWsi,0,$idxWs)),false,true).'favicon.ico';

                $res.='<link rel="shortcut icon" href="'.$iconWsi.'" type="image/x-icon">';
            }    
            
        
        }else
            $res.='<link rel="shortcut icon" href="'.$this->icon.'" type="image/x-icon">';
        
         
        //---------------------------------------------
        if (isset($Application->EXTENSION['CSS']))
        for($i=0;$i<count($Application->EXTENSION['CSS']);$i++){
            $css = $Application->EXTENSION['CSS'][$i];
            $need = true;
            if ($css['dcss']){
                $need = ($styles[$css['dcss']['style']] == $css['dcss']['name']);            
            }    

            if ($need){
                $res.='<link rel="stylesheet" href="';
                $res.=$css['remote'];
                $res.=($version!==''?'?'.$version:'');
                $res.='">'.DCR;
            }    
        };
        //---------------------------------------------
        if (isset($Application->EXTENSION['JSX']))
        if (count($Application->EXTENSION['JSX'])>0){
            
            RESOURCE("https://unpkg.com/react@16/umd/react.production.min.js");
            RESOURCE("https://unpkg.com/react-dom@16/umd/react-dom.production.min.js");
            RESOURCE("https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/6.25.0/babel.min.js");
            
        }
        
        //--------------------------------------------
        $js_build = false;
        if ($this->mode === 'production'){
            if (!$this->buildRunLock()){
                $js_build = $this->builder('js',$version,(WS_CONF::GET('bildFrameJS')==1?$this->main_js($dcss,$styles,$version):''));
            }
        }
        //--------------------------------------------
        if ($this->mode === 'assembly'){
            // посторение сборки для последующей компиляции ( сам проект будет отображаться в режиме development) 
            $this->assembly($version,$this->main_js($dcss,$styles,$version));
        }

        //--------------------------------------------
        if ($js_build!==false)
            $res.='<script type="text/javascript" src="'.$js_build.'"></script>'.DCR;
        
        
        if (isset($Application->EXTENSION['JS']))
        for($i=0;$i<count($Application->EXTENSION['JS']);$i++){
            
            if (($js_build===false)||($Application->EXTENSION['JS'][$i]['local']=='')){
                $js_script_file = $Application->EXTENSION['JS'][$i]['remote'];
                if ($this->loadOptimizedResources === true){
            
                    $f = APP::pathinfo($Application->EXTENSION['JS'][$i]['local']);
                    $js_script_file_min = $f['dirname'].'/'.$f['filename'].'.min.'.$f['extension'];
            
                    if (file_exists($js_script_file_min)){
                        $f = APP::pathinfo($js_script_file);
                        $js_script_file = $f['dirname'].'/'.$f['filename'].'.min.'.$f['extension'];
                    }    
                }
                
                $url_params  = $Application->EXTENSION['JS'][$i]['params'];

                $res.='<script type="text/javascript" src="';
                $res.=$js_script_file.$url_params;
                
                $res.=($version!==''?($url_params!==''?'&':'?').$version:'');
            
                $res.='"></script>'.DCR;
            }
        };
        
        
        //---------------------------------------------
        if (isset($Application->EXTENSION['JSX']))
        for($i=0;$i<count($Application->EXTENSION['JSX']);$i++){
            $jsx_script_file = $Application->EXTENSION['JSX'][$i]['remote'];

            $res.='<script type="text/babel" src="';
            $res.=$jsx_script_file;
            $res.=($version!==''?'?'.$version:'');
            
            $res.='"></script>'.DCR;
        };
        //---------------------------------------------
        
        $res.='<'.'style'.' id="ws_style" type="text/css">'.$dcss['css'].'</style>';
        
        //---------------------------------------------
        $res.='<'.'style type='.'"text/css"'.'>';
        //DCSS::CSS($this->root->RENDER('css'));
            $res.=$this->root->RENDER('css');
        //$res.=DCSS::RENDER();
        $res.='</style>'.DCR;

        //---------------------------------------------
        if (
            (
                (
                    ($this->mode==='development')
                    ||
                    ($this->mode==='assembly')
                ) 
                || 
                ($this->buildRunLock())
            ) 
            || (
                WS_CONF::GET('bildFrameJS')==0 
               )
            )
        {
            $res.='<script type='.'"text/javascript"'.'>'.DCR;
            $res.= $this->main_js($dcss,$styles,$version);
            $res.='</script>'.DCR;
        }; 
        //---------------------------------------------
        $res.='</head>'.DCR;
        //---------------------------------------------
        $res.=$this->root->RENDER('html');
        //---------------------------------------------
        $res.='</html>'.DCR;
        
        ENCODING::OUT($res);
        
    }
    /** генерация JS кода  из FRAME */
    private function main_js($dcss,$styles,$version){
        global $Application;
        global $REQUEST;
        
        $res = '';
        $res.="Ws.url = '".$Application->ADDR."';".DCR;
        $res.="Ws.version = '".$version."';".DCR;
        $res.="Ws.share = ".ARR::to_json($REQUEST->SHARE).';'.DCR;
        $res.='Dcss.vars='.ARR::to_json($dcss['vars']).';'.DCR;
        $res.='var Ar={};'.DCR;
        //---------------------------------------------
        $res.=$this->root->RENDER('script');
        //---------------------------------------------
        $res.='var Qs={'.DCR;
        $res.='update:function(){'.DCR;
            $res.='let t=Qs;'.DCR;
            $res.='t.body=$("body");'.DCR;
            $res.=$this->root->RENDER('groups');
            $res.=$this->root->RENDER('var');
            $res.='byReady("Qs");';
        $res.='}'.DCR.'};';
        //---------------------------------------------

        $res.='Ws._align=function(){'.DCR;
        $res.=$this->root->RENDER('align');
        $res.='};'.DCR;
        //---------------------------------------------
        $res.='$(document).ready(function(){'.DCR;
        
        if (MODULES::EXIST('SESSION_MOD'))
            $res.='session.auto(function(){';
            
            $res.='Qs.update();'.DCR;
            $res.='Dcss.styles='.ARR::to_json($styles).';'.DCR;
            $res.=WS_UTILS::code_end($this->root->RENDER('init')).DCR;
            $res.=WS_UTILS::code_end($this->root->RENDER('ready')).DCR;
            $res.=WS_UTILS::code_end($this->root->RENDER('last')).DCR;
        
            $res.='JX.window().resize(function(){Ws.align();}).scroll(function(){Ws.align();});'.DCR;
            $res.='JX.lh(['.$this->root->RENDER('lineHeight').']);'.DCR; 
            
            $res.='Ws.align();'.DCR;
            $res.='Ws.ready();'.DCR;
            $res.='byReady("Ws");'.DCR;

        if (MODULES::EXIST('SESSION_MOD'))
            $res.='});';
            
        $res.='});'.DCR;
         
        return $res; 
    }
    /** проверка возвращенного кода от googly compling на наличие ошибок*/
    private static function isErrorCompiling($code){
        if ( 
            ($code === false) 
            ||  
            (mb_strpos($code,'java.lang')!==false) 
            || 
            (mb_strpos(trim($code),'Error(')===0) 
            ||
            (mb_strpos(trim($code),'<html>')===0) 
        )
            return true;
        return false;
    }

    /** запуск/остановка/проверка на то, что процесс компиляции запущен другим процессом */
    private function buildRunLock($start = ''){
        $pBuild = WS_CONF::GET('renderPath','_render');
        $file = $pBuild.'build.start';
        
        if ($start === 'begin'){
            file_put_contents($file,'1');
            return true;
        }

        if ($start === 'end'){
            if (file_exists($file))
                unlink($file);
            return false;
        }

        return file_exists($file);
    }

    /** создание сборки */
    private function builder($type,$version,$right = ''){
        global $Application;
        
        if ($type==='js'){
            
            if (isset($Application->EXTENSION['JS'])){
                // определяем build - уникальное имя сборки
                $build = $Application->getExtHash('JS',$version);
                

                //$pBuild = '_render/';
                $pBuild = WS_CONF::GET('renderPath','_render');
                $nBuild = $pBuild.$build.'.js';
                $stepFile = $pBuild.'builder.step'; // файл склейка
                $existStepFile = file_exists($stepFile); 
                // проверяем, существует ли сборка
                $eBuild = true;
                
                if (!file_exists($pBuild)){
                    $eBuild= false;
                    mkdir($pBuild);
                };
                if (!file_exists($nBuild) || ($existStepFile))
                    $eBuild= false;
                

                if (!$eBuild){
                    $this->buildRunLock('begin');

                    $start = $existStepFile ? file_get_contents($stepFile) : 0 ; // начинаем, либо с 0, либо с того места где закончили
                    
                    if ( ($start!=='frame') && (gettype($Application->EXTENSION) === 'array') && (isset($Application->EXTENSION['JS'])) ){
                        

                        $cntJS =count($Application->EXTENSION['JS']);
                        
                        error_log('------------------------------------------');
                        error_log('build start ['.$nBuild.'] count:'.$cntJS);
                        
                        $all_code = file_exists($nBuild) ? file_get_contents($nBuild) : '' ;

                        $includeModuleInfo = WS_CONF::GET('includeModuleInfo',0) == 1 ? true : false ;
                        $optimize =  WS_CONF::GET('optimizeJS',0)==1 ? true : false ;
                        
                        $error = false;

                        for($i=$start;$i<$cntJS;$i++){
                            $file = trim($Application->EXTENSION['JS'][$i]['local']);
                            
                            if (($file!=='')&&(file_exists($file))){
                                $code_js = file_get_contents($file);

                                if ($optimize){
                                    $code_js_opt = self::optimization($code_js);
                                    
                                    if (self::isErrorCompiling($code_js_opt)){ 

                                        error_log(trim(substr($code_js_opt.'',0,200)).'..');
                                        error_log(($i+1).': ERROR build module, restart build ['.$file.']');
                                        $error = true;
                                        file_put_contents($stepFile,''.$i);
                                        break;
                                    }

                                    $code_js = $code_js_opt;
                                    error_log(($i+1).': build ok ['.$file.']');

                                };

                                $all_code.=($all_code!==''?';':'');
                                if ( $includeModuleInfo )
                                    $all_code.="\n".'/** MODULE >> ['.$file.'] */'."\n";
                                $all_code.=$code_js;
                            
                            }
                        };// for

                        // компилим код из frame
                        if (!$error && $right!==''){
                            
                            if ($optimize)
                                $right = self::optimization($right);
                            
                            if (($optimize) && (self::isErrorCompiling($right))){ 
                                error_log(trim(substr($right.'',0,200)).'..');
                                error_log(' ERROR build code frame');
                                $error = true;
                                file_put_contents($stepFile,'frame');
                            }else{
                                $all_code.=($all_code!==''?';':'');
                                if ( $includeModuleInfo )
                                    $all_code.="\n".'/** FRAME CODE >>  */'."\n";
                                $all_code.=$right;
                                error_log('frame : build ok ');
                            }
                        };

                        error_log('build end '.($error ? 'with error':'ok'));
                        error_log('------------------------------------------');

                        file_put_contents($nBuild,$all_code);

                        // если ошибок не было удаляем файл step
                        if ((!$error) && ($existStepFile))
                            unlink($stepFile);

                    }
                    
                    $this->buildRunLock('end'); 
                }    
                
                
                return $nBuild;
            }
        }
        
        return false;
        
    }
    /** 
     * получаем сборку для дальнейшей обработки внешними средствами
    */
    private function assembly($version,$frame){        
        global $Application;

        error_log('------------------------------------------');
        error_log('assembly start ');
        
        if ( (gettype($Application->EXTENSION) === 'array') && (isset($Application->EXTENSION['JS'])) ){
            // определяем build - уникальное имя сборки
            $build = $Application->getExtHash('JS',$version);
            
            //$pBuild = '_assembly/';
            $pBuild = APP::slash(WS_CONF::GET('assemblyPath','_assembly'),false,true);
            $excludes = WS_CONF::GET('assemblyExclude',[]);
            $excludePath = APP::slash(WS_CONF::GET('assemblyExcludePath',$pBuild.'exclude'),false,true);

            $nBuild = APP::slash($pBuild,false,true).$build.'.js';

            if (!file_exists(APP::slash($pBuild,false,false)))
                mkdir(APP::slash($pBuild,false,false));
            if (!file_exists(APP::slash($excludePath,false,false)))
                mkdir(APP::slash($excludePath,false,false));
            
            $cntJS =count($Application->EXTENSION['JS']);
            error_log('assembly count '.$cntJS);

            $includeModuleInfo = WS_CONF::GET('includeModuleInfo',0) == 1 ? true : false ;
            $all_code =  '' ;

            for($i=0;$i<$cntJS;$i++){
                $file = trim($Application->EXTENSION['JS'][$i]['local']);
                            
                if (($file!=='')&&(file_exists($file))){
                    
                    $exclude = false;
                    foreach($excludes as $rule){
                        // полное соотвествие
                        if ($file === $rule){
                            $exclude = true;
                            break;
                        }else
                        // *string*  содержит строку
                        if (($rule[0] === '*') && ($rule[mb_strlen($rule)-1] === '*')){
                            
                            $search = mb_substr($rule,1);
                            $search = mb_substr($search,0,mb_strlen($search)-1);
                            $exclude = (mb_strpos($file,$search)!==false);
                            break;
                        }else
                        // *string  строка справа
                        if ($rule[0] === '*'){
                    
                            $search = mb_substr($rule,1);
                            $exclude = (mb_substr($file,mb_strlen($file)-mb_strlen($search)) == $search);
                            break;
                        }else
                        // *string  строка слева
                        if ($rule[mb_strlen($rule)-1] === '*'){
                    
                            $search = mb_substr($rule,0,mb_strlen($rule)-1);
                            $exclude = (mb_substr($file,0,mb_strlen($search)) == $search);
                            break;
                        }
                    
                    }
                    
                    $code_js = file_get_contents($file);

                    if (!$exclude){
                        // добавляем код в общую кучу
                        $all_code.=($all_code!==''?';':'');
                        if ( $includeModuleInfo )
                            $all_code.="\n".'/** MODULE >> ['.$file.'] */'."\n";
                            $all_code.=$code_js;
                        error_log("$i: assembly add [$file] ok");    
                    }else{
                        // файл исключен из сборки и добавляется в отдельную папку
                        $code_js = file_get_contents($file);
                        $to = $excludePath.'/'.basename($file);
                        file_put_contents($to,$code_js);
                        error_log("$i: assembly exclude [$file] ok");    
                    }

                }//if (($file!=='')&&(file_exists($file))){
                
            };// for

        };// if ( (gettype($Application->EXTENSION) === 'array') && (isset($Application->EXTENSION['JS'])) ){
        
        // добавляем код из frame
        $all_code.=($all_code!==''?';':'');
        if ( $includeModuleInfo )
            $all_code.="\n".'/** FRAME CODE >>  */'."\n";

        $all_code.=$frame;
        error_log("assembly frame ok");    

        file_put_contents($nBuild,$all_code);

        error_log('assembly end ');
        error_log('------------------------------------------');
    }
    /**
     * Минификация, а также приведения к стандарту ES5
     */
    private function optimization($js_code){
        $res = false;
        $curl = curl_init();
        try{
            //$url = 'https://closure-compiler.appspot.com/compile';
            $url = WS_CONF::GET('urlOptimizeCompiler');
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_SSL_VERIFYPEER=> ( WS_CONF::GET('sslVerifyPeer',1) == 1 ? true : false ) ,
                CURLOPT_POSTFIELDS => http_build_query(array(
                    'compilation_level'=>'SIMPLE_OPTIMIZATIONS',
                    'output_format'=>'text',
                    'output_info'=>'compiled_code',
                    'js_code'=> $js_code
                    //'code_url'=>'https://../_render/f3d04e50550a2863aca1e79a8d24bdbc.js'
                )),
            ));

            $res = curl_exec($curl);
            if (empty($res))
                throw new Exception("google compile closure return empty..may be error in js code");
                
        }catch(Exception $e){
            _LOGF($e->getMessage(),'Optimization error:',__FILE__,__LINE__);
            $res = false;
        }
        curl_close($curl);
        return $res;
    }
    public function CONTENT(){
            
    }
    
    public function debug_info($cr='<br>'){
        //S: Вывод отладочной информации
        return 'WS: ok'.$cr;
    }
    
};

    
if ($Application->is_main(__FILE__)){
    
    echo '<hr>debug:'.__FILE__.'<hr>';  
    echo $Application->debug_info();    
    echo '<hr>end...<hr>';            
    
};

?>