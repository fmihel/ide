<?php

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
    
                if ($var['group']==''){
                    $var['group'] = $frame->group;
                    $var['.'] = ($var['group']!==''?'.':'');
                }
                
                $to = 'Qs.'.$var['group'].$var['.'].$var['id'];
                
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
}

if ($Application->is_main(__FILE__)){
    
    echo '<hr>debug:'.__FILE__.'<hr>';  
    echo $Application->debug_info();    
    echo '<hr>end...<hr>';            
    
};
    
?>