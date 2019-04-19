<?php

$simpleCacheBaseDriverPath = dirname(__FILE__).'/';
include_once $simpleCacheBaseDriverPath.'iSimpleCacheDriver.php';
include_once $simpleCacheBaseDriverPath.'../simpleCacheUtils.php';

class SimpleCacheBaseDriver implements iSimpleCacheDriver{
    private $param = array();
    
    public function __construct($o=array()){
        $this->param = SimpleCacheUtils::extend(array(
            'base'=>'deco',
            'table'=>'buffer',
            'timeout'=>600,
            'fields'=>array(
                'id'=>'ID_BUFFER',
                'key'=>'KEY_BUFFER',
                'buffer'=>'BUFFER',
                'date'=>'LAST_UPDATE',
                'time'=>'timeout_sec',
                'group'=>'group',
                'notes'=>'notes'
            ),
            
            'group'=>'common',
            'notes'=>'',
        ),$o);
    }

    public function get($key,$o=array()){
        $a = SimpleCacheUtils::extend($this->param,$o);
        $fields = $a['fields'];
        $q = 'select *,CURRENT_TIMESTAMP-`'.$fields['date'].'` `delta` from '.$a['table'].' where '.$fields['key']." = '".$key."'";
        if ($row = base::row($q,$a['base'])){
            if ( $row['delta'] < $row[$fields['time']] )
                return $row[$fields['buffer']];        
        };
        return false;
    }
    public function set($key,$data,$o=array()){
        $a = SimpleCacheUtils::extend($this->param,$o);
        $fields = $a['fields'];
        $data = base::real_escape($data,$this->param['base']);
        try{
        
            $q = 'insert into '.$a['table'];
            $q.=' (';
            $q.= ' `'.$fields['key'].'`';
            $q.= ',`'.$fields['buffer'].'`';
            $q.= ',`'.$fields['date'].'`';
            $q.= ',`'.$fields['time'].'`';
            $q.= ',`'.$fields['group'].'`';
            $q.= ',`'.$fields['notes'].'`';
            $q.=' )';
            $q.=' values';
            $q.=' (';
            $q.= " '".$key."'";
            $q.= ",'".$data."'";
            $q.= ",CURRENT_TIMESTAMP";
            $q.= ",".$a['timeout'];
            $q.= ",'".$a['group']."'";
            $q.= ",'".$a['notes']."'";

            $q.=' )';
            $q.=' on duplicate key update';
            $q.=' `'.$fields['key']."`='".$key."'";
            $q.=',`'.$fields['buffer']."`='".$data."'";
            $q.=',`'.$fields['date']."`= CURRENT_TIMESTAMP";
            $q.=',`'.$fields['time']."`= ".$a['timeout'];
            $q.=',`'.$fields['group']."`= '".$a['group']."'";
            $q.=',`'.$fields['notes']."`= '".$a['notes']."'";

            if (!base::query($q,$this->param['base']))
                return false;
            return true;

        }catch(Exception $e){
            return false;            
        }

    }
    /** 
     * Очистка буффера
     * Если первый параметр не указан или '' или array(), то будут удалены неактуальные данные кеша
     * Если $key - строка, то интерпретируется как значение ключа для очистки
     * Если $key - массив =  array('key'=>'ключь') или array('group'=>'значение группы для очистки') или array('where'=>'занчение блока WHERE в запросе MySQL')
     * @param mixed $key - строка или массив с указанием правила очистки
     * 
    */
    public function clear($key = '',$o=array()){
        $a = SimpleCacheUtils::extend($this->param,$o);
        $fields = $a['fields'];
        if (($key==='')||($key===array()))
            $q = 'delete from '.$a['table'].' where CURRENT_TIMESTAMP-`'.$fields['date'].'` >  `'.$fields['time'].'`';
        else{
            if (gettype($key)==='string')
                $q = 'delete from '.$a['table'].' where `'.$fields['key']."`='".$key."'";
            else{
                $key = SimpleCacheUtils::extend(array(
                    'key'=>false,
                    'group'=>false,
                    'where'=>false
                ),$key);
                
                if($key['key']!==false)
                    $q = 'delete from '.$a['table'].' where `'.$fields['key']."`='".$key['key']."'";    
                elseif($key['group']!==false){
                    $q = 'delete from '.$a['table'].' where `'.$fields['group']."`='".$key['group']."'";
                }elseif($key['where']!==false){
                    $where = $key['where'];
                    // замена алиасов имен полей 
                    // т.е можно писать    clear(array('where'=>':group LIKE "price%"'))
                    $from = array();;
                    $to = array();
                    foreach($fields as $k=>$v){
                        $from[] = ':'.$k;
                        $to[]   = '`'.$v.'`';
                    }
                    $where = str_replace($from,$to,$where);
                    // -------------------------

                    $q = 'delete from '.$a['table'].' where '.$where;    
                }else
                    return false;
                
            }    
        }    
        
        return base::query($q,$a['base']);
    }
    public function reset($o=array()){

        $a = SimpleCacheUtils::extend($this->param,$o);
        $q = 'delete from '.$a['table'].' where 1>0';
        return base::query($q,$a['base']);
        
    }
}

?>