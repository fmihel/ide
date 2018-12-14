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

RESOURCE('plugins','form/form.js');
RESOURCE('plugins','form/form.dcss');

RESOURCE('plugins','shadow/jshadow.js');

RESOURCE('plugins','jedit/jedit.dcss');
RESOURCE('plugins','jedit/jedit.js');

RESOURCE('plugins','grid/grid.js');
RESOURCE('plugins','grid/grid.dcss');    

//RESOURCE('plugins','mform/mform.js');
//RESOURCE('plugins','mform/mform.dcss');

require_once UNIT('utils','framet.php');
RESOURCE('plugins','common/framet.js');

class TWS extends WS{
        

    public function plugin(){
        $edits = '';
        $js = '';
        for($i=6;$i<50;$i++){
            $edits.='<edit'.$i.' "edit" |edit'.$i.'|>';
            $js.='{$edit'.$i.'}.jedit({type:"edit"});';
        };
        FRAMET('
            <edit1 "edit" |Label|>
            <edit2 "edit" |Combo|>
            <edit3 "edit" |Button|>
            <edit4 "edit" |Edit|>
            <edit5 "memo" |Memo|>
            '.$edits.'
        ',FRAME('plugin'))
        ->CSS('
        .edit{
            height:32px;
            width:300px
        }
        .memo{
            height:80px;
            width:300px
        }
        
        ')
        ->INIT('
        
            {$edit1}.jedit({type:"text",value:"labeled text"});
            '.$js.'
            {$edit2}.jedit({type:"button",
            disables:{
                btn_combo:false,    
            },
            combo:{
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
                    jconsole("click on:"+$D(o.tr).NAME);
                }
            }});
            
            {$edit3}.jedit({type:"button"});
            {$edit4}.jedit({});
            {$edit5}.jedit({type:"memo"});
            
            {$plugin}.mform({
                width:400,
                height:400,
                modal:false,
                onAlign:function(){
                    
                    JX.accom({$plugin}.children());
                    console.info("start..");
                    var tt = 0;
                    for(let i=0;i<10;i++){
                        let p = ut.random_str(100);
                        tt=p;
                    }    
                    
                    console.info("stop..",tt)
                },
                buttons:{
                    ok:function(){
                        jconsole("ok");
                    },
                    cancel:{click:function(){
                        jconsole("cancel");
                    }}
                }
                
                
            });
            
            
        ')->READY('
            {$quickStart}.trigger("click");
            activatePanel();
        ');
        

    }

    private function menu(){
        
        FRAME('left')->ALIGN('JX.arrange({$}.children(),{direct:"vert",type:"top",align:"center",gap:2,margin:{top:10}})');

        $this->item(
            'quickStart',                   //id
            'Quick start',                  //caption
            '
            /** default settings for form */
            {$plugin}.mform({
                width:400,
                height:400,
                showFooter:false,
                showHeader:false,
                draggable:true,
                resizable:true,
                modal:true,
                onAlign:function(){
                    // расположение элементов внутри 
                    JX.accom({$plugin}.children());
                }
                
                
            });
            ',      //sample code
            '
                {$plugin}.mform({});
            '          //onclick event
        );
        
        
        /*c:open */
        $this->item(
            'open',
            /*------------caption---------------*/
            'open',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // Открыть форму:
            //--------------------------------------
            
            {$plugin}.mform("open");
            
            

            //--------------------------------------
            // Открыть форму (и задать параметры)
            //--------------------------------------
            
            {$plugin}.mform("open",param);
            
            
            
            ',
            /*------------event-----------------*/
            '
            {$plugin}.mform("open");
            '
        );        

        
        /*c:close */
        $this->item(
            'close',
            /*------------caption---------------*/
            'close',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // Закрыть форму
            //--------------------------------------
            
            {$plugin}.mform("close");
            ',
            /*------------event-----------------*/
            '
            {$plugin}.mform("close");
            
            '
        );        

        
        /*c:modalView */
        $this->item(
            'modalView',
            /*------------caption---------------*/
            'modal',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // модальный вид формы
            // нужно включить до вызова open
            //--------------------------------------
            
            {$plugin}.mform({modal:true});
            
            ',
            /*------------event-----------------*/
            '
            
            var m = {$plugin}.mform("modal");
            {$plugin}.mform({modal:!m});
            {$}.mbtn({active:!m});

            ',
            '   
            {$}.mbtn({active:{$plugin}.mform("modal")});

            '
        );

        
        /*c:showFooter */
        $this->item(
            'showFooter',
            /*------------caption---------------*/
            'showFooter',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // показать подвал для кнопок 
            //--------------------------------------
            
            {$plugin}.mform({showFooter:true});
            
            ',
            /*------------event-----------------*/
            '
            var m = {$plugin}.mform("showFooter");
            {$plugin}.mform({showFooter:!m});
            {$}.mbtn({active:!m});
            ',
            '
                {$}.mbtn({active:{$plugin}.mform("showFooter")});
            '
        );        
        
        
        /*c:showHeader */
        $this->item(
            'showHeader',
            /*------------caption---------------*/
            'showHeader',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // показать заголовок для кнопки close и caption
            //--------------------------------------
            
            {$plugin}.mform({showHeader:true});
            
            ',
            /*------------event-----------------*/
            '
            
            var m = {$plugin}.mform("showHeader");
            {$plugin}.mform({showHeader:!m});
            {$}.mbtn({active:!m});
            ',
            '   {$}.mbtn({active:{$plugin}.mform("showHeader")});'
        );        


    
        
        /*c:caption */
        $this->item(
            'caption',
            /*------------caption---------------*/
            'caption',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // задать текст заголовка 
            //--------------------------------------
            
            {$plugin}.mform({caption:"text in caption"});
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.mform({caption:ut.random_str(10)});
            
            '
        );        


        
        /*c:draggable */
        $this->item(
            'draggable',
            /*------------caption---------------*/
            'draggable',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // включить/выключить режим переьаскивания формы (за заголовок)
            //--------------------------------------
            
            {$plugin}.mform({draggable:true});

            ',
            /*------------event-----------------*/
            '
            
            var m = {$plugin}.mform("draggable");
            {$plugin}.mform({draggable:!m});
            {$}.mbtn({active:!m});
            
            ',
            '
            {$}.mbtn({active:{$plugin}.mform("draggable")});
            '
        );        
        /*c:dragOnAllForm */
        $this->item(
            'dragOnAllForm',
            /*------------caption---------------*/
            'dragOnAllForm',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // перетаскиваем за всю форму
            //--------------------------------------
            
            {$plugin}.mform({dragOnAllForm:true});

            ',
            /*------------event-----------------*/
            '
            
            var m = {$plugin}.mform("dragOnAllForm");
            {$plugin}.mform({dragOnAllForm:!m});
            {$}.mbtn({active:!m});
            
            ',
            '
            {$}.mbtn({active:{$plugin}.mform("dragOnAllForm")});
            '
        );        

        
        
        /*c:resizable */
        $this->item(
            'resizable',
            /*------------caption---------------*/
            'resizable',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // включить выключить режим изменения размера формы 
            //--------------------------------------
            
            {$plugin}.mform({resizable:true});
            
            ',
            /*------------event-----------------*/
            '
            
            var m = {$plugin}.mform("resizable");
            {$plugin}.mform({resizable:!m});
            {$}.mbtn({active:!m});
            
            
            
            ',
            '
            {$}.mbtn({active:{$plugin}.mform("resizable")});
            '
        );        


        
        /*c:needCloseBtn */
        $this->item(
            'needCloseBtn',
            /*------------caption---------------*/
            'needCloseBtn',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // включить/выключить кнопку закрытия формы
            //--------------------------------------
            
            {$plugin}.mform({needCloseBtn:true});
            
            ',
            /*------------event-----------------*/
            '
            
            var m = {$plugin}.mform("needCloseBtn");
            {$plugin}.mform({needCloseBtn:!m});
            {$}.mbtn({active:!m});
            
            ',
            '
            {$}.mbtn({active:{$plugin}.mform("needCloseBtn")});
            '
        );        

        /*c:placeCloseOnTopRight */
        $this->item(
            'placeCloseOnTopRight',
            /*------------caption---------------*/
            'close btn pos',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // позиция кнопки закрытия 
            // true - вынесена вверхний правый угол
            // false - внутри заголовк 
            //--------------------------------------
            
            {$plugin}.mform({placeCloseOnTopRight:true});
            
            ',
            /*------------event-----------------*/
            '
            
            var m = {$plugin}.mform("placeCloseOnTopRight");
            {$plugin}.mform({placeCloseOnTopRight:!m});
            {$}.mbtn({active:!m});
            
            ',
            '
            {$}.mbtn({active:{$plugin}.mform("placeCloseOnTopRight")});
            '
        );        
        
        
        /*c:shadowAsClose */
        $this->item(
            'shadowAsClose',
            /*------------caption---------------*/
            'shadowAsClose',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // нажатие на тень (вне формы) рассматривается как нажатие кнопки close
            //--------------------------------------
            
            {$plugin}.mform({shadowAsClose:true});
            
            ',
            /*------------event-----------------*/
            '
            
            var m = {$plugin}.mform("shadowAsClose");
            {$plugin}.mform({shadowAsClose:!m});
            {$}.mbtn({active:!m});
            
            ',
            '
            {$}.mbtn({active:{$plugin}.mform("shadowAsClose")});
            '            
        );        
        
        /*c:showPin */
        $this->item(
            'showPin',
            /*------------caption---------------*/
            'showPin',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // кнопка фиксации 
            //--------------------------------------
            
            {$plugin}.mform({showPin:true});
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.mform({showPin:(!{$plugin}.mform("showPin"))});
            
            '
        );        

        
        /*c:shadowOpacity */
        $this->item(
            'shadowOpacity',
            /*------------caption---------------*/
            'shadowOpacity',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // уровень тени (1 - непрозрачность, 0 - макс прозрачность)
            //--------------------------------------
            
            {$plugin}.mform({shadowOpacity:0.9});
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.mform({shadowOpacity:0.9});
            
            '
        );        


    
        
        
        /*c:stretch */
        $this->item(
            'stretch',
            /*------------caption---------------*/
            'stretch',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // растягивание формы
            // custom   - не растягивать
            // horiz    - полностью по горизонтали
            // fullscreen - на весь экран ( можно задать margin )
            //--------------------------------------
            
            /** custom| horiz| fullscreen */
            {$plugin}.mform({
                stretch:"custom"
            }); 
            //use margin for set off on fullscreen mode
            {$plugin}.mform({
                stretch:"fullscreen",
                margin:50
                
            }); 
            ',
            /*------------event-----------------*/
            '
            
            var m = {$plugin}.mform("stretch");
            
            if (m==="custom") m = "horiz";
            else if (m==="horiz") m = "fullscreen";
            else if (m==="fullscreen") m = "custom";
            
            {$plugin}.mform({stretch:m,margin:50});
            {$}.mbtn({caption:"stretch:"+m});
            
            ','
                {$}.mbtn({caption:"stretch:"+{$plugin}.mform("stretch")});
            '
        );        

        
        /*c:addButton */
        $this->item(
            'addButton',
            /*------------caption---------------*/
            'add button',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // добавляем кнопку в подвал (сокращенная версия)
            //--------------------------------------
            
            
            {$plugin}.mform({
                buttons:{
                    ok:function(){
                        console.info("ok");
                    }
                }
            })
            
            
            //--------------------------------------
            // полная версия
            //--------------------------------------
            
            {$plugin}.mform({
                buttons:{
                    ok:{
                        caption:"save",
                        click:function(){
                            cosnole.info("ok")
                        }
                    }
                }
            })
            
            ',
            /*------------event-----------------*/
            '
            
            
            
            '
        );        

        
        /*c:disableButton */
        $this->item(
            'disableButton',
            /*------------caption---------------*/
            'disable button',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // сделать кнопку некликабельной
            //--------------------------------------
            
            var btns = {$plugin}.mform("buttons");
            {$plugin}.mform({button:{id:"ok",disable:!btns.id.disable}});

            ',
            /*------------event-----------------*/
            '
            var btns = {$plugin}.mform("buttons");
            {$plugin}.mform({button:{id:"ok",disable:!btns.ok.disable}});
            
            
            '
        );        
        /*c:captionButton */
        $this->item(
            'captionButton',
            /*------------caption---------------*/
            'caption button',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // поменять текст на кнопке
            //--------------------------------------
            
            var btns = {$plugin}.mform("buttons");
            {$plugin}..mform({button:{id:"ok",caption:"new"}});
            
            ',
            /*------------event-----------------*/
            '
            var btns = {$plugin}.mform("buttons");
            {$plugin}.mform({button:{id:"ok",caption:ut.random_str(6)}});
            '
        );        

        /*c:buttonPlace */
        $this->item(
            'buttonPlace',
            /*------------caption---------------*/
            'buttonPlace',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // расположение кнопко внутри подвала
            // left| right| center
            //--------------------------------------
            
            
            {$plugin}.mform({
                buttonPlace:"center"
            }); 
            ',
            /*------------event-----------------*/
            '
            
            var m = {$plugin}.mform("buttonPlace");
            
            if (m==="left") m = "right";
            else if (m==="right") m = "center";
            else if (m==="center") m = "left";
            
            {$plugin}.mform({buttonPlace:m});
            {$}.mbtn({caption:m});
            
            ','
                {$}.mbtn({caption:{$plugin}.mform("buttonPlace")});
            '
        );        

    
        
        
        /*c:posAlign */
        $this->item(
            'posAlign',
            /*------------caption---------------*/
            'position align',                   
            /*------------code------------------*/
            '
            //--------------------------------------
            // авто расположение формы
            // keep = true - будет стараться сохранять позицию формы
            //--------------------------------------
            
            {$plugin}.mform({
                margin:{top:-200,left:0},
                position:{
                    type:"align",
                    keep:true,
                    align:{
                        vert:"center",
                        horiz:"center"
                    }
                }
                
            });
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.mform({
                margin:{top:-200,left:0},
                position:{
                    type:"align",
                    keep:true,
                    align:{
                        vert:"center",
                        horiz:"center"
                    }
                }
                
            });
            
            '
        );        

        /*c:posCustom */
        $this->item(
            'posCustom',
            /*------------caption---------------*/
            'position custom',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // задание позиции формы вручную
            //--------------------------------------
            
            {$plugin}.mform({
                position:{
                    type:"custom",
                    x:300,
                    y:100
                }
                
            });
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.mform({
                position:{
                    type:"custom",
                    x:500,
                    y:100
                }
                
            });
            
            '
        );        

        /*c:posClinger */
        $this->item(
            'posClinger',
            /*------------caption---------------*/
            'position cling',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // прилипание формы к объекту
            //--------------------------------------
            
            {$plugin}.mform({
                position:{
                    type:"cling",
                    cling:{side:{a:"bottom",b:"top"},pivot:{a:"center",b:"center"}},
                    clinger:jQuery object
                }
                
            });
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.mform({
                position:{
                    type:"cling",
                    cling:{side:{a:"left",b:"left"},pivot:{a:"top",b:"top"},cross:true},
                    clinger:Qs.pluginFrame
                }
                
            });
            
            '
        );        
    
        
        
        /*c:onOpen */
        $this->item(
            'onOpen',
            /*------------caption---------------*/
            'onOpen',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // событие при открытии формы
            //--------------------------------------
            
            {$plugin}.mform({
                onOpen:function(){
                    console.info("on");
                }
            });
            
            ',
            /*------------event-----------------*/
            '
            var m = {$plugin}.mform("onOpen")===undefined;
            if (m)
                {$plugin}.mform({onOpen:function(){
                    jconsole("onOpen");
                }});
            else
                {$plugin}.mform({onOpen:undefined});
                
            {$}.mbtn({active:m});
            
            '
        );        
        
        /*c:onClose */
        $this->item(
            'onClose',
            /*------------caption---------------*/
            'onClose',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // событие при закрытии формы
            //--------------------------------------
            
            {$plugin}.mform({
                onClose:function(){
                    console.info("on");
                }
            });
            
            ',
            /*------------event-----------------*/
            '
            var m = {$plugin}.mform("onClose")===undefined;
            if (m)
                {$plugin}.mform({onClose:function(){
                    jconsole("onClose");
                }});
            else
                {$plugin}.mform({onClose:undefined});
                
            {$}.mbtn({active:m});
            
            '
        );        
        
        /*c:canClose */
        $this->item(
            'canClose',
            /*------------caption---------------*/
            'canClose',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // если определено, то должен венрнуть true чтобы форма закрылась
            //--------------------------------------
            
            {$plugin}.mform({
                canClose:function(){
                    
                    return false; 
                }
            });
            
            ',
            /*------------event-----------------*/
            '
            var m = {$plugin}.mform("canClose")===undefined;
            if (m)
                {$plugin}.mform({canClose:function(){
                    
                    var ret = (ut.random(1,3)===1);
                    
                    jconsole("canClose:"+(ret?"true":"false"));
                    
                    return ret;
                    
                    
                }});
            else
                {$plugin}.mform({canClose:undefined});
                
            {$}.mbtn({active:m});
            
            '
        );        

    
        /*c:onAlign */
        $this->item(
            'onAlign',
            /*------------caption---------------*/
            'onAlign',                  
            /*------------code------------------*/
            '
            //--------------------------------------
            // вызывает для перерисовки объектов внутри формы 
            // (после перерисовки внешней части формы)
            //--------------------------------------
            
            {$plugin}.mform({
                onAlign:function(){
                
                    JX.accom({$plugin}.children());
                    
                }
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
                background:#303130;
                color:silver;
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
                cursor:pointer;
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
            var p=function(name,def){
                var type = typeof def;
                var m = {$plugin}.mform(name);
                
                if (def!==undefined){
                    if (m!==def){
                        if (type==="boolean")
                            return "      "+name+":"+(m?"true":"false")+",\n";
                        return "      "+name+":"+(typeof m==="string"?"`"+m+"`":m)+",\n";
                    } 
                }else
                    return "      "+name+":"+(typeof m==="string"?"`"+m+"`":m)+",\n";
                return "";    
            }
            
            var t = {$plugin}.mform("position");
            var pos =""
            pos+="      position:{\n";
            pos+="          type:`"+t.type+"`,\n"
            if (t.type==="custom"){
            pos+="          x:"+t.x+",\n";
            pos+="          y:"+t.y+",\n";
            }else{
            
            pos+="          align:{\n";
            pos+="             vert:`"+t.align.vert+"`,\n";
            pos+="             horiz:`"+t.align.horiz+"`,\n";
            pos+="          }\n";
                
            }
            pos+="      },\n";
            
            
            var onon = "";
            if ({$plugin}.mform("onOpen")!==undefined){
            onon+="      onOpen:function(){\n";
            onon+="           console.info(`onOpen`);\n";
            onon+="      },\n";
            }
            if ({$plugin}.mform("onClose")!==undefined){
            onon+="      onClose:function(){\n";
            onon+="           console.info(`onClose`);\n";
            onon+="      },\n";
            }
            
            if ({$plugin}.mform("canClose")!==undefined){
            onon+="      canClose:function(){\n";
            onon+="           console.info(`canClose`);\n";
            onon+="           return true;\n";
            onon+="      },\n";
            
            }
            if ({$plugin}.mform("onAlign")!==undefined){
            onon+="      onAlign:function(){\n";
            onon+="           JX.accom({$plugin}.children());\n";
            onon+="      },\n";
            
            }
            
            
            {$right}.html("<xmp>  \n"
            +"  Qs.plugin.mform({\n"
            
            +p("caption","")
            +p("showHeader",false)
            +p("showFooter",false)
            +p("modal",true)
            +p("resizable",true)
            +p("draggable",true)
            +p("dragOnAllForm",false)
            +p("needCloseBtn",true)
            +p("placeCloseOnTopRight",false)
            +p("shadowAsClose",true)
            +p("stretch","custom")
            +p("buttonPlace","right")
            +onon
            +pos
            
            +"      buttons:{\n"
            +"          ok:function(){\n"
            +"              console.info(`ok`);\n"
            +"          },\n"
            +"          cancel:{\n"
            +"              caption:`close`,\n"
            +"              click:function(o){\n"
            +"                  console.info(`cancel`);\n"
            +"                  o.sender.close();\n"
            +"              },\n"
            +"          },\n"
            +"      },\n"
            +" });\n"
            +"</xmp>");
        
            
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
                JX.arrange({$}.children(),{direct:"vert",type:"stretch",align:"stretch",stretch:[{idx:0,rate:1},{idx:2,rate:20},{idx:4,rate:7}]});
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
            ->INIT('
            {$}.mbtn({
                click:function(){
                    var code = "'.$code.'";
                    
                    jconsole("clear");
                    Qs.codeFrom.text({$}.text());

                    Qs.code.html("<xmp>"+codeNorm(code)+"</xmp>");
                    '.$click.';
                    activatePanel();
                    Ws.align();
                    
                }
            });')
            ->READY($init.';');
            
        //$panel = FRAME($id.'_panel',FRAME('right'))    
        //->CLASSES('item_panel')
        //->STYLE('position:absolute;display:none');
        
        return array('item'=>$item,'panel'=>$panel);
    }
    

    public function CONTENT(){
        $this->layout();
        $this->plugin();

        $this->css();
        $this->javascript();
        $this->menu();
        
        FRAME('top')->VALUE('mform');
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
    
    $app->title = 'mform';
    $app->RUN();
    

}

?>
    