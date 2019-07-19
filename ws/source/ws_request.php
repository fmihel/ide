<?php
if(!isset($Application)) 
    require_once '../utils/application.php';


//----------------------------------------------------------------------
/*
    $REQUEST  - объект содержит информацию от сервера 
    TYPE - тип запроса
    ID - идентификатор отправителя
    DATA - данные отправителя
    SHARE - общие для сервера и броузера данные
    COM - данные полученные при выполнении GETDATA
    
    
*/
//----------------------------------------------------------------------
define('R_URL',0);
define('R_AJAX',1);
define('R_MODULE',2);
define('R_UPLOAD_CODE',3);

//----------------------------------------------------------------------
$REQUEST = new WS_REQUEST();
//----------------------------------------------------------------------
//----------------------------------------------------------------------


class WS_REQUEST 
{
//S: Содержит информацию о текущем запросе к серверу
/*D:В системе создается один экземпляр данного класса. 
[code]    
    $REQUEST:jREQUEST();
[/code]
Данный объект содержит информацию о текущем запросе к jSCENE.


*/
    public $TYPE;//Тип запроса (R_URL,R_AJAX,R_MODULE,R_UPLOAD_CODE)
    public $ID;// Идентификатор компонента отсылающего запрос
    public $MODULE;//Имя модуля к которому обращен запрос R_MODULE (ID = MODULE)
    
    public $VALUE;//Значение передаваемое в запросе
    public $SHARE;//Массив разделяемых данных

    public $UPLOAD_BLOCK;

    function __construct(){
        $this->TYPE = R_URL;
        $this->ID = -1;
        $this->VALUE = array();
        $this->MODULE = '';
        $this->UPLOAD_BLOCK = '';
        
        $this->SHARE = array();

        $this->_init_module();
        $this->_init_ajax();
        $this->_init_upload_code();
        
        $this->MODULE = $this->ID;
    }
    public function debug_info($cr='<br>'){
        $res='WS_REQUEST{'.$cr;
        $res.='TYPE = '.$this->TYPE.''.$cr;
        $res.='ID ='.$this->ID.''.$cr;
        $res.='VALUE ='.ARR::to_json($this->VALUE).''.$cr;
        $res.='SHARE ='.ARR::to_json($this->SHARE).''.$cr;
        $res.='MODULE ='.$this->MODULE.''.$cr;
        $res.='UPLOAD_BLOCK ='.$this->UPLOAD_BLOCK.''.$cr;

        $res.='}'.$cr;
        return $res;
    }
    private function _init_module(){
        global $Application;
        if (isset($_REQUEST['module']))
        {
            $this->TYPE = R_MODULE;
            $mod = json_decode($_REQUEST['module'],true);
            
            $this->ID = $mod['MODULE'];
            $this->MODULE = $mod['MODULE'];
            
            if (isset($mod['VALUE']))
                $this->VALUE = $mod['VALUE'];
                
            if (isset($mod['SHARE']))
                $this->SHARE = $mod['SHARE'];
        };
    }
    private function _init_upload_code(){
        global $Application;

        if ((isset($_REQUEST['UPLOAD_CODE']))&&($_REQUEST['UPLOAD_CODE']==true))
        {
            $this->TYPE = R_UPLOAD_CODE;

            if (isset($_REQUEST['UPLOAD_BLOCK']))
                $this->UPLOAD_BLOCK = $_REQUEST['UPLOAD_BLOCK'];

            if (isset($_REQUEST['ID']))
                $this->ID = $_REQUEST['ID'];

            if (isset($_REQUEST['VALUE']))
                $this->VALUE = $_REQUEST['VALUE'];

            if (isset($_REQUEST['SHARE']))
                $this->SHARE = $_REQUEST['SHARE'];
        };
    }
    private function _init_ajax(){
        global $AJAX;
        if ($this->IsAjax())
        {
            $this->TYPE = R_AJAX;

            if (isset($_REQUEST['ID']))
                $this->ID = $_REQUEST['ID'];

            if (isset($_REQUEST['VALUE']))
                $this->VALUE = $_REQUEST['VALUE'];

            if (isset($_REQUEST['SHARE']))
                $this->SHARE = $_REQUEST['SHARE'];
        };
    }
    
    public function IsAjax(){
        //SHORT: Признак AJAX запроса
        //DOC: Возвращает true если запрос через AJAX
        return (isset($_REQUEST['AJAX'])?$_REQUEST['AJAX']:false);
    } 
    

    public function SHARE($key,$default = ''){
        //SHORT: Возвращает даннае SHARE
        //DOC: Возвращает разделяемые данные из массива SHARE, если ключа $key не существует, возвращает $default
        if (isset($this->SHARE[$key]))
            return $this->SHARE[$key];
        else
            return $default;
    }
    
    public function RESPONSE($DATA,$ID = null){
        //S:Упаковка ответа клиенту по AJAX
        /*D:Метод применяется для создания(форматирования) ответа к клиенту (броузеру).Используется в методе <nobr>jSCENE->AJAX(&$response)</nobr>. 
[code]
    global $REQUEST;
    if ($REQUEST->ID === 'DESIGN_TIME')
    {
        foreach($REQUEST->VALUE as $id=>$data)
            jCOMPONENT_RES::SET($id,$data);
        jCOMPONENT_RES::SAVE();
        $response = RESPONSE("1");
        return true;
    }else
        return false;
[/code]
*/
        //P:$DATA - строка или массив
        //P:$ID - идентификатор компонента от имени которого поступит ответ

        if (is_array($DATA))
            $DATA = ARR::to_json($DATA);
        else
            $DATA = '"'.COMMON::pre_json($DATA).'"';

        if ($ID === null)
            $ID = $this->ID;
            
        return '[{"ID":"'.$ID.'","DATA":'.$DATA.'},{"SHARE":'.ARR::to_json($this->SHARE).'}]';
    }
    /**
     * альтернативный доступ к переменной VALUE,
     * WS_REQUEST::GET('x',0);
     * если не указать второй параметр, то GET создаст Exception
     */
    public static function GET($name,$default){
        global $REQUEST;

        if (isset($REQUEST->VALUE[$name]))
            return $REQUEST->VALUE[$name];
        else{    
            // если не определен параметр default то формируем исключение
            if (func_num_args()<2)
                throw new Exception("WS_REQUEST::GET $name  is not exists, and default is not defined");
            return $default;
        }    
    }
    
    /**
     * Проверка соотвествия ID
     *  if (WS_REQUEST::IS("load_info")){
     *      try{
     *          $len = WS_REQUEST::GET("len");
     * 
     *      }catch(Exception $e){
     *          error_log($e->getMessage());      
     *      }
     *      return true;
     *  }
     *  return false;
     */
    public static function IS($ID){
        global $REQUEST;
        return ($REQUEST->ID == $ID);
    }
    
    public static function OK($data = null){
        return ($data!==null)?['res'=>1,'data'=>$data]:['res'=>1];
    }
    
    public static function ERROR($msg,$res=0){
        if (gettype($msg)=='string')
            return array('res'=>$res,'msg'=>$msg);
        else
            return array('res'=>$res,$msg->getMessage());
    }


};
//----------------------------------------------------------------------

if ($Application->is_main(__FILE__))
{
    $msg = 'utils module: REQUEST generate is ok!!';
    echo $msg;
    
    echo $REQUEST->debug_info();
};

?>