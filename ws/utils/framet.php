<?php
/**
 * шаблоинзатор для ws
 * формат
 * 
 * <ID[:TAG NAME] ["CLASS NAME"] [{STYLES}] [[ATTRIBUTES]] [|TEXT CONTENT|] [ID]>
 * 
 * 
 * Ex:
 * 
 * FRAMET('
 *      %itemClass = "item_class";
 *      %itemAlert = item_alert;
 *      <page
 *          <panel
 *              <menu
 *                  <item1 |download|>
 *                  <item2 |test| "%itemClass">
 *                  <item3 |print| "%itemClass"> 
 *                  <item3 |save| "%itemClass">
 *               menu>
 *           >
 *          <content
 *              <caption |enter pasword|>
 *              <field:input [type=text] >
 *              <btn:input [type=button] |Ok|>
 *          >
 *          <footer {height:32px}>
 *      page>
 * ',$own,'WIN');
 * 
 * 
 * Ф-ция FRAMET ( интерфейс для FRAMET::RENDER() ) возвращает ссылку на объект FRAME - первый созданный из шаблона объект
 * 
*/ 
if(!isset($Application)){
    require_once 'application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = false; 
    
    require_once UNIT('ws','ws.php');
    define('FRAMET_DEBUG','FRAMET_DEBUG');
};

function FRAMET($template,$own,$group='',$toAll = array('style'=>"position:absolute") ){
    return FRAMET::RENDER($template,$own,$group,$toAll);
};

class GJX{
    public static $enableBuffer = true;
    public static $ref = array();

    /** ----------------------------------------------------------
     * -arrange-    
     * -arrange()-
     * -arrange(direct:vert,stretch:2)-
     * -arrange([a,b,c],{direct:vert,stretch:2})-
    */
    public static function arrangeParam($str,$extend=false){
        
        try{
            if (($str==='')&&($extend===false)) return array('','');    
            self::_jqInit();
            $str = self::_jqLock($str);
            
            if ($str !== ''){
                if ($str[0]==='['){ //[a,b,c],{direct:vert,stretch:2}
                    $str = '['.$str.']';
                }else{ // {direct:vert,stretch:2}  || direct:vert,stretch:2
                    if ($str[0]!=='{')
                        $str = '{'.$str.'}';
                    $str = '[[],'.$str.']';    
                }    
            };    
            //_LOGF($str,'str',__FILE__,__LINE__);
            
    
            if ($extend===false)
                $params = ARR::extend('[[],{}]',$str);
            else
                $params = ARR::extend('[[],{'.self::_jqLock($extend).'}]',$str);
            
    
            $out = array();
            self::_jqFlip();
            for($i=0;$i<count($params);$i++)
                $out[] = self::_jqUnLock(ARR::to_json($params[$i]));
            
            return $out;
            
        }catch(Exception $e){
            _LOGF($e->getMessage(),'Exception',__FILE__,__LINE__);
        }
        return array('','');
    }

    public static function arrange($param,$enableBuffer = 'global'){
        $out = 'JX.arrange(';
        
        $enableBuffer = ($enableBuffer==='global'?self::$enableBuffer:($enableBuffer?true:false));
        if (self::isEmptyArr($param[0])){
            
            $code = '{$}.children()';
            
            if ($enableBuffer){
                $buffer = self::buffer($code);
                $out=$buffer['code'].$out.$buffer['var'];
            }else
                $out.=$code;    
        }else
            $out.=$param[0];
        
        if(!self::isEmptyArr($param[1]))
            $out.=','.$param[1];

        $out.=');';
        return $out;
    }
    /** ----------------------------------------------------------
     * -cling($to,{})-
    */
    public static function clingParam($str,$extend=false){
        
        try{
            if (($str==='')&&($extend===false)) return array('','');    
            self::_jqInit();
            $str = self::_jqLock($str);
            $str = '['.$str.']';            

    
            if ($extend===false)
                $params = ARR::extend('[{},{}]',$str);
            else
                $params = ARR::extend('[{},{'.self::_jqLock($extend).'}]',$str);

            if (!isset($params[1]['abs']))
                $params[1]['abs'] = true;
                
            $out = array();
            self::_jqFlip();

            $out[] = self::_jqUnLock($params[0]);
            $out[] = '{$}';
            $out[] = self::_jqUnLock(ARR::to_json($params[1]));
            
            //for($i=0;$i<count($params);$i++)
            //    $out[] = self::_jqUnLock(ARR::to_json($params[$i]));
            
            //array_unshift($out,'{$}');
            
            return $out;
            
        }catch(Exception $e){
            _LOGF($e->getMessage(),'Exception',__FILE__,__LINE__);
        }
        return array('','','');
    }

    public static function cling($param){

        $out = 'JX.cling(';
        $out.=$param[0].','.$param[1].','.$param[2];
        $out.=');';
        
        return $out;
    }


    /** ----------------------------------------------------------
     * -workplace-    
     * -workplace()-
    */
    public static function workplaceParam($str){
        return array('{$}','{$}.children().eq(0)');
    }

    public static function workplace($param,$enableBuffer = 'global'){
        $out = 'JX.workplace(';
        $enableBuffer = ($enableBuffer==='global'?self::$enableBuffer:($enableBuffer?true:false));
        
        $out.=$param[0];
        
        if ($enableBuffer){
            $buffer = self::buffer($param[1]);
            $out=$buffer['code'].$out.','.$buffer['var'];
        }else
            $out.=','.$param[1];    

        $out.=');';
        return $out;
    }
    
    /** ----------------------------------------------------------
     * -stretch-    
     * -stretch()-
     * -stretch(by:obj)-
     * -stretch({by:obj})-
    */
    public static function stretchParam($str){
        
        try{
            if ($str==='') return array('{$}','');    
            self::_jqInit();
            $str = self::_jqLock($str);
            
            if ($str[0]!=='{')
                $str = '{'.$str.'}';

            $param = ARR::extend('{}',$str);
            
            self::_jqFlip();
            return array('{$}',self::_jqUnLock(ARR::to_json($param)));

        }catch(Exception $e){
            _LOGF($e->getMessage(),'Exception',__FILE__,__LINE__);
        }
        return array('','');
    }

    public static function stretch($param,$enableBuffer = 'global'){
        $out = 'JX.stretch(';
        
        $out.=$param[0].(strlen($param[1])>0?','.$param[1]:'');

        $out.=');';
        return $out;
    }


    
    private static function buffer($code){
        $jqs = 'a'.STR::random(5);
        $out =  'if (Ar.'.$jqs.'===undefined) Ar.'.$jqs.'='.$code.';';
        
        return array('var'=>'Ar.'.$jqs,'code'=>$out);
    }
    
    private static function isEmptyArr($s){
        return  ($s==='')||($s==='{}')||($s==='[]');
    }
    
    private static function _jqInit(){
        self::$ref = array();
    }    
    private static function _jqLock($str){
        
        preg_match_all('/\{\$[^\s\{\}]*\}/m', $str, $res, PREG_SET_ORDER, 0);
        
        for($i=0;$i<count($res);$i++){
            $jq = $res[$i][0];
            if (!isset(self::$ref[$jq])){
                $ref = 'r'.STR::random(5);
                $str = str_replace($jq,$ref,$str);
                self::$ref[$jq] = $ref;
            }    
        }
        

        return $str;
    }
    
    /*
    private static function _setJQueryRef($arr){
        $type = TYPE::info($arr);
        
        if ($type==='array'){
            
            for($i=0;$i<count($arr);$i++){
                $v = $arr[$i];
                if (TYPE::info($v)==='string'){
                    if (isset(self::$ref[$v]))
                        $arr[$i] = self::$ref[$v];
                }else
                    $arr[$i] = self::_setJQueryRef($arr[$i]);
            }
        }elseif($type ==='assoc'){
            
            foreach($arr as $k=>$v){
                if (TYPE::info($v)==='string'){
                    if (isset(self::$ref[$v]))
                        $arr[$k] = self::$ref[$v];
                }else
                    $arr[$k] = self::_setJQueryRef($arr[$k]);
            }
        }
        return $arr;
    }
    */
    private static function _jqFlip(){
        self::$ref = array_flip(self::$ref);        
    }    
    private static function _jqUnLock($str){
        
        foreach(self::$ref as $k=>$v){
            $str = str_replace('"'.$k.'"',$v,$str);
            $str = str_replace($k,$v,$str);
        }    
        
        return $str;
    }
    
        
}


class FRAMET{
    //-------------------------------------------------------------------------------
    static private $vars = array();
    static private $struct = array();
    
    //-------------------------------------------------------------------------------
    static private $len  = 0;
    static private $over  = array('"'=>'"','{'=>'}','['=>']','|'=>'|','~'=>'~');
    static private $oname = array('"'=>'class','{'=>'style','['=>'attr','|'=>'value','~'=>'align');
    static private $space = array('"'=>' ','{'=>';','['=>';','|'=>' ','~'=>';');
    static private $blocks = array('"','{','[','<','>','|','~');
    static private $root = false;
    static private $jx_short = array('wp'=>'workplace','a'=>'arrange','lh'=>'lineHeight');
    static private $var_pref = '%';
    static private $ref = array();
    
    //-------------------------------------------------------------------------------
        
    public static function RENDER($template,$own,$group='',$toAll){
        
        // загружаем переменные ( переменные хранятся в упорядоченном массиве self::$vars = array("var_name"=>"mean",....)

        self::_vars($template);

        if (count(self::$vars)>0)
            $template = self::_varsToStr($template);
        
        //------------------------------------------------------------------
        $template = str_replace(array("`",'\|'),array('"','&#124;'),$template);
        $template = str_replace(array("\r\n", "\n", "\r","\t"),' ',$template);

        
        self::$len = strlen($template);
        
        self::$struct = self::_get_struct($template,$i);
        
    
        //------------------------------------------------------------------
        
        self::$root = false;
        
        
        if (defined('FRAMET_DEBUG')){
            
            return print_r(self::$struct,true);
                
        }else{
            self::_render(self::$struct,$own,$group,$toAll);

            return self::$root;
        }    
        //------------------------------------------------------------------
    }
    
    /** замена всех вхождений переменной в строке на соотв значение */
    private static function _varsToStr($str){
        foreach(self::$vars as $k=>$m)
            $str = str_replace(self::$var_pref.$k,$m,$str);    
        return $str;    
    }
    
    /** добавление или изменение переменной в массиве */
    private static function _eval($k,$m){
        // убираем лидрующие ковычки
        $m = trim($m);
        if (($m[0]==='"')||($m[0]==="'"))
            $m = substr($m,1);
        $len = strlen($m)-1;
        if (($m[$len]==='"')||($m[$len]==="'"))
            $m = substr($m,0,$len);
        //-----------------------------    
        $m = self::_varsToStr($m);
        self::$vars[$k] = $m;
    }    
    
    /** формирование массива переменных */
    private static function _vars(&$template){
        self::$vars=array();
        $have = strpos($template,self::$var_pref);

        if ($have!==false){
            $re = '/\\'.self::$var_pref.'([\S]+)\s*\=([\w"\';\s\-\+\*\/\$\%\.\!\@\#\?\,\:\&\^\(\)\[\]\{\}\<\>\~]+);\s*$/m';
            preg_match_all($re, $template, $matches, PREG_SET_ORDER, 0);            
            
            for($i=0;$i<count($matches);$i++){
                
                $all    =   $matches[$i][0];
                $k      =   $matches[$i][1];
                $m      =   $matches[$i][2];

                self::_eval($k,$m);
                krsort(self::$vars,SORT_STRING);
                
                $template       = STR::str_replace_once($all,'',$template);
            }
        }

    }
    /** парсинг шаблона
     * Ф-ция последовательно перебирает сроку шаблоны, формируя на выходе массив-дерево php, структуры
     *  array(
     *      "id"    => string,
     *      "tag"   => string, 
     *      "class" => string,
     *      "style" => string,
     *      "attr"  => string,
     *      "child" =>array(...)
     * )
     * 
     * @return - массив
     */ 
    private static function _get_struct(&$template,&$i,$is_root=true){
        $out = array();

        while ($i<self::$len){

            if ($template[$i] === '<'){
            
                $find = self::_find($template,self::$blocks,$i+1);
                
                $tag = array('id'=>'','value'=>'','tag'=>'div','style'=>'','class'=>'','attr'=>'');

                if ($find['res'])
                    $tag['id']=substr($template,$i+1,$find['pos']-$i-1);

                while($i<self::$len){

                    if ($find['res']){
                        $i++;
                    
                        if ($find['char']==='>'){
                        
                            $i = $find['pos'];
                            break;
                    
                        }elseif ($find['char']==='<'){
                    
                            $i = $find['pos'];
                            $child = self::_get_struct($template,$i,false);
                        
                            if (!isset($tag['child']))
                                $tag['child'] = array();
                            $tag['child'] = array_merge($tag['child'],$child);    
                    
                        }else{
                    
                            $findr=self::_find($template,array(self::$over[$find['char']]),$find['pos']+1);
                    
                            $tag_name = self::$oname[$find['char']];
                    
                            if ($findr['res']){
                        
                                if (!isset($tag[$tag_name]))
                                    $tag[$tag_name] = '';
                            
                                $tag[$tag_name].=($tag[$tag_name]!==''?self::$space[$find['char']]:'').substr($template,$find['pos']+1,$findr['pos']-$find['pos']-1);
                            
                                $i = $findr['pos']+1;    

                            }else{
                                $i = self::$len;    
                                $tag[$tag_name]='-ERROR-';
                            }
                    
                        }

                        $find = self::_find($template,self::$blocks,$i);

                    }else{//not find 
                        
                        $i = self::$len;
                        $tag['id'] = '-ERROR-';
                    
                        
                    }
                
                }//while
                
                //----------------------------------------
                //пост обработка -------------------------
                
                $pos    = strpos($tag['id'],':');
                $sp     = strpos($tag['id'],' ');
                
                $id     = ($pos!==false?substr($tag['id'],0,$pos):($sp?substr($tag['id'],0,$sp):$tag['id']));
                $tg     = ($pos!==false?substr($tag['id'],$pos+1,($sp?$sp-$pos:self::$len)):'div');
                
                $tag['id'] = trim($id);
                $tag['tag'] = trim($tg);
                

                //----------------------------------------
                // обработку переменных вынес в RENDER сразу после построениея списка переменных
                // это дает возможность более широко использовать переменные
                //if (count(self::$vars)>0)
                //foreach($tag as $k=>$v){
                //    if (($k!=='id')&&($k!=='tag')){
                //        $tag[$k] = self::_varsToStr($v);
                //    }
                //}
                //----------------------------------------
                
                array_push($out,$tag);
                if (!$is_root) {
                    $i++;
                    break;
                }
            }
        
            $i++;
        
        }//while
    
        return $out;
    }
    
    private static function _find(&$str,$chars,$off=0){
        $out = array('res'=>false);
        for($i=0;$i<count($chars);$i++){
        
            $pos = strpos($str,$chars[$i],$off);
        
            if ($pos!==false){
                if (!isset($out['char'])){
                    $out['char']    = $chars[$i];
                    $out['pos']     = $pos;
                    $out['res']     = true;
                }else{
                    if ($out['pos']>$pos){
                        $out['char']    = $chars[$i];
                        $out['pos']     = $pos;
                    }
                }
            }
        }
        return $out;
    }
    
    private static function _attr($frame,$str){

        $attrs = explode(';',$str);
        
        for($i=0;$i<count($attrs);$i++){
            $item = explode('=',$attrs[$i]);
            if (count($item)>1){
                $item[1] = str_replace(array('"',"'"),'',$item[1]);
                $frame->ATTR($item[0],$item[1]);
            }
        }
        
    }

    /** преобразование структуры в объекты FRAME */
    private static function _render($struct,$own,$group,$toAll){
        
        for($i=0;$i<count($struct);$i++){
            
            $item = $struct[$i];
            $tag = $item['tag'];
            
            $frame = FRAME($item['id'],$own)
                ->TAG_NAME($item['tag'])
                ->GROUP($group)
                ->CLASSES($item['class']);
                
            if ($item['tag']==='input')
                $frame->ATTR('value',$item['value']);
            else
                $frame->VALUE($item['value']);
            
            self::_attr($frame,$item['attr']);    
            
            $style = isset($toAll['style'])?$toAll['style']:'';
            
            if (($tag==='tr')||($tag==='td'))
                $style = str_replace('position:absolute','',$style);
            
            $frame->STYLE($style);
            $frame->STYLE($item['style']);
            
            if (isset($toAll['class']))
                $frame->CLASSES($toAll['class']);

            if (isset($toAll['value']))
                $frame->CLASSES($toAll['value']);
            
            if (isset($toAll['attr'])){
                foreach($toAll['attr'] as $k=>$v)
                    $frame->ATTR($k,$v);
            }    
                
                
            if (self::$root===false)
                self::$root = $frame;
            
            if ((isset($item['child']))&&(count($item['child'])>0))
                self::_render($item['child'],$frame,$group,$toAll);
            
            if ((isset($item['align']))&&(strlen($item['align'])>0)){
                
                $funcs = explode(self::$space['~'],$item['align']);
                $frame->ALIGN(self::_align($funcs));

                if (array_search('lh',$funcs)!==false)
                    $frame->LH(true);
                    
                if (array_search('only',$funcs)!==false)
                    $frame->ONLY(true);
            }
    


        }
        
    }
    
    

    private static function _align($align){
        $out = '';
        
        $f = $align;
        $funcs = array();
        //_LOGF($align,'align',__FILE__,__LINE__);
    
        // создадим спсиок ф-ций с именами и параметрами
        for($i=0;$i<count($f);$i++){
            $skob = strpos($f[$i],'(');
            if ($skob===false){
                $name = $f[$i];       
                $param = '';
            }else{
                $name = substr($f[$i],0,$skob);        
                $skob2 = strpos($f[$i],')');
                $param = trim(substr($f[$i],$skob+1,$skob2-$skob-1));
            };
            
            if (isset(self::$jx_short[$name]))
                $name = self::$jx_short[$name];
                
            $funcs[] = array('name'=>$name,'param'=>$param);

            
            if ($name ==='vert'){  /* | */
                
                $p  = GJX::arrangeParam($param,'direct:vert,type:stretch,align:stretch,stretch:all');
                $out.=GJX::arrange($p);
                
            }elseif ($name ==='vertTop'){ /* ^ */  
                $p  = GJX::arrangeParam($param,'direct:vert,type:top,align:stretch');
                $out.=GJX::arrange($p);
            }elseif ($name ==='vertBottom'){ /*  v  */
                $p  = GJX::arrangeParam($param,'direct:vert,type:bottom,align:stretch');
                $out.=GJX::arrange($p);    
            }elseif ($name ==='horiz'){   /* - */
                $p  = GJX::arrangeParam($param,'direct:horiz,type:stretch,align:stretch,stretch:all');
                $out.=GJX::arrange($p);
            }elseif ($name ==='horizLeft'){  /* < */
                $p  = GJX::arrangeParam($param,'direct:horiz,type:left,align:stretch');
                $out.=GJX::arrange($p);                
            }elseif ($name ==='horizRight'){  /* > */
                $p  = GJX::arrangeParam($param,'direct:horiz,type:left,align:stretch');
                $out.=GJX::arrange($p);                
            }elseif ($name==='stretch'){      /* <> */
                $p  = GJX::stretchParam($param);
                $out.=GJX::stretch($p);

            }elseif ($name==='arrange'){  /* + */
                
                $p  = GJX::arrangeParam($param);
                $out.=GJX::arrange($p);

            }elseif ($name==='cling'){  /* & */
                
                $p  = GJX::clingParam($param);
                $out.=GJX::cling($p);

            }elseif ($name==='clingBottom'){  /*  &v*/
                $p  = GJX::clingParam($param,'side:{a:center,b:center},pivot:{a:bottom,b:top}');
                $out.=GJX::cling($p);
            }elseif ($name==='clingTop'){  /*  &^  */
                $p  = GJX::clingParam($param,'side:{a:center,b:center},pivot:{a:top,b:bottom}');
                $out.=GJX::cling($p);
            }elseif ($name==='clingLeft'){ /* <& */
                $p  = GJX::clingParam($param,'side:{a:left,b:right},pivot:{a:center,b:center}');
                $out.=GJX::cling($p);
            }elseif ($name==='clingRight'){  /* &> */
                $p  = GJX::clingParam($param,'side:{a:right,b:left},pivot:{a:center,b:center}');
                $out.=GJX::cling($p);
            }elseif ($name==='workplace'){
                
                $p  = GJX::workplaceParam($param);
                $out.=GJX::workplace($p);
                
            };    
        };
        
        
        return $out;
        //return '/*'.$out.'*/';
    }

}

if($Application->is_main(__FILE__)){
  
echo '<xmp>';
$c = '<i:textarea {position:static} --wp >

';

echo FRAMET($c,null);

echo '</xmp>';
}


?>