<?php

/*
Описание работы code_complit (CC)
CC предназначен для анализа вводимых данных и выдачи кратких подсказок по возможному варианту завершения фразы

Базой для CC является переменная 
  array_push($out,array('k'=>'function','n'=>'function','o'=>'function <{$cursor}>','t'=>'function'));
code_complit.data = { 
EXT1:[
  {  
    k:string,  -  ключевая фраза, к которой должно стремится вводимое слово 
    n:string,  -  описание объекта(то что будет видно в подсказке), который будет использован, в случае если будет произведен выбор 
    o:string,  -  объект со скриптом <{$cursor}>, с помощью которого будет осуществлена замена
    t:string   -  тип объекта (function  object) 
  },
],
EXT2:[...],
...}

1. Запуск механизма

При создании объекта ace editor 
jeditors.add
    на объект editor вешается обработчик change,
    который вызывает метод
jeditors._do_code_complit()
Далее
    определяем расширение обрабатываетмого файла
    определяеем примерную структуру вводимых данных (см key = jeditors._get_right_word(editor))
    code_complit.update({key:key,ext:ext});
    
    в ф-ции code_complit.update вызываем 
        code_complit.get_funcs() 
        в данной ф-ции производи поиск в базе наиближайших совпадений с key
    
    
*/
/*require_once UNIT('modules/code_complit/db_css.php');*/

require_once UNIT('utils','php_parse.php');

/*
   function __construct($owner){
        parent::__construct($owner);

   }    

*/


MODULES::ADD('CODE_COMPLIT');
class CODE_COMPLIT_UTILS{
    
    
    static function php_func_param($name){
        
        $refFunc = new ReflectionFunction($name);
        $out = '';
        foreach( $refFunc->getParameters() as $param ){
            //invokes ReflectionParameter::__toString
            $out.=($out==''?'':',').' '.$param->name.' ';
        }
        return $out;
    }
    
    static function get_php_funcs(){
        
        $no=array('#','{','}','(',')','?','$','@','>','<','[',']','^','=','&','+',',','.',':',';','/','-','~','*','`','|','"',"'",' ','%','!',chr(13),chr(10));
        $limit = 1860;
        
        
        $f =  get_defined_functions ();
        $f=$f['internal'];
        
        $funcs = array();
    
        $cnt = min(count($f),$limit);
        
        for($i=0;$i<$cnt;$i++){
            $need = ((trim($f[$i])!=='')&&(trim($f[$i])!=='_'));
            
            if ($need)
            for($j=0;$j<count($no);$j++){
                if (strpos(trim($f[$i]),$no[$j])!==false){
                    $need = false;
                    break;
                }
            };
            
            if ($need) 
                array_push($funcs,$f[$i]);    
        }
        
        sort($funcs);
        
        $out = array(); 
        self::add_common($out);        
        
        for($i=0;$i<count($funcs);$i++){
            $info = self::php_func_param($funcs[$i]);
            array_push($out,array('k'=>$funcs[$i],'n'=>$funcs[$i].'('.$info.')','o'=>$funcs[$i].'(<{$cursor}>)','t'=>'function'));
        }
        
        
        
        return $out;
        
    }
    
    public static function _paths(){
        global $USERS;
        $_PATHS = $USERS->get('cc_paths');
        $INCLUDE = array();
        $EXCLUDE = array();
        
        if (gettype($_PATHS) == 'array')
        for($i=0;$i<count($_PATHS);$i++){
            if (substr(trim($_PATHS[$i]),0,1)!='!')
                array_push($INCLUDE,$_PATHS[$i]);
            else
                array_push($EXCLUDE,substr(trim($_PATHS[$i]),1));
        }
        
        return array('include'=>$INCLUDE,'exclude'=>$EXCLUDE);
    }
    
    public static function _file_not_in_exclude($file,$exclude){
        
        for($i=0;$i<count($exclude);$i++){
            
            $len= strlen($exclude[$i]);
            $left = substr($file,0,$len);
            
            if ($left==$exclude[$i]) 
                return false;
                
        }
        
        return true;
        
        
    }
    
    static function add_common(&$out){
        
        
        array_push($out,array('k'=>'function','n'=>'function ','o'=>'function <{$cursor}>','t'=>'function'));
        array_push($out,array('k'=>'public','n'=>'public','o'=>'public <{$cursor}>','t'=>'function'));
        array_push($out,array('k'=>'private','n'=>'private','o'=>'private <{$cursor}>','t'=>'function'));
        array_push($out,array('k'=>'static','n'=>'static','o'=>'static <{$cursor}>','t'=>'function'));
        array_push($out,array('k'=>'class','n'=>'class','o'=>'class <{$cursor}>{};','t'=>'object'));
        array_push($out,array('k'=>'global','n'=>'global','o'=>'global <{$cursor}>','t'=>'function'));
        array_push($out,array('k'=>'__construct','n'=>'__construct','o'=>'__construct(){<{$cursor}>}','t'=>'function'));
        array_push($out,array('k'=>'__destruct','n'=>'__destruct','o'=>'__destructor(){<{$cursor}>}','t'=>'function'));
        array_push($out,array('k'=>'echo','n'=>'echo','o'=>'echo <{$cursor}>','t'=>'function'));
        array_push($out,array('k'=>'require_once','n'=>'require_once','o'=>'require_once <{$cursor}>','t'=>'function'));
         
        

        global $Application;
        global $USERS;
        $PATHS = self::_paths();
        

        

    /*
        $PATHS = array(
        'in_development/MySQL/',
        'ide/ws/'
        );
    */  
        /*'cc_paths'=>array('in_development/MySQL/','ide/ws/','!test/source/'),*/
        
        for($i=0;$i<count($PATHS['exclude']);$i++){
             $PATHS['exclude'][$i] = APP::slash(APP::rel_path($Application->PATH,$Application->ROOT.$PATHS['exclude'][$i]),false,true);
        };
        
        for($p = 0;$p<count($PATHS['include']);$p++){
            
            
            $DIR = APP::slash(APP::rel_path($Application->PATH,$Application->ROOT.$PATHS['include'][$p]),false,true);

            $files = DIR::files($DIR,array('php'),true,false);
            
            
            for($i=0;$i<count($files);$i++){
                if (self::_file_not_in_exclude($files[$i],$PATHS['exclude'])){
                    
                    $struct = PHP_PARSE::AS_CODE_COMPLIT($files[$i]);
                    for($j=0;$j<count($struct);$j++) array_push($out,$struct[$j]);    
                    
                }
            }
        }
        
        

    }
    
    static function create_file_view_prop(&$struct){
        global $Application;
        global $USERS;
        $path = APP::slash(APP::rel_path($Application->PATH,$Application->ROOT.$USERS->get('workplace','')),false,true);

        $len = 20;
        for($i=0;$i<count($struct);$i++){
            $fo=substr($struct[$i]['f'],strlen($path));
            $right = APP::get_file($fo);
            if (strlen($fo)-strlen($right)>$len)
                $fo = substr($fo,0,$len).'..'.$right;
            $struct[$i]['fo'] = $fo;
                
        }
        
    }
};    

class CODE_COMPLIT extends WS_MODULE{
    
    function __construct($owner){
        parent::__construct($owner);
        
        RESOURCE('modules/code_complit/code_complit.js');
        RESOURCE('modules/code_complit/code_complit.dcss');

        RESOURCE('modules/code_complit/db/db_js.js');
        RESOURCE('modules/code_complit/db/db_css.js');
        RESOURCE('modules/code_complit/db/db_php.js');
        
    }
    
    public function CONTENT(){
        global $DB_CSS;
        FRAME('code_complit',FRAME('modal'))
        ->CLASSES('cc_frame ws_scrollbar')
        ->STYLE('
        
        position:absolute;
        z-index:100;
        display:none;
        
        ')
        ->READY('
            code_complit.init();
        ');
        //->READY('
        //    
        //    code_complit.data["php"]=[];
        //    code_complit.data["php"] = '.ARR::to_json(CODE_COMPLIT_UTILS::get_php_funcs()).';
        //    
        //    code_complit.init();
        //');
    }

    public function AJAX(&$response){
        
        global $REQUEST;
        global $Application;
        global $USERS;
        
        $path = $Application->ROOT.$USERS->get('workplace');
        $path = APP::slash(APP::rel_path($Application->PATH,$path),false,true);
        $PATHS = CODE_COMPLIT_UTILS::_paths();
        
        
        if ($REQUEST->ID =='cc_load_files'){
            $_files = DIR::files($path,array('php'),true,false);
            
            for($i=0;$i<count($PATHS['exclude']);$i++){
                 $PATHS['exclude'][$i] = APP::slash(APP::rel_path($Application->PATH,$Application->ROOT.$PATHS['exclude'][$i]),false,true);
            };
            $files = array();    
            for($i=0;$i<count($_files);$i++){
                
                if (CODE_COMPLIT_UTILS::_file_not_in_exclude($_files[$i],$PATHS['exclude'])){
                    array_push($files,$_files[$i]);
                }    
            };


            $response=array('res'=>1,'list'=>$files);
            
            return true;
        }else
        if ($REQUEST->ID =='cc_code_file'){

            $name = $REQUEST->VALUE['name'];
            
            if ($REQUEST->VALUE['rel_path']==1){
                $path = APP::slash(APP::rel_path($Application->PATH,$Application->ROOT.$USERS->get('workplace','')),false,true);
                $name = $path.$name;
            }
            
            
            $struct = PHP_PARSE::AS_CODE_COMPLIT($name);
            CODE_COMPLIT_UTILS::create_file_view_prop($struct);

            $response=array('res'=>1,'struct'=>$struct,'name'=>$name);
            return true;
        }    
        
        return false;
    }
};
?>