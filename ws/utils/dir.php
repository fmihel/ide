<?php
/*
    https://ws-framework-fmihel.c9users.io/ide/ws/utils/dir.php
*/
//require_once 'application.php';
if(!isset($Application)){
   require_once 'application.php';
};

class DIR{
    private static function _exts($exts){
        //------------------------------------------------
        if (!is_array($exts)){
            $_ext=explode(',',$exts);
            $ext=array();
            for($i=0;$i<count($_ext);$i++){
                if(trim($_ext[$i])!=='')
                    array_push($ext,trim($_ext[$i]));
            };
        }else
            $ext=$exts;
        //------------------------------------------------
        //upper ext    
        for($i=0;$i<count($ext);$i++)
            $ext[$i] = strtoupper($ext[$i]);
        return $ext;
    }
    
    static function struct($path,$exts=array(),$only_dir=false,$level=10000,$_root=''){
        /*return file_struct begin from $path
        $res = array(
            array(  'name' - short name  Ex: menu
                    'path' - path from begin $path Ex: ws/inter/menu/
                    'is_file' - true if file
                    childs = array(...) - childs dir (if is_file = false :)
            )    
        )
        */
        
        $res = array();
        if ($_root=='') $_root=APP::slash($path,false,true);
        //------------------------------------------------
        $ext = DIR::_exts($exts);    
        //------------------------------------------------
        // add directory
        $dir = @scandir($path);
        if ($dir)
        for($i=0;$i<count($dir);$i++){
            $item = $dir[$i];
            if (($item!=='.')&&($item!=='..')){
                $item_path = APP::slash($path,false,false).APP::slash($item,true,false);
            
                if (is_dir($item_path)){
                    
                    
                    array_push($res,array(
                        'name'=>$item,
                        //'path'=>APP::abs_path($_root,$item_path),
                        'path'=>substr($item_path,strlen($_root)),
                        'is_file'=>false,
                        'childs'=>($level<=0?array():DIR::struct($item_path.'/',$ext,$only_dir,$level-1,$_root))));
                }
            }
        }
    
        // add files
        if (!$only_dir)
        for($i=0;$i<count($dir);$i++){
            $item = $dir[$i];
            if (($item!=='.')&&($item!=='..')){
                $item_file = APP::slash($path,false,false).APP::slash($item,true,false);
            
                if (is_file($item_file)){
                    $_ext = strtoupper(APP::ext($item));
                    if ((count($ext)==0)||(in_array($_ext,$ext)))
                    array_push($res,array(
                        'name'=>$item,
                        //'path'=>APP::abs_path($_root,$item_file),
                        'path'=>substr($item_file,strlen($_root)),
                        'is_file'=>true));
                }
            }
        }

        return $res;
    }
    static function _lstruct($struct,&$to){
        
        for($i=0;$i<count($struct);$i++){
            $el=$struct[$i];
            if ($el['is_file']){
                $add = array();
                foreach($el as $k=>$v){
                    if ($k!=='childs')
                        $add[$k]=$v;
                }
                array_push($to,$add);
            }
        }
        for($i=0;$i<count($struct);$i++){
            $el=$struct[$i];
            if (!$el['is_file']){
                //$add = array();
                //foreach($el as $k=>$v){
                //    if ($k!=='childs')
                //        $add[$k]=$v;
            //    }
            //    array_push($to,$add);
                DIR::_lstruct($el['childs'],$to);
            }
        }
        
    }    
    static function lstruct($path,$exts=array()){
        $struct = DIR::struct($path,$exts);
        $out = array();
        for($i=0;$i<count($struct);$i++){
            $el=$struct[$i];
            if ($el['is_file']){
                $add = array();
                foreach($el as $k=>$v){
                    if ($k!=='childs')
                        $add[$k]=$v;
                }
                array_push($out,$add);
            }
        }
        
        for($i=0;$i<count($struct);$i++){
            $el=$struct[$i];
            if (!$el['is_file']){
                //$add = array();
                //foreach($el as $k=>$v){
                //    if ($k!=='childs')
                //        $add[$k]=$v;
                //}
                //array_push($out,$add);
                DIR::_lstruct($el['childs'],$out);
            }
        }
        return $out;
    }    
    
    static function files($path,$exts='',$full_path=false,$only_root=true){
        
        //echo 'path:  '.$path."\n";        
        
        $struct     =   DIR::struct($path,$exts,false,0);
        $full_path  =   ($only_root?$full_path:true);


        $res        =   array();
        
        for($i=0;$i<count($struct);$i++){
            $item = $struct[$i];
            if ($item['is_file']){
                //array_push($res,($full_path?$item['path']:$item['name']));
                array_push($res,($full_path?$path:'').$item['name']);    
            }    
        }
        
        $dirs       =   ($only_root?array():self::dirs($path,true));
        for($i=0;$i<count($dirs);$i++){
            
            $next_path = $path.$dirs[$i].'/';

            $out = self::files($next_path,$exts,true,false);
            for($j=0;$j<count($out);$j++)
                array_push($res,$out[$j]);    
        }
        return $res;
    }
    
    static function dirs($path,$full_path=false){
        $struct = DIR::struct($path,'',true,0);
        $res = array();
        for($i=0;$i<count($struct);$i++){
            $item = $struct[$i];
            if (!$item['is_file'])
                array_push($res,($full_path?$item['path']:$item['name']));    
        }
        return $res;
    }
    /**
     * clear folder
     * $path is relation path to clear path ( delete all inside in $path,widthout $path)
     * example
     * you app place in:   home/ubuntu/www/app/test01/index.php
     * need clear folder:  home/ubuntu/www/aaa/bbb/
     * use next:
     * $path =  APP::slash(APP::rel_path($Application->PATH,$Application->ROOT.'aaa/bbb/'));
     * DIR::clear($path)
     * 
    */ 
    static function clear($path){
        
        $files = self::files($path,'',false);
        $dirs  = self::dirs($path,false);
        
        for($i=0;$i<count($files);$i++)
            unlink($path.$files[$i]);

        for($i=0;$i<count($dirs);$i++){
            self::clear(APP::slash($path.$dirs[$i],false,true));
            rmdir($path.$dirs[$i]);
        };    
    }
    
    static function info($src){
        $exist = file_exists($dir);
        
        if ($exist){
            $is_dir = is_dir($dir);
            $is_file = !$is_dir;
        }else{
            $is_dir = false;
            $is_file = false;
        };    
        
        return array('exist'=>$exist,'is_dir'=>$is_dir,'is_file'=>$is_file);
    }
    
    /**
     * проверка существовния папки
     */ 
    static function exist($dir){
        return (file_exists($dir) && is_dir($dir));
    }
    
    /**
     * копирует папку
    */
    static function copy($src,$dst,$stopOnError = false) { 
        $res = true;
        
        if (!DIR::exist($src)) return false;
        
        $dir = opendir($src);
        
        if ($dir!==false){
            @mkdir($dst); 
            while(false !== ( $file = readdir($dir)) ) { 
                if (( $file != '.' ) && ( $file != '..' )) { 
                    
                    if ( is_dir($src . '/' . $file) ){ 
                        if (!self::copy($src . '/' . $file,$dst . '/' . $file,$stopOnError))
                            $res = false;
                    }else{ 
                        if (!copy($src . '/' . $file,$dst . '/' . $file))
                            $res = false;
                    }
                    
                    if ((!$res)&&($stopOnError))
                        break;
                } 
            } 
            closedir($dir); 
            
        }else
            return false;
            
        return $res;    
    }
    
    

};//class DIR

if ($Application->is_main(__FILE__)){
    //$path =  APP::slash(APP::rel_path($Application->PATH,$Application->ROOT.'AAA'),false,true);
    //echo $path.'<br>';
    //DIR::clear($path);
    echo $Application->debug_info();
    
    /*
    $path = '../../';
    echo '<xmp>';

    $t=DIR::files($path,'php',false,true);
    print_r($t);
    echo '</xmp>';
    */
    
    
};

?>