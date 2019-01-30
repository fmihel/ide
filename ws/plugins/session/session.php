<?php

/** данный клас явлется интерфейсным и осуществляет передачу между модулем MOD_SESSION и 
 * пользовательским обработчиком Autorize
 * возможно использование следующих методов :
 * 
 * session::init(array()) - инициализация, и запуск авторизации по токену в SHARE (вызывается в самом начале)
 * 
 * session::$enable = bool - признак, что сессия авторизована
 * session::stop() - остановка сессии 
 * 
 * 
*/ 
    
class session{

    /** признак, прошла ли авторизация */
    static public $enable = false;

    /** ссылка на пользовательский класс обраюотчик */
    static public $autorize=false; // user class inherited from Autorize

    /** имя переменной содержащей токен */    
    static public $tokenName='token';

    /** тип авторизации (session только на сесию и только на одной вкладке, local не удаляется после закрытия) */    
    static public $sessionType='session';
    
    
    static private $token;
    static private $dev;
    
    static public $data = array();
    
    public static function init($param){
        if (isset($param['tokenName']))
            self::$tokenName = $param['tokenName'];
        if (isset($param['autorize']))
            self::$autorize = $param['autorize'];
        if (isset($param['sessionType']))
            self::$sessionType = $param['sessionType'];
        if (isset($param['start'])&&($param['start']))
            self::start();
                
    }
    /** запуск сессии */
    public static function start($data = false){
        global $REQUEST;
        $out = array('res'=>0);
        $share = false;

        if (!self::$autorize)
            return $out;
            
        // если данных на входе нет, то авторизация из данных SHARE
        if (!$data){
            
            if (($REQUEST)&&($REQUEST->SHARE)&&($REQUEST->SHARE['session'])){
                $share = $REQUEST->SHARE['session'];
                if (isset($share['token']))
                    $out = self::$autorize->fromToken($share['token'],$share['dev']);
            };
            
        }elseif (isset($data['data'])){
        
            $out = self::$autorize->fromData($data['data'],$data['dev']);
            
        }elseif (isset($data['token'])){
            
            $out = self::$autorize->fromToken($data['token'],isset($data['dev'])?$data['dev']:'');
        }
        self::$enable = $out['res']==1?true:false;
        if (self::$enable){
            self::$token = $out['token'];
            self::$dev   = !$data?$share['dev']:(isset($data['dev'])?$data['dev']:'');
            $REQUEST->SHARE['session']['enable']=1;            
        }else
            $REQUEST->SHARE['session']['enable']=0;            
        return $out; 
    }

    /** проверка сессии */
    public static function check($data){
        global $REQUEST;

        if (!self::$autorize) 
            return array('res'=>0);
                
        if (isset($data['token']))
            return self::$autorize->checkToken($data['token'],$data['dev']);
        else
            return self::$autorize->checkData($data['data'],$data['dev']);
    }


    /** остановка сессии */
    public static function stop($data=false){
        global $REQUEST;
        if (!$data){
            
            $token  = self::$token;
            $dev    = self::$dev;
        }else{
            $token  = $data['token'];
            $dev    = $data['dev'];
            
        }
        
        if (!self::$autorize)
            return array('res'=>0);
        $res = self::$autorize->disable($token,$dev);
        if ($res['res']==1)
            $REQUEST->SHARE['session']['enable']=0;
        return $res; 
        
    }
    /** генерирует уникальное значение */
    public static function generateToken($count=16){
        $result = '';
        for($i = 0;$i<$count;$i++){
            if (rand(1,10) > 6)        
                $result.=chr(rand(48,57));
            else
                $result.=chr(rand(65,90));
        };  
        
        return $result;
    }
    /** проверка является ли текущий запрос, запросом к модулю SESSION_MOD , в таком случае можно не запускать сессию автоматически*/
    public static function isSessionRequest(){
        global $REQUEST;
        $events = array('SESSION_DATA_CHECK','SESSION_STOP','SESSION_RELEVANCE','SESSION_AUTORIZE','SESSION_INIT');
        if (($REQUEST)&&(array_search($REQUEST->ID,$events)!==false))
            return true;
        else
            return false;
    }
    /** интерфейс для работы с переменной data */
    public static function data($name,$default = ''){
        return isset(self::$data[$name])?session::$data[$name]:$default;
    }
    
}

?>
    