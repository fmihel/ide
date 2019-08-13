<?php

class TEMPLATES{
    
    static function template_path($access){
        global $USERS;
        global $Application;
        
        
        if ($access=='admin')
            return 'templates/';
        
        if ($access=='user'){
            
            $user_template = trim($USERS->get('templates',false));
            if ($user_template==='') return false;
            
            $rel = APP::rel_path($Application->PATH,$Application->ROOT);

            if ($user_template!==false)
                $user_template = $rel.APP::slash($user_template,false,true);
            
            return $user_template;
                
        }; 
        return false;
    }
    static function load($access=''){
        /*load templates struct ad */
        
        $path = self::template_path($access);
        
        if ($path === false)
            return array();
            
        if($access=='user'){
            if (!is_dir($path))
                return array();
        }
         
        $struct = DIR::struct($path,'xml',false,1);
        
        for($i=0;$i<count($struct);$i++){
            $item = $struct[$i];
            $struct[$i]['caption']=$item['name'];
            $struct[$i]['access']=$access;
            if (!$item['is_file']){
                for($j=0;$j<count($item['childs']);$j++){
                    $file = $item['childs'][$j]['path'];
                    $xml = simplexml_load_file($path.$file);

                    $struct[$i]['childs'][$j]['caption'] = $xml->header->info;
                    $struct[$i]['childs'][$j]['access']  = $access;
                    $struct[$i]['childs'][$j]['short']  = $xml->header->short;
                    /** подготовка массива расширений (удаляем все пустые )*/
                    $exts = explode(',',trim($xml->header->ext));
                    $k=count($exts);
                    while($k>0){
                        $ext = trim($exts[$k-1]);
                        if ($ext=='') 
                            array_splice($exts,$k-1,1);
                        $k--;
                    }
                    $struct[$i]['childs'][$j]['ext']  = $exts;
                    
                }    
            };
        }
        
        return $struct;        
    }

    static function get_template($param){
        
        $path = self::template_path($param['access']);
        
        $res = array();
        $xml = simplexml_load_file($path.$param['file']);
        
        $res['file']    = $param['file'];
        $res['info'] = $xml->header->info;
        
        $res['vars']=array();
        
        if ($xml->header->vars)
        foreach ($xml->header->vars->item as $item) {
            
            $value  = $item.''; /*item is object!!!*/           
            $value = self::_template_to_code($value,false,$param,'','');
            array_push($res['vars'],array('name'=>$item['name'],'value'=>$value,'type'=>'edit','notes'=>$item['notes']));
        }
        $res['text']=$xml->template;
        
        return $res;
    }
    
    static function _template_to_code($code,$vars,$param,$cursor,$pref='<{'){
        global $Application;
        global $USERS;
        $LS = '';
        $RS = '';
        if ($pref == '<{'){
            $LS='<{';
            $RS='}>'; 
        };
        
        
        if ($vars)
        for($i=0;$i<count($vars);$i++){
            $name = $LS.$vars[$i]['name'].$RS;
            $value = $vars[$i]['value'];
            // задание первой буквы в верхнем регистре
            $upper = $LS.'U^'.$vars[$i]['name'].$RS;
            $uValue = $vars[$i]['value'];
            $uValue[0] = strtoupper($uValue[0]);

            // задание первой буквы в нижнем регистре
            $lower = $LS.'l^'.$vars[$i]['name'].$RS;
            $lValue = $vars[$i]['value'];
            $lValue[0] = strtolower($lValue[0]);

            $code = str_replace([$name,$upper,$lower],[$value,$uValue,$lValue],$code);    
            
            
        };
        

        $root                   = $Application->ROOT;
        $user_path              = APP::slash($USERS->get("workplace"),false,true);
        $user_path_from_root    = $Application->ROOT.APP::slash($USERS->get("workplace"),false,true);
        $filename               = $user_path.$param['filename'];
        $filename_short         = APP::get_file($filename);
        $filename_short_wext    = APP::without_ext($filename_short);
        $module                 = $filename_short_wext;
        $filename_from_root     = $user_path_from_root.$param['filename'];
        $filename_from_user     = substr($filename_from_root,strlen($user_path_from_root));
        $path                   = APP::get_path($filename);
        $path_from_root         = APP::get_path($filename_from_root);
        $path_to_root           = APP::rel_path($path_from_root,$root);
        $path_to_ws             = APP::slash(APP::rel_path($path_from_root,$Application->PATH),false,true).'ws/';

        $code = str_replace($LS.'$root'.$RS,$root,$code);    
        $code = str_replace($LS.'$user_path'.$RS,$user_path,$code);    
        $code = str_replace($LS.'$user_path_from_root'.$RS,$user_path_from_root,$code);    
        $code = str_replace($LS.'$filename'.$RS,$filename,$code);    
        $code = str_replace($LS.'$filename_from_root'.$RS,$filename_from_root,$code);    
        $code = str_replace($LS.'$path'.$RS,$path,$code);    
        $code = str_replace($LS.'$path_from_root'.$RS,$path_from_root,$code);    
        $code = str_replace($LS.'$path_to_root'.$RS,$path_to_root,$code);    
        $code = str_replace($LS.'$path_to_ws'.$RS,$path_to_ws,$code);    
        $code = str_replace($LS.'$filename_short'.$RS,$filename_short,$code);    
        $code = str_replace($LS.'$filename_short_wext'.$RS,$filename_short_wext,$code);    
        $code = str_replace($LS.'$module'.$RS,$module,$code);    
        $code = str_replace($LS.'$filename_from_user'.$RS,$filename_from_user,$code);    
        $code = str_replace($LS.'$cursor'.$RS,$cursor,$code);    

        return $code;
    }   

    static function template_to_code($param){
        global $Application;
        global $USERS;
        
        $path = self::template_path($param['access']);
        
        $xml = simplexml_load_file($path.$param['file']);
        if ($xml===false) return array('res'=>0,'msg'=>'open ['.$path.$param['file'].']');
        
        $code = $xml->template;
        $vars = $param['vars'];
        
        
        $cursor                 = '<{$cursor_'.STR::random(3).'}>';
        
        $blocks = self::extract_rem($code);
        for($i=0;$i<count($blocks);$i++){
            if ($blocks[$i]['type']=='code'){
                $blocks[$i]['data'] = self::_template_to_code($blocks[$i]['data'],$vars,$param,$cursor);
            }    
            
        }
        $code = self::concat_rem($blocks);
        
       
        return array('res'=>1,'code'=>$code,'cursor'=>$cursor);
    }   
    
    static function extract_rem($cdata_content){
        $left = '<{rem:';
        $right = ':rem}>';
        $len = 6;
        
        
        
        $pos = strpos($cdata_content,$left);
        if ($pos===false) return array(array('data'=>$cdata_content,'type'=>'code'));
        
        
        $res = array();
        $loop = 10000;
        while(strlen($cdata_content)>0){

            $pos = strpos($cdata_content,$left);
            if ($pos===false){
                array_push($res,array('data'=>$cdata_content,'type'=>'code'));
                $cdata_content = '';
            }else{
                $code = substr($cdata_content,0,$pos);
                $cdata_content = substr($cdata_content,$pos+$len);
            
                $pos = strpos($cdata_content,$right);
            
                if ($pos!==false){
                    $rem = substr($cdata_content,0,$pos);
            
                    $cdata_content = substr($cdata_content,$pos+$len);
            
                    array_push($res,array('data'=>$code,'type'=>'code'));
                    array_push($res,array('data'=>$rem,'type'=>'rem'));
                
                }else{
                    array_push($res,array('data'=>$cdata_content,'type'=>'code'));
                    $cdata_content = '';
                };
            };    
            
            $loop--;
            if ($loop<0) break; 
        }
        
        return $res;
    }
    static function concat_rem($blocks){
        $out = '';
        
        for($i=0;$i<count($blocks);$i++)
            $out.=$blocks[$i]['data'];
        
        return $out;
    }
    
};

MODULES::ADD('Template');

class Template extends WSI_PANEL{
    
    function __construct($owner){
        parent::__construct($owner);
        
        RESOURCE('modules/right_panel/template/template.js');
        RESOURCE('modules/right_panel/template/template.dcss');
        RESOURCE('plugins/com_inspector/com_inspector.js');
        RESOURCE('plugins/com_inspector/com_inspector.dcss');
        
    }    
    
    public function CONTENT(){

        parent::CONTENT();

        $templates= array_merge(TEMPLATES::load('admin'),TEMPLATES::load('user'));
        
        
        FRAME()
        ->SCRIPT('
        var _TEMPLATES = '.ARR::to_json($templates).';
        var com_inspector;
        ')
        ->READY('
        com_inspector=new comin({own:Qs.modal});
        ');
        
    }
    public function fn_update(){
        $res = '
            /*var item = editors.current();
            if ((!item)||(!item.editor)) return;*/
            
            Templates.update({data:_TEMPLATES});
            
        ';
        return $res;
    }
    public function AJAX(&$response){
        global $REQUEST;
        
        
        if ($REQUEST->ID=='get_template'){
            

            $params = $REQUEST->VALUE;
            $response=array('res'=>1,'template'=>TEMPLATES::get_template($params));
            return true;
            
        }else if ($REQUEST->ID=='template_to_code'){

            $response=TEMPLATES::template_to_code($REQUEST->VALUE);
            return true;
        };
        
        return false;
    }
    
}

?>