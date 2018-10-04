<?php
/*
 https://ws-framework-fmihel.c9users.io/ide/ws/plugins/mselect/example.php
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

class TWS extends WS{
        
    public function ONLOAD(){
        global $REQUEST;

        if ($REQUEST->IsAjax()){
            
        }else{
        
        };  
      
        return true;
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
        
        function jconsole(o){
            if (o==="clear"){
                 if (!{$btnConsoleClear}.mbtn("active"))
                    {$console}.jconsole("clear");
                return;
            }
            
            var obj = "put";
            if ("obj" in o) {
                obj = o.obj;
            }    
            {$console}.jconsole(obj,o);
        }
        
        
        function activatePanel(panel){
            {$right}.children().hide(0);    
            panel.show();
        }
        ');
    }
    private function layout(){
        
        $wp = FRAME('workplace',FRAME())->CLASSES('layout');
            
        FRAME('modal',FRAME())->CLASSES('layout')->STYLE('left:0px;top:0px;width:0px;height:0px;z-index:1000');
        
        $page = FRAME('page',$wp)->CLASSES('layout')
            ->ALIGN('JX.workplace(Qs.workplace,Qs.page);');        
        
        $own = $page
            ->ALIGN('
                JX.arrange([Qs.top,Qs.middle,Qs.bottom],{direct:"vert",stretch:Qs.middle});
                JX.arrange(Qs.middle.children(),{direct:"horiz",stretch:Qs.center});
            ');
                
        FRAME('top',$own)->CLASSES('layout')->STYLE('height:32px;line-height:32px;text-indent:5px;font-weight:bold;border-bottom:1px solid #C0C0C0');
        
        $middle = FRAME('middle',$own)->CLASSES('layout');
            
        FRAME('bottom',$own)->CLASSES('layout')->STYLE('height:32px;border-top:1px solid #C0C0C0');

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
        
        $plugin      = FRAME('plugin',$pluginFrame)->STYLE('position:absolute')->CLASSES('layout');
        FRAME('splitter_fl',$center)
            ->STYLE('position:absolute;height:10px;cursor:n-resize')
            ->CLASSES('splitter splitter_horiz')
            ->READY('{$}.splitter({horiz:false})');
        
        $codeFrame = FRAME('codeFrame',$center)->CLASSES('layout');
        $codeLabel = FRAME('codeLabel',$codeFrame)->CLASSES('layout')->VALUE('CODE:')->STYLE("left:10px;top:5px;color:#99B8D8");
        $codeFrom = FRAME('codeFrom',$codeFrame)->CLASSES('layout')->VALUE('...')->STYLE("left:80px;top:5px");
        $code      = FRAME('code',$codeFrame)->CLASSES('layout')->STYLE('overflow:auto;padding:2px')->VALUE('');

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
        
        //---------------------------------------------------------------------
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
    

    public function plugin(){
        $own = FRAME('plugin')
        //->ALIGN('JX.stretch(Qs.plugin,{margin:10});')
        ->ALIGN('JX.pos({$},{x:20,y:20,w:200,h:32})')
        ->INIT('{$}.mselect({
            
            onSelect:function(o){
                
                jconsole("clear");   
                jconsole({name:"onSelect",obj:o});   

            }
            
        })')
        
        ->VALUE('

            <div id=item1 t="10" y="g">Item1</div>
            <div id=item2 >Item2</div>
            <div id=item3 >Item3</div>

        ');
        
        
        
            
        
    }

    private function menu(){
        
        FRAME('left')->ALIGN('JX.arrange({$}.children(),{direct:"vert",type:"top",align:"center",gap:2,margin:{top:10}})');
        
        /*---------------------------------------------*/
        /*c:start */
        $this->item('start',
            'Getting Started',
            '$(".plugin").mselect(["item 1","item 2","item 3","item 4","item 5"]);',
            '{$plugin}.mselect(["item 1","item 2","item 3","item 4","item 5"]);'
        );        
        /*---------------------------------------------*/
        /*c:setData */
        $this->item('setData',
        'set data',
        '$(".plugin").mselect({data:[1,2,8,4,15,3,7]});',
        'Qs.plugin.mselect({data:[1,2,8,4,15,3,7]});'
        );
        /*---------------------------------------------*/
        /*c:getData */
        $this->item('getData',
        'get data',
        /*code*/
        'var data = $(".plugin").mselect("data");',
        /*click*/
        'var data = Qs.plugin.mselect("data");
            jconsole({name:"data",obj:data});
        ');        
        /*---------------------------------------------*/
        /*c:changeData */
        $this->item('changeData2',
        'change data id=2',
        '$(".plugin").mselect({data:[{id:2,value:33333}]});',
        'Qs.plugin.mselect({data:[{id:2,value:33333}]});'
        );
        /*---------------------------------------------*/
        /*c:editable */
        $this->item('editable',
        'editable',
        '$(".plugin").mselect({editable:true});',
        '
            let ed = Qs.plugin.mselect("editable");    
            Qs.plugin.mselect({editable:!ed});
        '
        );
        
        /*---------------------------------------------*/
        /*c:selected */
        $this->item('selected',
        'get selected',
        'var data = $(".plugin").mselect("selected");',
        'var data = Qs.plugin.mselect("selected");
         jconsole({name:"data",obj:data});
        ');
        /*---------------------------------------------*/
        /*c:toData */
        $this->item('toData',
        'toData',
        'var data = $(".plugin").mselect("toData",$(".plugin").mselect("selected"));
            
        ',
        'var data = Qs.plugin.mselect("selected");
        data = Qs.plugin.mselect("toData",data);
         jconsole({name:"data",obj:data});
        ');
        /*---------------------------------------------*/
        /*c:getValue */
        $this->item('getValue',
        'get value',
        /*code*/
        'var value = $(".plugin").mselect("value");',
        /*click*/
        'var value = Qs.plugin.mselect("value");
            jconsole({name:"value",obj:value});
        ');        
        /*---------------------------------------------*/
        /*c:getValues */
        $this->item('getValues',
        'get values',
        /*code*/
        'var values = $(".plugin").mselect("values");',
        /*click*/
        'var values = Qs.plugin.mselect("values");
            jconsole({name:"values",obj:values});
        ');        
        /*---------------------------------------------*/
        /*c:clear */
        $this->item('clear',
        'clear',
        '$(".plugin").mselect("clear");',
        'Qs.plugin.mselect("clear");'
        );        
        /*---------------------------------------------*/
        /*c:delete */
        $this->item('delete',
        'delete index=1',
        /*code*/
        '$(".plugin").mselect("delete",{index:1});',
        /*click*/
        '
        Qs.plugin.mselect("delete",{index:1});
        var data = Qs.plugin.mselect("values");
        jconsole({obj:data});
        ');        
        /*---------------------------------------------*/
        /*c:disabled */
        $this->item('disabled',
        'disabled',
        /*code*/
        'var data = $(".plugin").mselect("disabled");',
        /*click*/
        '
        var data = Qs.plugin.mselect("disabled");
        jconsole({obj:data});
        ');        
        /*---------------------------------------------*/
        /*c:setDisabled */        
        $this->item('setDisabled',
        /*.................*/
        'set disabled id=1',
        /*.................*/
        '$(".plugin").mselect("disabled",{id:1},true);',
        /*.................*/
        'Qs.plugin.mselect("disabled",{id:1},true);');        
        /*---------------------------------------------*/
        /*c:setUnDisabled */        
        $this->item('setUnDisabled',
        /*.................*/
        'set undisabled id=1',
        /*.................*/
        '$(".plugin").mselect("disabled",{id:1},false);',
        /*.................*/
        'Qs.plugin.mselect("disabled",{id:1},false);');        
        /*---------------------------------------------*/
        /*c:getDisabledState */
        $this->item('getDisabledState',
        /*.................*/
        'get disabled state ',
        /*.................*/
        'var state = $(".plugin").mselect("disabled",{id:1});',
        /*.................*/
        'jconsole({obj:Qs.plugin.mselect("disabled",{id:1}),name:"state"})');        
        /*---------------------------------------------*/
        /*c:setSelected */
        $this->item('setSelected',
        /*.................*/
        'set selected id=3',
        /*.................*/
        '$(".plugin").mselect("selected",{id:3});',
        /*.................*/
        '{$plugin}.mselect("selected",{id:3});');
        /*---------------------------------------------*/
        /*c:next */
        $this->item('next',
        /*.................*/
        'next',
        /*.................*/
        '$(".plugin").mselect("next");',
        /*.................*/
        '{$plugin}.mselect("next");');
        /*---------------------------------------------*/
        /*c:prev */
        $this->item('prev',
        /*.................*/
        'prev',
        /*.................*/
        '$(".plugin").mselect("prev");',
        /*.................*/
        '{$plugin}.mselect("prev");');
        /*---------------------------------------------*/
        
        /*c:onSelect */
        $this->item('onSelect',
        'onSelect ',
        '
        $(".plugin").mselect({
            onSelect:function(o){
                console.info(o);
            }        
        });
        ',
        '
        
         
        ');
        
    }

    public function CONTENT(){
        $this->layout();
        $this->plugin();

        $this->css();
        $this->javascript();
        $this->menu();
        
        FRAME('top')->VALUE('mselect');
        FRAME('left')->STYLE('width:220px');
        FRAME('right')->STYLE('width:250px');
        FRAME('codeFrame');//->STYLE('height:150px');
        FRAME('consoleFrame');//->STYLE('height:600px');
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
    
    $app->title = "mselect";
    $app->RUN();
    

}

?>
    