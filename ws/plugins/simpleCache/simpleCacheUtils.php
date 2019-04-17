<?php


class SimpleCacheUtils{
    public static function is_assoc($array){
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
    public static function extend($a = array(), $b = array()){
        
        if ((is_array($a)) && (is_array($b))) {
            
            if (($a===[])||(self::is_assoc($a))) {
                $res = $b;    
                foreach ($a as $k => $v) {
                    if (!isset($b[$k])) {
                        $res[$k] = $v;
                    } else {
                        if ((is_array($v)) && (is_array($b[$k]))) {
                            $res[$k] = self::extends($v, $b[$k]);
                        } else 
                            $res[$k] = $b[$k];
                    }
                }
                return $res;
            }
            
        };
        return $a;
    }
    /**
     * преобразуем массив списка параметров в ключь для записи в буффер
     * Ex:
     * class B{
     *      function a(){
     *          $key = SimpleCacheUtils::toKey(func_get_args(),__CLASS__,__FUNCTION__);
     *      }
     * }
     */
    public static function toKey(/**$...args*/){
        $ret = serialize(func_get_args());
        if (strlen($ret)>32)
            $ret = md5($ret);

        return $ret;
    }
}
?>