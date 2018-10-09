<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

    
if(!isset($Application)){
    require_once '../../../ws/utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = false;
    
    require_once UNIT('ws','ws.php');
};

RESOURCE('plugins','splitter/splitter.js');
RESOURCE('plugins','mbtn/mbtn.js');
RESOURCE('plugins','mbtn/mbtn.dcss');

RESOURCE('plugins','jconsole/jconsole.dcss');
RESOURCE('plugins','jconsole/jconsole.js');

RESOURCE('plugins','common/jlock.js');


RESOURCE('plugins','common/jgroup.js');

RESOURCE('plugins','mbtn/mbtn.js');
RESOURCE('plugins','mbtn/mbtn.dcss');

class TWS extends WS{
        

    public function plugin(){
        
        $own = FRAME('plugin')
        //->ALIGN('JX.stretch(Qs.plugin,{margin:10});')
        ->ALIGN('JX.pos({$},{x:20,y:20})')
        ->VALUE('button')
        ->INIT('{$}.mbtn({})'); // place code for plugin init

    }

    private function menu(){
        
        FRAME('left')->ALIGN('JX.arrange({$}.children(),{direct:"vert",type:"top",align:"center",gap:2,margin:{top:10}})');
        /*c:quickStart*/
        $this->item(
            'quickStart',                   //id
            'Quick start',                  //caption
            '$("#plugin").mbtn({});',//sample code
            '{$plugin}.mbtn({});'    //onclick event
        );
        
        
        

        /*c:css */
        $this->item(
            'css',
            /*------------caption---------------*/
            'set ext css',                  
            /*------------code------------------*/
            '
            
            {$plugin}.mbtn({
                css:{
                    active:"btn_active",
                    disable:"btn_disable",
                    hover:"btn_hover"
                }
            });
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.mbtn({
                css:{
                    active:"btn_active",
                    disable:"btn_disable",
                    hover:"btn_hover"
                }
            });
            
            '
        );        


    
        /*c:active */
        $this->item(
            'active',
            /*------------caption---------------*/
            'active',                  
            /*------------code------------------*/
            '
            
                let active = {$plugin}.mbtn("active");
                {$plugin}.mbtn({active:!active});
            
            ',
            /*------------event-----------------*/
            '
            
                let active = {$plugin}.mbtn("active");
                {$plugin}.mbtn({active:!active});
            
            '
        );        

        
        /*c:disable */
        $this->item(
            'disable',
            /*------------caption---------------*/
            'disable',                  
            /*------------code------------------*/
            '
            
                let disable = {$plugin}.mbtn("disable");
                {$plugin}.mbtn({disable:!disable});
            
            ',
            /*------------event-----------------*/
            '
            
                let disable = {$plugin}.mbtn("disable");
                {$plugin}.mbtn({disable:!disable});
            
            '
        );        
        
        
        
        /*c:click */
        $this->item(
            'click',
            /*------------caption---------------*/
            'click',                  
            /*------------code------------------*/
            '
            
            {$plugin}.mbtn({click(){
                jconsole("click");
            }});
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.mbtn({click(){
                jconsole("click");
            }});
            
            '
        );        


    

    
        

    }
    private function css(){
        FRAME()->CSS('
            /*c:body */
            body{
                overflow-x:hidden;
                overflow-y:hidden;
                -webkit-font-smoothing:antialiased;
                
                text-overflow: ellipsis;
                margin:0px;
                padding:0px;
                font-family:arial, sans-serif;
                font-size:13px;
                
            }
            
            /*c:layout */
            .layout{
                position:absolute;
                margin:0px;
                padding:0px;
                
            }
            
            /*c:splitter */
            .splitter{
                border:1px solid #C0C0C0;
            }
            
            /*c:splitter_vert */
            .splitter_vert{
                border-top:1px solid white;
                border-bottom:1px solid white;
                z-index:1;
                
            }
            
            /*c:splitter_horiz */
            .splitter_horiz{
                border-left:1px solid white;
                border-right:1px solid white;
                z-index:2;
                 
            }
            
            /*c:menu */
            .menu{
                height:24px;
                line-height:24px;
                width:140px;
                text-align:center;
                border:1px solid silver;
            }
            
            /*c:item_panel */
            .item_panel{
                overflow:auto;
            }
            
            
            /*c:btn */
            .btn{
                border:1px solid gray;
                background: linear-gradient(to bottom, #f2f5f6 0%,#e3eaed 37%,#c8d7dc 100%);
                text-align:center;
                cursor:pointer;
                height:52px;
                width:180px;
                line-height:52px;
                
            }
            /*
            .btn:hover{
                border:1px solid black;
            }
            */
            
            .btn_hover{
                background: linear-gradient(to bottom, #cfe7fa 0%,#6393c1 100%);    
            }
            .btn_active{
                background:#7F9DF5;
                color:white;
            }
            
            .btn_disable{
                color:silver;
                background:#F9F9F9;
                
            }

            
        ');
    }
    private function javascript(){
        FRAME()->SCRIPT('
        function codeNorm(code){
            return  code.replaceAll("<#enter#>","\n").replaceAll("<#quot#>",String.fromCharCode(34)).replaceAll("<#apos#>",String.fromCharCode(39));
        }
        
        function jconsole(o,param){
            if ((o==="clear")&&(param===undefined)){
                 if (!{$btnConsoleClear}.mbtn("active"))
                    {$console}.jconsole("clear");
                return;
            }
            {$console}.jconsole(o,param);
        }
        
        
        function activatePanel(panel){
            {$right}.children().hide(0);    
            panel.show();
        }
        ');
    }
    private function layout(){
        $wp = FRAME('workplace',FRAME())->CLASSES('layout');
            
        FRAME('modal',FRAME())->CLASSES('layout')
            ->STYLE('left:0px;top:0px;width:0px;height:0px;z-index:1000');
        
        $page = FRAME('page',$wp)->CLASSES('layout')
            ->ALIGN('JX.workplace(Qs.workplace,Qs.page);');        
        
        $own = $page
            ->ALIGN('
                JX.arrange([Qs.top,Qs.middle,Qs.bottom],{direct:"vert",stretch:Qs.middle});
                JX.arrange(Qs.middle.children(),{direct:"horiz",stretch:Qs.center});
            ');
                
        FRAME('top',$own)->CLASSES('layout')
            ->STYLE('height:32px;line-height:32px;text-indent:5px;font-weight:bold;border-bottom:1px solid #C0C0C0');
        
        $middle = FRAME('middle',$own)->CLASSES('layout');
            
        FRAME('bottom',$own)->CLASSES('layout')
            ->STYLE('height:32px;border-top:1px solid #C0C0C0');

        FRAME('left',$middle)->CLASSES('layout');

        FRAME('splitter_lc',$middle)
            ->STYLE('position:absolute;width:10px;cursor:ew-resize')
            ->CLASSES('splitter splitter_vert')
            ->READY('{$}.splitter({horiz:true})');
            
        $center = FRAME('center',$middle)->CLASSES('layout')
        ->SCRIPT('var centerAlignFirst = true')
        ->ALIGN('
            if (centerAlignFirst)
                JX.arrange({$}.children(),{direct:"vert",type:"stretch",align:"stretch",stretch:[{idx:0},{idx:2},{idx:4}]});
            else    
                JX.arrange({$}.children(),{direct:"vert",type:"stretch",align:"stretch",stretch:[{idx:0}]});
            centerAlignFirst=false;
            
            JX.stretch(Qs.code,{margin:{left:10,top:30,bottom:10,right:10}});
            JX.stretch(Qs.console,{margin:{left:10,top:30,bottom:10,right:10}});
        ');
        
        $pluginFrame = FRAME('pluginFrame',$center)->CLASSES('layout');
        
        $plugin = FRAME('plugin',$pluginFrame)->STYLE('position:absolute')->CLASSES('layout');
            
        FRAME('splitter_fl',$center)
            ->STYLE('position:absolute;height:10px;cursor:n-resize')
            ->CLASSES('splitter splitter_horiz')
            ->READY('{$}.splitter({horiz:false})');
        
        $codeFrame = FRAME('codeFrame',$center)->CLASSES('layout');
                FRAME('codeLabel',$codeFrame)->CLASSES('layout')->VALUE('CODE:')->STYLE("left:10px;top:5px;color:#99B8D8");
                FRAME('codeFrom',$codeFrame)->CLASSES('layout')->VALUE('...')->STYLE("left:80px;top:5px");
                FRAME('code',$codeFrame)->CLASSES('layout')->STYLE('overflow:auto;padding:2px')->VALUE('');

        FRAME('splitter_cc',$center)
            ->STYLE('position:absolute;height:10px;cursor:n-resize')
            ->CLASSES('splitter splitter_horiz')
            ->READY('{$}.splitter({horiz:false})');
        
        $consoleFrame = FRAME('consoleFrame',$center)->CLASSES('layout')->STYLE('height:200px');
            FRAME('consoleLabel',$consoleFrame)->CLASSES('layout')->VALUE('CONSOLE:')->STYLE("left:10px;top:5px;color:#99B8D8");
            FRAME('btnConsoleClear',$consoleFrame)->CLASSES('layout')->VALUE('not clear')->STYLE("left:100px;top:5px;width:60px;text-align:center;cursor:default")
            ->INIT('{$}.mbtn({click:function(){
                    {$}.mbtn("active",!{$}.mbtn("active"));
                            
            }})');
            FRAME('btnConsoleExpanded',$consoleFrame)->CLASSES('layout')->VALUE('expanded')->STYLE("left:190px;top:5px;width:60px;text-align:center;cursor:default")
            ->INIT('{$}.mbtn({click:function(){
                    {$}.mbtn("active",!{$}.mbtn("active"));
                    {$console}.jconsole("put",{expandAfter:{$}.mbtn("active")});
            }})');
            
        FRAME('console',$consoleFrame)->CLASSES('layout')->STYLE('overflow:auto')->VALUE('');

        //---------------------------------------------------------------------
        FRAME('splitter_cr',$middle)
            ->STYLE('position:absolute;width:10px;cursor:ew-resize')
            ->CLASSES('splitter splitter_vert')
            ->READY('{$}.splitter({horiz:true})');

        FRAME('right',$middle)->CLASSES('layout')
            ->ALIGN('
                $.each({$}.children(),function(i,o){
                    if (JX.visible(o)){
                        JX.stretch(o,{margin:5});
                        return true;
                    }
                });');
    }
    private function refactoring($code){
        $code = str_replace(array("\t"),array('    '),$code);
        $codes = explode("\n",$code);
        // подсчитываем мин кол - во пробелов вначале
        $min = -1;
        for($i=0;$i<count($codes);$i++){
            
            $len = strlen(ltrim($codes[$i]));
            $val = strlen($codes[$i])-$len;
            if (($len>0) && ($val>0)){
                if ($min==-1)
                    $min = $val;
                else
                    $min = min($val,$min);
            }
        };
        
    
        // удаляем из начала минимальное кол-во пробелов
        if ($min>0){
            $code = '';
            for($i=0;$i<count($codes);$i++){
                $val = strlen($codes[$i])-strlen(ltrim($codes[$i]));
            
                if ($val>=$min)
                    $codes[$i]= substr($codes[$i],$min);
            
                if (($code!=='')||(strlen(trim($codes[$i]))>0))
                    $code.=$codes[$i]."\n";    
            };
        }    
        return $code;        
    }
    private function item($id,$caption,$code='',$click=''){
        $own = FRAME('left');
        $code = $this->refactoring($code);

        $code = str_replace(array("\n",'"',"'"),array('<#enter#>','<#quot#>','<#apos#>'),$code);

        $item = FRAME($id,$own)
            ->CLASSES('menu')
            ->STYLE('position:absolute')
            ->VALUE($caption)
            ->INIT('{$}.mbtn({
                click:function(){
                    var code = "'.$code.'";
                    activatePanel(Qs.'.$id.'_panel);
                    jconsole("clear");
                    Qs.codeFrom.text({$}.text());

                    Qs.code.html("<xmp>"+codeNorm(code)+"</xmp>");
                    '.$click.';
                    
                    Ws.align();
                }
            });');
            
        $panel = FRAME($id.'_panel',FRAME('right'))    
        ->CLASSES('item_panel')
        ->STYLE('position:absolute;display:none');
        
        return array('item'=>$item,'panel'=>$panel);
    }
    

    public function CONTENT(){
        $this->layout();
        $this->plugin();

        $this->css();
        $this->javascript();
        $this->menu();
        
        FRAME('top')->VALUE('mbtn');
        FRAME('left')->STYLE('width:220px');
        FRAME('right')->STYLE('width:250px');
        FRAME('codeFrame')->STYLE('height:150px');
        FRAME('consoleFrame')->STYLE('height:600px');
        FRAME('plugin')->CLASSES('btn');
        
        
        FRAME('console')->INIT('
        {$}.jconsole("put",{
            clearBefore:false,
            expandAfter:false,
            fastInfo:{enable:true},
            
        })');


    }
    
  
  
    public function AJAX(&$response){
        global $REQUEST;
      
        return false;
    }

}      

if($Application->is_main(__FILE__)){
    
    $app = new TWS(); 
    
    $app->title = 'mbtn';
    $app->RUN();
    

}

?>
    