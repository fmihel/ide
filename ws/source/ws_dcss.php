<?php
/*
https://ws-framework-fmihel.c9users.io/ide/ws/source/ws_dcss.php
*/
if(!isset($Application)){
    require_once '../utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = true;
    
    require_once UNIT('utils','ctime.php');
    require_once UNIT('utils','common.php');

};
//----------------------------------------------------------
$_dcss = new WS_DCSS();
$_device = array(   'browser'=>array('w'=>1024,'h'=>768),
                    'size'=>array('w'=>1024,'h'=>768),
                    'device'=>array('w'=>1024,'h'=>768),
                    'mobile'=>0,
                    );
//----------------------------------------------------------

class DCSS{
    
    public static function CSS($css){
        global $_dcss;
        $_dcss->add($css.DCR);
    }

    public static function RENDER(/*$styles*/){
        
    
        global $_dcss;
        global $Application;

        $styles  = self::STYLES();
       
    
        // расчет списка css файлов привязанных к текущим стилям        
        $files = array(); 
        $css = (isset($Application->EXTENSION['CSS'])?$Application->EXTENSION['CSS']:array());
        
        
        for($i=0;$i<count($css);$i++){
            $c = $css[$i];
            $need = false;
            if ($c['dcss']){
                if ($styles[$c['dcss']['style']] == $css[$i]['dcss']['name'])
                    array_push($files,$c['remote']);
            }    

            if ($need)
                array_push($files,$css['remote']);
        };
        
          
        return array('css'=>$_dcss->render($styles),'vars'=>$_dcss->render_vars(),'files'=>$files);
    }
    
    public static function STYLES(/*set new styles*/){
        global $_dcss;
        global $_WS;
        
        $_dcss->version     = $_WS->version;
        $_dcss->renderPath  = $_WS->renderPath;
        $_dcss->mode        = DEVICE::isMobile()?'development':$_WS->mode;
        
        
        
        if(func_num_args()>0){
            
            $styles=func_get_arg(0);
            $_dcss->current_style($styles);
            
        }else    
            return $_dcss->current_style();
    }
    
}


class DEVICE{
    
    public static function SET($data){
        global $_device;
        $_device = $data;
        
    }
    
    public static function GET(){
        global $_device;
        return $_device ;
    }
    
    public static function asString(){
        global $_device;
        return serialize($_device);
    }
    
    public static function toPX($mm){
        global $_device;
        
        return MATH::translate($mm,0,$_device['size']['w'],0,$_device['browser']['w']);
    }   

    public static function isMobile(){
        global $_device;
        return $_device['mobile']==1;
    }

}
//----------------------------------------------------------
class WS_DCSS{

    private $dcss;
    private $vars;
    private $source;
    private $css;
    private $styles;
    private $strings;
    private $group_current;
    private $is_pre_render; 
    private $cache;
    
    public $renderPath;
    public $version;
    public $mode;
    
    function __construct(){
        
        $this->source = '';/*исодный анализируемыей dcss текст*/
        $this->dcss = array();        
        $this->vars = array();
        $this->css='';
        $this->styles = array();
        
        $this->is_render = false;
        $this->is_pre_render = false;
        
        $this->renderPath = '_render/';
        $this->version = '';
        $this->mode = 'development';
    }
    
    public function default_style(){
        $out = array();
        
        if (isset($this->dcss['styles']))
            foreach($this->dcss['styles'] as $k=>$v)
                $out[$k]=$v[0];
                
        return $out;        
    }
    
    public function current_style(/*new style*/){

        if(func_num_args()>0){
            
            $this->styles = func_get_arg(0);

        }else{

            if ($this->mode === 'production')
                $this->_cached_styles();
            else
                $this->pre_render();
                
            
            if (count($this->styles)==0)
                    $this->styles=$this->default_style();

            $default    =   $this->default_style();
            foreach($default as $k=>$v){
                if(!isset($this->styles[$k]))
                    $this->styles[$k]=$v;
            }
            
            return $this->styles;
            
        }    
    }
    
    public function add($source){
        $this->source.=$source;
    }

    private function pre_render(){
        /*выделяем dcss из исходников*/
        global $Application;
        
        if (!$this->is_pre_render){
            
            $this->source=$this->source.''.$Application->getExtConcat('DCSS',"\n");

            $this->_extract_comments($this->source);
            $this->_extract_dcss($this->source);
            
            $this->is_pre_render = true;
            
        }    

    }
    
    /** возвращает необходимость кеширования ( и создает необходимые папки ) */
    private function _cache_need($fileName){
        
        if (!file_exists($this->renderPath))
            mkdir($this->renderPath);
        return !file_exists($fileName);
    }
    
    /** загрузка сохраненного стиля 
     *  стиль будет помещен в $this->dcss['styles']
    */
    private function _cached_styles(){
        /*выделяем dcss из исходников*/
        global $Application;
        
        $file = $this->renderPath.'styles.dat';
    
        if ($this->_cache_need($file)){
            $this->pre_render();
            $json = serialize($this->dcss['styles']);
            file_put_contents($file,$json);
        }else{
            $json = file_get_contents($file);            
            $this->dcss['styles'] = unserialize($json);
        };    

    }
    /** возвращает имя файла, который будет записан кешь */
    private function _cached_name($ext='css'){
        /*выделяем dcss из исходников*/
        global $Application;
        
        $style = serialize($this->current_style());
        
        //$hash  = $Application->getExtHash('DCSS',$this->version.$style.DEVICE::asString());
        $hash  = $Application->getExtHash('DCSS',$this->version.$style);

        return $this->renderPath.$hash.'.'.$ext;
    }    
    
    /** сохраняем css в кешь */
    private function _cache($fileName){
        /*выделяем dcss из исходников*/
        global $Application;

        if ($this->_cache_need($fileName))
            file_put_contents($fileName,$this->css);

    }
    
    /** set $this->css    */
    private function _render(){
        global $Application;

        $this->pre_render();        
        
        if (!$this->is_render)
            $this->_generate();
        
        $this->is_render = true;
        
    }

    public function render(){
        global $Application;
        
        if ($this->mode==='production'){
            
            $file = $this->_cached_name('css');
        
            if ($this->_cache_need($file)){

                $this->_render();
                $this->_cache($file);
            
            }else
                $this->css = file_get_contents($file);
        }else
            $this->_render();
                    
        return $this->css;
    }

    private function _generate(){
        
        
        $this->styles = $this->current_style();
        
        
        $out = $this->source;
        
        $this->_extract_string($out);

        $this->_extract_vars($out);

        $this->_set_nocache();

        $this->_fill_css($out);

        $this->_fill_strings($out);

        $this->_auto_cache($out);

        $this->_uplotnyem($out);

        
        $this->css=$out;
        
        $out = '';
        return $out;   
    }
    
    private function _set_nocache(){
        global $_WS;    

        
        if ($_WS->version !== ''){
            if ($_WS->version === 'nocache')
                $version = '?nocache='.strtolower(STR::random(5));
            else 
                $version = '?'.$_WS->version;
        }else
            $version = '';
        
        $this->cache = $version;
        
        $this->var_mean('_cache_',$version);
        
    }    
    
    public function var_mean($name/*set new*/){
        $arg = func_get_args();
        if (count($arg)>1){
            $this->_var($name,$arg[1]);
        }else
            return $this->_var($name);
    }
    
    public function _render_vars(){
        
        $out = array();
        foreach($this->vars as $k=>$v){
            $mean = $this->var_mean($k);
            if (is_numeric($mean)) $mean = round($mean,1);
            
            if (mb_strlen($mean.'')<20)
                $out[$k]=$mean;    
        }
        return $out;
    }
    public function render_vars(){
        
        /*выделяем dcss из исходников*/
        global $Application;

        if ($this->mode==='production'){
            $file = $this->_cached_name('dat');
            
            if ($this->_cache_need($file)){

                $out = $this->_render_vars();
                file_put_contents($file,serialize($out));    
                
            }else{
                $json = file_get_contents($file);
                $out  = unserialize($json);
            }
        }else
            $out = $this->_render_vars();

        return $out;
    }
    
    private function _uplotnyem(&$css){
        $from= array("\n",'|','{ ',' }','; ',' ;',': ',' :','  ');
        $to = array(' ','','{','}',';',';',':',':',' ');
        //$from= array("\n\n",'|');
        //$to = array("\n",'');
        
        $css = STR::replace_loop($from,$to,$css);
        
        //while(mb_strpos($css,'  ')!==false)
        //    $css = str_replace('  ',' ',$css);
        
        //$css = STR::replace_loop($from,$to,$css);
        
    }
    
    private function _auto_cache(&$css){
        if($this->cache==='') return;
        $cache = mb_substr($this->cache,1);/*without ? */
        
        $re = '/background-image\s*\:\s*url\s*\([\'"]*(\s*\S*\s*[^\'"])[\'"]*\)\s*;/';        
        preg_match_all($re, $css, $m, PREG_SET_ORDER, 0);
        
        for($i=0;$i<count($m);$i++){
            
            $property  = $m[$i][0];
            $file      = $m[$i][1];
            
            if((mb_strpos($file,$cache)===false)&&(mb_strpos($file,'data:image')===false)){
                $new = str_replace($m[$i][1],$file.(mb_strpos($file,'?')===false?'?':'&').$cache,$property);
                $css = str_replace($property,$new,$css);
            }    
            
        }
        

    }
    private function _fill_strings(&$out){
        
        $from = array();
        $to = array();
        
        foreach($this->strings as $k=>$v){
            array_push($from,$k);
            array_push($to,$v);
        }
        $out = str_replace($from,$to,$out);
    }
    
    private function _mm($mean){
        
        if (mb_strpos($mean,'mm')!==false) {
        
            $re = '/[0-9]*[\.\,]?[0-9]+mm/';
            preg_match_all($re, $mean, $match, PREG_SET_ORDER, 0);
            
            for($i=0;$i<count($match);$i++){
                
                $val  = floatval(str_replace(',','.',$match[$i][0]));
                $val = DEVICE::toPX($val);
                $mean = STR::str_replace_once($match[$i][0],$val,$mean,true);
                
            }
        };
        
        return $mean;        
    }
    
    private function _eval($mean){
        
        //error_log('eval='.$mean);
        
        $mean = $this->_mm($mean);

        /*----------------------------------------------------------------------*/                
        /*тк заменяемая переменная может содержать, в себе др переменную, которая в свою очередь так же может содержать ссылку на переменную и т.д  то
        заменяем пока есть признак наличия переменной в строке но не более loop*/
        $loop = 100;
        while(mb_strpos($mean,'$')!==false){
                
            if ($loop<=0) {
                echo 'loop in ws_dcss2._eval...';
                exit;
            };
            $loop--;
                
            foreach($this->vars as $name=>$val){
                if (mb_strpos($mean,$name))
                    $mean = str_replace('$'.$name,$this->_var($name),$mean);
            }
        };
        /*----------------------------------------------------------------------*/                


        /*----------------------------------------------------------------------*/                
        // Проверка, есть ли в выражении арифметические операции
        $oper = array('-','+','/','*');

        for($i=0;$i<count($oper);$i++){
            if (mb_strpos($mean,$oper[$i])!==false){
                
                $eval = '';
                eval('$eval='.$mean.';');
                $mean = $eval;
                
                break;
            }
        }    
        /*----------------------------------------------------------------------*/                

        return $mean;
    }   
    
    private function _var($name /*set new*/){
        /*получаем значение переменной, ф-ция работает с */
        $arg = func_get_args();
        
        // устаннавливаем переменную
        
        if (count($arg)>1){
            $new_mean = $arg[1];
            
            if (isset($this->vars[$name])){
                $mean = $this->vars[$name];
                
                if (is_array($mean)){
                    
                    $style = array_keys($mean);
                    $style = $style[0];
                    
                    if (isset($this->styles[$style]))
                        $mean_name = $this->styles[$style];
                    else
                        $mean_name = $this->dcss['styles'][$style][0];
                    
                    
                    //error_log('vars='.print_r($this->vars[$name],true));
                    $this->vars[$name][$style][$mean_name] = $new_mean;
                    //error_log('new_mean='.print_r($new_mean,true));
                    //error_log('mean_name='.print_r($mean_name,true));
                    
                }else
                    $this->vars[$name] = $new_mean;    

                
            }else{
                
                $this->vars[$name] = $new_mean;
            }
            
            
        }else{
        // получаем переменную    
        if (isset($this->vars[$name])){
            
            $mean = $this->vars[$name];
            if (is_array($mean)){
                // получаем стиль к которому привязана переменная (это единственный ключ в переменной)
                $style = array_keys($mean);
                $style = $style[0];
                //error_log('style='.$style);
                
                $mean_name = '';
                if (isset($this->styles[$style]))
                    $mean_name = $this->styles[$style];// получаем раздел в стиле из которого будем брать конкретное значение
                else{
                    if (isset($this->dcss['styles'][$style]))
											$mean_name = $this->dcss['styles'][$style][0];
										
                    //error_log('mean_name='.print_r($mean_name,true));
                };
                
                //error_log('mean_name='.$mean_name);
                
                if (isset($mean[$style][$mean_name])){
                    
                    $mean = $mean[$style][$mean_name];// если существует раздел, то возвращаем значение из него

                }else{
                    // если не существует, то возвращаем значение первого попавшегося раздела
                    
                    $mean_name = array_keys($mean[$style]);
                    $mean = $mean[$style][$mean_name[0]];
                    
                }
                 
            }
            return $this->_eval($mean);
            
        }else {
            return false;
        }};
    }
    
    private function _var_from_fill($arr){
        
        $var = $this->_extract_var($arr['find']);
        $name = $var['name'];
        $mean = $var['mean'];
        
        //error_log("name=$name");
        //error_log("mean=".print_r($mean,true));
        
        if (!is_array($mean)){
            $this->_var($name,$mean);
            //_LOG("S update $name = [$mean]",__FILE__,__LINE__);
        }else{
            $style = array_keys($mean);
            $style = $style[0];
						
						if (isset($this->styles[$style])){
							$mean_name = $this->styles[$style];
            
							if (isset($mean[$style][$mean_name])){
								$value = $mean[$style][$mean_name];
                //_LOG("A update $name = [$value]",__FILE__,__LINE__);
                $this->_var($name,$value);
							}    
						}	
            
        }
        
    }
    private function _vars_in_css_str($str){
        $vars = array();
        $means = array();
        
        $len = mb_strlen($str);
        
        $ev = 0;
        $var = '';
        
        for($i=0;$i<$len;$i++){
            
            if (($ev === 0)&&($str[$i]==='$')){
                $ev = 1;
                $var = '';
            }elseif($ev === 1){
                if ($this->_isw($str[$i])){
                    $var.=$str[$i];
                }else{
                    
                    $vars[]='$'.$var;
                    $means[]= $this->_var($var);
                    $var = '';
                    $ev = 0;
                    $i--;
                }
            }    
        }
        
        if ($var!==''){
            $vars[]='$'.$var;
            $means[]= $this->_var($var);
        }    
        
        return array($vars,$means);
            
    }     
    private function _fill_css(&$out){
        $arr = explode("\n",$out);
        $count = count($arr);
        $i = 0;
        //echo $out."\n";
        while($i<$count){
            $pos = mb_strpos($arr[$i],'$');
            
            if ($pos!==false){
            
                $trim = trim($arr[$i]);
                $lenTrim = mb_strlen($trim); 
                $is_var_defined = true;
                
                /** ищем конструкцию типа 
                 * 
                 * $STRING = ..
                 * .. 
                 * .. ;
                 * 
                 */ 
                if (($trim[0]=='$')&&(mb_strpos($trim,'=')!==false)){
                    $buff = $trim;

                    array_splice($arr,$i,1);
                    $count--;
                    
                    $loop = 100;
                    while($trim[$lenTrim-1]!==';'){
                        
                        if (($loop--)===0){
                            _LOGF('dcss not have ";" ','Error',__FILE__,__LINE__);
                            exit;
                        }
                        
                        $trim       = trim($arr[$i]);
                        $lenTrim    = mb_strlen($trim); 
                        $buff.=$trim;
                        
                        array_splice($arr,$i,1);
                        $count--;
                        
                    }
                    
                    $this-> _var_from_fill(array('find'=>$buff));
                    $i--;

                }else{
                    // uses var in css code 
                    // insert var mean
                    
                    $vars = $this->_vars_in_css_str($arr[$i]);
                    //print_r($vars);
                    $arr[$i]=str_replace($vars[0],$vars[1],$arr[$i]);    
                    
                    
                }
            }
            $i++;
        }
        
        $out =  implode("\n",$arr);
        
    }    
    private function _fill_css2(&$out){
        
        $loop = 1000000;
        $css = $out;
        
        //error_log('len='.mb_strlen($css));
        //_LOG('['.$out.']',__FILE__,__LINE__);
        while(1>0){

            //----------------------------------------------------------
            
            if ($loop<=0) {
                
                echo "css:[\n".str_replace("\n\n",' ',$css)."\n]";
                echo __FILE__.' LOOP in func _fill_css()'."\n";
                return false;
                
            }    
            $loop--;    

            //----------------------------------------------------------
            // поиск первого вхождения определения переменной и использования переменной
            //----------------------------------------------------------
            
            //CT::MIDDLE_START('_find_word');
            $finds = self::_find_word($css);
            //CT::MIDDLE_STOP('_find_word');
            
            // выход из ф-ции если больше нечего искать                        
            if (!$finds['res']){
                $out = $css;
                //CT::MIDDLE_LOG('_find_word');
                //CT::MIDDLE_LOG('_find');
                return true;
            }else{
                $change =   $finds['CHANGE'];
                $set    =   $finds['SET'];
            }
            

            $is_change = false;

            //----------------------------------------------------------

            if (($change['res'])&&($change['off']==$set['off'])){
                $is_change = true;
                $c=$change;
            }else{
                if (($change['res'])&&($change['off']<$set['off'])){
                    $is_change = true;
                    $c=$change;
                }else{    
                    $is_change = false;
                    $c=$set;
                }    
            }

            $name = trim(str_replace(array('$','='),'',$c["find"]));
            

            //----------------------------------------------------------
            
            $mean = '';
            if (!$is_change){
                //  подставляем в css переменную
                $c['find']='$'.$name;
                $mean = $this->_var($name);
                //_LOG('SET $'.$name.'='.$mean,__FILE__,__LINE__);
            }else{
               // изменяем значение переменной $this->vars  и вырезаем этот кусок из css
                
                //CT::MIDDLE_START('_find');
                $re = '/\$'.$name.'+\s*=\s*[\s\S]+\s*;/U';
                $c = STR::find($css,$re,false);
                $c=$c[0];
                //CT::MIDDLE_STOP('_find');  
                //_LOG('CHANGE $'.$name." [".print_r($c,true)."]",__FILE__,__LINE__);
                
                $this->_var_from_fill($c);
                $mean='';
              
                
            }
            //----------------------------------------------------------
            $css = mb_substr($css,0,$c['off']).$mean.mb_substr($css,$c['off']+mb_strlen($c['find']));
            //----------------------------------------------------------
        }

    }
    
    private function _extract_comments(&$text){
        /*удаляем комментарии*/
        $text = preg_replace('#/\*(?:[^*]*(?:\*(?!/))*)*\*/#','',$text);
    }
   
    private function _split($re,$str){
        /*ф-ция вырезает все куски описанные регуляркой $re*/
        $res = preg_split($re, $str);
        $out = '';
        
        for($i=0;$i<count($res);$i++)
            if (trim($res[$i])!=='')
                $out.=$res[$i];
        return $out;
        
    }
    
    private function _extract_dcss(&$str){
        /*получаем dcss блок*/
        $re = '/\$dcss\s*=\s*{[\s\S]*}\s*;/U';
        $dcss = '';
        /*выделяем кусок с dcss*/
        preg_match($re, $str, $matches);
        
        $dcss = count($matches)>0?$this->_split('/\$dcss\s*=\s*/', $matches[0]):'';
        $this->dcss = ARR::from_json($dcss);

        /*остаток возрвщаем*/
        $str = $this->_split($re, $str);
    }
    
    private function _extract_string(&$str){
        
        $out = $this->_quoter($str,'_STRING_');
        $str = $out['str'];
        $this->strings = $out['id'];
    
    }
    
    private function _extract_vars(&$code){
        /*создаем список переменных*/  
        $re = '/\$[[:word:]]+\s*=[\s\S]*;/U';

        preg_match_all($re, $code, $matches);
        
        //error_log('code['.$code.']');
        //error_log(print_r($matches,true));
        
        $vars = array();
        for($i=0;$i<count($matches[0]);$i++){

            $arr = $this->_extract_var($matches[0][$i]);

            $var = $arr['name'];
            $mean = $arr['mean'];
            
            if (!isset($vars[$var]))    
                $vars[$var]=$mean;
        }
        
        $this->vars = $this->_order_vars($vars);    

        //error_log(print_r($this->vars,true));
        //$code=$this->_split($re,$code);
    }

    private function _quoter($str,$pref){
        
        /*
          ищет все строки, заключенные в ковычки, 
          и заменяет их на ключи key = $pref.i  
          строки помещает в хеш         
          
          возвращает хеш
          str - текст с заменными ключами
          id - массив ключь= строка
        */
        
        $char = '"';
        $id = array();
        $i = 0;

        $pos = mb_strpos($str,$char);

        while($pos!==false){
            
            $left   = mb_substr($str,0,$pos);
            $str    = mb_substr($str,$pos+1);
            $pos    = mb_strpos($str,$char);

            if ($pos!==false){
                $right = trim(mb_substr($str,$pos+1));
                
                if ($right[0]!==':'){
                
                    $key = $pref.$i.'_';
                    $i++;
                
                    $id[$key] = mb_substr($str,0,$pos);
                    $str = $left.$key.mb_substr($str,$pos+1);
                    
                }else{
                    $str = $left."'".mb_substr($str,0,$pos)."'".mb_substr($str,$pos+1);
                }    
                
            }else 
                break;
            
            $pos = mb_strpos($str,$char);

        }

        return array('str'=>$str,'id'=>$id);
    }

    private function _extract_var($code){
        /*выделение из строки имя переменной и ее значения (значение преобразуется в массив, если оно привязывается к стилю)*/  
        $re_var = '/\$[[:word:]]+\s*[^=]/';
        $re_mean = '/\$[[:word:]]+\s*=\s*/';
        
        preg_match($re_var, $code, $m);
        $var = trim(substr($m[0],1));
            
        $mean =STR::replace_last(';','',$this->_split($re_mean,$code));
        
        
        if (ARR::have_json($mean)){
            $mean = ARR::from_json($mean);
            
        }
        //error_log("var [$var] mean [".print_r($mean,true)."]");
        
        return array('name'=>$var,'mean'=>$mean);
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
    
    private static function _isw($o){
        $c = ord($o);

        if (($c>=97)&&($c<=122)) return true; // a-z
        if (($c>=65)&&($c<=90)) return true; // A-Z
        if (($c>=48)&&($c<=57)) return true; // num
        if (($c==95)) return true; // _

        return false;
    }

    private static function _isn($o){
        $c = ord($o);
        if ($c==32) return true;
        if ($c==10) return true;
        if ($c==9) return true;
    
        return false;
    }
    
    private static function _find_word($text){
    /*
        ищем переменную 
        $var =
        $var
        $var = ...;
        
    */
    $FW_START   = 0;
    $FW_STOP    = 1;
    $FW_DOLLAR  = 2;    //find $
    $FW_WORD    = 3;    //find word
    
    
    
    
    $pos = 0;
    $len = mb_strlen($text);
    //error_log('len='.$len);
    $out['CHANGE']  =array('find'=>'','off'=>-1,'event'=>$FW_START,'res'=>false);
    $out['SET']     =array('find'=>'','off'=>-1,'event'=>$FW_START,'res'=>false);
        


    
    $pos = mb_strpos($text,'$');
    
    while (($pos!==false)&&($pos<$len)){

        $c = $text[$pos];
        //error_log('['.$pos.'] "'.$c.'" ['.ord($c).']');        
        //--------------------------------------------
        if ($out['CHANGE']['event']==$FW_START){
            if ($c=='$'){
                
                $out['CHANGE']['event']     =   $FW_DOLLAR;
                $out['CHANGE']['find']   =   '$';
                $out['CHANGE']['off']       =   $pos;
                
            }    
        }else 
        if ($out['CHANGE']['event']==$FW_DOLLAR){
            if (self::_isw($c)){
                $out['CHANGE']['find'].=$c;
                $out['CHANGE']['event']=$FW_WORD;
            }else{
                if ($c=='$'){
                    $out['CHANGE']['event']=$FW_DOLLAR;
                    $out['CHANGE']['find']='$';
                    $out['CHANGE']['off']=$pos;
                }else{
                    $out['CHANGE']['event']=$FW_START;
                    $out['CHANGE']['find']='';
                    $out['CHANGE']['off']=-1;
                }    
            }
        }else 
        if ($out['CHANGE']['event']==$FW_WORD){            
            if (self::_isw($c))
                $out['CHANGE']['find'].=$c;
            else            
            if ($c=='='){
                
                if ($out['SET']['event']!=$FW_STOP){
                    $out['SET']=$out['CHANGE'];
                    $out['SET']['find']=trim($out['SET']['find']);
                    $out['SET']['event']=$FW_STOP;
                    $out['SET']['res']=true;
                }
                $out['CHANGE']['find'].=$c;
                $out['CHANGE']['event']=$FW_STOP;
                $out['CHANGE']['res']=true;

            }else
            if (self::_isn($c)){

                if ($out['SET']['event']!=$FW_STOP){
                    $out['SET']=$out['CHANGE'];
                    $out['SET']['find']=trim($out['SET']['find']);
                    $out['SET']['event']=$FW_STOP;
                    $out['SET']['res']=true;
                }
                
                $out['CHANGE']['find'].=$c;
            }else{

                if ($out['SET']['event']!=$FW_STOP){
                    $out['SET']=$out['CHANGE'];
                    $out['SET']['find']=trim($out['SET']['find']);
                    $out['SET']['event']=$FW_STOP;
                    $out['SET']['res']=true;
                }
                
                if ($c!=='$'){
                    $out['CHANGE']['event']=$FW_START;
                    $out['CHANGE']['find']='';
                }else{
                    $out['CHANGE']['event']=$FW_DOLLAR;
                    $out['CHANGE']['find']='$';
                    $out['CHANGE']['off']=$pos;
                }    
            }
        };    
        //--------------------------------------------
        $end = true;
        foreach($out as $k=>$v)
            $end = (($v['event']==$FW_STOP)&&($end));
        if ($end) break;
        //--------------------------------------------
        $pos++;
        //--------------------------------------------
    }//while
    
    $res = (($out['CHANGE']['event']==$FW_STOP)||($out['SET']['event']==$FW_STOP));
    $out['res'] = $res;
    
    return $out;
}
    
    
    public function debug_info($cr='<br>',$type = 'all'){
        $out='';
        $n="\n";
        if (($type==='all')||($type==='css'))
            $out.='css=['.$n.$this->css.']'.$cr;

        if (($type==='all')||($type==='source'))
            $out.='source=['.$n.$this->source.']'.$cr;

        if (($type==='all')||($type==='dcss'))
            $out.='dcss=['.$n.print_r($this->dcss,true).']'.$cr;
        
        if (($type==='all')||($type==='vars'))
            $out.='vars=['.$n.print_r($this->vars,true).']'.$cr;
        
        if (($type==='all')||($type==='strings'))
            $out.='strings=['.$n.print_r($this->strings,true).']'.$cr;

        
        
        return $cr.$out;
        
    } 
};



if ($Application->is_main(__FILE__)){


$re = '/background-image\s*\:\s*url\s*\([\'"]*(\s*\S*\s*[^\'"])[\'"]*\)\s*;/';
$css = '
.css{
	color:#ff00ff;
	background-image:url("yandex.jpg");
}

.css2{
	color:#ff00ff;
	background-image:url("yandex.jpg");
}
';

preg_match_all($re, $css, $matches, PREG_SET_ORDER, 0);


echo '<xmp>'.print_r($matches,true).'</xmp>';

for($i=0;$i<count($matches);$i++){
    $key  = $matches[$i][0];
    $file = $matches[$i][1];
    $file = $file.'?nocache';
    
    $new = str_replace($matches[$i][1],$file,$key);
    
    $css= str_replace($key,$new,$css);
}

echo '<xmp>'.$css.'</xmp>';


}//$Application->is_main(__FILE__)


?>