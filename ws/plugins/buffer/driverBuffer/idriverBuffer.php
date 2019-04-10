<?php

interface IDriverBuffer{
    public function __construct($o=array());
    public function get($key,$o=array());
    public function set($key,$data,$o=array());
    public function clear($key,$o=array());
}

?>