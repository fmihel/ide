<?php
/*
https://ws-framework-fmihel.c9users.io/ide/ws/utils/common.php
*/
//------------------------------------
// константы для типизации ARR::to_json
define("COMMON_TYPE_STRING",        "[@str13]:");
define("COMMON_TYPE_STRING_LEN",    "9");

//------------------------------------
// Вычисление замен для ф-ции pre_json
$UC_FROM = array('\\');
$UC_TO = array("\u005C");

for($i=0;$i<31;$i++){
    $uc = dechex($i).'';
    if (strlen($uc)==1) $uc='0'.$uc;
            
    array_push($UC_FROM,chr($i));
    array_push($UC_TO,'\u00'.$uc);
};

$_from = array('[',      ']',     "'",     '"',     '<',     '>');    
$_to =   array('\u005B', '\u005D','\u0027','\u0022','\u003C','\u003E');

for($i=0;$i<count($_from);$i++){
    array_push($UC_FROM,$_from[$i]);
    array_push($UC_TO,$_to[$i]);
}

//------------------------------------

class COMMON{
    public static function pre_json($str){
        //S:Подготавливает utf8 строку для парсинга json
        //R: string
        global $UC_FROM;
        global $UC_TO;

        //return htmlspecialchars(str_replace($from,$to,$str));
        return str_replace($UC_FROM,$UC_TO,$str);
    }
    
    /**
     * Позволяет получить значение параметра, внутри переменной
     * 
     * Ex:
     * К примеру имее структуру
     * 
     * $VAR = array("BOSS"=>array("NAME"=>"Mike","Phones"=>array("123-45-67","345-67-89")));
     * 
     * Для получени значения телефона, необходимо ( по хорошему выполнить след строку)
     * if (isset($VAR)&&(isset($VAR['BOSS']))&&(isset($VARS['BOSS']['Phones']))&&(isset($VARS['BOSS']['Phones'][0])))
     *      $phone = $VARS['BOSS']['Phones'][0];
     * else
     *      $phone = false;
     *          
     * либо
     * 
     * $phone = COMMON::get($VAR,'BOSS','Phones',0,false);
     * $phone = COMMON::get($VAR,'BOSS,Phones,0',false);
     * 
     * ВНИМАНИЕ!! Последний параметр значение по умолчанию, поэтому кол-во передаваемых параметров в ф-цию должно быть не меньне 3 (трех)!
     * 
     * !!
     */ 
    static public function get(){
        $mean = false;

        $count = func_num_args();
        if ($count<3){ 
            _LOGF('count params must more or equal 3','',__FILE__,__LINE__);
            
            return false;
        }    
    
        $var        =   func_get_arg(0);
        $default    =   func_get_arg($count-1);
        $params = array();

        for($i=1;$i<$count-1;$i++){
            $param = explode(',',func_get_arg($i));
            $params = array_merge($params,$param);
        }
        $count = count($params);
        
        
            
        if (!isset($var))
            return $default;

        for($i=0;$i<$count;$i++){

            $param = $params[$i];
            if ((is_string($param))&&(TYPE::is_numeric($param)))
                $param = intval($param);
                
            $type = TYPE::info($var);    
            if ((($type==='array')||($type==='assoc'))&&(isset($var[$param]))){
            //if ((TYPE::is($var,'array,assoc,object'))&&(isset($var[$param]))){
                if ($i===$count-1)
                    return $var[$param];    
                else    
                    $var = $var[$param];
                    
            }elseif (($type==='object')&&(property_exists($var,$param))){
                if ($i===$count-1)
                    return $var->{$param};    
                else    
                    $var = $var->{$param};                
            }else
                return $default;    
        }
    }    
    
};

class TYPE{
    public static function to($type,$v){
        
        $type = strtolower($type);
        if (($type==='boolean')||($type==='bool'))
            return self::toBool($v);
        if (($type==='num')||($type==='float')||($type==='real')||($type==='numeric')||($type==='double'))
            return self::toNum($v);
        if (($type==='int')||($type==='integer'))
            return intval($v);            
        if ($type==='string')
            return $v.'';
            
        return $v;     
    }
    public static function toBool($v){
        if (is_bool($v)) return $v; 
        $v=strtolower(trim($v));
        return (($v==='1')||($v==='true')); 
    }
    public static function toNum($v,$default=0,$forced = 1){
        
        if (is_float($v)) return $v;
        if (is_int($v))   return $v;
            
        $v = trim(str_replace(',','.',$v));
        
        $check = '';
        
        if($forced === 1){
            // простая проверка, на то что в строке только данные относящиеся к числу
            $check  = str_replace(array(1,2,3,4,5,6,7,8,9,0,'.','-','+'),'',$v);
        }elseif($forced === 2){
            // подробная проверка, на то что в строке только данные относящиеся к числу
            $check  = str_replace(array(1,2,3,4,5,6,7,8,9,0),'',$v);
            $check = STR::str_replace_once('.','',$check);
            $check = STR::str_replace_once('-','',$check);
            $check = STR::str_replace_once('+','',$check);
        }else
            return floatval($v);

        
        if ($check==='')
            return floatval($v);
        else
            return $default;
    
    }    
    public static function is_assoc($arr)
    {
        //S: Проверка на то, что массив ассоциативный
        //R: TRUE если массив $array ассоциативный
        $a1 = is_array($arr);
        if ($a1){ 
					$a2 = array_keys($arr);
					$a2 = is_numeric(array_shift($a2));
					return ($a1 && !$a2);
				}
				return false;	 
    }
    public static function is_numeric($val,$forced = false)
    {
        $val = trim($val);
        $is_num = is_numeric($val);  
        if($forced){
        //S:Проверка на то что значение цифра ( с учетом json-кого 0 впереди)
            if ($is_num)
                $is_num = ((strlen($val.'')==1) ||((strlen($val.'')>1) && (strpos($val,'0')!==0)));
        }        
        return $is_num;
    }
    /** возвращает расширенную информацию о типе */ 
    public static function info($v){
        $type = gettype($v);
        
        if (($type==='array')&&(ARR::is_assoc($v)))
            $type = 'assoc';
        
        return $type;
    }
    
    /**
     * проверяет является ли $v переменной одного из предстваленных в $types типом
     * Ex:
     * $v = array('A'=>1,2,4);
     * 
     * TYPE::is($v,'array') // false
     * TYPE::is($v,'array,assoc') // true
     * TYPE::is($v,array('array','assoc')) // false
     *  
     */
     
    public static function is($v,$types){
        if (gettype($types)==='string')
            $types = explode(',',$types);
        
        $t = self::info($v);
         
        for($i=0;$i<count($types);$i++)
            if ($t===$types[$i]) return true;

        return false;
    }
    /* приводит данные в $struct к определенному типу
    *  
    */
    public static function typist($struct,$rule,$deep=false){

        $rule = ARR::extend(array(),$rule);

        ARR::loop($struct,function($key,$val) use (&$struct,$rule){
            foreach($rule as $name=>$type)
            if ($key===$name)
                $struct[$key] = TYPE::to($type,$val); 
        });
    
}
    
     
};

class STR{
    
    static function str_replace_once($search, $replace, $text,$mb=false) { 
        if ($mb){
            $pos = mb_strpos($text, $search); 
            return $pos!==false ? substr_replace($text, $replace, $pos, mb_strlen($search)) : $text; 
        }else{
            $pos = strpos($text, $search); 
            return $pos!==false ? substr_replace($text, $replace, $pos, strlen($search)) : $text; 
        }    
    }
    
    static function replace_loop($search,$replace,$text){
        // заменяет $search на $replace пока они существуют в $text 
        $loop = 1000000;
        while (1>0){
            $loop--;
            if ($loop<0) {
                $msg = 'LOOP in STR::replace_loop '.__FILE__;
                error_log($msg);
                echo $msg;
                exit;
            };   
            
            $need = false;
            if (is_array($search)){
                for($i=0;$i<count($search);$i++){
                    $need = (mb_strpos($text,$search[$i])!==false);
                    if ($need) break;
                };
                
            }else
                $need = (mb_strpos($text,$search)!==false);
            
                
            if ($need)
                $text=str_replace($search,$replace,$text);
            else
                return $text;
        }
        
    }
   
    static function replace_last($search,$replace,$text){
        /*перезаписывает $search если он находится в конце строки*/
        
        $_text = trim($text);
        $pos = mb_strrpos($_text,$search);
        
        if (($pos!==false)&&(($pos+mb_strlen($search))==mb_strlen($_text)))
            return mb_substr($_text,0,$pos).$replace;
        else    
            return $text;
    }
    
    static function to_array($delimeter,$str){
        
        $arr = explode($delimeter,$str);
        $out = array();
        for($i=0;$i<count($arr);$i++){
            $value = trim($arr[$i]);
            if ($value!=='') array_push($out,$value);
        }
        
        return $out;
    }
    
    static function random($count){
        //S: Возвращает случайно заполненную строку длиной count
        $result = '';
        for($i = 0;$i<$count;$i++)  
        {
            if (rand(1,10) > 6)        
                $result.=chr(rand(48,57));
            else
                $result.=chr(rand(65,90));
        };  
        
        return $result;
    }
    
    public static function find($str,$template,$all=true){
        //error_log('tes'); 
        $lines  = array();

        if (preg_match_all('/\n/',$str,$_lines,PREG_OFFSET_CAPTURE)){
            for($i=0;$i<count($_lines[0]);$i++)
                array_push($lines,$_lines[0][$i][1]);
        }
  
        $res = array();
        //$template=self::_find_slash($template);
        
        //error_log('['.$template.']');
        
        if (preg_match_all($template,$str,$find,PREG_OFFSET_CAPTURE)){
            $find = $find[0];
            for($i=0;$i<count($find);$i++){
                $text = $find[$i][0];
                $_off = $find[$i][1];
                
                $line = 0;
                for($line=0;$line<count($lines);$line++)
                    if ($_off<$lines[$line]) break;
                
                $line_pos = ($line>0?$lines[$line-1]+1:0);
                $line_next = $lines[$line];
                
                $pos = $_off-$line_pos;
                
                
                //$left  = htmlspecialchars(substr($str,$line_pos,$pos));
                //$right = htmlspecialchars(substr($str,$line_pos+$pos+strlen($text),$line_next-$line_pos-strlen($text)-$pos));
                //$text = htmlspecialchars($text);
                
                //array_push($res,array('line'=>$line,'pos'=>$pos,'off'=>$_off,'find'=>$text,'left'=>$left,'right'=>$right));
                array_push($res,array('line'=>$line,'pos'=>$pos,'off'=>$_off,'find'=>$text));
                
                if (!$all) 
                    return $res;
            }   
    
        }
        return $res;
    }
    
    private function _find_slash($key){
        $key = trim($key);
        return ($key[0]!=='/'?'/':'').$key.($key[strlen($key)-1]!=='/'?'/':'');
    }
    
    public static function quoter($str,$pref){
        /*ищет все строки, заключенные в ковычки, 
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
                $key = $pref.$i;
                $i++;
                
                $id[$key] = mb_substr($str,0,$pos);
                $str = $left.$key.mb_substr($str,$pos+1);
                
            }else 
                break;
            
            $pos = mb_strpos($str,$char);

        }
        
        return array('str'=>$str,'id'=>$id);
    }
    
    public static function repeat($str,$count){
        $res = '';
        for($i=0;$i<$count;$i++)
            $res.=$str;    
        
        return $res;
    }
    /**
     * проверка, что строка содержит цифру (только)
     * Ex:
     * is_numeric('12')         : true
     * is_numeric('12.4')       : true
     * is_numeric(' 2.4')       : false
     * is_numeric('1')          : true
     * is_numeric('-12.4')      : true
     * is_numeric('+12.4')      : true
     * is_numeric('.40')        : true
     * is_numeric('1. 4')       : false
     * 
     * Ex: только целочисленное
     * is_numeric('1.4',true)       : false
     * is_numeric('15',true)        : true
     * is_numeric('-12')            : false
     * is_numeric('+12')            : false
     */ 
    public static function is_numeric($str,$onlyInt=false){
        //$str = '0123456789.-+';    
        $point = 0;
        $c = mb_strlen($str);
        
        if ($c===0) 
            return false;
        
        for($i=0;$i<$c;$i++){
            
            $code = ord($str[$i]);
            
            if ($i===0){
                if ($onlyInt){
                    
                    if (($code<48)||($code>57))
                        return false;
                        
                }else
                if (($code!==43)&&($code!==45)&&(($code<48)||($code>57))){ 
                    if ($code===46)
                        $point++;
                    else 
                        return false;
                }  
                
            }else{
                
                if (($code<48)||($code>57)){
                    if ((!onlyInt)&&($code===46)){
                        if ($point===0)
                            $point++;
                        else
                            return false;
                    }else 
                        return false;                    
                }
            } 
        }
        return true;    
    } 
    
    public static function to_utf($str){
        return mb_convert_encoding($str, 'utf-8', 'windows-1251');
    }
    public static function to_win($str){
        return mb_convert_encoding($str, 'windows-1251','utf-8');
    }

};

class ARR{ 
    /** сравнение массивов или хешей 
     *  soft = true - сравнение с приведением типов
    */
    public static function eq($a,$b,$param){
        
        $param = self::extend('{
            soft@bool:false,
            include:[],
            exclude:[]

        }',$param);

        $typeA = TYPE::info($a);
        $typeB = TYPE::info($b);
        
        $soft = $param['soft'];
        $include = $param['include'];
        if (TYPE::info($include)!=='array')
            $include = array($include);
        
        $exclude = $param['exclude'];    
        if (TYPE::info($exclude)!=='array')
            $exclude = array($exclude);
        //--------------------------------

        $cnt = count($include);
        if($cnt>0){
            $aaa = array();
            $bbb = array();
            for($i=0;$i<$cnt;$i++){
                $key = $include[$i];
                if (isset($a[$key])) 
                    $aaa[$key]=$a[$key];
                if (isset($b[$key])) 
                    $bbb[$key]=$b[$key];
            }
            $a = $aaa;
            $b = $bbb;
        }

        //--------------------------------
        $cnt = count($exclude);
        if($cnt>0){
            for($i=0;$i<$cnt;$i++){
                $key = $exclude[$i];
                if (isset($a[$key])) 
                    unset($a[$key]);
                    
                if (isset($b[$key])) 
                    unset($b[$key]);
            }
        }
        //--------------------------------
        
        if ($typeA!==$typeB) return false;
        
        
        if ($typeA==='array'){
            $count = count($a);
            if ($count!==count($b)) return false;
            
            for($i=0;$i<count;$i++){
                if ($soft){
                    if ($a[$i]!=$b[$i])
                        return false;
                }else{ 
                    if ($a[$i]!==$b[$i])
                        return false;
                }    
            }
            return true; 
                
        }else if ($typeA==='assoc'){
            $keyA = array_keys($a);
            $keyB = array_keys($b);

            $count = count($keyA);
            if ($count!==count($keyA)) return false;
            
            foreach($a as $k=>$v){
                if (!isset($b[$k])) 
                    return false;
                if ($soft){
                    if ($a[$k]!=$b[$k])
                        return false;
                }else{ 
                    if ($a[$k]!==$b[$k])
                        return false;
                }    
            }
            return true;
            
        }else
            return false;
        
        
    }
    public static function is_assoc($arr){
        
        if (is_array($arr)){
            if (array() === $arr) return false;
            return array_keys($arr) !== range(0, count($arr) - 1);
        }
        return false;
    }
    /**
     * преобразует входной хеш в объект. Преобразование касаеся всех элементов, включая и вложенные) 
    */
    public static function  to_object($arr){
        
        $type = TYPE::info($arr);
        if ($type==='array'){
            $res = array();
            for($i=0;$i<count($arr);$i++)
                $res[]=self::to_object($arr[$i]);
            return $res;
        }elseif($type==='assoc'){
            $res = array();
            foreach($arr as $k=>$v)
                $res[$k]=self::to_object($arr[$k]);
            return (object)$res;
        }else
            return $arr;
    }
    /** 
     * принудительная типизация коллекции ассоциативного массива 
    */
    public static function forcedTyping($data,$fields=array()){

        $all = (count($fields)===0);
    
        if (TYPE::is_assoc($data)){
            foreach($data as $k=>$v){
                if (($all)||(array_search($k,$fields)!==false))
                    $data[$k] = COMMON_TYPE_STRING.$v;
            }
        }elseif (is_array($data)){
            for($i=0;$i<count($data);$i++)
                $data[$i] = self::forcedTyping($data[$i],$fields); 
        
        };
    
        return $data;
        
    }
    
    public static function to_json($arr){
        //SHORT:Преобразует PHP массив в строку, которую можно парсить JSON
        /*DOC: Преобразует PHP массив в строку, для возможности парсить ее JSON на стороне клиента. При этом строки будут кодироваться посредством JUTILS::JSON_CODE.
        Для правильной раскодировки используйте ф-цию javascript [code]JUTILS.JSON_DECODE(str)[/code] Так же bool значения, после парсинга,правильней проверять с помощью ф-ции [code]JUTILS.AsBool(mean)[/code]
        */

        if (TYPE::is_assoc($arr)){
            $res = '{';
            foreach($arr as $Name=>$Value)
            {
                if ($res !=='{') 
                    $res.=',';        

                if (is_array($Value))
                    $res.= '"'.$Name.'":'.ARR::to_json($Value).'';          
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
                            $res.= '"'.$Name.'":'.$Value;
                        else{
                            
                            $strpos = mb_strpos($Value,COMMON_TYPE_STRING);
                            if ($strpos===0)
                                $Value = substr($Value,COMMON_TYPE_STRING_LEN); 
                                
                            $res.= '"'.$Name.'":"'.COMMON::pre_json($Value).'"';
                            
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
                    $res.= ARR::to_json($arr[$i]);
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
                        $res.= '"'.COMMON::pre_json($arr[$i]).'"';         
                }
							};
						}else{
							$res.= '"'.COMMON::pre_json($arr).'"';
						}	
            $res.=']';    
        }
        return $res;

    }
    
    /** 
     *  экранирование символов перед передачей в 
     *  to_json
     */ 
    public static function json_quote(&$arr,$add=true){
        
        $a = array(' ',"\n","\r","\t",'{','}','"',"'",':');
        $b = array('#&32;','#&13;','#&8;','#&9;','#&123;','#&125;','#&34;','#&39;','#&40;');
        
        $from   = ($add?$a:$b);
        $to     = ($add?$b:$a);

        foreach($arr as $k=>&$v){
            
            $type = gettype($v);
            
            if ($type==='string')
                $v = str_replace ($from,$to,$v);
            else if ($type==='array')
                self::json_quote($v,$add);
        }
        
    }
    
    public static function from_json($json){
        
        //---------------------------------------------------
        // ВНИМАНИЕ!  значения свойств массивов не должны содержать { " ' , и пробелы
        //  (все это экранируем)
        //---------------------------------------------------
        
        
        //---------------------------------------------------        
        /*убрали все пробелы*/
        //---------------------------------------------------
        $json = str_replace("\n",'',$json);
        while (mb_strpos($json,' ')!==false)
            $json=str_replace(' ','',$json);

        //---------------------------------------------------
        /*убрали все ковычки*/
        //---------------------------------------------------
        $json = str_replace(array('"',"'"),'',$json);

        $json = STR::replace_last(';','',$json);

        //---------------------------------------------------
        /* добавили ковычки для выполнения стандарта JSON*/
        //---------------------------------------------------
        //$json = preg_replace('/[[:word:]\#\.]+/','"\\0"',$json);
        //echo '<xmp>'.$json.'</xmp>';
        $json = preg_replace('/[^\{\}\:\,\[\]]+/','"\\0"',$json);
    
        //echo ''.$json.'';
        //exit;
        //---------------------------------------------------
        //---------------------------------------------------
        
        $res = json_decode($json,true);
        if (is_array($res))
            self::_json_id($res,$STR);
        else 
            $res = array();
            
            
        return $res;

    }
    
    /**
     * расширенный вариант для парсинга json в строке
     * json может содержать комментарии /* //
     * в значениях строк могут присутствовать пробелы и переносы 
     * максимально приближен к ECMAScript 5
     *
     * ВНИМАНИЕ! При сильном увеличении размера парсируемого текста может на порядок работать медленнее чем from_json
     * 
     * Ex: 
     * from_json_ex('a:10');    
     * >>  array('a'=>'10')
     * 
     * from_json_ex('{
     *     a:10
     * }');
     * >>  array('a'=>'10')
     * 
     * from_json_ex('{
     *     a:10
     * }');
     * 
     * 
    */ 
    public static function from_json_ex($str){
        $s = '';
        $i = 0;
        // на всякий случай ели нет, то обернем в фигурные скобки
        $str = ltrim($str);
        if ($str==='') $str='{}';
        if (($str[0]!=='{')&&($str[0]!=='[')) 
            $str='{'.$str.'}';
            

        $count = strlen($str);
        
        $root       = array();  // верхний уровень создаваемого массива
        $current    = &$root;   // текущий заполняемый уровень
        $parent     = null;     // родительский уровень
        $buffer     = array();  // дерево уровней и параметров к ним
        
        $key = false;           // текущий ключь (имя переменной)
        $val = false;           // текущее значение переменной
        $in = '';               // текущий сформированныйотрезок строки от последнего служебного символа до текущей позиции в парсируемой строке 
        $needVal = true;        // признак, что значение еще не вводилось (нужно для случаев, когда встречается замыкающая скобка } или ] )
        $string = '';           // формруемое значение найденной стоки (значение заключенное в ковычки )
        $isString = false;      // признакк того, что данные ля заполнения значением нужно брать из $string, а не из $in
        $bstring = false;       // признак того, что текущая позиция в строке находится внутри строкового значения (значение заключенное в ковычки ) и => служебные символы игнорируются до момента пока строка не будет закрыта
        $kov = '';
        $comment = false;
        
        $object = $str[0];
        
        for($i=0;$i<$count;$i++){

            $s  = $str[$i];

            if (($bstring===false)&&(($s==='{')||($s==='['))){ // найдено начало блока
                
                
                //_LOGF($object,'object',__FILE__,__LINE__);

                // сохраняем текущее состояние в буффере
                $buffer[]   =   array(
                    'current'   =>&$current,
                    'pos'       =>$i+1,
                    'object'    =>$object
                );
                $object = $s;

                $parent     = $buffer[count($buffer)-1];
                
                
                // создаем новый дочерний элемент
                if($key!==false){ 
                    
                    $current[$key]  = array();
                    $current        = &$current[$key];
                    
                }else{
                    $current[]  = array();
                    $current    = &$current[count($current)-1];
                }    
                
                $in = '';
                $key = false;
                $val = false;

            }elseif (($bstring===false)&&(($s==='}')||($s===']'))){ // найден конец блока 
                
                // если есть не введенные элементы, введем их
                $val = ($isString?$string:trim($in));
                // дополнительное услови, на необходимость вставки последнего значения (проверяем, что значение либо явно указано как строка, либо была задана в ковычках)
                $needVal = ($needVal)&&(($key!==false)||(($isString)||($val!=='')));

                
                if (($val!==false)&&($needVal)){
                    
                    if ($key!==false)
                        $current[$key] = $val;
                    elseif($object==='{')
                        $current[$val] = "";
                    else
                        $current[] = $val;
                }
                
                // откатим состояние на уровень выше
                $p=&$buffer[count($buffer)-1];
                $current = &$p['current'];
                $object = $p['object'];
                
                array_pop($buffer);

                $in = '';
                $key = false;
                $val = false;
                $needVal = false;
                $isString = false;
                
                
            }else{
                
                if ($bstring===false){ 

                    if ($s === ':'){
                        
                        $key = trim($isString?$string:$in);
                        $in='';
                        $string = '';
                        $isString = false;

                    }elseif ($s===','){
                        
                        if ($needVal){

                            $val = ($isString?$string:trim($in));
                            if ($key!==false)
                                $current[$key] = $val;
                            elseif($object==='{')
                                $current[$val] = "";
                            else
                                $current[] = $val;
                        }

                        $in='';
                        $string = '';
                        $needVal = true;
                        $key= false;
                        $isString = false;
                        
                    }elseif (($s==='"')||($s==="'")){
                        
                        $string = '';
                        $in='';
                        $bstring = true;
                        $isString = true;
                        $kov = $s;

                    }elseif (($s==='/')&&($i<$count-1)){
                        $ss = $str[$i+1];
                        
                        if (($ss==='*')||($ss==='/')){
                            $bstring = true;
                            $comment = $ss;
                            $i++;
                        }    
                        
                    }else
                        $in.=$s;
                        
                }else{
                    
                    if ($comment){ // обработка замыкающего тега для комментария
                        if (
                            (($comment ==='*')&&($s==='*')&&($i<$count-1)&&($str[$i+1]==='/'))
                            ||
                            (($comment ==='/')&&(ord($s)===10))
                        )
                        {
                            if ($comment==='*')
                                $i++;
                                
                            $bstring = false;
                            $comment = false;
                            
                            
                        }
                    }else{
                        
                        // обработка замыкающей ковычки    

                        if ($s!==$kov)
                            $string.=$s;
                        else
                            $bstring=false;
                        
                    }        
                    
                }        
                    
            }
            
        }
        return $root[0];
    }    
    
    private static function _json_id(&$json,&$STR){
        
        foreach($json as $k=>$v){
            if (is_array($v)){
                self::_json_id($v,$STR);
                $json[$k]=$v;
            }else{    
                if (isset($STR[$v]))
                    $json[$k]=str_replace('"','',$STR[$v]);
            }    
        }
    }
    public static function have_json($str,$fast=true){
        /*определяет находится ли json в строке*/
        if ($fast)
            return mb_strpos(trim($str),'{')===0;
        else
            return is_array(self::from_json($str));
        
    }
    public static function to_php_code($arr,$formated=false,$level=1){
        //S: преобразует в код php
        $div = "    ";
        $tab = ($formated?STR::repeat($div,$level):'');
        $tab_0 = ($formated?STR::repeat($div,$level-1):'');
        $cr = $formated?"\n":'';
        
        if (is_array($arr)){
            $res = '';
            
            if (ARR::is_assoc($arr)){

                foreach($arr as $k=>$v)
                    $res.=($res!=''?',':'').$cr.$tab."'".$k."'"."=>".ARR::to_php_code($v,$formated,$level+1);
                
            }else{
                for($i=0;$i<count($arr);$i++){
                    $res.=($res!=''?',':'').$cr.$tab.ARR::to_php_code($arr[$i],$formated,$level+1);
                }
            }
            return 'array('.$res.$cr.$tab_0.')';
            
        }else
            return "'".$arr."'";
    }
    
    
    private static function _union($a = array(), $b = array(),$deep,$_level=0){
        
        if ((is_array($a)) && (is_array($b))) {
            
            if (($a===array())||(self::is_assoc($a))) {
                $res = $b;    
                foreach ($a as $k => $v) {
                    if (!isset($b[$k])) {
                        $res[$k] = $v;
                    } else {
                        if (($deep) && ((is_array($v)) && (is_array($b[$k])))) {
                            $res[$k] = self::_union($v, $b[$k],$_level+1);
                        } else 
                            $res[$k] = $b[$k];
                    }
                }
                return $res;
            }
            
        };
        return $a;
    }    
    public static function union($a = array(), $b = array(),$deep=true){
        
        if ( (!is_array($a)) || (!is_array($b)) ){
            $msg = 'union params must be array';
            throw new Exception($msg);
        }
        return self::_union($a,$b,$deep,0);
    }
    public static function _extend(&$to,$from){

        $types =array('@boolean','@numeric','@double','@integer','@int','@float','@real','@num','@bool','@string');

        ARR::loop($from,function($key,$val,$i) use (&$to,$types){
            
    
            if (TYPE::is($val,'array')){

                $addType = '';
                if (!isset($to[$key])){
                    for($j=0;$j<count($types);$j++){
                        if(isset($to[$key.$types[$j]])){
                            $addType = $types[$j];
                            break;
                        };   
                    };
                };
                
                if ((isset($to[$key.$addType])&&(TYPE::is($to[$key.$addType],'array')))||(count($val)>0))
                    $to[$key.$addType] = $val;

            }else if (TYPE::is($val,'assoc,object')){
                
    
                if (isset($to[$key]))
                    ARR::_extend($to[$key],$val);
                else
                    $to[$key] = $val;
                    
                    
            }else{

                for($j=0;$j<count($types);$j++){
                    $param = $key.$types[$j];
                    if (isset($to[$param])){
                        $type = trim(mb_substr($types[$j],1));
                        $val = TYPE::to($type,$val);
                        unset($to[$param]);
                        break;
                    }    
                }
                $to[$key] = $val;
            }
    
        });
        
    
        ARR::loop($to,function($key,$val) use (&$to,$types){

            $posA = mb_strpos($key,'@');
            $type = false;
            
            if ($posA!==false){
                $type = mb_substr($key,$posA);
                $idx = array_search($type,$types);
                if ($idx!==false){
                    
                    $var = mb_substr($key,0,$posA);
                    $type = mb_substr($type,1);
                };
            };    
            
            if (TYPE::is($val,'array')){
                if ($type!==false){
                    $to[$var] = array();
                    for($j=0;$j<count($val);$j++)
                        $to[$var][] = TYPE::to($type,$val[$j]);
                    unset($to[$key]);    
                }else
                    ARR::_extend($to[$key],array());
                
            }elseif (TYPE::is($val,'assoc,object'))
                ARR::_extend($to[$key],array());
            else{
                if ($type!==false){
                    $to[$var] = TYPE::to($type,$val);
                    unset($to[$key]);    
                }    
            }    
            
                
            
            
        });

    }

    /** 
     * 
     * дополнение массива $to массивом $from
     * (в качестве типа могут быть массивы,хеши,объекты)
     * 
     * Ex:
     * $to      = array('mike'=>2,'some'=>'kjwe');
     * $from    = array('born'=>4,'some'=>'pikuli');
     * 
     * $out = ARR::extend($to,$from);
     *  $out have  - array('mike'=>2,'born'=>4,some'=>'pikuli'); 
     * 
     * Можно использовать json в строках 
     * 
     * ARR::extend($to,'{A:5,D:6}');
     * эквивалентно
     * ARR::extend($to,array('A'=>'5','D'=>'6''));
     * 
     * Замыкающие скобки можно опускать:
     * ARR::extend($to,'A:5,D:6');
     * 
     * Ex: можно указать типизацию параметров(в параметре $to), для этого после имени переменной необходимо
     * указать тип. При этом, после преобразования заначение переменной будет приведен в соотвествующий тип
     * 
     * ARR::extend('
     *  a@bool:true,
     *  b@num: 100.2,
     *  c:{
     *      ff@bool:true,
     *      pp:"wekjwldkj"
     *  }
     * ','
     *      a:false,
     *      c:{
     *          ff:false,
     *          aa:"edwelkj"
     *      }
     * ');
     * 
     * Ex: пример использования при передачи параметров в ф-циию
     * function animate($param){
     *  $a = ARR::extend('
     *      forced@bool:true,
     *      delay@bool:false,
     *      interval@int:1000',
     *  $param); 
     * 
     *  
     * 
     *  }
     * 
     * animate('forced:false,delay:true');
     * 
     * 
    */
    
    public static function extend($to,$from='',$type='asAssoc'){  
    
        $typeTo     =   TYPE::info($to);
        $typeFrom   =   TYPE::info($from);
        

        if ($typeTo==='string'){
            //if (!ARR::have_json($to)) $to = '{'.$to.'}';
            $to = ARR::from_json_ex($to);
            $typeTo =   TYPE::info($to);
        }
        
        if ($typeFrom==='string'){
            //if (!ARR::have_json($from)) $from = '{'.$from.'}';
            $from = ARR::from_json_ex($from);
            $typeFrom =   TYPE::info($from);
        }            
        
        
        if (($typeTo!=='object')&&($typeTo!=='array')&&($typeTo!=='assoc'))
            return $from;    
        
                
        if (($typeFrom!=='object')&&($typeFrom!=='array')&&($typeFrom!=='assoc')){
            $from = array();
            $typeFrom = 'array';
        }    
        
        
    
        self::_extend($to,$from);
        
        return $type==='asObject'?self::to_object($to):$to;     
    }
    /** универсальная ф-ция перебора элементов массив/хеша/свойств объекта 
     * Ex:
     * 
     * $arr = array(1,2,3,4,5);
     * ARR::loop($arr,function($i,$val,$step,$count,$all){
     *     echo $i.':'.$val;
     * });
     * 
     * $arr = array('A'=>1,'B'=>4);
     * ARR::loop($arr,function($key,$val,$step,$count,$all){
     *     echo $key.'='.$val;
     * });
     * 
     * Передача по ссылке (1):
     * 
     * $arr = array('A'=>1,'B'=>4);
     * ARR::loop($arr,function($key,$val,$step) use(&$arr){
     *     $arr[$key] = 'change'; 
     * });
     * Передача по ссылке (2):
     * 
     * $arr = array('A'=>1,'B'=>4);
     * ARR::loop(&$arr,function($key,$val,$step,$count,&$all){
     *     $all[$key] = 'change'; 
     * });
     */ 
    public static function loop($array,$callback){
    
        if (!is_callable($callback)){
            
            $msg = 'second parameter is not function';
            function_exists('_LOGF')?_LOGF($msg,'loop',__FILE__,__LINE__):error_log($msg);
             
            return ;
        } 
        
        $type = TYPE::info($array);
        
    
        if ($type==='array'){
            $count = count($array);
            try{
                
                for($i=0;$i<$count;$i++)
                    if ($callback($i,$array[$i],$i,$count,$array)===true) break;
            }catch(Exception $e){
                
                function_exists('_LOGF')?_LOGF($e,'loop',__FILE__,__LINE__):error_log(print_r($e,true));
            }
                
            
        }elseif ($type==='assoc'){
            $keys = array_keys($array);
            $i = 0;
            $count = count($keys);
            try{ 
                foreach($array as $k=>$v){
                    if ($callback($k,$array[$k],$i,$count,$array)===true) break;
                    $i++;
                }    
            }catch(Exception $e){

                function_exists('_LOGF')?_LOGF($e,'loop',__FILE__,__LINE__):error_log(print_r($e,true)); 
            }
    
        }elseif ($type==='object'){
            $keys = get_object_vars($array);
            $i=0;
            $count = count($keys);
            try{
                foreach($keys as $k=>$v){
                    if ($callback($k,$v,$i,$count,$array)===true) break;
                    $i++;
                }    
            }catch(Exception $e){

                function_exists('_LOGF')?_LOGF($e,'loop',__FILE__,__LINE__):error_log(print_r($e,true));

            }
            
        }
    }
        
        
};
    
class MATH{
        
        public static function translate($y,$y1,$y2,$x1,$x2){
      if (($y2-$y1) === 0)
        return 0;
    else
         return ($x2*($y-$y1)+$x1*($y2-$y))/($y2-$y1); 
    }
};

class IMG {
    
    public static function size($string_data){
         
         //S: размер изображения, можно использовать данные из DS
         if (gettype($string_data)!=='string')
            return array(0,0);
            
         if (mb_strlen(trim($string_data))===0)
            return  array(0,0);   
         
         
         $uri = 'data://application/octet-stream;base64,'.base64_encode($string_data);
         return getimagesize($uri);
    }
    
    public static function transparent($img,$size,$filter,$level){

        $to = array($level,$level,$level);

        $fone = imagecolorallocate($img, 255, 255, 255); 
        $wh   = imagecolorallocate($img, $to[0], $to[1], $to[2]);
        
        for($x=0;$x<$size[0];$x++){
            for($y=0;$y<$size[1];$y++){
                $rgb = imagecolorat($img,$x,$y);
                $r = ($rgb >> 16) & 0xFF;$g = ($rgb >> 8) & 0xFF;$b = $rgb & 0xFF;
                
                if (($r>$filter)&&($g>$filter)&&($b>$filter))
                    imagesetpixel($img, $x,$y, $fone);
                else
                    imagesetpixel($img, $x,$y, $wh);
                    
            }
        }
        
        $replace = imagecolorallocate($img, 255,255,255);
        imagecolortransparent($img,$replace);
        
        return $img;
    }    
    
    public static function getEmpty(){
        
        $img = imagecreate(1,1);
        $replace = imagecolorallocate($img, 0,0,0);
        imagecolortransparent ($img,$replace);
        
        return $img;    
    }
    
    /** масштабируем рисунок */
    public static function inscribe($size,$byparam,$mean){
        
        if (($size[0]===0)||($size[1]===0))
            return $size;
        
        if ($byparam ==='h'){
            $k = $mean/$size[1];
            return array($size[0]*$k,$mean);
        }else{
            
            $k = $mean/$size[0];
            return array($mean,$size[1]*$k);
        }    
            
    }


};

class EMAIL{
  
    /** отправка почты */
    public static function send($ToMail,$FromMail,$Theme,$Message){
        $headers = 'From: '.$FromMail."\r\n" .'Reply-To: '.$FromMail."\r\n";
        $headers .= 'Content-type: text/html; charset=utf8' . "\r\n";
        return mail($ToMail, $Theme, $Message, $headers);
    }
  
    /** проверка валидности почты */
    public static function valid($email){ 
        if (trim($email)==="") 
            return false; 
        
        $domain = @explode("@",$email);
        $domain = @$domain[1];

        return (!preg_match("/^([a-zA-Z0-9~\._-]{2,})(@{1}[a-zA-Z0-9~\._-]{2,})(\.{1}[a-zA-Z]{2,4})$/i",$email)) ? false : true ;
    }
};



?>