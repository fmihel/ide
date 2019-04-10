<?php

class Buffer2 {
    public $driver;
    function __construct($classDriver,$o=array()){
        
        $this->driver = new $classDriver($o);
    }
    function __destruct()
    {
        //$this->clear();
    }
    public function get($key,$o=array()){
        if ($data = $this->driver->get($key,$o)){
            return unserialize($data);
        }
        return false;
    }
    public function set($key,$data,$o=array()){
        
        return $this->driver->set($key,serialize($data),$o);
    }
    public function clear($key='',$o=array()){
        return $this->driver->clear($key,$o);
    }
}

?>