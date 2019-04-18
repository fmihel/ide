<?php

$simpleCachePath = dirname(__FILE__).'/';
include_once $simpleCachePath.'simpleCacheUtils.php';

/**
 * Простой кэш для приложения. Сам класс 
 * является провайдером для выполнения методов доступа к данным кеша. 
 * Для физичекой реализации необходимо подключить соотвествующий драйвер (см __construct).
 * 
 * @example test/test.php
 * 
 */
class SimpleCache {
    private $driver;
    private $_enable;
    private $preload = array();
    /**
     * Создание экземпляра
     * @param string $classDriver строка с именем класса драйвера (см. iSimpleCacheDriver и папку drivers)
     * @param mixed[] $o массив параметров передаваемых в драйвер (в зависимости от драйвера правметры могут быть различны)
     */
    function __construct($classDriver,$o=array()){
        $this->_enable = true;
        $this->driver = new $classDriver($o);
    }
    function __destruct()
    {
        //$this->clear();
    }
    /**
     * Получени данных из кеша
     * @param string $key уникальный идентификатор кеша, для формирования кеша из параметров кешируемой ф-ции используй toKey(...)
     * @param mixed[] $o необязательный список параметров, который переопределит параметры по умолчанию в драйвере
     * @return mixed Если кеш существует то возвращается его содержимое, если нет - false
     */
    public function get($key,$o=array()){
        if (!$this->_enable)
            return false;
        
        if (isset($this->preload[$key]))
            return $this->preload[$key];

        if ($data = $this->driver->get($key,$o))
            return unserialize($data);
        else    
            return false;
    }

    /**
     * Помещение данных в кеш
     * @param string  $key уникальный идентификатор кеша, для формирования кеша из параметров кешируемой ф-ции используй toKey(...)
     * @param mixed   $data кешируемые данные
     * @param mixed[] $o необязательный список параметров, который переопределит параметры по умолчанию в драйвере
     * @return bool 
     */
    public function set($key,$data,$o=array()){
        if (!$this->_enable)
            return true;
         $this->preload[$key] = $data;
         return $this->driver->set($key,serialize($data),$o);
    }
    /** 
     * Выборочная очистка кеша
     * @param string  $key уникальный идентификатор кеша, для формирования кеша из параметров кешируемой ф-ции используй toKey(...)
     * если параметр не установить, то будут удалены все неактуальные скешированные данные (см timeout)
     * @param mixed[] $o необязательный список параметров, который переопределит параметры по умолчанию в драйвере
     * 
    */
    public function clear($key='',$o=array()){
        if (!$this->_enable)
            return true;
        
        if ($key==='')
            $this->preload=array();
        else 
            unset($this->preload[$key]);    
            
        return $this->driver->clear($key,$o);
    }
    /**
     * Преобразование массива аргументов в идентификатор кеша.
     * @param mixed[] $args - массив аргуметов
     * @example $cache->toKey(__CLASS__,__FUNCTION__,func_get_args());
     * @return string строка не более 32 символов соотвествующая ключу
     */
    public function toKey(/**...args */){
        return SimpleCacheUtils::toKey(func_get_args());
    }

    /** 
     * Полная очистка кеша. Стирает все данные
     * @param mixed[] $o необязательный список параметров, который переопределит параметры по умолчанию в драйвере
     * 
    */
    public function reset($o=array()){
        if (!$this->_enable)
            return true;        
        $this->preload = array();    
        return $this->driver->reset($o);        
    }
    
    /**
     * Устанвавливает или возвращает признак что кеширование включено
     * @example enable() - возвращает признак
     * @example enable(true) - разрешает кеширование
     * @return bool признак того что кеширование включено
     */
    public function enable(/**bool */){
        if (func_num_args()>0){
            $args = func_get_args();
            $this->_enable = $args[0]?true:false;
        }
        return $this->_enable;
    }
}

?>