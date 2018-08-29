<?php


define('LOGIN','1');
define('PASS','1');
define('TOKEN','qwjhejqwhedjkhqwkjh');

class Autorize{
    
    /** 
     * действия при авторизации по token и (или) dev 
     * 
     */
     
    public function fromToken($token,$dev){
        _LOG("token = [".$token."] dev=[".(is_null($dev)?'null':$dev).']',__FILE__,__LINE__);
    
        
        if ($token === TOKEN)
            return array('res'=>1,'token'=>TOKEN);
        else    
            return array('res'=>0);        
    }

    /** действия при авторизации по пользовательским данным ( к примеру логин, пароль) и (или) dev 
     * 
     */
    public function fromData($data,$dev){
        
        if (($data['login']==LOGIN)&&($data['pass']==PASS))
            return array('res'=>1,'token'=>TOKEN);
        else
            return array('res'=>0);
    }

    /** проверка актуальности сессии */
    public function checkToken($token,$dev){

        return array('res'=>(TOKEN==$token?1:0));
        
    }
    /** проверка данных на возможность создания сессии */
    public function checkData($data,$dev){
        
        if (($data['login']==LOGIN)&&($data['pass']==PASS))
            return array('res'=>1);
        else
            return array('res'=>0);
    }

    /** действия по остановке сессии */
    public function disable($token,$dev){
        
        return array('res'=>1);
    }
    
};



?>