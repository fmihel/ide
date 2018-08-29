<?php

/*
https://ws-framework-fmihel.c9users.io/ide/ws/plugins/jconsole/example.php
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

    
if(!isset($Application)){
    require_once '../../utils/application.php';
    
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
RESOURCE('plugins','mselect/mselect.js');
RESOURCE('plugins','mselect/mselect.dcss');


//RESOURCE('plugins','jconsole/jconsole.js');
//RESOURCE('plugins','jconsole/jconsole.dcss');

class TWS extends WS{
        
    public function ONLOAD(){
        global $REQUEST;

        if ($REQUEST->IsAjax()){
            
        }else{
        
        };  
      
        return true;
    }
    public function plugin(){
        
        $own = FRAME('plugin')
        ->ALIGN('JX.stretch(Qs.plugin,{margin:10});');
        //->ALIGN('JX.pos({$},{x:20,y:20,w:200,h:32})');
        //->INIT('{$}.jconsole({})'); // place code for plugin init

    }

    private function menu(){
        
        FRAME('left')->ALIGN('JX.arrange({$}.children(),{direct:"vert",type:"top",align:"center",gap:2,margin:{top:10}})');
        /*c:quickStart*/
        $this->item(
            'quickStart',                   //id
            'Quick start',                  //caption
            '$("#plugin").jconsole("debug string");',//sample code
            '{$plugin}.jconsole("debug string");'    //onclick event
        );
        /*c:viewString*/
        $this->item(
            'viewString',                   //id
            'view string',                  //caption
            '$("#plugin").jconsole("string");',
            '{$plugin}.jconsole(ut.random_str(ut.random(10,100)));'    
        );        
        /*c:viewStringWidthName*/
        $this->item(
            'viewStringWidthName',                   //id
            'add name',                  //caption
            '
                $("#plugin").jconsole("string","varName");
                //or
                $("#plugin").jconsole("string",{name:"varName"});
            ',
            '{$plugin}.jconsole(ut.random_str(ut.random(10,100)),"varName");'    
        );        
        
        /*c:viewArray*/
        $this->item(
            'viewArray',                   //id
            'view array',                  //caption
            '
            var array = [1,2,3,4,5,6,7,453,667,43,65,33,244,6];
            $("#plugin").jconsole(array);
            ',
            '{$plugin}.jconsole([1,2,3,4,5,6,7,453,667,43,65,33,244,6],"array");'    
        );        
        /*c:viewObject*/
        $this->item(
            'viewObject',                   //id
            'view object',                  //caption
            '
            var data = {file:"name.txt",random_str:"string",array:[12,3,4,5,"text"],prop:function(){},bool:true};
            $("#plugin").jconsole(data);

            ',
            '
            var data = {file:"name.txt",random_str:ut.random_str(10),array:[12,3,4,5,"text"],prop:function(){},bool:true};
            {$plugin}.jconsole(data);
            '    
        );        
        /*c:clear*/
        $this->item(
            'clear',                   //id
            'clear',                  //caption
            '$("#plugin").jconsole("clear");',//sample code
            '{$plugin}.jconsole("clear");'    //onclick event
        );        
        /*c:clearBefore*/
        $this->item(
            'clearBefor',                   //id
            'clearBefore',                  //caption
            '//always clear before output
            
            $("#plugin").jconsole("put",{clearBefore:true});',//sample code
            '
            {$}.mbtn("active",!{$}.mbtn("active"));
            {$plugin}.jconsole("put",{clearBefore:{$}.mbtn("active")});
            
            '
        );        
        /*c:expandAfter*/
        $item = $this->item(
            'expandAfter',                   //id
            'expandAfter',                  //caption
            '
            
            $("#plugin").jconsole("put", {expandAfter:true});',//sample code
            
            '
            {$}.mbtn("active",!{$}.mbtn("active"));
            {$plugin}.jconsole("put",{expandAfter:{$}.mbtn("active")});
            ',
            '{$}.mbtn("active",true)'
        );
        
        
        
        /*c:scrollAfter*/
        $item = $this->item(
            'scrollAfter',                   //id
            'scrollAfter',                  //caption
            '
            
            $("#plugin").jconsole("put",{scrollAfter:true });',//sample code
            
            '
            {$}.mbtn("active",!{$}.mbtn("active"));
            {$plugin}.jconsole("put",{scrollAfter:{$}.mbtn("active")});
            
            ',
            '{$}.mbtn("active",true)'
        );
        

        /*c:fastInfo*/
        $item=$item = $this->item(
            'fastInfo',                   //id
            'fastInfo',                  //caption
            '$("#plugin").jconsole("put",{fastInfo:{enable:true}});',//sample code
            '
            '
        );
        
        $own = $item['panel']
        ->ALIGN('JX.tile({$}.children(),{count:2,gap:5,margin:5})');

        FRAME('enableFastInfoLabel',$own)
            ->STYLE('position:absolute')
            ->VALUE('fastInfo:');
        FRAME('enableFastInfo',$own)
            ->VALUE('enable')
            ->CLASSES('menu')
            ->INIT('{$}.mbtn({click:function(){
                {$}.mbtn("active",!{$}.mbtn("active"));
                {$plugin}.jconsole("put",{fastInfo:{enable:{$}.mbtn("active")}});
            }})')
            ->STYLE('position:absolute');
        
        FRAME('viewElementsLabel',$own)
            ->STYLE('position:absolute')
            ->VALUE('viewElements:');
        FRAME('viewElements',$own)
            ->CLASSES('mselect')
            ->INIT('
            {$}.mselect({data:[2,5,10],onSelect:function(){
            
                {$plugin}.jconsole("put",{fastInfo:{viewElements:parseInt({$}.mselect("value"))}});
                
            }});
            {$}.mselect("selected",{index:1});
            ')
            ->STYLE('position:absolute');
            
        FRAME('cropStringLabel',$own)
            ->STYLE('position:absolute')
            ->VALUE('cropString:');
        FRAME('cropString',$own)
            ->CLASSES('mselect')
            ->INIT('{$}.mselect({data:[2,5,10],onSelect:function(){
            
                {$plugin}.jconsole("put",{fastInfo:{cropString:parseInt({$}.mselect("value"))}});
                
            }});{$}.mselect("selected",{index:2});')
            ->STYLE('position:absolute');


        $item=$item = $this->item(
            'terminal',                   //id
            'terminal',                  //caption
            '$("#plugin").jconsole("put",{terminal:{plugin:true,browserConsole:true}});',//sample code
            '
            '
        );
        
        $own = $item['panel']
        ->ALIGN('JX.tile({$}.children(),{count:2,gap:5,margin:5})');

        FRAME('enablePluginLabel',$own)
            ->STYLE('position:absolute')
            ->VALUE('plugin:');
        FRAME('enablePlugin',$own)
            ->VALUE('enable')
            ->CLASSES('menu')
            ->INIT('{$}.mbtn({click:function(){
                {$}.mbtn("active",!{$}.mbtn("active"));
                {$plugin}.jconsole("put",{terminal:{plugin:{$}.mbtn("active")}});
            }});
            {$}.mbtn({active:true})
            ')
            ->STYLE('position:absolute');


        FRAME('enableBrowserConsoleLabel',$own)
            ->STYLE('position:absolute')
            ->VALUE('browserConsole:');
        FRAME('enableBrowserConsole',$own)
            ->VALUE('enable')
            ->CLASSES('menu')
            ->INIT('{$}.mbtn({click:function(){
                {$}.mbtn("active",!{$}.mbtn("active"));
                {$plugin}.jconsole("put",{terminal:{browserConsole:{$}.mbtn("active")}});
            }});
            {$}.mbtn({active:true})
            ')
            ->STYLE('position:absolute');
            
        /*c:view*/
        
        $item=$item = $this->item(
            'view',                   //id
            'view',                  //caption
            '$("#plugin").jconsole("put",{view:{
                func:true,
                array:true,
                object:true,
                string:true,
                number:true,
                undef:true,
                nil:true,
                bool:true
            }});',//sample code
            '
            '
        );
        
        $own = $item['panel']
        ->ALIGN('JX.tile({$}.children(),{count:2,gap:5,margin:5})');

        FRAME('enableFuncLabel',$own)->STYLE('position:absolute')
        ->VALUE('func:');
        
        FRAME('enableFunc',$own)
            ->VALUE('enable')
            ->CLASSES('menu')
            ->INIT('{$}.mbtn({click:function(){
                {$}.mbtn("active",!{$}.mbtn("active"));
                {$plugin}.jconsole("put",{view:{func:{$}.mbtn("active")}});
            }});
            {$}.mbtn({active:true})
            ')
            ->STYLE('position:absolute');
        
        FRAME('enableObjectLabel',$own)->STYLE('position:absolute')
        ->VALUE('object:');
        
        FRAME('enableObject',$own)
            ->VALUE('enable')
            ->CLASSES('menu')
            ->INIT('{$}.mbtn({click:function(){
                {$}.mbtn("active",!{$}.mbtn("active"));
                {$plugin}.jconsole("put",{view:{object:{$}.mbtn("active")}});
            }});
            {$}.mbtn({active:true})
            ')
            ->STYLE('position:absolute');

        FRAME('enableArrayLabel',$own)->STYLE('position:absolute')
        ->VALUE('array:');
        
        FRAME('enableArray',$own)
            ->VALUE('enable')
            ->CLASSES('menu')
            ->INIT('{$}.mbtn({click:function(){
                {$}.mbtn("active",!{$}.mbtn("active"));
                {$plugin}.jconsole("put",{view:{array:{$}.mbtn("active")}});
            }});
            {$}.mbtn({active:true})
            ')
            ->STYLE('position:absolute');
        
        FRAME('enableNullLabel',$own)->STYLE('position:absolute')
        ->VALUE('nil:');
        
        FRAME('enableNull',$own)
            ->VALUE('enable')
            ->CLASSES('menu')
            ->INIT('{$}.mbtn({click:function(){
                {$}.mbtn("active",!{$}.mbtn("active"));
                {$plugin}.jconsole("put",{view:{nil:{$}.mbtn("active")}});
            }});
            {$}.mbtn({active:true})
            ')
            ->STYLE('position:absolute');

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
            /*c:mselect */
            .mselect{
                height:24px;
                line-height:24px;
                width:140px;
                border:1px solid silver;
                
            }
        ');
    }
    private function javascript(){
        FRAME()->SCRIPT('
        function codeNorm(code){
            return  code.replaceAll("<#enter#>","\n").replaceAll("<#quot#>",String.fromCharCode(34)).replaceAll("<#apos#>",String.fromCharCode(39));
        }
        
        function jconsole(o){
            if (o==="clear"){
                 if (!{$btnConsoleClear}.mbtn("active"))
                    {$console}.jconsole("clear");
                return;
            }
            {$console}.jconsole(o);
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
    private function item($id,$caption,$code='',$click='',$init=''){
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
            });')
            ->INIT($init);
            
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
        
        FRAME('top')->VALUE('jconsole');
        FRAME('left')->STYLE('width:220px');
        FRAME('right')->STYLE('width:250px');
        FRAME('codeFrame')->STYLE('height:150px');
        FRAME('consoleFrame')->STYLE('height:600px');
        FRAME('plugin')->STYLE('overflow:auto');        
        
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
    
    $app->title = 'jconsole';
    $app->RUN();
    

}

?>
    