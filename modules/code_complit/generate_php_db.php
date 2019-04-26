<?php
/**
    https://ws-framework-fmihel.c9users.io/ide/modules/code_complit/generate_php_db.php
*/ 
require_once '../../ws/utils/common.php';

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
         
        return;    

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
    public static function to_json($arr){
        //SHORT:Преобразует PHP массив в строку, которую можно парсить JSON
        /*DOC: Преобразует PHP массив в строку, для возможности парсить ее JSON на стороне клиента. При этом строки будут кодироваться посредством JUTILS::JSON_CODE.
        Для правильной раскодировки используйте ф-цию javascript [code]JUTILS.JSON_DECODE(str)[/code] Так же bool значения, после парсинга,правильней проверять с помощью ф-ции [code]JUTILS.AsBool(mean)[/code]
        */
        
        if (TYPE::is_assoc($arr)){
            $res = '{';
            $cr = "<br>";
            foreach($arr as $Name=>$Value)
            {
                if ($res !=='{') 
                    $res.=',';        
                else 
                    $res=$cr.$res;
                    
                if (is_array($Value))
                    $res.= '"'.$Name.'":'.self::to_json($Value).'';          
                else
                {
                    if (is_bool($Value))
                    {
                        if ($Value)
                            $res.= '"'.$Name.'":true';
                        else
                            $res.= '"'.$Name.'":false';
                    }
                    else
                    {
                        
                        if (TYPE::is_numeric($Value,true))
                        {
                            $res.= '"'.$Name.'":'.$Value;
                        }
                        else
                        {
                            $res.= '"'.$Name.'":"'.$Value.'"';
                        }
                    };
                }
            };
            $res.='}';    
        }else{
            
            $res = '[';
            if (is_array($arr)){
							for($i = 0;$i<count($arr);$i++){
                if ($res !=='[') $res.=',';        
                if (is_array($arr[$i]))
                    $res.= self::to_json($arr[$i]);
                else{                    
                    if (is_bool($arr[$i]))
                    {
                        if ($arr[$i])
                            $res.= '"'.$Name.'":true';
                        else
                            $res.= '"'.$Name.'":false';
                    }else if (TYPE::is_numeric($arr[$i],true))
                        $res.= $arr[$i];
                    else
                        $res.= '"'.$arr[$i].'"';         
                }
							};
						}else{
							$res.= '"'.$arr.'"';
						}	
            $res.=']'.$cr;    
        }
        return $res;

    }
    
    
};    


echo 'code_complit.data.php = '.CODE_COMPLIT_UTILS::to_json(CODE_COMPLIT_UTILS::get_php_funcs()).';';


?>