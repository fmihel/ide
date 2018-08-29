<?php
require_once UNIT('utils','common.php');

$IS_FS_FILE = (APP::get_file($_SERVER['SCRIPT_NAME'])=='fs.php');

/*проверка на существование конфигурации от пользователя, она лежит в той же папке где ide*/
if ($IS_FS_FILE) /*заходим из обработчика для Explorer*/
    $path_to_users_data = '../../users/';
else
    $path_to_users_data = '../users/';


if (!is_dir($path_to_users_data)){
    
    if ($IS_FS_FILE) 
        $path_to_users_data = '../users/';
    else
        $path_to_users_data = 'users/';
}
    


$user_data_default=array('theme'=>'light');


class USERS {
    
    private $usersPath;
    public $data;
    private $current;
    function __construct($usersPath){
        global $admin_data;    
        $this->usersPath = $usersPath;
        $this->data = array();
        $this->current = -1;

    }
    function add_user($email,$file){
        $file = $this->usersPath.$file;
        require_once $file;
        array_push($this->data,array('email'=>$email,'file'=>$file,'user_data'=>$user_data));
    }
    function autorize_from_share(){
        global $REQUEST;
        if (isset($REQUEST->SHARE['session']))
            return $this->autorize($REQUEST->SHARE['session']);
        
        return false;
    }
    function current_to_json(){
        if ($this->current!==-1){
            $copy = $this->data[$this->current]['user_data'];
            //unset($copy['login']);
            //unset($copy['password']);
            $copy['password']='xxxx';
            return ARR::to_json($copy);
        }    
        return '';
    }
    function current_data(){
        if ($this->current!==-1){
            return $this->data[$this->current];
        }
        return array();
    }
    function can_autorize($login,$password){
        $current = -1;
        for($i=0;$i<count($this->data);$i++){
            if (($this->data[$i]['user_data']['login']==$login) && ($this->data[$i]['user_data']['password']==$password)){
                $current = $i;
                break;
            }
        }
        return ($current!==-1);        
    }
    function autorize(/*$login,$password || $session*/){
        global $REQUEST;
        
        $this->current = -1;
        
        if (func_num_args()==1) {
            $session = func_get_arg(0);
            for($i=0;$i<count($this->data);$i++){
                if ((isset($this->data[$i]['user_data']['session']))&&($this->data[$i]['user_data']['session']==$session)){
                    
                    $this->current = $i;
                    break;
                }
            }
            
            
        }else  if (func_num_args()==2) {  
            $login = func_get_arg(0);
            $password = func_get_arg(1);
            
            for($i=0;$i<count($this->data);$i++){
                if (($this->data[$i]['user_data']['login']==$login) && ($this->data[$i]['user_data']['password']==$password)){
                    $this->current = $i;
                    break;
                }
            }
            
            if ($this->current!==-1){
                $session = STR::random(16);
                $this->put('session',$session);
                $this->save_current();
            }
        }
            
        
        return ($this->current!=-1);
    }
    function is_autorize(){
        return ($this->current!==-1);
    }
    function get($key,$default=''){
        if ($this->current!==-1){
            $user_data = $this->data[$this->current]['user_data'];
            return (isset($user_data[$key])?$user_data[$key]:$default);
        }else{
            return 'no autorize';
        }        
    }
    function exist($key){
        if ($this->current!==-1){
            $user_data = $this->data[$this->current]['user_data'];
            return isset($user_data[$key]);
        }else
            return false;
    }
    function put($key,$val){
        if ($this->current!==-1){
            if ($key==='login'){
                for($i=0;$i<count($this->data);$i++){
                    if ($i!==$this->current)
                        if ($this->data[$i]['user_data']['login']==$val) return false;
                }
            }
            
            $this->data[$this->current]['user_data'][$key] = $val;
            
            return true;
        };
        return false;
    }
    
    function save_current(){
        if ($this->current!==-1){
            
            $file = $this->data[$this->current]['file'];
            if ($file=='admin_file') return true;
            $user_data = $this->data[$this->current]['user_data'];
            $begin = true;
            $code = '';
            
            foreach($user_data as $k=>$v)
                $code.=($code==''?'':",\n")."'".$k."'=>".ARR::to_php_code($v);    
                
            $code = "<?php\n\$user_data=array(\n$code\n);\n?>";
            
            return (file_put_contents($file,$code)!==false);
        }
    }
    
    function debug_info(){
        $res = '';
        for($i=0;$i<count($this->data);$i++){
            $res.='data['.$i.']={<br>';
            $res.='&nbsp;file = '.$this->data[$i]['file'].'<br>';
            $res.='&nbsp;email = '.$this->data[$i]['email'].'<br>';
            foreach($this->data[$i]['user_data'] as $k=>$v){
                $res.="&nbsp;$k = $v<br>";
            }
            $res.='}<br>';
        }
        return $res;
    }

    
}    


/*
if (APP::get_file($_SERVER[SCRIPT_NAME])=='fs.php')
    $path_to_users_data='../'.$path_to_users_data;
*/

$USERS = new USERS($path_to_users_data);

require_once $path_to_users_data.'users_list.php';


?>
