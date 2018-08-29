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


//RESOURCE('plugins','evc/evc.js');
//RESOURCE('plugins','evc/evc.dcss');

class TWS extends WS{
        

    public function plugin(){
        
        $own = FRAME('plugin')
        ->ALIGN('JX.stretch(Qs.plugin,{margin:10});')
        //->ALIGN('JX.pos({$},{x:20,y:20,w:200,h:32})')
        ->VALUE('
        
        Event collector - объект на замену jhandler.<br>
        Создает стековую систему событийных триггеров. Позволяет запускать обработчик события, <br>
        блокировать его выполнени внутри указанного блока, и по завершении блока выполнять триггер если он необходим.<br>

        ');
        //->INIT('{$}.evc({})'); // place code for plugin init

    }

    private function menu(){
        
        FRAME('left')->ALIGN('JX.arrange({$}.children(),{direct:"vert",type:"top",align:"center",gap:2,margin:{top:10}})');
        
        /*c:include */
        $this->item(
            'include',
            /*------------caption---------------*/
            'include',                  
            /*------------code------------------*/
            '
            php: подключение
                
                RESOURCE("plugins","eventCollector/evc.js");
            
        
            ',
            /*------------event-----------------*/
            '
            

            '
        );        


    
        $this->item(
            'quickStart',                   //id
            'create',                  //caption
            '
            javascript: Создание коллекции
                
                var ev = getEventCollector();
                
            Ex:
            
            var plugin = {
                ev:undefined,
                init:function(){
                    // создание коллекции и добавление в нее двух обработчиков триггеров change и align
                    plugin.ev = getEventCollector({
                        change:function(){ plugin.change();},
                        align:function(){ plugin.align();},
                        
                    }) 
                },
                // обработчик триггера change
                change:function(){
                    if (plugin.ev.need("change")){
                        try{
                            // TO DO 
                        }catch(e){};
                        plugin.ev.complete("change");
                    }
                },
                // обработчик триггера align
                align:function(){
                    if (plugin.ev.need("align")){
                        try{
                            // TO DO 
                        }catch(e){};
                        plugin.ev.complete("align");
                    }
                    
                }
                
            }
            ',//sample code
            ''    //onclick event
        );        
        
        
        /*c:handler */
        $this->item(
            'handler',
            /*------------caption---------------*/
            'триггер/обработчик',                  
            /*------------code------------------*/
            '
            // обработчик - это обычная ф-ция объекта, которая будет вызывана при наступлении соотвествующего события.
            // для правильной обработки она должна иметь следующую структуру
            var plugin = {
                ...
                // обработчик триггера change
                change:function(){
                    if (plugin.ev.need("change")){  // проверка необходимости заупска кода триггера
                        try{
                            // TO DO  - код триггера 
                            
                        }catch(e){};
                        plugin.ev.complete("change"); // конец обработки и запуск приаттаченных обработчиков
                    }
                },
                ...
            }
            
            
            
            ',
            /*------------event-----------------*/
            '
            '
        );        

        
        /*c:doTrigger */
        $this->item(
            'doTrigger',
            /*------------caption---------------*/
            'вызов обработчика',                  
            /*------------code------------------*/
            '
            
            // Вызов обработчика можно осуществлять как через метод объекта
            plugin.change();
            
            // так и с помощью метода do коллектора
            plugin.do("change");
            
            // оба метода равнозначны, первый возможно немного быстрее
            
            ',
            /*------------event-----------------*/
            '
            
            '
        );        
        
        
        /*c:begin_end */
        $this->item(
            'begin_end',
            /*------------caption---------------*/
            'жесткая блокировка',                  
            /*------------code------------------*/
            '
            
            // блокировка вызова обработчика
            var plugin = {
                proc1:function(){
                    plugin.ev.begin("change");
                    ...
                    
                        plugin.change() - код в триггере выполнен не будет , пока не вызовем end
                        
                    ..
                    plugin.ev.end("change"); - будет выполнен код в обработчике plugin.change();
                    
                    
                }
            }
            
            ',
            /*------------event-----------------*/
            '

            '
        );        

        
        /*c:begin_end_soft */
        $this->item(
            'begin_end_soft',
            /*------------caption---------------*/
            'мягкая блокировка ',                  
            /*------------code------------------*/
            '
            
            // мягкая блокировка вызова обработчика
            var plugin = {
                proc1:function(){
                    plugin.ev.begin("change");
                    ...
                        plugin.change() - код в триггере выполнен не будет , пока не вызовем end
                    ..
                    plugin.ev.end("!change"); - код в обработчике change будет выполнен, только если внутри блока хотя бы один раз был вызван plugin.change()
                    
                }
            }
            
            ',
            /*------------event-----------------*/
            '
            
            
            
            '
        );        

        
        /*c:begin_end_multi */
        $this->item(
            'begin_end_multi',
            /*------------caption---------------*/
            'множ-ная блокировка',                  
            /*------------code------------------*/
            '
            
            // множественная блокировка - возможность заблокировать сразу несколько обработчиков
            var plugin = {
                proc1:function(){
                    plugin.ev.begin("change","align");
                    ...
                        
                    ..
                    plugin.ev.end("!change","align");
                    
                }
            }
            
            ',
            /*------------event-----------------*/
            '

            '
        );        
        
        /*c:bind */
        $this->item(
            'bind',
            /*------------caption---------------*/
            'bind',                  
            /*------------code------------------*/
            '
            
            // добавление дополнительных обработчиков
            plugin.ev.bind("change",function(){
                ...
            });
            
            
            ',
            /*------------event-----------------*/
            '

            '
        );        

        
        /*c:unBind */
        $this->item(
            'unBind',
            /*------------caption---------------*/
            'unBind',                  
            /*------------code------------------*/
            '
            
              // удаление дополнительных обработчиков
            plugin.ev.unBind("change",function(){
                ...
            });
            
            ',
            /*------------event-----------------*/
            '
            
            
            
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
        
        FRAME('top')->VALUE('evc');
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
    
    $app->title = 'evc';
    $app->RUN();
    

}

?>