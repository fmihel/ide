<?php
require_once UNIT('ws','ws.php');

MODULES::ADD('UPDATE');
define('RELEASE_ADDR','https://ws-framework-fmihel.c9users.io/release/');
class UPDATE extends WS_MODULE{
    
    function __construct($owner){
        parent::__construct($owner);
        RESOURCE('modules/update/update.js');
        RESOURCE('modules/update/update.dcss');
        
    }
      
    public function CONTENT(){
        $own = FRAME('modal');
        
        $update_form = FRAME('update_form',$own)
            ->STYLE('position:absolute;display:none')
            ->READY('
            {$}.mform({
                width:380,
                height:300,
                caption:"Update",
                showHeader:true,
                placeCloseOnTopRight:false,
                showFooter:true,
                resizable:false,
                
                position:{
                    align:{vert:"top",margin:{top:100}}    
                },
                onOpen:function(){
                    update.new_version = "check";
                    update.align();    
                    Qs.upProcess.text("");
                    update.check_update();                    
                },
                buttons:{
                    close:{caption:"close",click:function(){
                        {$}.mform("close");    
                    }}
                }
                        
            });
            ')
            ->READY('
                update.init();
                update.version = "'.$this->getCurrentVersion().'";
                setTimeout(function(){{$}.css("display","block");},2000);
            ');
            
        $own = $update_form;    
        
        $lCurrent = FRAME('lCurrent',$own)
            ->STYLE('position:absolute')
            ->CLASSES('up_label')
            ->VALUE('current version :');
            
        $tCurrent = FRAME('tCurrent',$own)
            ->STYLE('position:absolute')
            ->CLASSES('up_txt')
            ->VALUE($this->getCurrentVersion());

        $lNew = FRAME('lNew',$own)
            ->STYLE('position:absolute')
            ->CLASSES('up_label')
            ->VALUE('check...');

        $bUpdate = FRAME('bUpdate',$own)
            ->STYLE('position:absolute')
            ->CLASSES('up_btn')
            ->VALUE('update')
            ->EVENT('click','
             if ((update.new_version!=="update")&&(update.new_version!=="check")&&(update.new_version!=="error")&&(update.new_version!==update.version)){
                 update.run();
             }
            ');

        $upProcess = FRAME('upProcess',$own)
            ->STYLE('position:absolute')
            ->CLASSES('up_process')
            ->VALUE('');
    }
    
    private function getCurrentVersion(){
        
        global $app;
        return $app->version;
        
        //return '010517_1';
        
    }
    private function getFileAndVersion(){
        $html = file_get_contents(RELEASE_ADDR);
        //$re = '/["\']([\S]+)\.zip["\']/';
        $re = '/["\']([\S]+)_([0-9]+)\.zip["\']/';
        
        if ((mb_strpos($html,'.zip')!==false)&&(preg_match_all($re, $html, $matches, PREG_SET_ORDER, 0)!==false)){
            $max = -1;
            for($i=0;$i<count($matches);$i++){
                $v2 = strtotime($matches[$i][1])-$matches[$i][2];
                if ($max === -1)
                    $max = $i;
                else{
                    $v1 = strtotime($matches[$max][1])+$matches[$max][2];
        
                    if ($v2<$v1)
                        $max = $i;
                }
            }

            return array(
                'version'=>$matches[$max][1].'_'.$matches[$max][2],
                'file'=>str_replace(array('"',"'"),'',$matches[$max][0]));
        };    
        
        return false;
    }
    private function getNewVersion(){
        
        $res = $this->getFileAndVersion();
        if ($res!==false)
            return array('res'=>1,'version'=>$res['version']);

        return array('res'=>0);
    }
    
    private function downloadNewVersion(){
        $res = $this->getFileAndVersion();
        
        if ($res!==false){
            $download =  fopen(RELEASE_ADDR.$res['file'], 'r');
            if ($download!==false){
                if (file_put_contents($res['file'],$download)!==false)
                    return array('res'=>1);
            }
        }
        return array('res'=>0);     
        //return array('res'=>1);     
        
    }
    
    private function installNewVersion(){
    
        global $Application;
        $res = $this->getFileAndVersion();
            
        $zip = new ZipArchive;
        if ($zip->open($res['file'],ZipArchive::CREATE)){
            if ($zip->extractTo('../'))
                $out['res']=1;
        };    
            
        $zip->close();
        
        unlink($res['file']);
        

        return $out;
        
    }
    
    public function AJAX(&$response){
        global $REQUEST;
        if ($REQUEST->ID =='get_new_version'){
            
            $response = $this->getNewVersion();
            return true;
        }else
        if ($REQUEST->ID =='downloadNewVersion'){
            
            $response = $this->downloadNewVersion();
            return true;
        }else
        if ($REQUEST->ID =='installNewVersion'){
            
            $response = $this->installNewVersion();
            return true;
        }
        
        
        return false;
        
    }
    
    
};

if ($Application->is_main(__FILE__)){
    echo 'module1';
    
    
}
?>