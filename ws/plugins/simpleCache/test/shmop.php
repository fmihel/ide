<?php

function random_str($count){
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


function exists($id){
    $status = @shmop_open($id, "a", 0, 0);
    return $status;    
}
function free($id){
    if ($id){
        shmop_delete($id);
        shmop_close($id);
    }
}
function save_cache($data, $name, $timeout) {
    // delete cache
    $id=exists(get_cache_id($name));
    free($id);
    // get id for name of cache
    $id=@shmop_open(get_cache_id($name), "c", 0644, strlen(serialize($data)));
    
    // return int for data size or boolean false for fail
    if ($id) {
        set_timeout($name, $timeout);
        return shmop_write($id, serialize($data), 0);
    }
    else 
        return false;
}

function get_cache($name) {
    if (!check_timeout($name)) {
        $id=@shmop_open(get_cache_id($name), "a", 0, 0);

        if ($id) 
            $data=unserialize(shmop_read($id, 0, shmop_size($id)));
        else 
            return false;          // failed to load data

        if ($data) {                // array retrieved
            shmop_close($id);
            return $data;
        }
        else return false;          // failed to load data
    }
    else return false;              // data was expired
}

function get_cache_id($name) {
    // maintain list of caches here
    $id=array(  'test1' => 1,
                'test2' => 2
                );

    return $id[$name];
}

function set_timeout($name, $int) {
    $time_key = 100;
    $timeout=new DateTime(date('Y-m-d H:i:s'));
    date_add($timeout, date_interval_create_from_date_string("$int seconds"));
    $timeout=date_format($timeout, 'YmdHis');

    $id=@shmop_open($time_key, "a", 0, 0);
    if ($id) 
        $tl=unserialize(shmop_read($id, 0, shmop_size($id)));
    else 
        $tl=array();
    free($id);    

    $tl[$name] = $timeout;
    $id=@shmop_open($time_key, "c", 0644, strlen(serialize($tl)));
    shmop_write($id, serialize($tl), 0);
}

function check_timeout($name) {
    $now=new DateTime(date('Y-m-d H:i:s'));
    $now=date_format($now, 'YmdHis');

    $id=shmop_open(100, "a", 0, 0);
    if ($id) 
        $tl=unserialize(shmop_read($id, 0, shmop_size($id)));
    else 
        return true;

    shmop_close($id);

    $timeout=$tl[$name];
    return (intval($now)>intval($timeout));
}

class Shmop {

    public $id;
    private $res;

    public function __construct($id){
        $this->id = $id;
        $this->res = $this->exist();
        //if ($this->res)
        //    $this->res = shmop_open($this->id, "w", 0, 0);

    }
    public function __destruct(){
        $this->free();    
    }
    public function exist(){
        return @shmop_open($this->id,"a",0,0);
    }
    public function free(){
        if ($this->res){
            shmop_delete($this->res);
            shmop_close($this->res);
            $this->res = false;
        }
    }
    public function write($data){
        $sData = serialize($data);
        if(!$this->res){
            //$this->free();
            $this->res = @shmop_open($this->id,"c", 0644, strlen($sData));    
        }    
        return shmop_write($this->res, $sData, 0);
    }
    public function read($default = false){
        if ($this->res) 
            return unserialize(shmop_read($this->res, 0, shmop_size($this->res)));
        else
            return $default;    
    }
}

class Buffers{
    
    private $names;
    private $idName;
    public $buf;
    public $shmops;

    public function __construct(){
        $this->idName = 1;
        $this->names = new Shmop($this->idName);
        $names = $this->names->read(array());
        echo 'names:'.print_r($names,true).'<hr>';

        $this->buf = array();
        $this->shmops = array();
    }
    public function __destruct(){
        foreach($this->shmops as $shmop){
            $shmop->free();
        }    

        $this->names->free();
    }
    public function getId($name){
        $off = 100;
        $names = $this->names->read(array());
        $id = array_search($name,$names);
        if ($id !== false)
            return $id+$off;
        else{
            $id = count($names)+$off;
            $names[] = $name;
            $this->names->write($names);
            return $id;
        }    
    }
    public function exist($name){
        $names = $this->names->read(array());
        return (array_search($name,$names)!==false);
    }
    public function get($name){
        
        if (isset($this->buf[$name]))
            return $this->buf[$name];

        $id = $this->getId($name);
        if (!isset($this->shmops[$name])){
            $shmop = new Shmop($id);
            $this->shmops[$name] = $shmop;
        }else
            $shmop = $this->shmops[$name];   
         
        
        $this->buf[$name]   = $shmop->read();
        return $this->buf[$name];
        
    }
    public function set($name,$data){
        $this->buf[$name] = $data;
        $id = $this->getId($name);
        if (!isset($this->shmops[$name])){
            $shmop = new Shmop($id);
            $this->shmops[$name] = $shmop;
        }else
            $shmop = $this->shmops[$name];   
        
        $shmop->write($data);    
            
    }

}

class SHMOP2
{
    private $shmId;
    private $shmHeaderSize;
    private $shmHeaderOffset;
    private $shmBlockSize;
    private $shmMaxBlockSize;

    function __construct(string $pathname, string $flags, int $mode, int $size)
    {
        $this->shmHeaderSize = strlen($size);
        $this->shmHeaderOffset = $this->shmHeaderSize + 1;
        $this->shmMaxBlockSize = $size;
        $this->shmBlockSize = $size + $this->shmHeaderOffset + 1;

        $this->shmId = shmop_open(ftok($pathname, 's'), $flags, $mode, $this->shmBlockSize);

        if(!$this->shmId)
            throw new Exception('shmop_open error');
    }
    
    function __destruct()
    {
        if(!$this->shmId)
            return;

        $this->close();
    }
    
    public function read()
    {
        // Check SpinLock
        if(shmop_read($this->shmId, 0, 1) === "\0")
            return false;
        
        // Get Header
        $dataSize = (int)shmop_read($this->shmId, 1, $this->shmHeaderSize);

        $data = shmop_read($this->shmId, $this->shmHeaderOffset, $dataSize);
        // release spinlock
        shmop_write($this->shmId, "\0", 0);
        return $data;
    }
    
    public function write(string $data)
    {
        // Check SpinLock
        if(shmop_read($this->shmId, 0, 1) !== "\0")
            return false;

        $dataSize = strlen($data);

        if($dataSize < 1)
            throw new Exception('dataSize < 1');
        
        if($dataSize > $this->shmMaxBlockSize)
            throw new Exception('dataSize > shmMaxBlockSize: '. $this->shmMaxBlockSize);
        
        // pack very slow use kludge
        $dataSize .= "\0";

        // Write Header
        shmop_write($this->shmId, $dataSize, 1);
        // Write Data
        shmop_write($this->shmId, $data, $this->shmHeaderOffset);
        // Write spinlock
        shmop_write($this->shmId, "\1", 0);
        return true;
    }

    public function eof()
    {
        return (shmop_read($this->shmId, 0, 1) === "\2") ? true : false;
    }
    
    public function sendeof()
    {
        // Check SpinLock
        if(shmop_read($this->shmId, 0, 1) !== "\0")
            return false;

        shmop_write($this->shmId, "\2", 0);
        return true;
    }
    
    public function canWrite()
    {
        // Check SpinLock
        return (shmop_read($this->shmId, 0, 1) === "\0") ? true : false;
    }
    
    public function close()
    {
        return @shmop_close($this->shmId);
    }

    private function delete()
    {
        $del = @shmop_delete($this->shmId);

        if($del === false)
            return false;

        return @shmop_close($this->shmId);
    }
}

function out(...$a){
    $s = '';
    foreach($a as $v){
        $s.=$v.' ';
    }    
    echo   $s.'<hr>'; 
}



$blockSize = 10;
$data = "1234";
$shm = new SHMOP2(dirname(__FILE__).'/shmop.php','c',644,$blockSize);

//$shm->write($data);
$data = $shm->read();
//while(1){
//    
//    if(!$shm->canWrite())
//        continue;

//}

$shm->close();
out($data);
?>