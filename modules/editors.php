<?php

$content_type_by_ext = array(
    'editor'=>array('php','js','dcss','css','xml','txt','html','htm'),
    'image'=>array('png','jpg','svg')
);

$DECONET_CODE_PAGES=array('CP1251','MAC','KOI8-R');

MODULES::ADD('EDITORS');
class EDITORS extends WS_MODULE{
   
   function __construct($owner){
        parent::__construct($owner);
        
        RESOURCE('plugins/ace/ace.js');
        RESOURCE('plugins/jeditors/jeditors.js');
        RESOURCE('plugins/jeditors/jeditors.dcss');
        RESOURCE('plugins/jtab/jtab.js');
        //RESOURCE('plugins/jfunc_hint/jfunc_hint.js');
        //RESOURCE('plugins/jfunc_hint/jfunc_hint.dcss');
   }    
    
    public function CONTENT(){
        FRAME()
        ->SCRIPT('var editors;')
        ->ALIGN('editors.align({delayed:true});')
        ->READY('
            if (typeof(jfunc_hint)!=="undefined")
                jfunc_hint.init({stick:Qs.center,own:Qs.modal});
            editors = new jeditors({
                own:Qs.center,
                onopen:function(o){
                        right_panel.update(o);
                },
                onchange:function(o){
                        right_panel.update(o);
                },
                onaction:function(o){
                        right_panel.update(o);
                },
                onrestory:function(o){
                    right_panel.update(o);
                }
                
            });

            if (Ws.user_data.opened){
                editors.restory_opened(Ws.user_data.opened);
                delete Ws.user_data.opened;
            }
        ;');
        
        FRAME('code_page',FRAME('modal'))
        ->STYLE('position:absolute')
        ->CLASSES('code_page')
        ->ALIGN('
        var pos = JX.abs(Qs.center);
        JX.pos({$},{x:pos.x+pos.w-140,y:pos.y+pos.h-28});
        ');
    }
    
    private function _get_content_type($ext){
        global $content_type_by_ext;    
        
        foreach($content_type_by_ext as $type=>$exts)
            if (array_search($ext,$exts)!==false) return $type;

        return 'editor';
    }
    
    /*
    private function _detect_encoding($text){
        $rus = array('a','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
                     'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я');
                     
        for($i=0;$i<count($rus);$i++){
            $char=iconv('UTF-8','Windows-1251',$rus[$i]);
            if (strpos($text,$char)!==false) 
                return 'Windows-1251';
        };
        
        return 'UTF-8';
    }*/
    
   
    
    private function _loadFromFile($filename){

        $content=file_get_contents($filename); 
        
        // определяем кодировку...
        $code_page = ENCODING::simple_codepage($content);
        
        
        /*файлы проекта deconet, увы, находятся в различных кодировках, поэтому данный блок проверяет сохраненную кодировку и преобразует в UTF-8*/
        
        global $DECONET_CODE_PAGES;
        if (array_search($code_page,$DECONET_CODE_PAGES)!==false)
            $content = iconv('Windows-1251',"UTF-8",$content);

        
        
        return array('content'=>$content,'code_page'=>$code_page);
    }
    
    private function _saveToFile($filename,$content,$code_page='UTF-8',$toUTF8=false){

        //$code_page = $this->get_codepage(file_get_contents($filename));
        global $DECONET_CODE_PAGES;
        if (array_search($code_page,$DECONET_CODE_PAGES)!==false){
            //_LOG($code_page,__FILE__,__LINE__);
            if (!$toUTF8)
                $content = iconv("UTF-8",'Windows-1251',$content);
        }


        return (file_put_contents($filename,$content)!==false);
        
    }
    
    public function AJAX(&$response){
        global $REQUEST;
        global $Application;
        global $USERS;
        
        if ($REQUEST->ID=="get_file"){
            
            
            $path = APP::slash(APP::rel_path($Application->PATH,$Application->ROOT.$USERS->get('workplace','')),false,true);
            $filename = $path.$REQUEST->VALUE['filename'];
            
            $type = $this->_get_content_type(APP::ext($filename));     
            
            if (file_exists($filename)){
                
                $content = 'in development... (see editors.php)';
                
                if ($type==='editor')
                    $content=$this->_loadFromFile($filename);            
                
                $url = APP::abs_path($Application->PATH,APP::get_path($filename));
                
                $url =$Application->DOMEN.substr($url,strlen($Application->ROOT)).APP::get_file($filename);
                
                $response = array('res'=>1,'type'=>$type,'content'=>$content['content'],'url'=>$url,'code_page'=>$content['code_page']);
                    
            }else
                $response = array('res'=>0,'msg'=>'file not exists');            
                
            return true;
        }else if ($REQUEST->ID=="set_file"){
            
            $path = APP::slash(APP::rel_path($Application->PATH,$Application->ROOT.$USERS->get('workplace','')),false,true);
            $filename = $path.$REQUEST->VALUE['filename'];
            $code_page = $REQUEST->VALUE['code_page'];
            $toUTF8 = (isset($REQUEST->VALUE['toUTF8'])?($REQUEST->VALUE['toUTF8']==1):false);
            
            $type = $REQUEST->VALUE['type'];     
            $content = $REQUEST->VALUE['content'];            
            if ($this->_saveToFile($filename,$content,$code_page,$toUTF8)!==false)
                $response = array('res'=>1);
            else
                $response = array('res'=>0);
                    
            return true;
            
        }else if ($REQUEST->ID=="story_opened"){
            
            $opened = $REQUEST->VALUE['opened'];
            
            $USERS->put('opened',$opened);
            if ($USERS->save_current())
                $response = array('res'=>1);
            else    
                $response = array('res'=>0,'msg'=>'error story opened ');
            

            return true;
        }
        
        
        return false;
    }
};
?>