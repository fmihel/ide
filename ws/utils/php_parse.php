<?php
//----------------------------------------------------------------------
/*
https://ws-framework-fmihel.c9users.io/ide/ws/utils/php_parse.php
*/

if(!isset($Application)){
    require_once 'application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = false;

};

define('T_OPEN_BODY',1400);
define('T_CLOSE_BODY',1401);
define('T_OPEN_PARAM',1402);
define('T_CLOSE_PARAM',1403);
define('T_OTHER',1404);


class PHP_PARSE
{  
    private static function _token($value)
    {
        $res = array(   'TYPE'=>T_OTHER,
                        'MEAN'=>$value
                    );
                
        if (is_array($value))
        { 
            $res['TYPE'] = $value[0];
            $res['MEAN'] = $value[1];
        }else
        {
            if ($value === '{')
                $res['TYPE'] = T_OPEN_BODY;
            if ($value === '}')
                $res['TYPE'] = T_CLOSE_BODY;
            if ($value === '(')
                $res['TYPE'] = T_OPEN_PARAM;
            if ($value === ')')
                $res['TYPE'] = T_CLOSE_PARAM;
        };
        return $res;
    }

    private static function _next_token($token,&$i)
    {
        $res = PHP_PARSE::_token($token[$i]);
        $i++;
        return $res;
    }
    
    private static function _visible_token($token,$i)
    {
        $T = PHP_PARSE::_token($token[$i]);
        if ($T['TYPE'] == T_PUBLIC)
            return 'public';
        else
        if ($T['TYPE'] == T_PRIVATE)
            return 'private';
        else
        if ($T['TYPE'] == T_PROTECTED)
            return 'protected';

        return 'global';
    }
    
    private static function _static_token($token,$i)
    {
        $T = PHP_PARSE::_token($token[$i]);
        if ($T['TYPE'] == T_STATIC)
            return 'static';
        else
        return 'dynamic';
    }
    private static function _func_absorb($token,&$i)
    {
        //------------------------------------------------------
        $res = array("NAME"=>"noname",
                "VISIBLE"=>"public",
                "TYPE"=>"dynamic",
                "PARAMS"=>array()
                );
        //------------------------------------------------------
        PHP_PARSE::_next_token($token,$i);// T_WHITESPACE
        $T = PHP_PARSE::_next_token($token,$i);// T_STRING
        $res['NAME'] = $T['MEAN'];
        //------------------------------------------------------
        // область видимости
        $T = PHP_PARSE::_token($token[$i-5]);

        if ($T['TYPE'] !== T_STATIC)
            $res['VISIBLE'] = PHP_PARSE::_visible_token($token,$i-5);
        else
        {
            $res['VISIBLE'] = PHP_PARSE::_visible_token($token,$i-7);
            $res['TYPE'] = 'static';
        };
        //------------------------------------------------------
        // set params -----------------------------------------
        while(($T['TYPE']!==T_CLOSE_PARAM) && ($i<count($token)))
        {
            if ($T['TYPE'] == T_VARIABLE)
                array_push($res['PARAMS'],$T['MEAN']);
            $T = PHP_PARSE::_next_token($token,$i);
        }
        //------------------------------------------------------
        //-absorb-----------------------------------------------
    
        $inc=-1;
        while ($i<count($token))
        {
            $T = PHP_PARSE::_next_token($token,$i);
            if ($inc ==-1)
            {
                if ($T['TYPE'] == T_OPEN_BODY)
                    $inc = 1;
            }else
            {
                if ($T['TYPE'] == T_OPEN_BODY)
                    $inc++;
                if ($T['TYPE'] == T_CLOSE_BODY)
                    $inc--;
            };
            if ($inc === 0)
                break;            
        }
        //------------------------------------------------------
        return $res;
    }
    
    public static function GET_STRUCT($file)
    {
        
        // возвращает массив с разбором $file
        // 
        // 
        $res = array();
        $token = token_get_all(file_get_contents($file));
    
        $state  =   'search';
        $class  =   null;

        $i=0;
    
        while($i<count($token))
        {
            $T = PHP_PARSE::_next_token($token,$i);
            if ($state=='search')
            {
                if($T['TYPE'] == T_CLASS)
                {
                    PHP_PARSE::_next_token($token,$i);// T_WHITESPACE
                    $T =PHP_PARSE::_next_token($token,$i);// T_STRING (name of class)
                    $class = array( 'CLASS'=>$T['MEAN'],'FUNCS'=>array());
                    array_push($res,$class);
                    $state = 'class';
                }else
                if ($T['TYPE'] == T_FUNCTION)
                {
                    $func = PHP_PARSE::_func_absorb($token,$i);
                    array_push($res,array('FUNC'=>$func));
                };
            }
            else
            if ($state=='class') 
            {
                if ($T['TYPE'] == T_CLOSE_BODY)
                    $state = 'search';
                else
                if ($T['TYPE'] == T_FUNCTION)
                {
                    $func = PHP_PARSE::_func_absorb($token,$i);
                    array_push($res[count($res)-1]['FUNCS'],$func);
                }
            };
        };
    
        return $res;
    }
    private static function _params($params){
        $out = '';
        
        for($i=0;$i<count($params);$i++)
            $out.=($out!==''?',':'').$params[$i];
        
        return $out;
    }
    private static function _d($funcs){
        $out=array();
        for($i=0;$i<count($funcs);$i++){
            $d = ($funcs[$i]['TYPE']=='static'?'::':'->');
            if (array_search($d,$out)===false)
                array_push($out,$d);        
        }
        return $out;
    }
    
    public static function AS_CODE_COMPLIT($file){
        $s=self::GET_STRUCT($file);
        $out = array();
        for($i=0;$i<count($s);$i++){
            
            if(isset($s[$i]['FUNC'])){
                $f = $s[$i]['FUNC'];
                array_push($out,array(
                        'k'=>$f['NAME'],
                        'n'=>$f['NAME'].'('.self::_params($f['PARAMS']).')',
                        'o'=>$f['NAME'].'(<{$cursor}>)',
                        't'=>'function',
                        'f'=>$file
                ));
            }
            
            if(isset($s[$i]['CLASS'])){
                $c = $s[$i]['CLASS'];
                $class=array(
                        'k'=>$c,
                        'n'=>$c,
                        'o'=>$c.'<{$cursor}>',
                        'd'=>self::_d($s[$i]['FUNCS']),
                        't'=>'object',
                        'f'=>$file
                );
                
                $f = $s[$i]['FUNCS'];
                $fn = array();
                for($j=0;$j<count($f);$j++){
                    $func = $f[$j];
                    $D = ($func['TYPE']=='static'?'::':'->');
                    array_push($fn,array(
                        'k'=>$D.$func['NAME'],
                        'n'=>$func['NAME'].'('.self::_params($func['PARAMS']).')',
                        'o'=>$D.$func['NAME'].'(<{$cursor}>)',
                        'd'=>$D,
                        't'=>'function'
                        
                    ));
                };
                
                $class['s']=$fn;
                
                array_push($out,$class);
                    
            }
            
            
        }
        return $out;
    }
};

function myfunc2(){
    
};
//----------------------------------------------------------------------
if($Application->is_main(__FILE__))
{
    $file = __FILE__;
    //$file = '../../test/code_analize/test02.php';
    
    $arr = PHP_PARSE::AS_CODE_COMPLIT($file);
    //$arr = PHP_PARSE::GET_STRUCT($file);
        
    //echo '<xmp>';
    //print_r(token_get_all(file_get_contents($file)));
    //echo '</xmp>';
    //exit;

    
    echo '<xmp>';
    print_r($arr);
    echo '</xmp>';
    exit;
    
    foreach($arr as $k=>$v)
    {
        if (isset($v['FUNC']))
        {
            echo 'global function '.$v['FUNC']['NAME'].'</br>';
        }else
        if (isset($v['CLASS']))
        {
            echo 'class '.$v['CLASS'].'</br>';
            for($i=0;$i<count($v['FUNCS']);$i++)
            {
                $m = $v['FUNCS'][$i];
                echo ' '.$m['NAME'].'</br>';
                
            }
        };
    }
    
};
?>