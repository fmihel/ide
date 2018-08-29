<?php

if(!isset($Application)) 
    require_once '../utils/application.php';

require_once UNIT('utils','common.php');

if ($Application->is_main(__FILE__))
    define('DCR',"");

/*
Ex: define dcss var

DCSS::THEME('dark',array('dark','mobile'));
DCSS::THEME('light',array('light','mobile'));

DCSS::CURRENT_THEME('dark');


DCSS::SET('item_color', array('dark'=>'#000000','light'=>'#FFFFFF'))
DCSS::SET('width',      array('COMMON'=>10,'mobile'=>8));

*/

function DCSS($name/*$values*/){
    
    DCSS::SET($name,$values);
    if (func_num_args()>1)
        DCSS::SET($name,func_get_arg(1));
    else    
        return DCSS::GET($name);
}

class DCSS{
    public static function RENDER(){
        global $_dcss;
        return $_dcss->render();
    }
    public static function CSS($css){
        global $_dcss;
        $_dcss->add_css($css.DCR);
    }
  
    public static function CURRENT_THEME($name){
        global $_dcss;
        $_dcss->current_theme = $name;        
    } 
    
    public static function THEME($name,$styles){
        global $_dcss;
        if(!is_array($styles)){
            $s = explode(',',$styles);
            $styles = array();
            for($i=0;$i<count($s);$i++)
                if($s[$i]!=='')array_push($styles,$s[$i]);
        }
        $_dcss->set_theme($name,$styles);            
    }
    public static function GET($name){
        //S: return var in current theme 
        global $_dcss;
        return $_dcss->get_var_theme($name);
    } 

    public static function SET($name,$values){
        /* values = array('dark'=>'#FFFFFF','white'=>'#000000')*/
        global $_dcss;
        if (is_array($values)){
            foreach($values as $style=>$val)
                $_dcss->set_var($name,$val,$style);
        }else 
            $_dcss->set_var($name,$values,'COMMON');
            
    } 
};

class WS_DCSS {
    public $dcss;
    public $vars;
    public $themes;
    public $current_theme;    
    
    function __construct(){
        
        $this->dcss = '';
        $this->vars = array('COMMON'=>array());
        $this->themes = array('COMMON'=>array());
        $this->current_theme = 'COMMON';
        
    }

    public function set_theme($name,$styles){
        if (!isset($this->themes[$name]))
            $this->themes[$name] = $styles;
    }

    public function set_var($name,$value,$style = 'COMMON'){
        if (!isset($this->vars[$style]))
            $this->vars[$style] = array();

        $this->vars[$style][$name] = $value;
    }
    
    public function get_var($name,$style = 'COMMON'){
        
        if (!isset($this->vars[$style])) 
            return 'undefined';
            
        if (!isset($this->vars[$style][$name]))
            return 'undefined';
        
        return $this->vars[$style][$name];
    }

    public function get_var_theme($name/*$theme = by def set current theme*/){
        
        $theme=(func_num_args()>1 ? func_get_arg(1) : $this->current_theme);
        $vars = $this->get_vars($theme);
        
        return $vars[$name];            

        
    }

    public function add_css($css){
        $this->dcss.=$css.DCR;
    }
    
    private function get_vars(){
        
        $theme_name=(func_num_args()>0 ? func_get_arg(0) : $this->current_theme);
        $theme = $this->themes[$theme_name];
        
        $out = array();
        for($i=0;$i<count($theme);$i++){
            $style = $this->vars[$theme[$i]];
            foreach($style as $name=>$value)
                $out[$name] = $value;              
        }
        return $out;
        
    }

    private function remove_comments($code){
        // удаляем строки начинающиеся с #
        //$code = preg_replace('/#.*/','',$code);
        // удаляем строки начинающиеся с //
        //$code = preg_replace('#//.*#','',$code);
        // удаляем многострочные комментарии /* */
        $code = preg_replace('#/\*(?:[^*]*(?:\*(?!/))*)*\*/#','',$code);

        return $code;
    }
    
    private function is_arithmetics($str){
        $rule = '/(?:\d+\s*[*+\/-]\s*)+\d+$/';
        //$str = str_replace('.','0',$str);
        $str=str_replace(array('.','(',')'),array('0','',''),$str);
        $match = preg_match($rule,$str); 
        
        //_LOG("str=[$str]  match=[$match]",__FILE__,__LINE__);
        
        return ($match==1);
    }
    
    private function _eval($vars,$value){
        
        
        foreach($vars as $_name=>$_value){
            $var = '$'.$_name;
            $value = str_replace($var,$_value,$value);
        };//foreach
        
        
        
        $out = $value;
        if ($this->is_arithmetics($value)){
            eval('$out='.$value.';');
        };    
        return $out;    
    }
    
    private function add_vars_from_code(&$vars,$code){
        $re = '/\$[a-zA-Z0-9_]*\s*=\s*\S*\s*;/';
        preg_match_all($re, $code, $matches);
        $re = '/\$[a-zA-Z0-9_]*\s*[^=]/';
        for($i=0;$i<count($matches[0]);$i++){
            preg_match($re, $matches[0][$i], $m);
            $var = trim(substr($m[0],1));
            if (!isset($vars[$var]))
                $vars[$var]='undefined';
        }
    }
    
    private function _order_vars($vars){
        $out = array();
        while(count($vars)>0){
            $max_name='';
            $len = 0;
            foreach($vars as $name=>$value){
                if ($len==0){
                    $len = strlen($name);
                    $max_name   = $name;
                }else{
                    $clen = strlen($name);    
                    if ($clen>$len){
                        $len = $clen;
                        $max_name   = $name;
                    }
                }
            }
            
            $out[$max_name]=$vars[$max_name];
            unset($vars[$max_name]);
        }//foreach
        return $out;
    }
    
    public function generate($code){
        
        $vars = $this->get_vars();
        $code = $this->remove_comments($code);
        $this->add_vars_from_code($vars,$code);
        
        $vars = $this->_order_vars($vars);
        
        $out = '';
        
        $loop = 100000;
        
        while(strlen($code)>0){
        
            if ($loop<=0) {echo 'ws_dcss.generate: loop...';exit;};
            $loop--;
            
            //---------------------------------------
            // поиск наиближайшего упоминания переменной
            $p_var = false;        
            $var = '';
            $name = '';
            $_p_var = false;
            
            foreach($vars as $_name=>$value){
                $_var = '$'.$_name;
                $_p_var = strpos($code,$_var);
                if($_p_var!==false){
                    if (($p_var===false)||($_p_var<$p_var)){
                        $var = $_var;
                        $p_var = $_p_var;
                        $name = $_name;
                    }
                };
            }//foreach
            //-----------------------------------------

            if ($p_var!==false){
            
                $eq = '';
                $p_eq = false;
                
                if (preg_match('/\$[a-zA-Z0-9_]*\s*=\s*\S*\s*;/', $code, $m)){
                    $eq = $m[0];    
                    $p_eq = strpos($code,$eq);
                }
                            
                if (($p_eq!==false)&&($p_eq===$p_var)){
                
                                
                    $code = STR::str_replace_once($eq,'',$code);

                    $eq = trim($eq);
                    $p_tz = strpos($eq,';');
                    $p_eq = strpos($eq,'=')+1;
                    
                    $vars[$name] = $this->_eval($vars,substr($eq,$p_eq,$p_tz-$p_eq));
                                
                }else{
                    $mean = trim($vars[$name]);
                    $code = STR::str_replace_once($var.'|',$mean,$code);
                    $code = STR::str_replace_once($var,$mean,$code);
                }
            }else{
                $out.=trim($code);
                $code = '';
            }
            
        };//while
        
        return trim($out);        
        
    }

    
    public function render(){
        global $Application;
        
        $res = '';
        $res.=$this->dcss.DCR;

        for($i=0;$i<count($Application->EXTENSION['DCSS']);$i++){
            $filename = $Application->EXTENSION['DCSS'][$i]['local'];
            $res.=file_get_contents($filename).DCR;
        };
        return $this->generate($res);
    }    
}

$_dcss = new WS_DCSS();

if($Application->is_main(__FILE__)){
    

$code = '

 $width = $color;
 $m = DDDDDD;
 .dcss{
    width:$width;
    color:$m;
}'; 

$code1='

 $s = $width;

    
    .splitter:hover{
        background-color:$s;
    }
';

DCSS::THEME('theme1','dark,mobile');
DCSS::THEME('theme2','dark,desktop');
DCSS::THEME('theme3','light,desktop');
DCSS::THEME('theme4','light,mobile');

DCSS('color',array('dark'=>'#FFFFFF','light'=>'#000000'));
DCSS('width',array('mobile'=>10,'desktop'=>20));

DCSS::CURRENT_THEME('theme4');

//echo $code;

$_dcss->add_css($code);
//print_r($_dcss->vars);
//print_r($_dcss->themes);

echo '<hr>';
echo $_dcss->render();



}//$Application;
?>