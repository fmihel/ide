<?php
/*
https://ws-framework-fmihel.c9users.io/ide/ws/plugins/jedit/example.php
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

    
if(!isset($Application)){
    require_once '../../utils/application.php';
    //require_once '../../wsi/ide/ws/utils/application.php';
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = false;
    
    require_once UNIT('ws','ws.php');
};

RESOURCE('plugins','jedit/jedit.js');
RESOURCE('plugins','jedit/jedit.dcss');

RESOURCE('plugins','mbtn/mbtn.dcss');

RESOURCE('plugins','splitter/splitter.js');
RESOURCE('plugins','mbtn/mbtn.js');

RESOURCE('plugins','jconsole/jconsole.dcss');
RESOURCE('plugins','jconsole/jconsole.js');

RESOURCE('plugins','common/jlock.js');

RESOURCE('plugins','grid/grid.dcss');
RESOURCE('plugins','grid/grid.js');
RESOURCE('plugins','shadow/jshadow.js');


//RESOURCE('jedit.js');

class TWS extends WS{
        

    public function plugin(){
        
        $own = FRAME('plugin')
        ->STYLE('border:1px dashed silver')
        ->ALIGN('JX.arrange([Qs.plugin],{direct:"horiz",type:"stretch",align:"center",stretch:[{idx:0}]});') 
        ->STYLE('border:1px dashed red;height:68px;width:350px;left:20px;top:20px;')
        ->VALUE('label..')
        ->INIT('
        
        '); // place code for plugin init

    }

    private function menu(){
        
        FRAME('left')->ALIGN('JX.arrange({$}.children(),{direct:"vert",type:"top",align:"center",gap:2,margin:{top:10}})');
        
        /*c:quickStart*/
        $this->item(
            'quickStart',                   //id
            'Quick start',                  //caption
            '$("#plugin").jedit({});',//sample code
            '
            {$plugin}.jedit({
                onBtnClick:function(o){
                    jconsole(o);        
                }
            });

            {$plugin}.jedit("on","comboClicks",function(o){
                jconsole(o);
                o.edit.combo("close");
            });
            
            {$plugin}.jedit("on","change",function(o){
                jconsole(o);
            });3
            
            {$plugin}.jedit("on","draw",function(o){
                if (o.value==0)
                    o.value=""; 
            });
            
            
            
            '    //onclick event
        );        
        
        
        
        /*c:width */
        $this->item(
            'width',
            /*------------caption---------------*/
            'width',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit({width:200});
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jedit({width:ut.random(200,400)});
            
            '
        );        
        
        /*c:widths */
        $this->item(
            'widths',
            /*------------caption---------------*/
            'widths',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit({widths:{label:200}});
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jedit({widths:{label:200}});
            
            '
        );        

        
        /*c:comboHeight */
        $this->item(
            'comboHeight',
            /*------------caption---------------*/
            'comboHeight',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit({combo:{height:50}});
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jedit({combo:{height:50}});
            
            '
        );        


    
    
        
        /*c:gapLabel */
        $this->item(
            'gapLabel',
            /*------------caption---------------*/
            'gapLabel',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit({gapLabel:20});
            
            ',
            /*------------event-----------------*/
            '
            
            var a = !{$}.mbtn("active");
            {$plugin}.jedit({gapLabel:(a?20:0)});
            {$}.mbtn("active",a);
            
            
            '
        );        
        
        /*c:topLabel */
        $this->item(
            'topLabel',
            /*------------caption---------------*/
            'topLabel',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit({topLabel:true});
            
            ',
            /*------------event-----------------*/
            '
            var a = !{$}.mbtn("active");
            {$plugin}.jedit({topLabel:a});
            {$}.mbtn("active",a);
            
            '
        );        


    
        
        /*c:edit */
        $this->item(
            'edit',
            /*------------caption---------------*/
            'edit',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit({type:"edit"});
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jedit({type:"edit"});
            
            '
        );        

        
        /*c:text */
        $this->item(
            'text',
            /*------------caption---------------*/
            'text',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit({type:"text"});
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jedit({type:"text"});
            
            '
        );        

        
        /*c:button */
        $this->item(
            'button',
            /*------------caption---------------*/
            'button',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit({type:"button"});
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jedit({type:"button"});
            
            '
        );        
        /*c:memo */
        $this->item(
            'memo',
            /*------------caption---------------*/
            'memo',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit({type:"memo"});
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jedit({type:"memo"});
            
            '
        );        

        /*c:checkbox */
        $this->item(
            'checkbox',
            /*------------caption---------------*/
            'checkbox',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit({type:"checkbox"});
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jedit({type:"checkbox"});
            
            '
        );        


        /*c:alignCheckboxRight */
        $this->item(
            'alignCheckbox',
            /*------------caption---------------*/
            'alignCheckbox',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit({alignCheckbox:"right"});
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jedit({alignCheckbox:"right"});
            
            '
        );        

    
        /*c:device */
        $this->item(
            'device',
            /*------------caption---------------*/
            'device',                  
            /*------------code------------------*/
            '
            
            device info
            
            ',
            /*------------event-----------------*/
            '
            
            jconsole(dvc);
            
            '
        );        


    
        
        /*c:combo */
        $this->item(
            'combo',
            /*------------caption---------------*/
            'combo',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit({combo:{
                fields:[{name:"NAME"}],
                key:"NAME",
                data:[
                    {ID:1,NAME:"Mike"},
                    {ID:2,NAME:"Soma"},
                    {ID:3,NAME:"Dory"},
                    {ID:4,NAME:"Troy"},
                    {ID:5,NAME:"Dummy"},
                ],
                onClick:function(o){
                    console.info("click on:"+$D(o.tr).NAME);
                }
            }});
            

            ',
            /*------------event-----------------*/
            '
            
            
            {$plugin}.jedit({combo:{
                fields:[{name:"NAME"}],
                key:"NAME",
                data:[
                    {ID:1,NAME:"Mike"},
                    {ID:2,NAME:"Soma"},
                    {ID:3,NAME:"Dory"},
                    {ID:4,NAME:"Troy"},
                    {ID:5,NAME:"Dummy"},
                ],
                onClick:function(o){
                    console.info("click on:"+$D(o.tr).NAME);
                }
            }});
            
            '
        );        
        
        /*c:disableLabel */
        $this->item(
            'disables',
            /*------------caption---------------*/
            'disables',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit({disables:{
                label       :false,
                btn_combo   :false,
                btn_add     :false,
                dim         :false
                
                
            }});
            
            ',
            /*------------event-----------------*/
            '
            
            var a = !{$}.mbtn("active");
            {$}.mbtn("active",a);
            {$plugin}.jedit({disables:{label:a,btn_combo:a,btn_add:a,dim:a}});
            
            '
        );        

        
        /*c:disable */
        $this->item(
            'disable',
            /*------------caption---------------*/
            'disable',                  
            /*------------code------------------*/
            '
            
            // no code
            
            ',
            /*------------event-----------------*/
            '
            
            var a = !{$}.mbtn("active");
            {$}.mbtn("active",a);
            {$plugin}.jedit({disable:a});
            
            '
        );        

        /*c:readonly */
        $this->item(
            'readOnly',
            /*------------caption---------------*/
            'readOnly',                  
            /*------------code------------------*/
            '
            
            var a = !{$}.mbtn("active");
            {$}.mbtn("active",a);
            
            {$plugin}.jedit({readOnly:a});
            
            var ro = {$plugin}.jedit("readOnly"); 
            
            ',
            /*------------event-----------------*/
            '
            
            var a = !{$}.mbtn("active");
            {$}.mbtn("active",a);
            {$plugin}.jedit({readOnly:a});
            
            '
        );        

    
    

        
        /*c:value */
        $this->item(
            'value',
            /*------------caption---------------*/
            'value',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit("value",ut.random_str(5));
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jedit("value",ut.random_str(5));
            
            '
        );        
        
        /*c:get as text */
        $this->item(
            'getAsText',
            /*------------caption---------------*/
            'text displayed',                  
            /*------------code------------------*/
            '
            //возвращает отображаемое значение jedit
            var s = {$plugin}.jedit("text");
            
            ',
            /*------------event-----------------*/
            '
            var s = {$plugin}.jedit("text");
            jconsole(s);
            
            '
        );        


        /*c:checked */
        $this->item(
            'checked',
            /*------------caption---------------*/
            'checked',                  
            /*------------code------------------*/
            '
            
            var c = {$plugin}.jedit("checked");
            
            ',
            /*------------event-----------------*/
            '
            
            var c = {$plugin}.jedit("checked");
            jconsole(c);
            
            '
        );        

        /*c:value_no_change */
        $this->item(
            'value_no_schange',
            /*------------caption---------------*/
            'value no change',                  
            /*------------code------------------*/
            '
            // dont call calback "change"
            {$plugin}.jedit("value",ut.random_str(5),true);
            or
            {$plugin}.jedit("begin","change");
            {$plugin}.jedit("value",ut.random_str(5));
            {$plugin}.jedit("end","change");
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jedit("value",ut.random_str(5),true);
            
            '
        );        


    
        
        /*c:destroy */
        $this->item(
            'destroy',
            /*------------caption---------------*/
            'destroy',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit("destroy");
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jedit("destroy");
            
            '
        );        


        
        /*c:asText */
        $this->item(
            'asText',
            /*------------caption---------------*/
            'asText',                  
            /*------------code------------------*/
            '
            {$plugin}.jedit({attr:{type:"text"}});
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jedit({attr:{type:"text"}});
            
            '
        );        
        
        
        /*c:asPassword */
        $this->item(
            'asPassword',
            /*------------caption---------------*/
            'asPassword',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit({attr:{type:"password"}});
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jedit({attr:{type:"password"}});
            
            '
        );        

        
        /*c:margin */
        $this->item(
            'margin',
            /*------------caption---------------*/
            'margin',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit({margin:10});
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jedit({margin:10});
            
            '
        );        

        
        /*c:select */
        $this->item(
            'select',
            /*------------caption---------------*/
            'select',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit("begin","change");
            
            {$plugin}.jedit("combo","select",function(o){
                return $D(o.tr).NAME=="Troy";
            });
            {$plugin}.jedit("end","change");
            
            ',
            /*------------event-----------------*/
            '
            {$plugin}.jedit("begin","change");
            
            {$plugin}.jedit("combo","select",function(o){
                return $D(o.tr).NAME=="Troy";
            });
            {$plugin}.jedit("end","change");
            
            '
        );        

        /*c:set_from_data */
        $this->item(
            'setData',
            /*------------caption---------------*/
            'setData',                  
            /*------------code------------------*/
            '
            {$plugin}.jedit({field:"NAME",onSetData:function(o){
                o.value = o.value.replace("good","bad");
                
            }});
            
            var data = {DATA:"01/02/13",ID:0,NAME:"Mike is good"};
            
            {$plugin}.jedit("setData",data);
            

            ',
            /*------------event-----------------*/
            '
            {$plugin}.jedit({field:"NAME",onSetData:function(o){
                o.value = o.value.replace("good","bad");
                
            }});
            
            var data = {DATA:"01/02/13",ID:0,NAME:"Mike is good"};
            
            {$plugin}.jedit("setData",data);
            
            '
        );        

        /*c:get_from_data */
        $this->item(
            'getData',
            /*------------caption---------------*/
            'getData',                  
            /*------------code------------------*/
            '
            {$plugin}.jedit({field:"NAME"});
            
            var data = {DATA:"01/02/13",ID:0,NAME:"bru bru"};
            
            {$plugin}.jedit("getData",data);
            

            ',
            /*------------event-----------------*/
            '
            {$plugin}.jedit({field:"NAME",onGetData:function(o){
                o.value = o.value.replaceAll("M","N");
            }});
            
            var data = {DATA:"01/02/13",ID:0,NAME:"Mike"};
            
            {$plugin}.jedit("getData",data);
            jconsole(data);
            '
        );   
        
        
        /*c:tip */
        $this->item(
            'tip',
            /*------------caption---------------*/
            'tip',                  
            /*------------code------------------*/
            '
            /** всплывающее сообщение для контрола */
            /** простой пример */
            {$plugin}.jedit({
                tip:{
                    msg:"text in tip box",
                    show:true
                }
            });
            
            /** расширенные настройки */
            {$plugin}.jedit({
                tip:{
                    msg     :"text in tip",
                    place   :"right", /* bottom | top | right | left */
                    width   :"auto", /* int| auto |edit */
                    height  :"auto", /* int | auto */
                    modal   :false,
                    delay   :20000,
                    arrOff  :"center",/** place arrow, arrOff =  int | "center"   */  
                    arrW:   14, 
                    show    :false, 
                    padding:{left:5,top:5,bottom:5,right:20},
                    pivot:{}  /* pivot:{a:"left",b:"right"}   a-plugin   b-tip  */
                }                
            });
            ',
            /*------------event-----------------*/
            '
            {$plugin}.jedit({
               disables:{
                    icon_tip:false,
                }
            });
            {$plugin}.jedit({
                tip:{
                    msg:"text in tip box",
                    modal:true,
                    delay:0
                    
                }
            });
            
            '
        );        
        
        
        /*c:tip_example1 */
        $this->item(
            'tip_example1',
            /*------------caption---------------*/
            'tip bottom',                  
            /*------------code------------------*/
            '
            {$plugin}.jedit({
                tip:{
                    msg     :"string1<br>string2<br>string3<br>string4",
                    place   :"bottom",
                    show    : true,
                    width   :"auto",
                }                
            });
            
            ', 
            /*------------event-----------------*/
            '
            {$plugin}.jedit({
                tip:{
                    msg     :"string1<br>string2<br>string3<br>string4",
                    place   :"bottom",
                    show    : true,
                    width   :"edit",
                    pivot:  {a:"center",b:"center"}
                }                
            });
            '
        );        
        /*c:tip_example1 */
        $this->item(
            'tip_example2',
            /*------------caption---------------*/
            'tip right',                  
            /*------------code------------------*/
            '
            {$plugin}.jedit({
                tip:{
                    msg     :"string1<br>string2<br>string3<br>string4",
                    place   :"bottom",
                    show    : true,
                    width   :"auto",
                    arrOff  : "center",
                    pivot:  {a:"top",b:"top"}
                    
                }                
            });
            
            ', 
            /*------------event-----------------*/
            '
            {$plugin}.jedit({
                tip:{
                    msg     :"string1<br>string2<br>string3<br>string4",
                    place   :"right",
                    show    : true,
                    width   :"auto",
                    arrOff  : "center",
                    pivot:  {a:"top",b:"top"}
                    
                }                
            });
            '
        );        

        
        /*c:error */
        $this->item(
            'error',
            /*------------caption---------------*/
            'error',                  
            /*------------code------------------*/
            '
            
            {$plugin}.jedit({
                error:!{$plugin}.jedit("get","error")
            });
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jedit({
                error:!{$plugin}.jedit("get","error")
            });
            
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
                /*
                background:#242424;
                color:gray;
                */
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
        
        FRAME('top')->VALUE('jedit');
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
    
    $app->title = 'jedit';
    $app->RUN();
};

?>