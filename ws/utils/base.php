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


$_base=array();

class _db{
    public $db;
    public $transaction;
    public $charset;
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
}

class base{
    
    public static function connect($server,$user,$pass,$base_name,$base,$die = true){
        global $_base;
        
        if (isset($_base[$base]))
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
        
        $_base[$base]=$_db;
        
        return true;
        
    }
    
    public static function disconnect($base){
        global $_base;
        
        if(isset($_base[$base])){
            
            unset($base);
        }   
        
    }
    /** 
     * set or return charset
     * example set default charset
     * base::charSet('UTF-8','mybase');
     * example return default charset
     * $v = base::charSet(null,'mybase');
    */
    public static function charSet($coding=null,$base=null){
            
            $_base = self::getbase($base);
            if ($_base===false) 
                return false;
                
            if (is_null($coding)){
                return $_base->charset;
            }else{
                if ($coding === '') 
                    return false;
                if (!$_base->db->set_charset($coding)) {
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
        global $_base;
        if (count($_base)===0) return false;
        
        $keys = array_keys($_base);
        if (is_null($base)) $base = $keys[0]; 

        return  isset($_base[$base])?$_base[$base]:false;
    }
    
    private static function db($base){
        $base  = self::getbase($base);
        return  $base===false?false:$base->db;
        
    }

    public static function query($sql,$base=null,$coding=null){
        $db = self::db($base);
        if (!$db) return false;
        
        if (!is_null($coding)){
            $story  =   self::charSet(null,$base);
            self::charSet($coding,$base);
        }    
            
        $res =  $db->query($sql);
        
        if (!is_null($coding))
            self::charSet($story,$base);
            

        return $res;

    }


    public static function assign($ds){
        return ($ds!==false);    
    }
    
    public static function ds($sql,$base=null,$coding=null){

        $db = self::db($base);
        
        if (!is_null($coding)){
            $story  =   self::charSet(null,$base);
            self::charSet($coding,$base);
        }
        
        $ds = $db->query($sql);
        
        if (!is_null($coding))
            self::charSet($story,$base);
            
        if (!$ds)
            return false;
        else{
            $ds->data_seek(0);
            return $ds;    
        }                    
    }
    
    public static function isEmpty($ds){
        return ( ($ds===false) || ($ds->num_rows===0) );
    }
    /** список таблиц */
    public static function tables($base=null){
        $res = array();
        $q = 'SHOW TABLES';
        $ds = base::ds($q,$base);
        if ($ds){
            
            while(base::by($ds,$row))
                foreach($row as $field => $table)
                    array_push($res,$table);    
        }
            
        return $res;    

    }
    /** возвращает либо список имен полей, либо массив ('Field','Type')*/
    public static function fieldsInfo($tableName,$short=true,$base=null){
        $out = array();
        $q = 'SHOW COLUMNS FROM `'.$tableName.'`';
        $ds = base::ds($q,$base);
        if ($ds){
            while(base::by($ds,$row)){
                
                if ($short)
                    array_push($out,$row['Field']);
                else{
                    
                    array_push($out,array(
                        'Field'=>$row['Field'],
                        'Type'=>$row['Type'],
                        'Key'=>$row['Key'],
                        'Extra'=>$row['Extra']
                    ));
                    
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
            
        }else
            return $ff;
            
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
    
    public static function rows($sqlOrDs,$base=null,$coding=null){

        $ds = gettype($sqlOrDs)==='string'?self::ds($sqlOrDs,$base,$coding):$sqlOrDs;

        if ($ds){
            $out = array();
            while(base::by($ds,$row)){
                array_push($out,$row);
            }
            return $out;
        }
        
        return array();    
    
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

        return base::value($q,$index,false,$base);
        
    }
    /** генерация екста запроса по вхдным данным 
    * @param {string} typeQuery insert|update
    * @param {string} table - имя таблицы
    * @param {array} param  = array(
    *   types=>array,           - array('NAME'=>'string',..)
    *   include=>array|string,  = array('')
    *   exclude=>array|string
    *   rename=>array,
    *   refactoring = true - вывод в удобном для анализа виде
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
        $bRename   = count($rename)>0;
        
        
        if ($param['refactoring']===true){
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

            if (($need)&&($bRename)){
                if (isset($rename[$field]))
                    $field = $rename[$field];
            } 

        
            if ($bInclude)
                $need = (array_search($field,$include)!==false);
        
            if (($need)&&($bExclude))
                $need = (array_search($field,$exclude)===false);
        
            if (($need)&&($bTypes)){
                if (isset($types[$field])!==false){
                    
                    $tp = $types[$field];
                    if (($tp === 'string')||($tp === 'date')){
                        
                        $value = '"'.$value.'"';
                    }
                
                }
            }
        
        
        
            if ($need){
                
                $tab ='';
                if ($param['refactoring']===true){
                    $sl = strlen($field)+2+($queryType!=='insert'?1:0);
                    $tab = ($sl<8?"\t\t":($sl<17?"\t":""));
                    
                }else 
                    $tab = '';
                    
                $left.=($left!==''?',':'').$DCR."`$field`".($queryType==='insert'?'':'='.$tab.$value);
                if ($queryType==='insert'){
                    if ($param['refactoring']===true)
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
    /** принудительная типизация 
     * используется константа COMMON_TYPE_STRING из модуля common.php
    */
    public static function forcedTyping($dsOrRowOrRows,$fields=array()){
    }    
    
    private static function _log($msg,$line){
        
        if (function_exists('_LOG'))
            _LOG($msg,__FILE__,$line);
        else
            error_log('['.date("d-M-Y").' '.date("H:i:s").' '.__FILE__.$line.']'."\n".$msg);
    }
    
}

?>