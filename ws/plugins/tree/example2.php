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


RESOURCE('plugins','tree/mtree.js');
RESOURCE('plugins','tree/mtree.dcss');

class TWS extends WS{
        

    public function plugin(){
        
        $own = FRAME('plugin')
        ->STYLE('overflow:auto')
        ->ALIGN('JX.stretch(Qs.plugin,{margin:10});')
        //->ALIGN('JX.pos({$},{x:20,y:20,w:200,h:32})')
        
        ->VALUE('Init plugin in php in  TWS->plugin().  For example : {$}.mtree({});')
        ->INIT('{$}.mtree({
             data:'.$this->fill_tree().',
            
        })'); 

    }

    private function menu(){
        
        FRAME('left')->ALIGN('JX.arrange({$}.children(),{direct:"vert",type:"top",align:"center",gap:2,margin:{top:10}})');

        $this->item(
            'quickStart',                   //id
            'Quick start',                  //caption
            '$("#plugin").mtree({});',//sample code
            '{$plugin}.mtree({});'    //onclick event
        );        
        
        
        /*c:getData */
        $this->item(
            'getData',
            /*------------caption---------------*/
            'getData',                  
            /*------------code------------------*/
            '
            // получени данных дерева
            var data = {$plugin}.mtree("data");
            
            ',
            /*------------event-----------------*/
            '
            
            var data = {$plugin}.mtree("data");
            jconsole(data);
            
            '
        );        
        
        
        /*c:canSelected */
        $this->item(
            'canSelected',
            /*------------caption---------------*/
            'canSelected',                  
            /*------------code------------------*/
            '
            // включить/выключить возможность выделения узла
            {$plugin}.mtree({canSelected:true});
            // получить данные 
            var bool = {$plugin}.mtree("canSelected");
            
            ',
            /*------------event-----------------*/
            '
            var bool = !{$plugin}.mtree("canSelected");
            {$plugin}.mtree({canSelected:bool});
            {$}.mbtn("active",bool);        
            ',
            '{$}.mbtn("active",{$plugin}.mtree("canSelected"));'
        );        

        /*c:multiSelect*/
        $this->item(
            'multiSelect',
            /*------------caption---------------*/
            'multiSelect',                  
            /*------------code------------------*/
            '
            // выделение нескольких узловы
            {$plugin}.mtree({multiSelect:true});
            // получить данные 
            var bool = {$plugin}.mtree("multiSelect");
            
            ',
            /*------------event-----------------*/
            '
            var bool = !{$plugin}.mtree("multiSelect");
            {$plugin}.mtree({multiSelect:bool});
            {$}.mbtn("active",bool);        
            ',
            '{$}.mbtn("active",{$plugin}.mtree("multiSelect"));'
        );        
        
        
        /*c:deepSelect */
        $this->item(
            'deepSelect',
            /*------------caption---------------*/
            'deepSelect',                  
            /*------------code------------------*/
            '
            // глубокое сканирование 
            {$plugin}.mtree({deepSelect:true});
            // получить данные 
            var bool = {$plugin}.mtree("deepSelect");
            ',
            /*------------event-----------------*/
            '
            var bool = !{$plugin}.mtree("deepSelect");
            {$plugin}.mtree({deepSelect:bool});
            {$}.mbtn("active",bool);',
            '{$}.mbtn("active",{$plugin}.mtree("deepSelect"));'
        );        
        
        
    
        
        /*c:selectOnlyLastNode */
        $this->item(
            'selectOnlyLastNode',
            /*------------caption---------------*/
            'selectOnlyLastNode',                  
            /*------------code------------------*/
            '
            
            // выделять только конечный узел
            
            var bool = !{$plugin}.mtree("selectOnlyLastNode");

            {$plugin}.mtree({selectOnlyLastNode:bool});

            ',
            /*------------event-----------------*/
            '
            
            var bool = !{$plugin}.mtree("selectOnlyLastNode");
            {$plugin}.mtree({selectOnlyLastNode:bool});
            {$}.mbtn("active",bool);
            
            ',
            '
            var bool = {$plugin}.mtree("selectOnlyLastNode");
            {$plugin}.mtree({selectOnlyLastNode:bool});
            
            '
        );        

        
        /*c:toggleSelect */
        $this->item(
            'toggleSelect',
            /*------------caption---------------*/
            'toggleSelect',                  
            /*------------code------------------*/
            '
            
            // повторный клик по выделенному узлу снимает выделение
            
            var bool = !my.mtree("toggleSelect");
            my.mtree({toggleSelect:bool});
            {$}.mbtn("active",bool);            
            ',
            /*------------event-----------------*/
            '
            
            var bool = !{$plugin}.mtree("toggleSelect");
            {$plugin}.mtree({toggleSelect:bool});
            {$}.mbtn("active",bool);
            
            ',
            '
            var bool = {$plugin}.mtree("toggleSelect");
            {$plugin}.mtree({toggleSelect:bool});
            '
        );        

        
        /*c:collapseOn */
        $this->item(
            'collapseOn',
            /*------------caption---------------*/
            'collapseOn',                  
            /*------------code------------------*/
            '
            // определяет, клик по какому объекту вызывает свораивание/разворачивание
            // unfold - иконка сворачивания
            // icon   - иконка узла
            // item   - вся линия узла
            
            {$plugin}.mtree({collapseOn:"unfold"});        
            ',
            /*------------event-----------------*/
            '
                {$plugin}.mtree({collapseOn:"unfold"});        
            '
        );        


        
        /*c:select */
        $this->item(
            'select',
            /*------------caption---------------*/
            'select',                  
            /*------------code------------------*/
            '
            
            // возвращает массив выбранных узлов (jQuery)
             
             var data = {$plugin}.mtree("select");
            
            ',
            /*------------event-----------------*/
            '
            
            jconsole({$plugin}.mtree("select"));
            
            '
        );        

        
        
        /*c:each */
        $this->item(
            'each',
            /*------------caption---------------*/
            'each',                  
            /*------------code------------------*/
            '
            
            // обход узлов дерева
            
            {$plugin}.mtree("each",{
                info:true,
                step:function(o){
                    o.info.text.text(o.node[0].id);
                }
                
            })            
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.mtree("each",{
                info:false,
                step:function(o){
                    jconsole(o);
                    
                }
                
            })            
            
            '
        );        

        
        /*c:find */
        $this->item(
            'find',
            /*------------caption---------------*/
            'find',                  
            /*------------code------------------*/
            '
            // поиск элемента удовлетворяющего условию

            var nodes = {$plugin}.mtree("find",{id:"node15"});
            if (nodes.length>0){
                {$plugin}.mtree("select",false);
                {$plugin}.mtree("select",nodes[0],true);
                
                jconsole({$plugin}.mtree("find","closest"));// поиск вверх по дереву
            }
            
            
            ',
            /*------------event-----------------*/
            '
            var nodes = {$plugin}.mtree("find",{id:"node15"});
            if (nodes.length>0){
                {$plugin}.mtree("select",false);
                {$plugin}.mtree("select",nodes[0],true);
                
                jconsole({$plugin}.mtree("find","closest"));
            }
            
            '
        );        


        
        /*c:collapse */
        $this->item(
            'collapse',
            /*------------caption---------------*/
            'collapse',                  
            /*------------code------------------*/
            '
            
            // свернуть все
            {$plugin}.mtree("collapse");
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.mtree("collapse");
            
            '
        );        

        
        /*c:expand */
        $this->item(
            'expand',
            /*------------caption---------------*/
            'expand',                  
            /*------------code------------------*/
            '
            
            // развернуть до выделенного узла
            
            {$plugin}.mtree("collapse");            
            {$plugin}.mtree("expand",{animate:false});
            
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.mtree("collapse");            
            {$plugin}.mtree("expand",{animate:false});
            
            '
        );        
        
        
        /*c:collapse2 */
        $this->item(
            'collapse2',
            /*------------caption---------------*/
            'collapse2',                  
            /*------------code------------------*/
            '
            // ????
            my.mtree("collapse",{ node:my.mtree("select")[0],env:true});            
            
            ',
            /*------------event-----------------*/
            '
            {$plugin}.mtree("collapse",{ node:{$plugin}.mtree("select")[0],env:true});            
            
            '
        );        
        

        
        /*c:collapseEnv */
        $this->item(
            'collapseEnv',
            /*------------caption---------------*/
            'collapseEnv',                  
            /*------------code------------------*/
            '
            // сворачивать соседние ветки

            var bool = !{$plugin}.mtree("collapseEnv");
            {$plugin}.mtree({collapseEnv:bool});
            {$}.mbtn("active",bool);
            
            ',
            /*------------event-----------------*/
            '
            var bool = !{$plugin}.mtree("collapseEnv");
            {$plugin}.mtree({collapseEnv:bool});
            {$}.mbtn("active",bool);
            ',
            '
            var bool = {$plugin}.mtree("collapseEnv");
            {$plugin}.mtree({collapseEnv:bool});
            
            '
        );        


        
        /*c:redraw */
        $this->item(
            'redraw',
            /*------------caption---------------*/
            'redraw',                  
            /*------------code------------------*/
            '
            
            // перерисовка 

            {$plugin}.mtree("each",{
                info:true,
                step:function(s){
                    if (!s.info.last)
                        s.info.node.css("background-color","#F6F5F5");
                    else
                        s.info.node.css("background-color","white");
                }
            });
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.mtree("each",{
                info:true,
                step:function(s){
                    if (!s.info.last)
                        s.info.node.css("background-color","#F6F5F5");
                    else
                        s.info.node.css("background-color","white");
                }
            });
            
            '
        );        

        
        
        /*c:animate */
        $this->item(
            'animate',
            /*------------caption---------------*/
            'animate',                  
            /*------------code------------------*/
            '
            // анимация

            if (my.mtree("animate")==0)
                my.mtree({animate:400});
            else    
                my.mtree({animate:0});

            ',
            /*------------event-----------------*/
            '
            '
        );        

        
        
        /*c:story */
        $this->item(
            'story',
            /*------------caption---------------*/
            'story',                  
            /*------------code------------------*/
            '
            
            var data  = {$plugin}.mtree("state");
            bdata.json_set({key:my[0].id,val:data});
            
            ',
            /*------------event-----------------*/
            '
            
            var data  = {$plugin}.mtree("state");
            bdata.json_set({key:my[0].id,val:data});

            '
        );        


        
        /*c:hide */
        $this->item(
            'hide',
            /*------------caption---------------*/
            'hide',                  
            /*------------code------------------*/
            '
            // скрыть узел
            var list = Qs.plugin.mtree("select");
            {$plugin}.mtree("visible",list,false);
            
            ',
            /*------------event-----------------*/
            '
            var list = Qs.plugin.mtree("select");
            {$plugin}.mtree("visible",list,false);
            
            '
        );        

        /*c:hide */
        $this->item(
            'show',
            /*------------caption---------------*/
            'show',                  
            /*------------code------------------*/
            '
            // показать узел
            var hidden = Qs.plugin.mtree("visible",undefined,false);
            {$plugin}.mtree("visible",hidden,true);
            
            ',
            /*------------event-----------------*/
            '
            var hidden = Qs.plugin.mtree("visible",undefined,false);
            {$plugin}.mtree("visible",hidden,true);

            '
        );        


        
        /*c:get_hidden */
        $this->item(
            'get_hidden',
            /*------------caption---------------*/
            'get_hidden',                  
            /*------------code------------------*/
            '
            // список скрытых узлов 
            var hidden = Qs.plugin.mtree("visible",undefined,false);
            
            // список видимых узлов 
            var visible = Qs.plugin.mtree("visible",undefined,true);
            
            ',
            /*------------event-----------------*/
            '
            var hidden = Qs.plugin.mtree("visible",undefined,false);
            jconsole(hidden);
            
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
            ->READY($init);
            
        $panel = FRAME($id.'_panel',FRAME('right'))    
        ->CLASSES('item_panel')
        ->STYLE('position:absolute;display:none');
        
        return array('item'=>$item,'panel'=>$panel);
    }
    private function fill_tree(){
        $own = FRAME('my');

        $c1 = 3;
        $c2 = 3;
        $c3  = 3;
        $c4 = 3;
        
        $out = array();
        for($i=0;$i<$c1;$i++){
            
            $folder = array('caption'=>'folder '.$i);
            $child = array();
            for($j=0;$j<$c2;$j++){
                $folder2 = array('caption'=>'sub '.$j);
                $child2 = array();
                for($k=0;$k<$c3;$k++){
                    
                    $folder3 = array('caption'=>'inside '.$k);
                    $child3 = array();
                    for($m=0;$m<$c4;$m++){
                        array_push($child3,array('caption'=>'file','icon'=>'file'));
                    }                    

                    array_push($child2,array('caption'=>'folder','child'=>$child3));
                    
                }
                $folder2['child'] = $child2;
                array_push($child,$folder2);
            }
            $folder['child'] = $child;
            
            array_push($out,$folder);
                        
        };
        
        return ARR::to_json($out);        
    }
    

    public function CONTENT(){
        $this->layout();
        $this->plugin();

        $this->css();
        $this->javascript();
        $this->menu();
        
        FRAME('top')->VALUE('mtree');
        FRAME('left')->STYLE('width:220px');
        FRAME('right')->STYLE('width:250px');
        FRAME('codeFrame')->STYLE('height:150px');
        FRAME('consoleFrame')->STYLE('height:600px');
        FRAME('plugin');        
        
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
    
    $app->title = 'mtree';
    $app->RUN();
    

}

?>
    