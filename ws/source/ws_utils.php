<?php
define('LAZY_STORY_VAR','_lazy_story_wrapped_dev_tmp_93');
if(!isset($Application)) 
    require_once '../utils/application.php';

class WS_UTILS{
    
    public static function macro($frame,$code){
        
        $from   = array(
                '{$}',                          //0
                '{#}',                          //1
                '{CALLBACK}',                   //2
                '{AJAX}',                       //3
                '`',                            //4
                '{NAVIGATE}',                   //5    
                '{ID}'                          //1
        );
        $to     = array(
                'Qs.'.$frame->RENDER('group.var') , //0
                '#'.$frame->RENDER('id'),       //1
                'Ws.ajax',                      //2
                'Ws.ajax',                      //3
                "'",                            //4
                'Ws.navigate',                  //5
                $frame->RENDER('id')            //1                
        );
        $res =  str_replace($from,$to,$code);

        
        $story_from  =  '\u003C{$cursor}\u003E';
        $story_to = '{<'.STR::random(7).'>}';
        $res = str_replace($story_from,$story_to,$res);

        //** вставка переменных Dcss 
        if (mb_strpos($res,'{$Dcss.')!==false){
            preg_match_all('/{\$Dcss\.[a-z,A-Z,0-9,_]*}/', $res, $matches, PREG_SET_ORDER, 0);

    
            for($i=0;$i<count($matches);$i++){
                $from = $matches[$i][0];
                $var = str_replace(array('{$Dcss.','}'),'',$from);
                
                $to = 'Dcss.vars.'.$var;
                $res =  str_replace($from,$to,$res);  
            }
        }         

        //** обработка переменных, типа {$name} name данная переменная будет заменена 
        if (mb_strpos($res,'{$')!==false){
            
            preg_match_all('/{\$[a-z,A-Z,0-9,_,(:|\\\\)]*}/', $res, $matches, PREG_SET_ORDER, 0);

            for($i=0;$i<count($matches);$i++){
                $from = $matches[$i][0];
                $id = str_replace(array('{$','}'),'',$from);
                $var = self::extGroupAndId($id);

                if ($var['group']===''){
                    $var['group'] = $frame->group;
                    $var['.'] = ($var['group']!==''?'.':'');
                }
                
                if ($var['group']!==false)
                    $to = 'Qs.'.$var['group'].$var['.'].$var['id'];
                else
                    $to = 'Qs.'.$var['id'];

                $res =  str_replace($from,$to,$res);  
            }
        }
        
        $res = str_replace($story_to,$story_from,$res);
        return $res;
    }
    
    public static function extGroupAndId($id){
        // group:id
        $_id = $id;
        $_group = '';
        $have_group = mb_strpos($id,':');
        if ($have_group!==false){
            $_group  =   trim(substr($id,0,$have_group));
            $_id     =   trim(substr($id,$have_group+1)); 
        }else{
            $have_group = mb_strpos($id,"\\");
            if ($have_group!==false){
                $_group  =   trim(substr($id,0,$have_group));
                $_id     =   trim(substr($id,$have_group+1)); 
            }    
        };
        if (($have_group!==false)&&(trim($_group)==''))
            $_group = false;
        
        return array('group'=>$_group,'id'=>$_id,'.'=>($_group!==''?'.':''),'_'=>($_group!==''?'_':''));
    }
    
    public static function code_end($code)
    {
        //S: Добавляет в конец строки ; если нет
        //R: string
        $code = trim($code);
        if ($code!=='')
        {
            if ($code[strlen($code)-1]!==';')
                    $code.=";";
            return $code.DCR;
        }else
            return "";
    }
    
    static public function concat($a1,$a2){
        //SHORT: связывает две строчки кода, если нет в конце ; то добавляет
        //PARAM:$code1 - левая часть строки
        //PARAM:$code2 - правая часть строки
        //RETURN: string
        //DOC: Соединяет две строки и помещает между ними ";" (если его нет)
        
        if ($a1!=='')
            return WS_UTILS::code_end($a1).trim($a2);
        else
            return trim($a2);
    }
    /** проверка необходимости обернуть файл в модульную обертку */
    public static function lazyNeedWrapper($filename){
        $wrapper = WS_CONF::GET('assemblyModuleWrapper',[]);
    
        if ($wrapper===true){
            return true;
        }else{ 
            foreach($wrapper as $name){
                if (strpos($filename,$name)!==false){
                    return true;
                }
            }
        }
        return false;
    }
    /** оборачиваем код в модульную обертку */
    public static function lazyWrapper($code_js,$param=[]){
        return "(()=>{".$code_js."\n})()";
    }
    /** возвращает свободное имя файла, для обернутого 
     * @return {array} ['local'=>string,'short'=">string]
     * Ex: lazyGetWrapperName('C:\work\ppp\test.js')
     * ['local'=>'C:\work\ppp\test-dev-02.js','short'=>'test-dec-02.js']
    */
    public static function lazyGetWrapperName($local,$param=[]){
        $p = array_merge([
            'postfix'=>'dev',
        ],$param);
        $info = APP::pathinfo($local);
        $idx = 0;
        $short_name = $info['filename'].'-'.$p['postfix'].'.'.$info['extension'];
        $rename = $info['dirname'].'/'.$short_name;
        while(file_exists($rename)){
            $short_name = $info['filename'].'-'.$p['postfix'].'-'.$idx.'.'.$info['extension'];
            $rename = $info['dirname'].'/'.$short_name;
            $idx++;
        }
        return ['local'=>self::lazyLocallyPath($rename),'short'=>$short_name];
    }
    /** преобразует url в его локальный эквивалент 
     * @return {array} ['local'=>string,'version'=>string,'httpPath'=>string]
    */
    public static function lazyUrlToLocal($url){
        global $Application;
        $parse = parse_url($url);
        $httpPath = $parse['scheme'].":"."/".self::lazySlash($parse['host'],true,true).self::lazySlash(APP::get_path($parse['path']),false,true);
        $local = str_replace($Application->HTTP,$Application->PATH,$url);
        $pos = strpos($local,"?");
        $version = '';
        if ($pos !== false){
            $version=substr($local,$pos);
            $local=substr($local,0,$pos);
        }
        
        // для серверных имен
        $local = self::lazyLocallyPath($local);
        
        if (strpos($local,':')===false)
            $local = self::lazySlash($local,true,false);
            
        return ['local'=>$local,'version'=>$version,'httpPath'=>$httpPath];        
    }
    /** очистка предыдущих созданных обернутых копий */
    public static function lazyClearWrappedDev(){
        global $Application;
        try {
            $var = LAZY_STORY_VAR;
            $name = self::lazySlash($Application->PATH,false,true).$var.'.php';
            $name = self::lazyLocallyPath($name);
            
            if (file_exists($name)){
                include $name;
                $files = $$var;
                foreach($files as $file){
                    if (file_exists($file) && (unlink($file) === false)){
                        error_log('Error ['.__FILE__.':'.__LINE__.'] cant delete file '.$file);
                    }
                }
                if (unlink($name) === false)
                    error_log('Error ['.__FILE__.':'.__LINE__.'] cant delete file '.$file);
            }
                       
        } catch (\Exception $e) {
            error_log('Exception ['.__FILE__.':'.__LINE__.'] '.$e->getMessage());
        };
    }
    /** сохранение списка обернутых копий */
    public static function lazyStoryWrappedDev($lazy=[]){
        global $Application;
        try {
            $var = LAZY_STORY_VAR;
            $name = self::lazySlash($Application->PATH,false,true).$var.'.php';
            $name = self::lazyLocallyPath($name);
            
            $files = $lazy;
            if (file_exists($name)){
                include $name;
                $files = array_merge($files,$$var);
            };

            $code = '<?php $'.$var.'='.ARR::to_php_code($files).';?>';
            if (file_put_contents($name,$code) === false)
                throw new Exception("can save to file ".$name);
                

        } catch (\Exception $e) {
            error_log('Exception ['.__FILE__.':'.__LINE__.'] '.$e->getMessage());
        };
    }
    /** добавляет слеш спереди в маршруты  НЕ для DOS систем */
    private static function lazyLocallyPath($path){
        if (strpos($path,':')===false)
            return APP::slash($path,true,false);
        return $path;
    }
    private static function lazySlash($str,$left,$right){
        $slash    = _DIRECTORY_SEPARATOR;
        

        $str      = trim($str);
        $str      = str_replace(['/','\\'],$slash,$str);

        
        $leftPos  =  mb_strpos($str,$slash);
        $rightPos =  mb_strrpos($str,$slash);
        $leftBool = ($leftPos === 0);
        $rightBool  = ($rightPos === (mb_strlen($str)-1));
        
        if ($left && ($leftBool === false)){
            $str = $slash.$str;    
        };

        if (!$left && ($leftBool !==false)){
            $str = mb_substr($str,1);
        };
        if ($right && ($rightBool === false)){
            $str.=$slash; 
        };
        if (!$right && ($rightBool !==false)){
            $str = mb_substr($str,0,mb_strlen($str)-1);
        };

        return $str;
    }
}

if ($Application->is_main(__FILE__)){
    
    echo '<hr>debug:'.__FILE__.'<hr>';  
    echo $Application->debug_info();    
    echo '<hr>end...<hr>';            
    
};
    
?>