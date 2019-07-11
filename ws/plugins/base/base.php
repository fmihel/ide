<?php

/**
 * base - static class
 * небольшой класс работы с mysql (надстройка над mysqli, исключительно для моего удобства)
 * 
 * Ex: подключение 
 * base::connect('server','user','password','base name','base');
 * 
 * 'base' - внутреннее(программное) имя базы (опять же для удобства)
 * при последующих вызовов методов base если не указы
 * 
 * 
 * Ex: простой запрос
 * if (!base::query('select * from TEST')) echo 'error ';
 * 
 * Ex: возврат данных и цикл
 * $ds = base::ds('select ID,NAME from TEST');
 * if ($ds) 
 *    while(base::by($ds,$row)){
 *      echo $row['ID'].$row['NAME'];              
 *  }
 * 
 * Ex: транзакция (только для InnoDB)
 * 
 * base::startTransaction();
 * base::query('insert into TEST (NAME) values ("my name")');
 * base::commit();
 * 
 * 
*/


/** класс настроек вывода для генераторов _join и _tables */
class baseSetup{
    
    public static $refactoring=false;
    public static $CR="\n";
    public static $die = true;
    public static $debug = false;
    public static $removeEscape = true;
    
    public static function debug($bool='get'){
        if ($bool==='get')
            return self::$debug;
            
        self::$debug = ($bool===true?true:false);
        self::$refactoring = self::$debug;
    }
    
    public static function CR(){
        return (self::$refactoring?self::$CR:' ');
    }
    public static function log($e){
        base::_log($e);
        if (self::$die){
            
            echo "\n";
            print_r($e);
            exit;
        }    
    }
    public static function removeEscape($bool='get'){
        if ($bool==='get') return self::$removeEscape;
        
        self::$removeEscape = $bool?true:false;
    }
    
};
class _db{
    public $db;
    public $transaction;
    public $charset;
    public $errors=array();
    public $error="";
    /*
    public $server;
    public $user;
    public $pass;
    public $base_name;
    */
    function __construct(){
        $this->db=null;
        $this->transaction  = 0;
        $this->charset = '';
        /*
        $this->server       = '';
        $this->user         = '';
        $this->pass         = '';
        $this->base_name    = '';
        */
    }
};
class _join{
    
    private $str = '';
    private $def = array();
    private $prevTable = null;
    /**
     * 1. join(CLASS_TABLE1,CLASS_TABLE2,ID1,ID2);     table1 join tabl2 2 on ID1=ID2
     * 2. join(CLASS_TABLE1,CLASS_TABLE2,ID1);         table1 join tabl2 2 on ID1=ID1
     * 3. join(CLASS_TABLE1,CLASS_TABLE2);             будут найдены совпадающие поля, которые являются индексами
     * 4. join(CLASS_TABLE2,ID1,ID2);               - аналогично 1., в качастве CLASS_TABLE1 будет взята таблица, использованная последней в предыдущем join
     * 
     * @return $this;
    */
    public function join($table1,$table2=false,$index1=false,$index2=false){
        
        try{
            
            if (($table2===false)||(!_table::existClass($table2))){
                $index2 = $index1;
                $index1 = $table2;
                $table2 = $table1;
                $table1 = $this->prevTable;
            }

            $table1 = _table::toClass($table1);
            $table2 = _table::toClass($table2);
            
            if (($index1===false)&&($index2===false)){
                $fld1 = array_keys($table1::$types);
                $fld2 = array_keys($table2::$types);
            
                if (array_search($table2::INDEX,$fld1))
                    $index1 = $table2::INDEX;
                else if (array_search($table1::INDEX,$fld2))
                    $index1 = $table1::INDEX;
                else
                    throw new Exception("can`t find join fields");
                    
            }
            if ($index2===false)
                $index2 = $index1;
        
            $this->_joins($table1,$table2,$index1,$index2,'join');
        
            return $this;
            
        }catch(Exception $e){
            baseSetup::log($e);
            return $this;   
        }
        
    }
    /** 
     * соединение left outer join 
     * все записи из $table1 и если есть из  $table2
     */
    public function leftOuter($table1,$table2=false,$index1=false,$index2=false){
        
        try{
            if (($table2===false)||(!_table::existClass($table2))){

                $index2 = $index1;
                $index1 = $table2;
                $table2 = $table1;
                $table1 = $this->prevTable;
            
            }

            $table1 = _table::toClass($table1);
            $table2 = _table::toClass($table2);

            if (($index1===false)&&($index2===false)){
                
                $fld1 = array_keys($table1::$types);
                $fld2 = array_keys($table2::$types);
            
                if (array_search($table2::INDEX,$fld1))
                    $index1 = $table2::INDEX;
                else if (array_search($table1::INDEX,$fld2))
                    $index1 = $table1::INDEX;
                else
                    throw new Exception("can`t find join fields");
            }
            
            if ($index2===false)
                $index2 = $index1;
        
            $this->_joins($table1,$table2,$index1,$index2,'left');
        
            return $this;
            
        }catch(Exception $e){
            baseSetup::log($e);
            return $this;
        }    
    }
    
    private function _joins($table1,$table2,$index1,$index2,$type){
        
        $CR = baseSetup::CR();
        $first = ($this->str==='');
        $prev = $table2;
        
        if (!$first)
            $this->str.= ($type==='join'?'join':'left outer join').$CR;
            
        $def = $table1::define();
        if (array_search($def,$this->def)===false){
            $this->str.= $def.$CR;
            $this->def[]=$def;
            $prev = $table1;
        }else if (!$first){
            $def = $table2::define();
            if (array_search($def,$this->def)===false){
                $this->str.= $def.$CR;
                $this->def[]=$def;
                $prev = $table2;
            }else
                throw new Exception("redefined [$table2] [$table1]");
        }    
        
        if ($first)
            $this->str.= ($type==='join'?'join':'left outer join').$CR;
        
        if ($first){
            $def = $table2::define();
            if (array_search($def,$this->def)===false){
            
                $this->str.= $def.$CR;
                $this->def[]=$def;
                $prev = $table2;
            }    
        }    
        $this->str.= 'on'.$CR;
        $this->str.= $table1::field($index1).'='.$table2::field($index2).$CR;
        $this->prevTable = $prev;
    }

    public function asString(){
        return $this->str;
    }
    
};
class _table{
    
    public static function __callStatic($name,$arguments){
        $arg = '';
        
        if (count($arguments)>0)
            for($i=0;$i<count($arguments);$i++)
                $arg.=' '.$arguments[$i].' ';
                
        return static::field($name).$arg;
    }
    /** возвращает имя поля с добавленным алиасом 
     *  K_NAME =>  ALIAS_K_NAME
    */
    public static function field($field){
        return static::ALIAS.'.'.$field;
    }
    /** возвращает строку, содержащую список полей из указанного набора selectName
     *  наборы указваются в массиве select объекта _table;
     *  _table::select[selectName]
    */
    public static function fields($selectName='default',&$exists=false){
        $CR = baseSetup::CR();
        
        $out = '';
        if ($selectName==='')
            return $out;
        
        if (gettype($selectName)==='array')
            $fields = $selectName;
        else       
            $fields = static::$select[$selectName];
        
        foreach($fields as $i=>$field){
            
            $name = gettype($i)==='integer'?self::pref($field):self::pref($i);
            
            $need = true;
            if ($exists!==false){
                if (array_search($name,$exists)!==false)
                    $need = false;
                else
                    $exists[]=$name;
            }
            if ($need)
                $out.=($out!==''?','.$CR:'').(gettype($i)==='integer'?static::ALIAS.'.'.$field:self::macro($field)).' '.$name;

        }

        return $out;
    }
    /** добавляем префикс к имени поля */
    public static function pref($name){
        if (is_array($name)){
            $out = array();
            foreach($name as $k=>$v)
                $out[self::pref($k)] = $v;
            
            return $out;
        }else{
            $name = str_replace('::','',$name);
            
            if ((isset(static::$noAlias))&&(count(static::$noAlias)>0)&&(array_search($name,static::$noAlias)!==false))
                return $name;    
            else
                return static::ALIAS.'_'.$name;    
                
        }    
    }
    /** удаляем префикс из имени поля если надо 
     *  (если поле не входит в таблицу, то возвращает false)
     *  так же $name может быть хешем, тогда будет произведена работа с ключами
     *  поля которые не входят в таблицу из результата будут исключены
    */
    public static function unPref($name){
        if (is_array($name)){
            $out = array();
            foreach($name as $k=>$v){
                $n = self::unPref($k);
                if ($n!==false)
                    $out[$n] = $v;
            }    
            return $out;
            
        }else{
            if ((isset(static::$noAlias))&&(count(static::$noAlias)>0)&&(array_search($name,static::$noAlias)!==false)) 
                return $name;
            else{
                $need = false;    
                $up = array_keys(static::$types);
                foreach($up as $k=>$v){
                    $v = self::pref($v);
                    if ($v===$name) {
                        $need = true;
                        break;
                    }
                }
                return ($need ? str_replace(static::ALIAS.'_','',$name):false);
            }        
        }    
    }
    /** генерирует запрос к данной таблице с указанием условия $where и набора selectName 
     *  наборы указваются в массиве select объекта _table;
     *  _table::select[selectName]
     * 
     * where:string - может содержать макрокоманды
     * TABLE::FIELD - преобразует в представление поля FIELD таблицы TABLE в запросе 
     * ::FIELD - преобразует в представление поля FIELD текущей таблицы 
    */
    public static function select($where='',$selectName='default'){
        
        $CR = baseSetup::CR();
        
        return 'select'.$CR.self::fields($selectName).$CR.'from'.$CR.self::define().$CR.self::macro($where);
    }
    
    /** возвращает значение поля или sql */
    public static function value($name,$where,$default,$base=false,$coding='UTF8'){
        
    
        $CR = baseSetup::CR();
        if (strpos($name,'::')!==false)
            $name = str_replace('::','',$name);
        else{
            $name = self::unPref($name);
            if ($name===false) 
                return $default;
        }
        
        $q = 'select'.$CR.$name.$CR.'from'.$CR.static::NAME.$CR.'where'.$CR.self::macro($where);

        return $base===false?$q:base::value($q,$name,$default,$base,$coding);
        
    } 
    
    public static function val(&$data,$field/**/){
        if (func_num_args()>2)
            $data[self::pref($field)]=func_get_arg(2);
        else{
            if (isset($data[self::pref($field)]))
                return $data[self::pref($field)];
            else    
                return null;    
        }    
    }
    /** формирует запрос update/insert с учетом полей из data*/
    
    public static function _query($queryType,$data,$param=array()){

        $prm = array_merge(array(
            /** включает жесткую проврку значения по типу (если тип не string и значение пустое, то это поле исключается из запроса) */
            'excludeEmpty'=>true, 
        ),$param);
        $table = static::NAME;

        $types      = static::$types;
        $bTypes     = count($types)>0;
        
        $exclude    = array(static::INDEX);
        $bExclude   = count($exclude)>0;
    
        $include    = static::$update;
        $bInclude   = count($include)>0;
        
        
        
        
        if (baseSetup::$refactoring===true){
            $DCR = "\n\t";
            $CR = "\n";
        }else{
            $DCR = '';
            $CR = "";
        }
        

        $left = '';
        $right = '';
        $is_empty = true;
        
        foreach($data as $f=>$v){
        

            $field = $f;
            $value = $v;
            
            $need = false;
            foreach($include as $val){
                $real = self::pref($val);
                if ($field===$real){
                    $field = self::unPref($field);
                    $need = ($field!==false);
                    break;
                }
            }

            if (($need)&&($bExclude))
                $need = (array_search($field,$exclude)===false);
        
            if (($need)&&($bTypes)){
                if (isset($types[$field])!==false){
                    
                    $tp = $types[$field];
                    $value = base::typePerform($value,$tp);
                    
                    if ($prm['excludeEmpty']){
                        if ($types[$field]!=='string')
                            $need = strlen($value.'')>0;
                    }    
                    
                }
            }

            if ($need){
                
                $tab ='';
                if (baseSetup::$refactoring===true){
                    $sl = strlen($field)+2+($queryType!=='insert'?1:0);
                    $tab = ($sl<8?"\t\t":($sl<17?"\t":""));
                    
                }else 
                    $tab = '';
                    
                $left.=($left!==''?',':'').$DCR."`$field`".($queryType==='insert'?'':'='.$tab.$value);
                if ($queryType==='insert'){
                    if (baseSetup::$refactoring===true)
                        $left.=$tab.'/*'.$value.'*/';
                    
                    $right.=($right!==''?',':'').$value; 
                }
                $is_empty = false;
            }
        }
    
        if ($is_empty) 
            return false;
        else    
            return ($queryType==='insert'?'insert into ':'update ').$DCR."`$table` ".$CR.($queryType==='insert'?"(".$CR."$left".$CR.") ".$CR."values ($right) ":"set $left ").$CR;
    }
    /** заменяет макро команды в именах полей на поля с алиасами */
    private static function _macroField($_data){
        
        $data = array();
        foreach($_data as $k=>$v){
            if (strpos($k,'::')===0) $k = self::pref(str_replace('::','',$k));
            $data[$k]=$v;
        }
        return $data;
    }
    /** генерирует запрос на обнолвение, используя данные из data и условие $where 
     * where:string - может содержать макрокоманды
     * ::FIELD - преобразует в значение поля FIELD в data
     * :: - преобразует в значение поля INDEX в data 
    */
    public static function update($data,$where='',$base=null,$coding='UTF8'){
        $CR = baseSetup::CR();
        
        $data = self::_macroField($data);
        $q = self::_query('update',$data);
        
        if ($where!=='')
            $q.= $CR.'where'.$CR.self::macroData($where,$data);
        
        if (baseSetup::debug())
            base::_log('_table::update['.$q.']',__LINE__);
        
        if (is_null($base))
            return $q;
        else{

            if(!base::query($q,$base,$coding)){
                base::_log("Error [$q]",__LINE__);
                throw new Exception("Error [$q]");    
            }
            
            return true;
        }    
        
        
    }
    /** генерирует запрос на вставку, используя данные из data и условие $where 
     * where:string - может содержать макрокоманды
     * ::FIELD - преобразует в значение поля FIELD в data
     * :: - преобразует в значение поля INDEX в data 
    */
    public static function insert($data,$base=null,$coding='UTF8'){
        $CR = baseSetup::CR();
        
        $data = self::_macroField($data);
        $out = self::_query('insert',$data);
        
        if (is_null($base))
            return $out;
        else{

            if(!base::query($out,$base,$coding)){
                base::_log("Error [$out]",__LINE__);
                throw new Exception("Error [$out]");    
            }
            
            return true;
        }    
    }
    

    /** 
     * добавляет запись в таблицу используя механизм UUID
     * @return {int} идентификатор новой записи или false в случае ошибки
     */ 
    public static function insert_uuid($base=null){
        
        return base::insert_uuid(static::NAME,static::INDEX,$base);
        
    }
    /** генерирует определение текущей таблицы  в запросе */
    public static function define(){
        return static::NAME.' '.static::ALIAS;
    }    
    /**
     * генерирует join условие, для текущей и указанной таблицы
    */
    public static function join($table,$indexSelf=false,$indexTable=false){
        $ret = new _join();
        $slf = get_called_class();
        $ret->join($slf,$table,$indexSelf,$indexTable);
        return $ret;
    }
    
    /**
     * генерирует left outer условие, для текущей и указанной таблицы (все из текущей, из $table если есть)
    */
    public static function leftOuter($table,$indexSelf=false,$indexTable=false){
        $ret = new _join();
        $slf = get_called_class();
        $ret->leftOuter($slf,$table,$indexSelf,$indexTable);
        return $ret;
    }
    /** обработка макрокоманд в str в запросах update и insert */
    public static function macroData($str,$data){
        
        if (defined(get_called_class().'::INDEX'))
            $str = str_replace('{INDEX}',static::INDEX,$str);
        
        $re = '/::([a-zA-Z_0-9]*)/';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        $slf = get_called_class();
        
        for($i=0;$i<count($matches);$i++){
            $key    =   $matches[$i][0];
            $field  = trim($matches[$i][1]);
            
            
            $rep = false;
            if ($field==='')
                $field = $slf::INDEX;
            $alias = self::pref($field);
            
            if (isset($data[$alias])){
                
                $rep = base::typePerform($data[$alias],static::$types[$field]);
                $str = str_replace($key,$rep,$str);
                
            }
        }    
        return $str;
        
    }

    /** обработка макрокоманд в str в запросах select */
    public static function macro($str){
        if (defined(get_called_class().'::INDEX'))
            $str = str_replace('{INDEX}',static::INDEX,$str);
        
        $re = '/([a-zA-Z_0-9]*)::([\{a-zA-Z_0-9\}]*)/';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        for($i=0;$i<count($matches);$i++){
            
            $key =   $matches[$i][0];
            $table = trim($matches[$i][1]);
            $field = trim($matches[$i][2]);

    
            if ($table==='')
                $table = get_called_class();
            
            $rep = false;
            if (self::existClass($table)){
                $table = self::toClass($table);
                if ($field==='{INDEX}')
                    $field = $table::INDEX;
                    
                $rep = $field!==''?$table::field($field):$table::define();
            }

            if ($rep!==false)
                $str = str_replace($key,$rep,$str);
            
        }
        return $str;
        
    }
    
    /** попытка получить имя класса из name с учетом сокращенного имени, если нет такого класса,
     * генерируется исключение
     */ 
    public static function toClass($name){

        if (class_exists($name.'_TABLE'))
            return $name.'_TABLE';
        
        if (class_exists($name))
            return $name;

        throw new Exception("class [$name] or [$name"."_TABLE] not defined");
        
    }
    public static function existClass($name){
        
        if (class_exists($name))
            return true;

        if (class_exists($name.'_TABLE'))
            return true;
        return false;
        
        
    }
    /** 
     * создание копии строки 
     * 
     * $copyFromId - id| ds | assoc |array  - 
     * $change - false|["NAME"=>"Mike","NUMBER"=>10,..] массив данных для замены (скопированы будут все данные, а указанные в change буду заменены)
     * $exclude false|array("ID_NUM","NAME","PHONE")- список полей, которые не надо клонировать
     * 
     * @return 
     *      $copyFromId = id|assoc   =>  array ('res'=>0|1,'from'=>id,INDEX_NAME=>new_id);
     *      $copyFromId = ds|array   =>  array ('res'=>0|1,'list'=>array(..));
     *      error                    =>  array ('res'=>0);
     * 
    */
    public static function copyRow($copyFromId,$copyToId='insert',$change=false,$exclude=false,$base=null,$coding = 'UTF8'){
        
        $tp = TYPE::info($copyFromId);
        
        if ($tp==='object'){//ds
            
            $list   = array();
            $res    = 1;
            while(base::by($copyFromId,$row)){
                $r = self::copyRow($row[static::INDEX],'insert',$change,$exclude,$base,$coding);
                if ($r==0) $res = 0;
                $list[] = $r;
            };
            
            return array('res'=>$res,'list'=>$list);
            

        }elseif ($tp==='array'){
            
            $list   = array();
            $res    = 1;
            for($i=0;$i<count($copyFromId);$i++){
                $r = self::copyRow($copyFromId[$i],'insert',$change,$exclude,$base,$coding);
                if ($r==0) $res = 0;
                $list[] = $r;
            }

            return array('res'=>$res,'list'=>$list);
            
        }elseif ($tp==='assoc'){
            return self::copyRow($copyFromId[static::INDEX],$copyToId,$change,$exclude,$base,$coding);
        }else
        try{
            if ($base==='null')
                throw new Exception("base===null");

            if ($copyToId==='insert')
                $copyToId = self::insert_uuid($base);

            if ($copyToId===false)
                throw new Exception("insert_uuid");
                
            $q = 'select * from '.static::NAME.' where '.static::INDEX.'='.$copyFromId;
            $row = base::row($q,$base,$coding);
            if ($row===false)
                throw new Exception($q);
    
            if ($exclude===false) 
                $exclude = array();
                
            $exclude[]=static::INDEX;
            $exclude[]='UUID';
            
            $fields = '';
            foreach(static::$types as $field=>$type){
                if (isset($row[$field])){
                    if (($exclude===false)||(array_search($field,$exclude)===false)){
                        if (isset($change[$field]))
                            $row[$field] =  $change[$field];
                        $fields.=($fields!==''?',':'').$field.'='.base::typePerform($row[$field],$type);
                    }
                }    
            };
            
            $q = 'update '.static::NAME.' set '.$fields.' where '.static::INDEX.'='.$copyToId;
            
            if (!base::query($q,$base,$coding))
                throw new Exception($q);

            return array('res'=>1,'from'=>$copyFromId,static::INDEX=>$copyToId);
            
        }catch(Exception $e){
            _LOGF($e->getMessage(),'Exception',__FILE__,__LINE__);
            return array('res'=>0,'msg'=>$e->getMessage(),'from'=>$copyFromId);
        }
        
    }
    
    
};
class base{
    static private $_base = array();
    static private $_codings = array();
    public static function connect($server,$user,$pass,$base_name,$base,$die = true){

        if (isset(self::$_base[$base]))
            return true;
        
        $db = new mysqli($server,$user,$pass,$base_name);

        if ($db->connect_errno){
            $msg = "can`t connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error;
            
            if ($die){
                echo $msg;
                exit;
            }
            self::_log($msg);
            return false;
        }
        
        $_db = new _db();
        $_db->db = $db;
        
        self::$_base[$base]=$_db;
        
        return true;
        
    }
    
    public static function disconnect($base){

        if(isset(self::$_base[$base])){
            
            unset($base);
        }   
        
    }
    /** 
     * set or return charset
     * 
     * example set default charset
     * base::charSet('UTF-8','mybase');
     * 
     * example return default charset
     * $v = base::charSet(null,'mybase');
     *
     * example: story/restory codings
     * base::charSet('story','mybase');
     * base::charSet('UTF-8','mybase');
     * ...
     * base::charSet('restory','mybase');
     * 
    */
    public static function charSet($coding=null,$base=null){
            /** убираем путаницу с UTF-8 и utf8 */ 
            if (!is_null($coding)){
                $coding = strtolower($coding);
                if ($coding==='utf-8')
                    $coding = 'utf8';
            }    
            
            $_base = self::getbase($base);
            if ($_base===false) 
                return false;
                
            if (is_null($coding)){
                return $_base->charset;
            }else{
                if ($coding === '') 
                    return false;
                    
                if ($coding === 'story'){
                    
                    if (!isset(self::$_codings[$base]))
                        self::$_codings[$base]=[]; 
                    
                    self::$_codings[$base][]=$_base->db->get_charset()->charset;

                }else if ($coding === 'restory'){
                    $_base->db->set_charset(array_pop(self::$_codings[$base]));
                }else if (!$_base->db->set_charset($coding)) {
                    self::_log('error set charSet = '.$coding,__LINE__);
                    return false;
                }else
                    $_base->charset = $coding;
            
                return true;
            }    
        /*
        $db = self::db($base);
        
        if (!$db) return false;

        if (!$db->set_charset($coding)) {
            self::_log('error set charSet = '.$coding,__LINE__);
            return false;
        };
        
        return true;
        */
        
    }
    

    public static function debug_info($base=null){
            
    }

    private static function getbase($base){
        if (count(self::$_base)===0) return false;
        
        $keys = array_keys(self::$_base);
        if (is_null($base)) $base = $keys[0]; 

        return  isset(self::$_base[$base])?self::$_base[$base]:false;
    }
    
    private static function db($base){
        $base  = self::getbase($base);
        return  $base===false?false:$base->db;
        
    }
    private static function dbE($base){
        $result = self::db($base);
        if (!$result)
            throw new \Exception(" not exists basename = '$base'");
        return $result;
    }
    
    private static function _storyError($base){
        $_base = self::getbase($base);
        if ($_base===false) 
            return;
        else{    
            $err = $_base->db->error;
            if ($err!==''){
                if ($_base->error!==$err){
                    $_base->error = $err;
                    $_base->errors[]=$err;
                }
            }
        }
    }
    
    public static function error($base){
        $_base = self::getbase($base);
        if ($_base)
            return $_base->error;
        else
            return "not exists base='$base'";
    }
    public static function query($sql,$base=null,$coding=null){
        $db = self::db($base);
        if (!$db) return false;
        
        if (!is_null($coding)){
            $story  =   self::charSet(null,$base);
            self::charSet($coding,$base);
            self::_storyError($base);
        }    
        

        $res =  $db->query($sql);
        self::_storyError($base);
        
        if (!is_null($coding)){
            self::charSet($story,$base);
            self::_storyError($base);
        }    
            

        return $res;

    }
    
    private static function doThrow($sql,$base,$error_msg=''){
        throw new \Exception($error_msg.($error_msg!==''?' ':'').self::error($base).' in "'.$sql.'"');
    }
    /** выполняет запрс, в случае ошибки вызывает исключение с описанием ошибки */
    public static function queryE($sql,$base=null,$coding=null,$error_msg=''){

        if (!self::query($sql,$base,$coding))
            self::doThrow($sql,$base,$error_msg);
    }


    public static function assign($ds){
        return ($ds!==false);    
    }
    /**
     * @return  false - если ошибка
     * @return object - если запрос выполнен
     */
    public static function ds($sql,$base=null,$coding=null){

        $db = self::db($base);
        if (!$db)
            return false;

        if (!is_null($coding)){
            $story  =   self::charSet(null,$base);
            self::charSet($coding,$base);
            self::_storyError($base);
        }
        
        
        $ds = $db->query($sql);
        self::_storyError($base);
        
        if (!is_null($coding)){
            self::charSet($story,$base);
            self::_storyError($base);
        }    
            
        if (!$ds)
            return false;
        else{
            $ds->data_seek(0);
            return $ds;    
        }                    
    }
    public static function dsE($sql,$base=null,$coding=null,$error_msg=''){
        $ds = self::ds($sql,$base,$coding);
        if (!$ds)
            self::doThrow($sql,$base,$error_msg);
        return $ds;
    }
    
    public static function isEmpty($ds){
        return ( ($ds===false) || ($ds->num_rows===0) );
    }
    /**
     * Возвращает кол-во записей запроса sql или в ds
     * если задать countFieldName, то будет искать соответсвтвующее поле и выдаст его значение
     * можно задать countFieldName как число, тогда это будет номер необходимого поля
     */
    public static function count($sqlOrDs,$base=null,$countFieldName=''){
        
        $ds = gettype($sqlOrDs)==='string'?self::ds($sqlOrDs,$base,null):$sqlOrDs;
        if (!$ds)
            throw new Exception('Error in ds');
        $type = gettype($countFieldName);

        if (($countFieldName!='')||($type==='integer')){
            $fields = self::fields($ds);

            if ($type === 'integer'){
                $row = self::row($ds);
                return intval($row[$fields[$countFieldName]]);
            }else{
                $fields = self::fields($ds);
                $countFieldName = strtoupper(trim($countFieldName));
                foreach($fields as $name){
                    if (
                        (strtoupper(trim($name)) === $countFieldName) 
                        || 
                        (strpos(strtoupper(trim($name)),$countFieldName.'(')===0)
                    ){
                        $row = self::row($ds);
                        return intval($row[$name]);
                    }
                }
            }   
        }
        
        return $ds->num_rows;        
    }

    /** список таблиц */
    public static function tables($base=null){
        $res = array();
        $q = 'SHOW TABLES';
        $ds = self::ds($q,$base);
        if ($ds){
            
            while(self::by($ds,$row))
                foreach($row as $field => $table)
                    array_push($res,$table);    
        }
            
        return $res;    

    }
    public static function haveField($field,$tableName,$base=null){
        $list = self::fieldsInfo($tableName,true,$base);
        return (array_search($field,$list)!==false);
    }    
    /** возвращает либо список имен полей, либо массив ('Field','Type')*/
    public static function fieldsInfo($tableName,$short=true,$base=null){
        $out = array();
        $q = 'SHOW COLUMNS FROM `'.$tableName.'`';
        $ds = self::ds($q,$base);
        if ($ds){
            while(self::by($ds,$row)){
                
                if ($short)
                    array_push($out,$row['Field']);
                else{
                    $out[]=$row;
                    /*
                    array_push($out,array(
                        'Field'=>$row['Field'],
                        'Type'=>$row['Type'],
                        'Key'=>$row['Key'],
                        'Extra'=>$row['Extra']
                    ));
                    */
                    
                }
                
            }
        }else
            _LOG("Error [$q]",__FILE__,__LINE__);
            
        return $out;    
        
    }
    
    public static function fields($ds,$short_info=true){
        $ff = $ds->fetch_fields();
        if ($short_info){
            $out = array();
            for($i=0;$i<count($ff);$i++)
                array_push($out,$ff[$i]->name);
            
            return $out;
            
        }else{
            
            for($i=0;$i<count($ff);$i++)
                $ff[$i]->stype = self::fieldTypeToStr($ff[$i]->type);
                
            return $ff;
        }    
            
    }
    /* from:
        http://php.net/manual/ru/mysqli-result.fetch-fields.php
    */
    public static function map_field_type_to_bind_type($field_type){
        switch ($field_type)
        {
        case MYSQLI_TYPE_DECIMAL:
        case MYSQLI_TYPE_NEWDECIMAL:
        case MYSQLI_TYPE_FLOAT:
        case MYSQLI_TYPE_DOUBLE:
            return 'd';
    
        case MYSQLI_TYPE_BIT:
        case MYSQLI_TYPE_TINY:
        case MYSQLI_TYPE_SHORT:
        case MYSQLI_TYPE_LONG:
        case MYSQLI_TYPE_LONGLONG:
        case MYSQLI_TYPE_INT24:
        case MYSQLI_TYPE_YEAR:
        case MYSQLI_TYPE_ENUM:
            return 'i';
    
        case MYSQLI_TYPE_TIMESTAMP:
        case MYSQLI_TYPE_DATE:
        case MYSQLI_TYPE_TIME:
        case MYSQLI_TYPE_DATETIME:
        case MYSQLI_TYPE_NEWDATE:
        case MYSQLI_TYPE_INTERVAL:
        case MYSQLI_TYPE_SET:
        case MYSQLI_TYPE_VAR_STRING:
        case MYSQLI_TYPE_STRING:
        case MYSQLI_TYPE_CHAR:
        case MYSQLI_TYPE_GEOMETRY:
            return 's';
    
        case MYSQLI_TYPE_TINY_BLOB:
        case MYSQLI_TYPE_MEDIUM_BLOB:
        case MYSQLI_TYPE_LONG_BLOB:
        case MYSQLI_TYPE_BLOB:
            return 'b';
    
        default:
            trigger_error("unknown type: $field_type");
            return 's';
        };
    }
    
    public static function fieldTypeToStr($field_type){
        
        switch ($field_type){
        
            case MYSQLI_TYPE_DECIMAL:
            case MYSQLI_TYPE_NEWDECIMAL:
            case MYSQLI_TYPE_FLOAT:
            case MYSQLI_TYPE_DOUBLE:
                return 'float';
    
            case MYSQLI_TYPE_BIT:
            case MYSQLI_TYPE_TINY:
            case MYSQLI_TYPE_SHORT:
            case MYSQLI_TYPE_LONG:
            case MYSQLI_TYPE_LONGLONG:
            case MYSQLI_TYPE_INT24:
            case MYSQLI_TYPE_YEAR:
            case MYSQLI_TYPE_ENUM:
                return 'int';
        
            case MYSQLI_TYPE_TIMESTAMP:
            case MYSQLI_TYPE_DATE:
            case MYSQLI_TYPE_TIME:
            case MYSQLI_TYPE_DATETIME:
            case MYSQLI_TYPE_NEWDATE:
            case MYSQLI_TYPE_INTERVAL:
                return 'date';
                
            case MYSQLI_TYPE_SET:
            case MYSQLI_TYPE_VAR_STRING:
            case MYSQLI_TYPE_STRING:
            case MYSQLI_TYPE_CHAR:
            case MYSQLI_TYPE_GEOMETRY:
                return 'string';
        
            case MYSQLI_TYPE_TINY_BLOB:
            case MYSQLI_TYPE_MEDIUM_BLOB:
            case MYSQLI_TYPE_LONG_BLOB:
            case MYSQLI_TYPE_BLOB:
                return 'blob';
                
            default:
                return 'uncknown';
        };
    }
    
    public static function first($ds){
        $ds->data_seek(0);
    }
    
    public static function row($sqlOrDs,$base=null,$coding=null){
        
        $ds = gettype($sqlOrDs)==='string'?self::ds($sqlOrDs,$base,$coding):$sqlOrDs;
        if ($ds)        
            return $ds->fetch_assoc();
        else
            return false;
    }
    public static function rowE($sqlOrDs,$base=null,$coding=null,$error_msg=''){
        
        $ds = gettype($sqlOrDs)==='string'?self::dsE($sqlOrDs,$base,$coding,$error_msg):$sqlOrDs;
        if (!$ds)
            self::doThrow($sqlOrDs,$base,$error_msg);
        return $ds->fetch_assoc();
    }
    
    public static function rows($sqlOrDs,$base=null,$coding=null){

        $ds = gettype($sqlOrDs)==='string'?self::ds($sqlOrDs,$base,$coding):$sqlOrDs;

        if ($ds){
            $out = array();
            while(self::by($ds,$row))
                array_push($out,$row);

            return $out;
        }
        
        return array();    
    
    }
    public static function rowsE($sqlOrDs,$base=null,$coding=null,$error_msg=''){

        $ds = gettype($sqlOrDs)==='string'?self::dsE($sqlOrDs,$base,$coding,$error_msg):$sqlOrDs;

        if ($ds){
            $out = array();
            while(self::by($ds,$row))
                array_push($out,$row);

            return $out;
        }else
            self::doThrow($sqlOrDs,$base,$error_msg);

    }
    
    public static function by($ds,&$row){
        /*
            $ds = base::ds('...');
            while(base::by($ds)){
                
            }
        */
        $row = $ds->fetch_assoc();
        return ($row!==NULL);
    }

    public static function loop($sqlOrDs,$func,$base=null){
        /*
            base::loop('select * ...',function($row){
                echo $row['ID'];
                // use return true for stop!!!

            })
        */
        if (is_null($func)) return false;
        
        $ds = gettype($sqlOrDs)==='string'?self::ds($sqlOrDs,$base):$sqlOrDs;
        
        if (self::assign($ds)){
           while(self::by($ds,$row)){
                if ($func($row))
                    break;
           }
           return true;
        }
            
        return false;
    }

    public static function asTable($sqlOrDs,$base=null){
        $c = '';
        $ds = gettype($sqlOrDs)==='string'?self::ds($sqlOrDs,$base):$sqlOrDs;
        
        $fields = self::fields($ds);
        
        $c.='<tr>';
        for($i=0;$i<count($fields);$i++){
            $c.='<td>'.$fields[$i].'</td>';
        }
        $c.='</tr>';
        
        
        if (self::isEmpty($ds)){
            $c.='<tr><td colspan="'.(count($fields)).'">empty</td></tr>';
            
        }else{    
            $row = array();
            while(self::by($ds,$row)){
                $_c='';
                foreach($row as $name=>$mean)
                    $_c.='<td>'.$mean.'</td>';

                $c.='<tr>'.$_c.'</tr>';
            }
        }
        return '<table border=1 cellpadding=0 cellspacing=0>'.$c.'</table>';
    }


    public static function val($sql,$default='',$base=null,$coding=null){
        return self::value($sql,'',$default,$base,$coding); 
    }

    public static function valE($sql,$default='',$base=null,$coding=null,$error_msg=''){
        return self::valueE($sql,'',$default,$base,$coding,$error_msg); 
    }

    public static function value($sql,$field='',$default='',$base=null,$coding=null){
        
        $ds = self::ds($sql,$base,$coding);
        
        if (!$ds) 
            return $default;

        $fields = self::fields($ds);
        
        if ($field === '')
            $field = $fields[0];
        else if (array_search($field,$fields)===false) return $default;
        
        if (self::isEmpty($ds))
            return $default;
        else{    
            $row = self::row($ds);
            return $row[$field];
        };    
        
    }    
    public static function valueE($sql,$field='',$default='',$base=null,$coding=null,$error_msg=''){
        
        $ds = self::dsE($sql,$base,$coding,$error_msg);
        
        $fields = self::fields($ds);
        
        if ($field === '')
            $field = $fields[0];
        else if (array_search($field,$fields)===false)
            self::doThrow($sql,$base,$error_msg." field ='$field' not exists");
            
        
        if (self::isEmpty($ds))
            return $default;
        else{    
            $row = self::rowE($ds,null,null,$error_msg);
            return $row[$field];
        };    
        
    }    
    
    public static function startTransaction($base=null){
        $b = self::getbase($base);
        if (!$b) return false;
        
        if ($b->transaction==0)
            $b->db->autocommit(false);    
        $b->transaction+=1;
        return true;
    }
    
    public static function commit($base=null){
        $b = self::getbase($base);
        if (!$b) return false;
        
        $b->transaction-=1;
        
        if ($b->transaction==0){

            $b->db->commit();
            return true;
        }
        
        if ($b->transaction<0){ 
            self::_log('transaction overflow loop...');
        }
        
        return false;
    }
    
    public static function rollback($base=null){
        $b = self::getbase($base);
        if (!$b) return false;
        
        $b->transaction-=1;
        
        if ($b->transaction==0){ 
            $b->db->rollback();
            return true;
        }
        
        if ($b->transaction<0){ 
            self::_log('transaction overflow loop...');
        }
        
        return false;
        
    }
    
    private static function _uuidProxy(){
        $chrLeft  = 97; //a
        $chrRight  = 102;//f
        $chr0  = 48;
        $chr9  = 57;

        $is_num = (rand(0,10)<7?true:false);

        if ($is_num)
            $code = rand($chr0,$chr9);
        else
            $code = rand($chrLeft,$chrRight);

        return chr($code);
    }
        
    private static function _uuid($count=32){
        $uuid = '';
        for($i=0;$i<$count;$i++)
            $uuid.=self::_uuidProxy();
        return $uuid;
    }
        
    public static function uuid($count=32){
        return self::_uuid($count);
    }
    
    public static function insert_uuid($table,$index,$base,$fieldUUID='UUID',$countUUID=32){
        $uuid = self::uuid($countUUID);

        $q='insert into `'.$table.'` set `'.$fieldUUID."` = '".$uuid."'";
        
        if (!self::query($q,$base)){
            self::_log("Error [$q]",__LINE__);
            return false;
        }    

        $q = 'select `'.$index.'` from `'.$table.'` where `'.$fieldUUID."`='".$uuid."'";

        return self::value($q,$index,false,$base);
        
    }
    public static function insert_uuidE($table,$index,$base,$fieldUUID='UUID',$countUUID=32,$error_msg=''){
        
        $uuid = self::uuid($countUUID);

        $q='insert into `'.$table.'` set `'.$fieldUUID."` = '".$uuid."'";
        self::queryE($q,$base,null,$error_msg);

        $q = 'select `'.$index.'` from `'.$table.'` where `'.$fieldUUID."`='".$uuid."'";

        return self::valueE($q,$index,false,$base,null,$error_msg);
        
    }


    /** преобоазует значение к представлеию в SQL запросе в зависимости от его типа */
    public static function typePerform($value,$type){
        
        
        if (($type==='string')||($type==='date')){
            return '"'.self::esc($value).'"';
        }
        return $value;
        
    }    
    /** генерация текста запроса по входным данным 
    * @param {string} typeQuery insert|update
    * @param {string} table - имя таблицы
    * @param {array} param  = array(
    *   types=>array,           - array('NAME'=>'string',..)
    *   include=>array|string,  = array('')
    *   exclude=>array|string
    *   rename=>array,
    *   refactoring = true - вывод в удобном для анализа виде
    *   pref=>array|string|string;string (префексы перед именем поля, перед формированием запроса он удаляется, преобразуя поле 
    *                 в соотвествующее в таблице
    *   
    *   ex: pref = "tab";
    *       "tab_NAME" - > "NAME"
    *   
    * )
    *    
    * )
    * @return string|bool    вернет либо запрос, либо false если ни одного поля не было добавлено в запрос
    */
    
    public static function dataToSQL($queryType,$table,$data,$param=array()){
        
    
        $types      = isset($param['types'])?$param['types']:array();
        $bTypes     = count($types)>0;
        
        $exclude    = isset($param['exclude'])?$param['exclude']:array();
        if (gettype($exclude)==='string') $exclude = explode(';',str_replace(',',';',$exclude));
        $bExclude   = count($exclude)>0;
    
        $include    = isset($param['include'])?$param['include']:array();
        if (gettype($include)==='string') $include = explode(';',str_replace(',',';',$include));
        $bInclude   = count($include)>0;
    
        $rename     =  isset($param['rename'])?$param['rename']:array();
        $bRename    = count($rename)>0;


        $pref     =  isset($param['alias'])?$param['alias']:array();
        if (is_string($pref))
            $pref=array($pref);
        $bPref    = count($pref)>0;
            
        
          
        if (@$param['refactoring']===true){
            $DCR = "\n\t";
            $CR = "\n";
        }else{
            $DCR = '';
            $CR = "";
        }
        

        $left = '';
        $right = '';
        $is_empty = true;
        
        foreach($data as $f=>$v){
        
            $need = true;
            $field = $f;
            $value = $v;
            
            if (($need)&&($bPref)){
                for($i=0;$i<count($pref);$i++){
                    if (strpos($field,$pref[$i].'_')===0){
                        $field = str_replace($pref[$i].'_','',$field);
                        break;
                    }
                }
            } 
            

            if (($need)&&($bRename)){
                if (isset($rename[$field]))
                    $field = $rename[$field];
            } 
            
        
            if ($bInclude){
                $need = (array_search($field,$include)!==false);
            }    
        
            if (($need)&&($bExclude))
                $need = (array_search($field,$exclude)===false);
        
            if (($need)&&($bTypes)){
                if (isset($types[$field])!==false){
                    
                    $tp = $types[$field];
                    $value = self::typePerform($value,$tp);

                }
            }
        
        
        
            if ($need){
                
                $tab ='';
                if (@$param['refactoring']===true){
                    $sl = strlen($field)+2+($queryType!=='insert'?1:0);
                    $tab = ($sl<8?"\t\t":($sl<17?"\t":""));
                    
                }else 
                    $tab = '';
                    
                $left.=($left!==''?',':'').$DCR."`$field`".($queryType==='insert'?'':'='.$tab.$value);
                if ($queryType==='insert'){
                    if (@$param['refactoring']===true)
                        $left.=$tab.'/*'.$value.'*/';
                    
                    $right.=($right!==''?',':'').$value; 
                }
                $is_empty = false;
            }
        }
    
        if ($is_empty) 
            return false;
        else    
            return ($queryType==='insert'?'insert into ':'update ').$DCR."`$table` ".$CR.($queryType==='insert'?"(".$CR."$left".$CR.") ".$CR."values ($right) ":"set $left ").$CR;
    }
    /** преобразуем данные из $param в список полей для формирования запроса sql */
    public static function fieldsToSQL($param){


        $exclude    = isset($param['exclude'])?$param['exclude']:array();
        if (gettype($exclude)==='string') $exclude = explode(';',str_replace(',',';',$exclude));
        $bExclude   = count($exclude)>0;
    
        $include    = isset($param['include'])?$param['include']:array();
        if (gettype($include)==='string') $include = explode(';',str_replace(',',';',$include));
        $bInclude   = count($include)>0;
    
        $pref     =  isset($param['alias'])?$param['alias']:array();
        $bPref    = count($pref)>0;
            
        $types    = isset($param['types'])?$param['types']:array();
        $bTypes   = count($types)>0;
        
        if ($param['refactoring']===true){
            $DCR = "\n\t";
            $CR = "\n";
        }else{
            $DCR = '';
            $CR = "";
        }
        $fields = array();
        if ($bTypes){
            $_f = array_keys($types);
          //_LOG('['.print_r($include,true).']',__FILE__,__LINE__);
    
            for($i=0;$i<count($_f);$i++){
                $need = true;
                $field = $_f[$i];
                //_LOG("$field",__FILE__,__LINE__);
    
                if ($bInclude)
                    $need = (array_search($field,$include)!==false);
                    
                //_LOG("$field:[$need]",__FILE__,__LINE__);
                
                if (($need)&&($bExclude))
                    $need = (array_search($field,$exclude)===false);

                if ($need)
                    $fields[]=$field;
            }        
        }else if ($bInclude){
            $_f = $include;
            for($i=0;$i<count($_f);$i++){
                $need = true;
                $field = $_f[$i];
                
                if ($bExclude)
                    $need = (array_search($field,$exclude)===false);

                if ($need)
                    $fields[]=$field;
            }        

        }else
            return '';
        //_LOG('['.print_r($fields,true).']',__FILE__,__LINE__);
      
        $res = '';
        
        for($i=0;$i<count($fields);$i++)
            $res.=($res===''?$CR:','.$CR).$pref.'.'.$fields[$i].' '.$pref.'_'.$fields[$i];
        return $res.' '.$CR;
    }
        
    /** генератор запроса select 
    */
    public static function select($tabs,$join=null,$where=''){
        $CR = baseSetup::CR();
        $out = '';
        $exists = array();
        
        foreach($tabs as $i=>$select){
            
            $fields = (gettype($i)==='integer'?$select::fields('default',$exists):$i::fields($select,$exists));
            /*--------------------------*/

            $out.=($out!==''?','.$CR:'').$fields;
            
        }
        $out='select distinct'.$CR.$out;
        if (is_object($join))
            $out.=$CR.'from'.$CR.$join->asString();
        else if (is_string($join))
            $out.=$CR.'from'.$CR._table::macro($join);
        
        if (strlen($where)>0)
            $out.=$CR._table::macro($where);
        
        return $out;
    }
    public static function real_escape($string,$base=null){
        $db = self::db($base);
        if (!$db) 
            return $string;
        else    
            return $db->real_escape_string($string);
    }
    
    public static function esc($string,$base=null){
        //$db = self::db($base);
        //if (!$db) return $string;
        //return $db->real_escape_string($string);
        if (baseSetup::removeEscape()){
            $from = array('"');
            $to   = array('\"');
            return str_replace($from,$to,$string);
        }else
            return $string;
        
    }
    
    /** генерирует тестовыеданные, для запроса select 
     * используется для проверки возможности обработки данных update и insert
    */
    public static function testSelectData($tabs){
        
        $data = array();
        
        foreach($tabs as $i=>$select){

            if (gettype($i)==='integer'){
                $table = $select;
                $get = 'default';
            }else{
                $table = $i;
                $get = $select;
            };
            
            $fields = $table::$select[$get];
            $types = $table::$types;
            foreach($fields as $k=>$field){
                $mean = STR::random(10);
                
                if (isset($types[$field])){
                    
                    $t = $types[$field];
                    if ($t==='int')
                        $mean = rand(0,10000);
                    elseif (($t==='float')||($t==='double'))
                        $mean = rand(10,100)/rand(5,7);
                    elseif ($t=='string')    
                        $mean = STR::random(10);
                    elseif ($t=='date')    
                        $mean = rand(1,9).'/'.rand(1,12).'/'.rand(2000,2018);
                        
                }
                
                $data[$table::pref($field)] = $mean;
            }
            
        }
        return $data;
        
    }
    
    /** принудительная типизация 
     *  используется константа COMMON_TYPE_STRING из модуля common.php
     *  Ex1:
     * 
     *  $ds = base::ds('select *,CAPTION CC from K_TOVAR where ID_K_TOVAR = 23674');
     *  $rows = base::rows($ds);
     *  $fields = base::fields($ds,false);
     * 
     *  $rows = base::forcedTyping($rows,$ds); // будут помечены все строковые поля
     *  or
     *  $rows = base::forcedTyping($rows,$fields); // будут помечены все строковые поля
     *  or
     *  $rows = base::forcedTyping($rows,'ID_K_TOVAR,CC'); // будут помечены только ID_K_TOVAR и CC
     *  or 
     *  $rows = base::forcedTyping($rows); // все будут помечены как строки
     *  
     * 
    */
    public static function forcedTyping($rowOrRows,$fieldsOrDs=array()){
        
         $aType = TYPE::info($fieldsOrDs);
         
         if ($aType==='object'){
            $fieldsOrDs = self::fields($fieldsOrDs,false);
            $aType = 'array';
         }    
        
         if ($aType==='array'){
            $fields = array();
            for($i=0;$i<count($fieldsOrDs);$i++){
                $fType = TYPE::info($fieldsOrDs[$i]);
                if ($fType==='string')
                    $fields[]=$fieldsOrDs[$i];
                else{
                    $o = $fieldsOrDs[$i];
                    if ($o->stype ==='string'){
                        $fields[]=$o->name;
                    }
                }    
            };

         }elseif($aType==='string'){
             $fields = trim($fieldsOrDs);

             if ($fields[0]!=='[')
                $fields='['.$fields.']';
                
             $fields = ARR::extend($fields);
         }
         
         $type = TYPE::info($rowOrRows);
         
         
         return ARR::forcedTyping($rowOrRows,$fields);
         
    
         
    }
    
    public static function _log($msg,$line=0){
        
        $msg = gettype($msg)==='object'?print_r($msg,true):$msg;
        
        if (function_exists('_LOG'))
            _LOG($msg,__FILE__,$line);
        else
            error_log('['.date("d-M-Y").' '.date("H:i:s").' '.__FILE__.$line.']'."\n".$msg);
    }
    
};
?>