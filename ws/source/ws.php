<?php

if(!isset($Application))
  require_once '../utils/application.php';
require_once UNIT('utils','ctime.php'); 
//CT::START('WS');


define('DCR',"\n");
//define('DCR',"");

require_once UNIT('ws','ws_request.php');
require_once UNIT('ws','ws_utils.php');
require_once UNIT('ws','ws_common.php');
require_once UNIT('ws','ws_frame.php');
require_once UNIT('ws','ws_module.php');
require_once UNIT('ws','ws_dcss.php');

require_once UNIT('utils','common.php');

//RESOURCE('https://code.jquery.com/jquery-3.1.1.min.js');
//RESOURCE('plugins','jquery/jquery-3.1.1.js');
//RESOURCE('plugins','jquery/jquery-3.2.1.js');
RESOURCE('plugins','jquery/jquery-3.3.1.js');
RESOURCE('plugins','common/addition.js');
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


$_WS = null;// ссылка на объект WS инициализируем в конструкторе WS

class WS extends WS_CONTENT{
    public $root;
    
    public $title; // название приложения 
    public $tabColor; // цвет вкладки в chrome mobile
    public $version; // номер версии (используется для кеширования)
    public $loadOptimizedResources; // будут загружаться оптимизированные скрипты ( если существуют), т.е у которых к имени файла добавлено .min
    public $icon;// путь к иконке приложения (если не указан то берется из корня приложения, если  в корне нет, то берет от wsi)
    public $mode;// тип сборки production | development
    function __construct(){
        global $_WS;
        $_WS = $this;
        parent::__construct();
        
        $this->title    = false;
        $this->tabColor = false;
        
        $this->root = null;
        $this->version = '';
        $this->loadOptimizedResources=false;
        $this->icon = '';
        // тип сборки production | development
        $this->mode = 'development';
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
        
        //error_log(print_r($styles,true));
        //CT::START('RENDER');
        $dcss   = DCSS::RENDER();
        //CT::STOP('RENDER');
        //CT::STOP('WS'); 
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
        $js_build = ($this->mode === 'production')?$this->builder('js',$version):false;
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

                $res.='<script type="text/javascript" src="';
                $res.=$js_script_file;
                $res.=($version!==''?'?'.$version:'');
            
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
        $res.='<script type='.'"text/javascript"'.'>'.DCR;
        //---------------------------------------------
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
            
            
        if (MODULES::EXIST('SESSION_MOD'))
            $res.='});';
            
        $res.='});'.DCR;
        //---------------------------------------------
        $res.='</script>'.DCR;
        //---------------------------------------------
        $res.='</head>'.DCR;
        //---------------------------------------------
        $res.=$this->root->RENDER('html');
        //---------------------------------------------
        $res.='</html>'.DCR;
        
        ENCODING::OUT($res);
        
    }
    /** создание сборки */
    public function builder($type,$version){
        global $Application;
        
        if ($type==='js'){
            
            if (isset($Application->EXTENSION['JS'])){
                // определяем build - уникальное имя сборки
                $build = '';
                
                for($i=0;$i<count($Application->EXTENSION['JS']);$i++)
                    $build.= $Application->EXTENSION['JS'][$i]['local'];
                $build.=$version;
                $build = md5($build);
    
                $pBuild = '_render/';
                $nBuild = $pBuild.$build.'.js';
                // проверяем, существует ли сборка
                $eBuild = true;
                
                if (!file_exists($pBuild)){
                    $eBuild= false;
                    mkdir($pBuild);
                };
                if (!file_exists($nBuild))
                    $eBuild= false;
                
                if (!$eBuild){
                    $cBuild = '';
                    for($i=0;$i<count($Application->EXTENSION['JS']);$i++){
                        
                        $fBuild = trim($Application->EXTENSION['JS'][$i]['local']);
                        if ($fBuild!==''&&(file_exists($fBuild))){
                           // _LOGF($fBuild,$i.':',__FILE__,__LINE__);                
                            $cBuild.=($cBuild!=''?';':'').file_get_contents($fBuild);
                        }    
                    }
                    file_put_contents($nBuild,$cBuild);
                }
                
                return $nBuild;
            }
        }
        
        return false;
        
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