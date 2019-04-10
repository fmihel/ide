<?php

$driverBaseUnitPath = dirname(__FILE__).'/';
require_once $driverBaseUnitPath.'idriverBuffer.php';
require_once $driverBaseUnitPath.'../bufferUtils.php';

class DriverBufferBase implements IDriverBuffer{
    private $param = array();
    
    public function __construct($o=array()){
        $this->param = BufferUtils::extend(array(
            'base'=>'deco',
            'table'=>'buffer',
            'timeout'=>600,
            'idField'=>'ID_BUFFER',
            'keyField'=>'KEY_BUFFER',
            'dataField'=>'BUFFER',
            'lastField'=>'LAST_UPDATE',
            'timeoutField'=>'timeout_sec',
        ),$o);
    }

    public function get($key,$o=array()){
        $a = BufferUtils::extend($this->param,$o);

        $q = 'select *,CURRENT_TIMESTAMP-`'.$a['lastField'].'` `delta` from '.$a['table'].' where '.$a['keyField']." = '".$key."'";
        if ($row = base::row($q,$a['base'])){
            if ( $row['delta'] < $row[$a['timeoutField']] )
                return $row[$a['dataField']];        
        };
        return false;
    }

    public function set($key,$data,$o=array()){
        $a = BufferUtils::extend($this->param,$o);

        try{
        
            $q = 'insert into '.$a['table'];
            $q.=' (';
            $q.= ' `'.$a['keyField'].'`';
            $q.= ',`'.$a['dataField'].'`';
            $q.= ',`'.$a['lastField'].'`';
            $q.= ',`'.$a['timeoutField'].'`';
            $q.=' )';
            $q.=' values';
            $q.=' (';
            $q.= " '".$key."'";
            $q.= ",'".$data."'";
            $q.= ",CURRENT_TIMESTAMP";
            $q.= ",".$a['timeout'];

            $q.=' )';
            $q.=' on duplicate key update';
            $q.=' `'.$a['keyField']."`='".$key."'";
            $q.=',`'.$a['dataField']."`='".$data."'";
            $q.=',`'.$a['lastField']."`= CURRENT_TIMESTAMP";
            $q.=',`'.$a['timeoutField']."`= ".$a['timeout'];

            if (!base::query($q,$this->param['base']))
                return false;
            return true;

        }catch(Exception $e){
            return false;            
        }

    }
    public function clear($key = '',$o=array()){
        $a = BufferUtils::extend($this->param,$o);
        if($key===''){
            $q = 'delete from '.$a['table'].' where CURRENT_TIMESTAMP-`'.$a['lastField'].'` >  `'.$a['timeoutField'].'`';
        }else{
            $q = 'delete from '.$a['table'].' where `'.$a['keyField']."`='".$key."'";
        }
        return base::query($q,$a['base']);
    }
}

?>